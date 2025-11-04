<?php
/** @var string $appName */
/** @var array<int,string> $permissions */
/** @var string $adminName */
/** @var array<string,string>|null $flash */
$permissions = $permissions ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($appName) ?> – Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">DragonStone Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <?php if (in_array('catalog.manage', $permissions, true)): ?>
                    <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
                <?php endif; ?>
                <?php if (in_array('orders.manage', $permissions, true)): ?>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <?php endif; ?>
                <?php if (in_array('community.moderate', $permissions, true)): ?>
                    <li class="nav-item"><a class="nav-link" href="community.php">Community</a></li>
                <?php endif; ?>
                <?php if (in_array('ecopoints.adjust', $permissions, true)): ?>
                    <li class="nav-item"><a class="nav-link" href="ecopoints.php">EcoPoints</a></li>
                <?php endif; ?>
                <?php if (in_array('reports.view', $permissions, true)): ?>
                    <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($adminName) ?>
            </span>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Sign out</a>
        </div>
    </div>
</nav>

<main class="container-fluid py-4">
    <?php if (!empty($flash)): ?>
        <?php $flashType = $flash['type'] ?? 'success'; ?>
        <div class="alert alert-<?= htmlspecialchars($flashType) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="container">
