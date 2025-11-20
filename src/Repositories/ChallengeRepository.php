<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class ChallengeRepository
{
    /**
     * Retrieve all challenges meant for community browsing (scheduled + active).
     *
     * @return array<int,array<string,mixed>>
     */
    public static function publicChallenges(PDO $pdo): array
    {
        $stmt = $pdo->query(
            "SELECT id,
                    title,
                    description,
                    start_date,
                    end_date,
                    eco_points_reward,
                    status
             FROM challenges
             WHERE status IN ('scheduled','active')
             ORDER BY start_date ASC"
        );

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Retrieve every challenge for administration.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query(
            "SELECT id,
                    title,
                    description,
                    start_date,
                    end_date,
                    eco_points_reward,
                    status
             FROM challenges
             ORDER BY start_date DESC"
        );

        return $stmt->fetchAll() ?: [];
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO challenges (title, description, start_date, end_date, eco_points_reward, status)
             VALUES (:title, :description, :start_date, :end_date, :reward, :status)'
        );
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':reward' => $data['eco_points_reward'],
            ':status' => $data['status'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function exists(PDO $pdo, int $challengeId): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM challenges WHERE id = :id AND status IN ("scheduled","active") LIMIT 1');
        $stmt->bindValue(':id', $challengeId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }
}
