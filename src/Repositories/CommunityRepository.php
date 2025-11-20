<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class CommunityRepository
{
    /**
     * @return array<int,array<string,mixed>>
     */
    public static function approvedPosts(PDO $pdo, int $limit = 6): array
    {
        $stmt = $pdo->prepare(
            'SELECT cp.id,
                    cp.title,
                    cp.body,
                    cp.created_at,
                    ch.title AS challenge_title,
                    c.first_name,
                    c.last_name
             FROM community_posts cp
             INNER JOIN customers c ON cp.customer_id = c.id
             LEFT JOIN challenges ch ON cp.challenge_id = ch.id
             WHERE cp.status = "approved"
             ORDER BY cp.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public static function createPost(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO community_posts (customer_id, challenge_id, title, body, status, created_at)
             VALUES (:customer_id, :challenge_id, :title, :body, :status, NOW())'
        );
        $stmt->execute([
            ':customer_id' => $data['customer_id'],
            ':challenge_id' => $data['challenge_id'],
            ':title' => $data['title'],
            ':body' => $data['body'],
            ':status' => 'pending',
        ]);

        return (int)$pdo->lastInsertId();
    }
}
