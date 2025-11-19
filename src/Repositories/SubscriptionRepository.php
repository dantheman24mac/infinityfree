<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class SubscriptionRepository
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public static function allWithItems(PDO $pdo, ?int $customerId = null): array
    {
        if ($customerId === null) {
            return [];
        }

        $stmt = $pdo->prepare(
            'SELECT s.id,
                    s.name,
                    s.interval_unit,
                    s.next_renewal,
                    s.status,
                    s.currency_code,
                    s.reward_points,
                    c.first_name,
                    c.last_name,
                    c.email
             FROM subscriptions s
             INNER JOIN customers c ON s.customer_id = c.id
             WHERE s.customer_id = :customer_id
             ORDER BY s.next_renewal'
        );
        $stmt->bindValue(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();

        $subscriptions = $stmt->fetchAll() ?: [];
        if (empty($subscriptions)) {
            return [];
        }

        $subscriptionIds = array_map(static fn(array $row): int => (int)$row['id'], $subscriptions);
        $placeholders = implode(',', array_fill(0, count($subscriptionIds), '?'));
        $itemStmt = $pdo->prepare(
            "SELECT si.subscription_id,
                    si.quantity,
                    si.unit_price_snapshot,
                    p.name,
                    p.price,
                    p.sustainability_score
             FROM subscriptions_items si
             INNER JOIN products p ON si.product_id = p.id
             WHERE si.subscription_id IN ($placeholders)
             ORDER BY p.name"
        );
        foreach ($subscriptionIds as $index => $id) {
            $itemStmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $itemStmt->execute();
        $items = $itemStmt->fetchAll() ?: [];

        $grouped = [];
        foreach ($items as $item) {
            $grouped[(int)$item['subscription_id']][] = $item;
        }

        return array_map(static function (array $subscription) use ($grouped): array {
            $id = (int)$subscription['id'];
            $subscription['items'] = $grouped[$id] ?? [];
            $subscription['estimated_points'] = array_reduce(
                $subscription['items'],
                static fn(int $carry, array $item): int => $carry + ((int)$item['sustainability_score'] * (int)$item['quantity']),
                0
            );
            return $subscription;
        }, $subscriptions);
    }
}
