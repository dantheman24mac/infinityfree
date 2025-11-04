<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class AdminRepository
{
    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = :id AND is_active = 1 LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $admin = $stmt->fetch();

        return $admin ?: null;
    }

    public static function roles(PDO $pdo, int $adminId): array
    {
        $stmt = $pdo->prepare(
            'SELECT r.* FROM roles r
             INNER JOIN admin_roles ar ON r.id = ar.role_id
             WHERE ar.admin_id = :admin_id'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public static function permissions(PDO $pdo, int $adminId): array
    {
        $stmt = $pdo->prepare(
            'SELECT p.code
             FROM permissions p
             INNER JOIN role_permissions rp ON p.id = rp.permission_id
             INNER JOIN admin_roles ar ON rp.role_id = ar.role_id
             WHERE ar.admin_id = :admin_id'
        );
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        $stmt->execute();

        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return array_unique($permissions ?: []);
    }
}
