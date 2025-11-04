<?php
/** @var array<int,array<string,mixed>> $salesByCategory */
/** @var array<int,array<string,mixed>> $communityEngagement */
?>
<div class="mb-4">
    <h2 class="h3 mb-1">Reporting Dashboard</h2>
    <p class="text-muted">High-level metrics from custom SQL views ready for export or presentation.</p>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="h6 mb-0">Sales by category</h3>
                <button class="btn btn-sm btn-outline-success" onclick="window.print()">Print</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">EcoPoints</th>
                            <th class="text-end">Orders</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($salesByCategory)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No sales data yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($salesByCategory as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td class="text-end"><?= htmlspecialchars(format_currency((float)$row['revenue'])) ?></td>
                                    <td class="text-end"><?= (int)$row['eco_points_awarded'] ?></td>
                                    <td class="text-end"><?= (int)$row['order_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h3 class="h6 mb-0">Community engagement</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Post ID</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th class="text-end">Comments</th>
                            <th class="text-end">Created</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($communityEngagement)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No community posts yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($communityEngagement as $row): ?>
                                <tr>
                                    <td>#<?= (int)$row['post_id'] ?></td>
                                    <td><?= htmlspecialchars($row['author'] ?? 'Unknown') ?></td>
                                    <td class="text-capitalize">
                                        <span class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['status']) ?></span>
                                    </td>
                                    <td class="text-end"><?= (int)$row['comment_count'] ?></td>
                                    <td class="text-end"><small><?= htmlspecialchars($row['created_at']) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h3 class="h6">Export guidance</h3>
        <p class="text-muted mb-0">Use phpMyAdmin or CLI (`mysqldump`) to export the reported views for inclusion in Deliverable documentation. The tables above map directly to the `vw_sales_by_category` and `vw_community_engagement` SQL views.</p>
    </div>
</div>
