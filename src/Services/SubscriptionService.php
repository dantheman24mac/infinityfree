<?php

declare(strict_types=1);

namespace DragonStone\Services;

use DateInterval;
use DateTimeImmutable;
use DragonStone\Repositories\CustomerRepository;
use DragonStone\Repositories\OrderRepository;
use DragonStone\Repositories\ProductRepository;
use DragonStone\Repositories\SubscriptionRepository;
use PDO;
use RuntimeException;

final class SubscriptionService
{
    /**
     * @return array<string,mixed>
     */
    public static function create(PDO $pdo, array $payload): array
    {
        $customer = CustomerRepository::findOrCreate($pdo, $payload['customer']);
        $productId = (int)$payload['product_id'];
        $quantity = max(1, (int)$payload['quantity']);
        $intervalUnit = in_array($payload['interval_unit'], ['weekly', 'monthly', 'quarterly'], true)
            ? $payload['interval_unit']
            : 'monthly';

        $product = ProductRepository::find($pdo, $productId);
        if ($product === null) {
            throw new RuntimeException('Product not found for subscription.');
        }

        $nextRenewal = self::calculateNextRenewal($intervalUnit);
        $currencyCode = $payload['currency_code'];

        $pdo->beginTransaction();
        try {
            $subscriptionStmt = $pdo->prepare(
                'INSERT INTO subscriptions (customer_id, name, interval_unit, next_renewal, last_processed, currency_code, status, auto_renew, reward_points)
                 VALUES (:customer_id, :name, :interval_unit, :next_renewal, :last_processed, :currency_code, :status, :auto_renew, :reward_points)'
            );
            $subscriptionStmt->execute([
                ':customer_id' => (int)$customer['id'],
                ':name' => $payload['name'],
                ':interval_unit' => $intervalUnit,
                ':next_renewal' => $nextRenewal,
                ':last_processed' => date('Y-m-d'),
                ':currency_code' => $currencyCode,
                ':status' => 'active',
                ':auto_renew' => 1,
                ':reward_points' => max(0, (int)($payload['reward_points'] ?? 0)),
            ]);
            $subscriptionId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO subscriptions_items (subscription_id, product_id, quantity, unit_price_snapshot, last_fulfilled)
                 VALUES (:subscription_id, :product_id, :quantity, :unit_price, :last_fulfilled)'
            );
            $itemStmt->execute([
                ':subscription_id' => $subscriptionId,
                ':product_id' => $productId,
                ':quantity' => $quantity,
                ':unit_price' => (float)$product['price'],
                ':last_fulfilled' => date('Y-m-d'),
            ]);

            $pdo->commit();

            return [
                'subscription_id' => $subscriptionId,
                'customer' => $customer,
                'next_renewal' => $nextRenewal,
            ];
        } catch (\Throwable $throwable) {
            $pdo->rollBack();
            throw $throwable;
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public static function processDueRenewals(PDO $pdo): array
    {
        $dueSubscriptions = self::fetchDueSubscriptions($pdo);
        if (empty($dueSubscriptions)) {
            return [];
        }

        $results = [];
        foreach ($dueSubscriptions as $subscription) {
            $items = self::buildItemsForSubscription($pdo, (int)$subscription['id']);
            if (empty($items)) {
                continue;
            }

            $subtotal = array_reduce($items, static function (float $carry, array $item): float {
                return $carry + (float)$item['subtotal'];
            }, 0.0);

            $requestedPoints = (int)$subscription['reward_points'];
            $availablePoints = (int)($subscription['eco_points'] ?? 0);
            $redeemedPoints = RewardService::clampRedeemablePoints($requestedPoints, $availablePoints, $subtotal);
            $discountValue = RewardService::currencyValue($redeemedPoints);

            $checkoutData = [
                'payment_method' => 'card',
                'shipping_provider' => 'Subscription Courier',
                'currency_code' => $subscription['currency_code'],
                'currency_rate' => CurrencyService::rate($subscription['currency_code']),
                'redeemed_points' => $redeemedPoints,
                'totals' => [
                    'subtotal' => $subtotal,
                    'discount' => $discountValue,
                ],
            ];
            $checkoutData['totals']['grand_total'] = max(
                $checkoutData['totals']['subtotal'] - $checkoutData['totals']['discount'],
                0
            );
            $checkoutData['totals']['display_total'] = CurrencyService::convertFromBase(
                $checkoutData['totals']['grand_total'],
                $subscription['currency_code']
            );

            try {
                $orderSummary = OrderRepository::create(
                    $pdo,
                    (int)$subscription['customer_id'],
                    $items,
                    $checkoutData,
                    'subscription'
                );
                $orderSummary['subscription_id'] = (int)$subscription['id'];
                self::markRenewed($pdo, (int)$subscription['id'], $subscription['interval_unit']);
                $results[] = $orderSummary;
            } catch (\Throwable $throwable) {
                // Record failure but continue processing others
                $results[] = [
                    'subscription_id' => (int)$subscription['id'],
                    'error' => $throwable->getMessage(),
                ];
            }
        }

        return $results;
    }

    private static function calculateNextRenewal(string $intervalUnit): string
    {
        $intervalMap = [
            'weekly' => 'P1W',
            'monthly' => 'P1M',
            'quarterly' => 'P3M',
        ];

        $interval = $intervalMap[$intervalUnit] ?? 'P1M';
        $date = new DateTimeImmutable();
        $next = $date->add(new DateInterval($interval));

        return $next->format('Y-m-d');
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private static function fetchDueSubscriptions(PDO $pdo): array
    {
        $stmt = $pdo->prepare(
            'SELECT s.*, c.email, c.first_name, c.last_name, c.eco_points
             FROM subscriptions s
             INNER JOIN customers c ON s.customer_id = c.id
             WHERE s.status = "active"
               AND s.auto_renew = 1
               AND s.next_renewal <= CURRENT_DATE'
        );
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private static function buildItemsForSubscription(PDO $pdo, int $subscriptionId): array
    {
        $stmt = $pdo->prepare(
            'SELECT si.product_id,
                    si.quantity,
                    si.unit_price_snapshot,
                    p.name,
                    p.sustainability_score
             FROM subscriptions_items si
             INNER JOIN products p ON si.product_id = p.id
             WHERE si.subscription_id = :id'
        );
        $stmt->bindValue(':id', $subscriptionId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll() ?: [];

        return array_map(static function (array $row): array {
            $unitPrice = (float)$row['unit_price_snapshot'];
            $quantity = (int)$row['quantity'];

            return [
                'product_id' => (int)$row['product_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
                'estimated_points' => (int)$row['sustainability_score'] * $quantity,
            ];
        }, $rows);
    }

    private static function markRenewed(PDO $pdo, int $subscriptionId, string $intervalUnit): void
    {
        $nextRenewal = self::calculateNextRenewal($intervalUnit);
        $stmt = $pdo->prepare('UPDATE subscriptions SET next_renewal = :next, last_processed = CURRENT_DATE WHERE id = :id');
        $stmt->execute([
            ':next' => $nextRenewal,
            ':id' => $subscriptionId,
        ]);
    }
}
