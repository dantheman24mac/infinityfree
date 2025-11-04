<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class CategoryRepository
{
    /**
     * Fetch a limited list of categories for the homepage.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function featured(PDO $pdo, int $limit = 6): array
    {
        $stmt = $pdo->prepare(
            'SELECT id, name, description FROM categories ORDER BY name LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Retrieve all categories for filtering.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function all(PDO $pdo): array
    {
        $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');

        return $stmt->fetchAll() ?: [];
    }
}

