<?php
/** @var string $appName */
/** @var string $title */
/** @var string $baseUrl */
$rootUrl = rtrim($baseUrl, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DragonStone – Sustainable living made accessible.">
    <title><?= htmlspecialchars($title ?? $appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($rootUrl) ?>/assets/css/main.css">
</head>
<body>
<header class="bg-dark text-white py-4">
    <div class="container d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <a class="text-white text-decoration-none" href="<?= htmlspecialchars($rootUrl) ?>/">
            <h1 class="h3 m-0"><?= htmlspecialchars($appName) ?></h1>
        </a>
        <nav class="mt-3 mt-md-0">
            <a class="nav-link d-inline-block text-white-50" href="?page=catalog">Shop</a>
            <a class="nav-link d-inline-block text-white-50" href="?page=subscriptions">Subscriptions</a>
            <a class="nav-link d-inline-block text-white-50" href="?page=community">Community</a>
            <a class="nav-link d-inline-block text-white-50" href="?page=impact">Impact</a>
            <a class="nav-link d-inline-block text-white-50 position-relative" href="?page=cart">
                Cart
                <?php if (!empty($cartCount)): ?>
                    <span class="badge bg-success position-absolute top-0 start-100 translate-middle rounded-pill">
                        <?= (int)$cartCount ?>
                    </span>
                <?php endif; ?>
            </a>
            <a class="btn btn-outline-light btn-sm ms-md-3" href="<?= htmlspecialchars($rootUrl) ?>/admin/login.php">Admin Portal</a>
        </nav>
    </div>
</header>

<main class="container mt-5 pb-5">
    <?php if (!empty($flash)): ?>
        <?php $flashType = $flash['type'] ?? 'success'; ?>
        <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
