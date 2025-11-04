<?php

declare(strict_types=1);

use DragonStone\Services\CartService;

/**
 * Render a PHP view with shared layout.
 *
 * @param string $view Relative path inside src/views without extension.
 * @param array<string,mixed> $data Variables available to the view.
 */
function render(string $view, array $data = []): void
{
    $baseViewPath = __DIR__ . '/views/';
    $viewPath = $baseViewPath . $view . '.php';

    if (!file_exists($viewPath)) {
        http_response_code(404);
        echo 'View not found';
        return;
    }

    $defaultData = [
        'appName' => $_ENV['APP_NAME'] ?? 'DragonStone',
        'baseUrl' => $_ENV['APP_URL'] ?? '/',
        'cartCount' => session_status() === PHP_SESSION_ACTIVE ? CartService::itemCount() : 0,
        'flash' => pull_flash(),
    ];

    extract(array_merge($defaultData, $data));

    require $baseViewPath . 'layout/header.php';
    require $viewPath;
    require $baseViewPath . 'layout/footer.php';
}

/**
 * Render admin views with dedicated layout.
 */
function renderAdmin(string $view, array $data = []): void
{
    $baseViewPath = __DIR__ . '/views/';
    $viewPath = $baseViewPath . $view . '.php';

    if (!file_exists($viewPath)) {
        http_response_code(404);
        echo 'Admin view not found';
        return;
    }

    $defaultData = [
        'appName' => $_ENV['APP_NAME'] ?? 'DragonStone Admin',
        'baseUrl' => $_ENV['APP_URL'] ?? '/',
        'flash' => pull_flash(),
        'adminName' => $_SESSION['admin_name'] ?? 'Team Member',
        'permissions' => $data['permissions'] ?? [],
    ];

    extract(array_merge($defaultData, $data));

    require $baseViewPath . 'layout/admin-header.php';
    require $viewPath;
    require $baseViewPath . 'layout/admin-footer.php';
}

/**
 * Generate a formatted currency string.
 */
function format_currency(float $amount, string $currency = 'USD'): string
{
    if (!function_exists('numfmt_create')) {
        return sprintf('%s %.2f', $currency, $amount);
    }

    $fmt = numfmt_create('en_US', \NumberFormatter::CURRENCY);
    if ($fmt === false) {
        return sprintf('%s %.2f', $currency, $amount);
    }

    return numfmt_format_currency($fmt, $amount, $currency);
}

/**
 * Retrieve one-time flash message from the session.
 */
function pull_flash(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return null;
    }

    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    if (isset($_SESSION['flash_message'])) {
        $flash = ['type' => 'success', 'message' => $_SESSION['flash_message']];
        unset($_SESSION['flash_message']);
        return $flash;
    }

    return null;
}
