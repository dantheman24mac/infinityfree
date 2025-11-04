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

if (!in_array('reports.view', $permissions, true)) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to view reports.',
        'permissions' => $permissions,
    ]);
    exit;
}

$salesByCategory = $pdo->query('SELECT * FROM vw_sales_by_category ORDER BY revenue DESC')->fetchAll() ?: [];
$communityEngagement = $pdo->query('SELECT * FROM vw_community_engagement ORDER BY created_at DESC LIMIT 20')->fetchAll() ?: [];

renderAdmin('admin/reports', [
    'title' => 'Reporting Dashboard',
    'salesByCategory' => $salesByCategory,
    'communityEngagement' => $communityEngagement,
    'permissions' => $permissions,
]);
