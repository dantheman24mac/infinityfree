<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;
use DragonStone\Repositories\AdminRepository;
use DragonStone\Repositories\OrderRepository;
use DragonStone\Repositories\ProductRepository;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false || !file_exists($basePath . '/vendor/autoload.php')) {
    $basePath = realpath(dirname(__DIR__));
}

require_once $basePath . '/vendor/autoload.php';
require_once $basePath . '/config/database.php';
require_once $basePath . '/src/helpers.php';

loadEnv($basePath ?: dirname(__DIR__));

session_start();

if (!AdminAuth::check()) {
    header('Location: login.php');
    exit;
}

$pdo = databaseConnection();
$adminId = AdminAuth::id();
$admin = AdminRepository::findById($pdo, $adminId ?? 0);
$permissions = AdminRepository::permissions($pdo, $adminId ?? 0);

$recentOrders = $pdo->query(
    'SELECT o.order_reference, o.total, o.placed_at, c.first_name, c.last_name
     FROM orders o
     INNER JOIN customers c ON o.customer_id = c.id
     ORDER BY o.placed_at DESC LIMIT 5'
)->fetchAll() ?: [];

$topProducts = $pdo->query(
    'SELECT p.name, c.name AS category, SUM(oi.quantity) AS total_qty
     FROM order_items oi
     INNER JOIN products p ON oi.product_id = p.id
     INNER JOIN categories c ON p.category_id = c.id
     GROUP BY p.id, p.name, c.name
     ORDER BY total_qty DESC
     LIMIT 5'
)->fetchAll() ?: [];

renderAdmin('admin/dashboard', [
    'title' => 'Admin Dashboard',
    'admin' => $admin,
    'permissions' => $permissions,
    'recentOrders' => $recentOrders,
    'topProducts' => $topProducts,
]);
