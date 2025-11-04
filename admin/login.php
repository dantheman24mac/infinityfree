<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false || !file_exists($basePath . '/vendor/autoload.php')) {
    $basePath = realpath(dirname(__DIR__));
}

require_once $basePath . '/vendor/autoload.php';
require_once $basePath . '/config/database.php';
require_once $basePath . '/src/helpers.php';

loadEnv($basePath ?: dirname(__DIR__));

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        try {
            $pdo = databaseConnection();
            if (AdminAuth::attempt($pdo, $email, $password)) {
                header('Location: dashboard.php');
                exit;
            }
            $error = 'Invalid credentials or inactive account.';
        } catch (\Throwable $exception) {
            $error = 'Unable to authenticate at this time.';
        }
    }
}

render('admin/login', [
    'title' => 'Admin Portal Sign-in',
    'error' => $error,
]);
