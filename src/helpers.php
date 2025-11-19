<?php

declare(strict_types=1);

use DragonStone\Services\CartService;
use DragonStone\Services\CurrencyService;

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
        'activeCurrency' => CurrencyService::getActiveCurrency(),
        'currencyOptions' => CurrencyService::available(),
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
        'activeCurrency' => CurrencyService::getActiveCurrency(),
    ];

    extract(array_merge($defaultData, $data));

    require $baseViewPath . 'layout/admin-header.php';
    require $viewPath;
    require $baseViewPath . 'layout/admin-footer.php';
}

/**
 * Generate a formatted currency string without conversion.
 */
function format_currency(float $amount, string $currency = 'USD'): string
{
    return CurrencyService::format($amount, $currency);
}

/**
 * Format an amount using the visitor's active currency.
 */
function format_price(float $amount, ?string $currency = null): string
{
    $currency = $currency ?? CurrencyService::getActiveCurrency();
    $converted = CurrencyService::convertFromBase($amount, $currency);

    return CurrencyService::format($converted, $currency);
}

function format_carbon(float $kilograms): string
{
    return number_format(max($kilograms, 0), 2) . ' kg CO₂e';
}

function active_currency(): string
{
    return CurrencyService::getActiveCurrency();
}

function currency_options(): array
{
    return CurrencyService::available();
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
