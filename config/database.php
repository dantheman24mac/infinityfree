<?php

declare(strict_types=1);

use Dotenv\Dotenv;

/**
 * Load environment variables when running locally.
 */
if (!function_exists('loadEnv')) {
    function loadEnv(string $basePath): void
    {
        if (!file_exists($basePath . '/.env')) {
            return;
        }

        $dotenv = Dotenv::createImmutable($basePath);
        $dotenv->load();
    }
}

/**
 * Create a PDO instance using environment configuration.
 */
if (!function_exists('databaseConnection')) {
    function databaseConnection(): PDO
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db = $_ENV['DB_DATABASE'] ?? 'dragonstone';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $password, $options);
    }
}

