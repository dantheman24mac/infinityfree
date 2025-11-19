<?php

declare(strict_types=1);

use DragonStone\Repositories\CategoryRepository;
use DragonStone\Repositories\ProductRepository;
use DragonStone\Services\CartService;
use DragonStone\Repositories\CustomerRepository;
use DragonStone\Repositories\OrderRepository;
use DragonStone\Repositories\SubscriptionRepository;
use DragonStone\Services\CurrencyService;
use DragonStone\Services\RewardService;
use DragonStone\Services\SubscriptionService;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false || !file_exists($basePath . '/vendor/autoload.php')) {
    $basePath = realpath(__DIR__);
}

require_once $basePath . '/vendor/autoload.php';
require_once $basePath . '/config/database.php';
require_once $basePath . '/src/helpers.php';

loadEnv($basePath ?: __DIR__);

session_start();

$pdo = databaseConnection();
$action = $_POST['action'] ?? null;
$redirectUrl = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? ($_ENV['APP_URL'] ?? '/'));
$redirectUrl = filter_var($redirectUrl, FILTER_SANITIZE_URL) ?: ($_ENV['APP_URL'] ?? '/');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== null) {
    switch ($action) {
        case 'cart_add':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($productId > 0) {
                CartService::addItem($productId, $quantity);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product added to cart.'];
            }
            header('Location: ' . $redirectUrl);
            exit;

        case 'cart_update':
            $items = $_POST['items'] ?? [];
            foreach ($items as $productId => $qty) {
                CartService::updateItem((int)$productId, (int)$qty);
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cart updated.'];
            header('Location: ' . $redirectUrl);
            exit;

        case 'cart_remove':
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            if ($productId > 0) {
                CartService::removeItem($productId);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Item removed from cart.'];
            }
            header('Location: ' . $redirectUrl);
            exit;

        case 'cart_clear':
            CartService::clear();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Cart cleared.'];
            header('Location: ' . $redirectUrl);
            exit;

        case 'set_currency':
            $requestedCurrency = $_POST['currency_code'] ?? CurrencyService::baseCurrency();
            CurrencyService::setActiveCurrency($requestedCurrency);
            $_SESSION['flash'] = ['type' => 'info', 'message' => 'Currency preference updated.'];
            header('Location: ' . $redirectUrl);
            exit;

        case 'subscription_create':
            $required = ['first_name', 'last_name', 'email', 'subscription_name'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please complete all required subscription fields.'];
                    header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=subscriptions');
                    exit;
                }
            }

            $customerPayload = [
                'first_name' => trim((string)$_POST['first_name']),
                'last_name' => trim((string)$_POST['last_name']),
                'email' => strtolower(trim((string)$_POST['email'])),
                'phone' => trim((string)($_POST['phone'] ?? '')),
                'city' => trim((string)($_POST['city'] ?? '')),
                'country' => trim((string)($_POST['country'] ?? '')),
            ];

            try {
                $subscriptionResult = SubscriptionService::create($pdo, [
                    'customer' => $customerPayload,
                    'name' => trim((string)$_POST['subscription_name']),
                    'product_id' => (int)($_POST['product_id'] ?? 0),
                    'quantity' => (int)($_POST['quantity'] ?? 1),
                    'interval_unit' => $_POST['interval_unit'] ?? 'monthly',
                    'currency_code' => CurrencyService::getActiveCurrency(),
                    'reward_points' => (int)($_POST['subscription_reward_points'] ?? 0),
                ]);
                $_SESSION['customer_id'] = (int)($subscriptionResult['customer']['id'] ?? 0);
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Subscription scheduled successfully.'];
            } catch (\Throwable $exception) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Unable to create subscription: ' . $exception->getMessage()];
            }
            header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=subscriptions');
            exit;

        case 'checkout_submit':
            $items = CartService::detailedItems($pdo);
            if (empty($items)) {
                $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Add items to your cart before checking out.'];
                header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=catalog');
                exit;
            }

            $requiredFields = ['first_name', 'last_name', 'email'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please complete all required fields before placing your order.'];
                    header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=checkout');
                    exit;
                }
            }

            $customerData = [
                'first_name' => trim((string)$_POST['first_name']),
                'last_name' => trim((string)$_POST['last_name']),
                'email' => strtolower(trim((string)$_POST['email'])),
                'phone' => trim((string)($_POST['phone'] ?? '')),
                'city' => trim((string)($_POST['city'] ?? '')),
                'country' => trim((string)($_POST['country'] ?? '')),
            ];

            $customer = CustomerRepository::findOrCreate($pdo, $customerData);
            $_SESSION['customer_id'] = (int)$customer['id'];

            $cartSubtotal = CartService::total($pdo);
            $currencyCode = CurrencyService::getActiveCurrency();
            $currencyRate = CurrencyService::rate($currencyCode);
            $requestedRedeemPoints = (int)($_POST['redeem_points'] ?? 0);
            $availablePoints = (int)($customer['eco_points'] ?? 0);
            $redeemPoints = RewardService::clampRedeemablePoints($requestedRedeemPoints, $availablePoints, $cartSubtotal);
            $discountAmount = RewardService::currencyValue($redeemPoints);
            $grandTotal = max($cartSubtotal - $discountAmount, 0);

            $checkoutData = [
                'payment_method' => $_POST['payment_method'] ?? 'card',
                'shipping_provider' => $_POST['shipping_provider'] ?? 'DragonStone Green Logistics',
                'currency_code' => $currencyCode,
                'currency_rate' => $currencyRate,
                'redeemed_points' => $redeemPoints,
                'totals' => [
                    'subtotal' => $cartSubtotal,
                    'discount' => $discountAmount,
                    'grand_total' => $grandTotal,
                    'display_total' => CurrencyService::convertFromBase($grandTotal, $currencyCode),
                ],
            ];

            try {
                $orderSummary = OrderRepository::create($pdo, (int)$customer['id'], $items, $checkoutData);
                CartService::clear();
                $_SESSION['order_confirmation'] = $orderSummary;
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Order placed successfully.'];
                header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=order-confirmation');
                exit;
            } catch (\Throwable $exception) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Something went wrong while placing your order. Please try again.'];
                header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=checkout');
                exit;
            }
            break;
        default:
            break;
    }
}

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'cart':
        $cartItems = CartService::detailedItems($pdo);
        $cartTotal = CartService::total($pdo);
        $cartCarbon = CartService::carbonTotal($pdo);

        render('cart', [
            'title' => 'Your Cart',
            'items' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCarbonTotal' => $cartCarbon,
        ]);
        break;

    case 'checkout':
        $cartItems = CartService::detailedItems($pdo);
        if (empty($cartItems)) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Add items to your cart before checking out.'];
            header('Location: ' . ($_ENV['APP_URL'] ?? '/') . '?page=catalog');
            exit;
        }

        $cartTotal = CartService::total($pdo);
        $totalPoints = CartService::pointsTotal($pdo);
        $cartCarbon = CartService::carbonTotal($pdo);

        render('checkout', [
            'title' => 'Checkout',
            'items' => $cartItems,
            'cartTotal' => $cartTotal,
            'totalPoints' => $totalPoints,
            'cartCarbonTotal' => $cartCarbon,
        ]);
        break;

    case 'order-confirmation':
        $orderSummary = $_SESSION['order_confirmation'] ?? [];
        unset($_SESSION['order_confirmation']);

        render('order-confirmation', [
            'title' => 'Order Confirmation',
            'order' => $orderSummary,
        ]);
        break;

    case 'catalog':
        $categories = CategoryRepository::all($pdo);
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $products = ProductRepository::browse($pdo, $categoryId);

        render('catalog', [
            'title' => 'Shop Sustainable Collections',
            'categories' => $categories,
            'products' => $products,
            'activeCategory' => $categoryId,
        ]);
        break;

    case 'product':
        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $productId > 0 ? ProductRepository::find($pdo, $productId) : null;

        if ($product === null) {
            http_response_code(404);
            render('errors/404', [
                'title' => 'Product not found',
                'message' => 'The product you are looking for could not be located.',
            ]);
            break;
        }

        $impactMetrics = ProductRepository::impactMetrics($pdo, $productId);
        $tags = ProductRepository::tags($pdo, $productId);
        $product['estimated_points'] = (int)$product['sustainability_score'] * 2;

        render('product-detail', [
            'title' => $product['name'] . ' – DragonStone',
            'product' => $product,
            'impactMetrics' => $impactMetrics,
            'tags' => $tags,
            'currentUrl' => $_SERVER['REQUEST_URI'] ?? '',
        ]);
        break;

    case 'subscriptions':
        $currentCustomerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
        $subscriptions = $currentCustomerId !== null ? SubscriptionRepository::allWithItems($pdo, $currentCustomerId) : [];
        $eligibleProducts = ProductRepository::subscriptionEligible($pdo);

        $selectedProduct = isset($_GET['product']) ? (int)$_GET['product'] : 0;

        render('subscriptions', [
            'title' => 'Subscription Hub',
            'subscriptions' => $subscriptions,
            'eligibleProducts' => $eligibleProducts,
            'selectedProductId' => $selectedProduct,
            'hasCustomerContext' => $currentCustomerId !== null,
        ]);
        break;

    case 'community':
    case 'impact':
        render('placeholders/coming-soon', [
            'title' => ucfirst($page) . ' – Coming Soon',
            'section' => ucfirst($page),
        ]);
        break;

    case 'home':
    default:
        $featuredCategories = CategoryRepository::featured($pdo, 6);
        $featuredProducts = ProductRepository::featured($pdo, 3);
        $impactHighlights = ProductRepository::impactHighlights($pdo, 4);

        render('home', [
            'title' => 'DragonStone – Sustainable living, curated for you.',
            'featuredCategories' => $featuredCategories,
            'featuredProducts' => $featuredProducts,
            'impactHighlights' => $impactHighlights,
        ]);
        break;
}
