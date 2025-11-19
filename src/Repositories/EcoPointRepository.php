<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class EcoPointRepository
{
    public static function findRuleByKey(PDO $pdo, string $key): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM ecopoint_rules WHERE action_key = :key AND is_active = 1 LIMIT 1');
        $stmt->bindValue(':key', $key);
        $stmt->execute();

        $rule = $stmt->fetch();

        return $rule ?: null;
    }

    public static function recordOrderAward(PDO $pdo, int $customerId, int $orderId, string $orderReference, int $points, string $sourceType = 'order'): void
    {
        if ($points <= 0) {
            return;
        }

        $rule = self::findRuleByKey($pdo, 'order.completed');
        $ruleId = $rule['id'] ?? null;

        $stmt = $pdo->prepare(
            'INSERT INTO ecopoint_transactions (customer_id, rule_id, source_type, source_reference, points)
             VALUES (:customer_id, :rule_id, :source_type, :source_reference, :points)'
        );
        $stmt->execute([
            ':customer_id' => $customerId,
            ':rule_id' => $ruleId,
            ':source_type' => $sourceType,
            ':source_reference' => $orderReference,
            ':points' => $points,
        ]);
    }

    public static function recordManualAdjustment(PDO $pdo, int $customerId, int $points, string $note, ?int $adminId = null): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO ecopoint_transactions (customer_id, rule_id, source_type, source_reference, points, created_by_admin)
             VALUES (:customer_id, NULL, :source_type, :reference, :points, :admin_id)'
        );
        $stmt->execute([
            ':customer_id' => $customerId,
            ':source_type' => 'manual',
            ':reference' => $note,
            ':points' => $points,
            ':admin_id' => $adminId,
        ]);
    }

    public static function recordRedemption(PDO $pdo, int $customerId, string $orderReference, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO ecopoint_transactions (customer_id, rule_id, source_type, source_reference, points)
             VALUES (:customer_id, NULL, :source_type, :source_reference, :points)'
        );
        $stmt->execute([
            ':customer_id' => $customerId,
            ':source_type' => 'redemption',
            ':source_reference' => $orderReference,
            ':points' => $points * -1,
        ]);
    }
}
