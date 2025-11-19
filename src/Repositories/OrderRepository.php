<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;
use PDOException;
use DragonStone\Repositories\CustomerRepository;
use DragonStone\Repositories\EcoPointRepository;

class OrderRepository
{
/**
 * @param array<int,array<string,mixed>> $items
 * @param array<string,mixed> $checkoutData
 */
    public static function create(PDO $pdo, int $customerId, array $items, array $checkoutData, string $sourceType = 'order'): array

    {
        if (empty($items)) {
            throw new \InvalidArgumentException('Cannot create order without cart items.');
        }

        $pdo->beginTransaction();
        try {
            $calculatedSubtotal = array_reduce($items, static function (float $carry, array $item): float {
                return $carry + (float)$item['subtotal'];
            }, 0.0);
            $totalPoints = array_reduce($items, static function (int $carry, array $item): int {
                return $carry + (int)$item['estimated_points'];
            }, 0);

            $totals = $checkoutData['totals'] ?? [];
            $subtotal = $calculatedSubtotal;
            $discount = min($totals['discount'] ?? 0.0, $subtotal);
            $grandTotal = max($subtotal - $discount, 0);
            $currencyCode = $checkoutData['currency_code'] ?? 'USD';
            $currencyRate = $checkoutData['currency_rate'] ?? 1.0;
            $totalConverted = $totals['display_total'] ?? round($grandTotal * $currencyRate, 2);
            $redeemedPoints = (int)($checkoutData['redeemed_points'] ?? 0);

            $orderReference = self::generateOrderReference();
            $stmt = $pdo->prepare(
                'INSERT INTO orders (customer_id, order_reference, subtotal, discount_total, total, total_converted, currency_code, currency_rate, eco_points_awarded, eco_points_redeemed, status, placed_at)
                 VALUES (:customer_id, :reference, :subtotal, :discount, :total, :total_converted, :currency_code, :currency_rate, :points_awarded, :points_redeemed, :status, NOW())'
            );
            $stmt->execute([
                ':customer_id' => $customerId,
                ':reference' => $orderReference,
                ':subtotal' => $subtotal,
                ':discount' => $discount,
                ':total' => $grandTotal,
                ':total_converted' => $totalConverted,
                ':currency_code' => $currencyCode,
                ':currency_rate' => $currencyRate,
                ':points_awarded' => $totalPoints,
                ':points_redeemed' => $redeemedPoints,
                ':status' => 'paid',
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $orderItemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, unit_price_display, currency_code, eco_points)
                 VALUES (:order_id, :product_id, :quantity, :unit_price, :unit_price_display, :currency_code, :eco_points)'
            );
            foreach ($items as $item) {
                $displayUnitPrice = $item['unit_price_display'] ?? round((float)$item['unit_price'] * $currencyRate, 2);
                $orderItemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['unit_price'],
                    ':unit_price_display' => $displayUnitPrice,
                    ':currency_code' => $currencyCode,
                    ':eco_points' => $item['estimated_points'],
                ]);
            }

            $paymentStmt = $pdo->prepare(
                'INSERT INTO payments (order_id, method, amount, amount_converted, currency_code, status, processed_at)
                 VALUES (:order_id, :method, :amount, :amount_converted, :currency_code, :status, NOW())'
            );
            $paymentMethod = $checkoutData['payment_method'] ?? 'card';
            $paymentStmt->execute([
                ':order_id' => $orderId,
                ':method' => $paymentMethod,
                ':amount' => $grandTotal,
                ':amount_converted' => $totalConverted,
                ':currency_code' => $currencyCode,
                ':status' => 'captured',
            ]);

            $shipmentStmt = $pdo->prepare(
                'INSERT INTO shipments (order_id, provider, tracking_number, shipped_at)
                 VALUES (:order_id, :provider, :tracking, NOW())'
            );
            $shipmentStmt->execute([
                ':order_id' => $orderId,
                ':provider' => $checkoutData['shipping_provider'] ?? 'Prototype Courier',
                ':tracking' => $checkoutData['tracking_number'] ?? 'TRACK' . random_int(100000, 999999),
            ]);

            EcoPointRepository::recordOrderAward($pdo, $customerId, $orderId, $orderReference, $totalPoints, $sourceType);
            CustomerRepository::addEcoPoints($pdo, $customerId, $totalPoints);

            if ($redeemedPoints > 0) {
                EcoPointRepository::recordRedemption($pdo, $customerId, $orderReference, $redeemedPoints);
                CustomerRepository::deductEcoPoints($pdo, $customerId, $redeemedPoints);
            }

            $pdo->commit();

            return self::findSummary($pdo, $orderId);
        } catch (PDOException $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    private static function generateOrderReference(): string
    {
        return 'DS-' . strtoupper(bin2hex(random_bytes(3))) . '-' . date('dmy');
    }

    public static function findSummary(PDO $pdo, int $orderId): array
    {
        $orderStmt = $pdo->prepare(
            'SELECT o.*, c.first_name, c.last_name, c.email
             FROM orders o
             INNER JOIN customers c ON o.customer_id = c.id
             WHERE o.id = :id'
        );
        $orderStmt->bindValue(':id', $orderId, PDO::PARAM_INT);
        $orderStmt->execute();
        $order = $orderStmt->fetch();
        if (!$order) {
            return [];
        }

        $itemsStmt = $pdo->prepare(
            'SELECT oi.*, p.name, p.carbon_footprint_kg
             FROM order_items oi
             INNER JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id'
        );
        $itemsStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $itemsStmt->execute();

        $order['items'] = $itemsStmt->fetchAll() ?: [];

        return $order;
    }
}
