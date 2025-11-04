<?php

declare(strict_types=1);

namespace DragonStone\Repositories;

use PDO;

class CustomerRepository
{
    public static function findByEmail(PDO $pdo, string $email): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM customers WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO customers (first_name, last_name, email, password_hash, phone, city, country, eco_points)
             VALUES (:first_name, :last_name, :email, :password_hash, :phone, :city, :country, :eco_points)'
        );
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':phone' => $data['phone'] ?? null,
            ':city' => $data['city'] ?? null,
            ':country' => $data['country'] ?? null,
            ':eco_points' => $data['eco_points'] ?? 0,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function updateContact(PDO $pdo, int $customerId, array $data): void
    {
        $stmt = $pdo->prepare(
            'UPDATE customers
             SET first_name = :first_name,
                 last_name = :last_name,
                 phone = :phone,
                 city = :city,
                 country = :country
             WHERE id = :id'
        );
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':phone' => $data['phone'] ?? null,
            ':city' => $data['city'] ?? null,
            ':country' => $data['country'] ?? null,
            ':id' => $customerId,
        ]);
    }

    public static function findOrCreate(PDO $pdo, array $data): array
    {
        $existing = self::findByEmail($pdo, $data['email']);
        if ($existing !== null) {
            self::updateContact($pdo, (int)$existing['id'], $data);
            return self::findById($pdo, (int)$existing['id']) ?? $existing;
        }

        $passwordHash = password_hash('dragonstone-prototype', PASSWORD_BCRYPT);
        $customerId = self::create($pdo, [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'eco_points' => $data['eco_points'] ?? 0,
        ]);

        return self::findById($pdo, $customerId) ?? [];
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result ?: null;
    }

    public static function addEcoPoints(PDO $pdo, int $customerId, int $points): void
    {
        $stmt = $pdo->prepare('UPDATE customers SET eco_points = eco_points + :points WHERE id = :id');
        $stmt->execute([
            ':points' => $points,
            ':id' => $customerId,
        ]);
    }
}
