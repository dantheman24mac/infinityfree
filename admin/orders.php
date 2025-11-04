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

if (!in_array('orders.manage', $permissions, true)) {
    http_response_code(403);
    renderAdmin('admin/error', [
        'title' => 'Access denied',
        'message' => 'You do not have permission to manage orders.',
        'permissions' => $permissions,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $status = $_POST['status'] ?? '';
    $allowedStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];

    if ($orderId > 0 && in_array($status, $allowedStatuses, true)) {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Order status updated.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid status change request.'];
    }

    header('Location: /assignment-ecommerce/prototype/public/admin/orders.php');
    exit;
}

$orders = $pdo->query(
    'SELECT o.*, c.first_name, c.last_name, c.email
     FROM orders o
     INNER JOIN customers c ON o.customer_id = c.id
     ORDER BY o.placed_at DESC'
)->fetchAll() ?: [];

$shipmentsStmt = $pdo->query('SELECT order_id, provider, tracking_number, shipped_at, delivered_at FROM shipments');
$shipments = [];
foreach ($shipmentsStmt->fetchAll() ?: [] as $shipment) {
    $shipments[(int)$shipment['order_id']] = $shipment;
}

$paymentsStmt = $pdo->query('SELECT order_id, method, status, processed_at FROM payments');
$payments = [];
foreach ($paymentsStmt->fetchAll() ?: [] as $payment) {
    $payments[(int)$payment['order_id']] = $payment;
}

renderAdmin('admin/orders', [
    'title' => 'Order Management',
    'orders' => $orders,
    'shipments' => $shipments,
    'payments' => $payments,
    'permissions' => $permissions,
]);
