<?php

declare(strict_types=1);

namespace DragonStone\Auth;

use PDO;

class AdminAuth
{
    private const SESSION_KEY = 'admin_user_id';

    public static function attempt(PDO $pdo, string $email, string $password): bool
    {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = :email AND is_active = 1 LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $admin = $stmt->fetch();
        if (!$admin) {
            return false;
        }

        if (!password_verify($password, $admin['password_hash'])) {
            return false;
        }

        $_SESSION[self::SESSION_KEY] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];

        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION['admin_name']);
    }

    public static function id(): ?int
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }
}
