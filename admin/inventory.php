<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;
use DragonStone\Repositories\AdminRepository;

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
$permissions = AdminRepository::permissions($pdo, $adminId ?? 0);

if (!in_array('catalog.manage', $permissions, true)) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to view inventory snapshots.',
        'permissions' => $permissions,
    ]);
    exit;
}

$snapshots = $pdo->query(
    'SELECT i.*, p.name, p.sku
     FROM inventory_snapshots i
     INNER JOIN products p ON i.product_id = p.id
     ORDER BY i.snapshot_date DESC, p.name'
)->fetchAll() ?: [];

renderAdmin('admin/inventory', [
    'title' => 'Inventory Snapshots',
    'snapshots' => $snapshots,
    'permissions' => $permissions,
]);
