<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;
use DragonStone\Repositories\AdminRepository;
use DragonStone\Repositories\CategoryRepository;
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
$permissions = AdminRepository::permissions($pdo, $adminId ?? 0);

$canManageCatalog = in_array('catalog.manage', $permissions, true);
if (!$canManageCatalog) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to manage the catalog.',
        'permissions' => $permissions,
    ]);
    exit;
}

$categories = CategoryRepository::all($pdo);
$products = ProductRepository::browse($pdo);

renderAdmin('admin/catalog', [
    'title' => 'Catalog Management',
    'products' => $products,
    'categories' => $categories,
    'permissions' => $permissions,
]);
