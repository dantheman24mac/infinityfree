<?php

declare(strict_types=1);

use DragonStone\Auth\AdminAuth;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false || !file_exists($basePath . '/vendor/autoload.php')) {
    $basePath = realpath(dirname(__DIR__));
}

require_once $basePath . '/vendor/autoload.php';
require_once $basePath . '/config/database.php';

loadEnv($basePath ?: dirname(__DIR__));

session_start();
AdminAuth::logout();

header('Location: login.php');
exit;
