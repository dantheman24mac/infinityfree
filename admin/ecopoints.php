<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;
use DragonStone\Repositories\AdminRepository;
use DragonStone\Repositories\CustomerRepository;
use DragonStone\Repositories\EcoPointRepository;

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

if (!in_array('ecopoints.adjust', $permissions, true)) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to adjust EcoPoints.',
        'permissions' => $permissions,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $points = (int)($_POST['points'] ?? 0);
    $note = trim((string)($_POST['note'] ?? 'Manual adjustment'));

    if ($customerId > 0 && $points !== 0) {
        EcoPointRepository::recordManualAdjustment($pdo, $customerId, $points, $note, $adminId);
        CustomerRepository::addEcoPoints($pdo, $customerId, $points);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'EcoPoints updated successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Select a customer and non-zero point value.'];
    }

    header('Location: /assignment-ecommerce/prototype/public/admin/ecopoints.php');
    exit;
}

$customers = $pdo->query('SELECT id, first_name, last_name, email, eco_points FROM customers ORDER BY first_name')->fetchAll() ?: [];
$transactions = $pdo->query(
    'SELECT e.*, c.first_name, c.last_name
     FROM ecopoint_transactions e
     INNER JOIN customers c ON e.customer_id = c.id
     ORDER BY e.created_at DESC
     LIMIT 30'
)->fetchAll() ?: [];

renderAdmin('admin/ecopoints', [
    'title' => 'EcoPoints Ledger',
    'customers' => $customers,
    'transactions' => $transactions,
    'permissions' => $permissions,
]);
