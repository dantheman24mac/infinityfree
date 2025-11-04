<?php
/** @var array<string,mixed>|null $admin */
/** @var array<int,string> $permissions */
/** @var array<int,array<string,mixed>> $recentOrders */
/** @var array<int,array<string,mixed>> $topProducts */
?>
<div class="row mb-4">
    <div class="col">
        <h2 class="h3 mb-1">Welcome back<?= $admin ? ', ' . htmlspecialchars($admin['first_name']) : '' ?></h2>
        <p class="text-muted mb-0">Manage DragonStone’s catalog, orders, EcoPoints, and community from one hub.</p>
    </div>
    <div class="col-auto">
        <a class="btn btn-outline-secondary" href="/assignment-ecommerce/prototype/public/admin/logout.php">Sign out</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Recent orders</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted">No orders have been placed yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Placed</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_reference']) ?></td>
                                    <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                    <td class="text-end"><?= htmlspecialchars(format_currency((float)$order['total'])) ?></td>
                                    <td class="text-end"><small><?= htmlspecialchars($order['placed_at']) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a class="btn btn-sm btn-success" href="orders.php">Manage orders</a>
                <a class="btn btn-sm btn-outline-success" href="reports.php">View reports</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Top products</h3>
            </div>
            <div class="card-body">
                <?php if (empty($topProducts)): ?>
                    <p class="text-muted">Sales data will populate here as orders come in.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($topProducts as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                                    <small class="text-muted">Category: <?= htmlspecialchars($product['category']) ?></small>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success">Qty <?= (int)$product['total_qty'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a class="btn btn-sm btn-success" href="catalog.php">Manage catalog</a>
                <a class="btn btn-sm btn-outline-success" href="inventory.php">Inventory snapshots</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h3 class="h6 mb-0">Quick actions</h3>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <a class="btn btn-outline-success w-100" href="catalog.php">Catalog</a>
            </div>
            <div class="col-md-3">
                <a class="btn btn-outline-success w-100" href="orders.php">Orders</a>
            </div>
            <div class="col-md-3">
                <a class="btn btn-outline-success w-100" href="community.php">Community</a>
            </div>
            <div class="col-md-3">
                <a class="btn btn-outline-success w-100" href="ecopoints.php">EcoPoints</a>
            </div>
        </div>
    </div>
</div>
