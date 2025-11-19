<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class ProductRepository
{
    /**
     * Fetch top products ordered by sustainability score and recency.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function featured(PDO $pdo, int $limit = 3): array
    {
        $stmt = $pdo->prepare(
            'SELECT p.id,
                    p.name,
                    p.summary,
                    p.price,
                    p.sustainability_score,
                    p.carbon_footprint_kg,
                    c.name AS category_name
             FROM products p
             INNER JOIN categories c ON p.category_id = c.id
             ORDER BY p.sustainability_score DESC, p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Browse products with optional category filter.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function browse(PDO $pdo, ?int $categoryId = null): array
    {
        $query = 'SELECT p.id,
                         p.name,
                         p.summary,
                         p.price,
                         p.carbon_footprint_kg,
                         c.name AS category_name
                  FROM products p
                  INNER JOIN categories c ON p.category_id = c.id';

        if ($categoryId !== null) {
            $query .= ' WHERE p.category_id = :categoryId';
        }

        $query .= ' ORDER BY p.name';

        $stmt = $pdo->prepare($query);
        if ($categoryId !== null) {
            $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Find a single product with category relationship.
     *
     * @return array<string,mixed>|null
     */
    public static function find(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT p.*,
                    c.name AS category_name
             FROM products p
             INNER JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Retrieve sustainability metrics for a product.
     *
     * @return array<int,array<string,string>>
     */
    public static function impactMetrics(PDO $pdo, int $productId): array
    {
        $stmt = $pdo->prepare(
            'SELECT metric_label, metric_value, baseline_comparison
             FROM product_impact_metrics
             WHERE product_id = :id
             ORDER BY metric_label'
        );
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Fetch product tags as a simple list.
     *
     * @return array<int,string>
     */
    public static function tags(PDO $pdo, int $productId): array
    {
        $stmt = $pdo->prepare(
            'SELECT t.label
             FROM product_tags pt
             INNER JOIN tags t ON pt.tag_id = t.id
             WHERE pt.product_id = :id
             ORDER BY t.label'
        );
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $result ?: [];
    }

    /**
     * Fetch products by ID list keyed by ID.
     *
     * @param array<int,int> $ids
     * @return array<int,array<string,mixed>>
     */
    public static function findByIds(PDO $pdo, array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p
             INNER JOIN categories c ON p.category_id = c.id
             WHERE p.id IN ($placeholders)"
        );
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll() ?: [];
        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Highlight impact metrics across products for the homepage table.
     *
     * @return array<int,array<string,string|int>>
     */
    public static function impactHighlights(PDO $pdo, int $limit = 4): array
    {
        $stmt = $pdo->prepare(
            'SELECT p.name AS product_name,
                    c.name AS category_name,
                    pim.metric_label,
                    pim.metric_value,
                    p.sustainability_score
             FROM product_impact_metrics pim
             INNER JOIN products p ON pim.product_id = p.id
             INNER JOIN categories c ON p.category_id = c.id
             ORDER BY p.sustainability_score DESC, pim.metric_label
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll() ?: [];

        return array_map(static function (array $row): array {
            return [
                'product' => $row['product_name'],
                'category' => $row['category_name'],
                'impact' => sprintf('%s: %s', $row['metric_label'], $row['metric_value']),
                'points' => (int)$row['sustainability_score'] * 2,
            ];
        }, $rows);
    }

    /**
     * Retrieve subscription-eligible products for quick selection.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function subscriptionEligible(PDO $pdo, int $limit = 10): array
    {
        $stmt = $pdo->prepare(
            'SELECT p.id, p.name, p.price, p.carbon_footprint_kg
             FROM products p
             WHERE p.subscription_eligible = 1
             ORDER BY p.name
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }
}
