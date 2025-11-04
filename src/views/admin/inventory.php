<?php
/** @var array<int,array<string,mixed>> $snapshots */
?>
<div class="mb-4">
    <h2 class="h3 mb-1">Inventory Snapshots</h2>
    <p class="text-muted">Monitor stock levels, restock ETAs, and identify products requiring attention.</p>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Product</th>
                <th class="text-end">Quantity</th>
                <th class="text-end">Snapshot date</th>
                <th class="text-end">Restock ETA</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($snapshots)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No inventory records available.</td></tr>
            <?php else: ?>
                <?php foreach ($snapshots as $snapshot): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($snapshot['name']) ?></div>
                            <small class="text-muted">SKU: <?= htmlspecialchars($snapshot['sku']) ?></small>
                        </td>
                        <td class="text-end">
                            <span class="badge bg-<?= (int)$snapshot['quantity'] < 50 ? 'warning' : 'success' ?> bg-opacity-10 text-<?= (int)$snapshot['quantity'] < 50 ? 'warning' : 'success' ?>">
                                <?= (int)$snapshot['quantity'] ?> units
                            </span>
                        </td>
                        <td class="text-end"><small><?= htmlspecialchars($snapshot['snapshot_date']) ?></small></td>
                        <td class="text-end"><small><?= $snapshot['restock_eta'] ? htmlspecialchars($snapshot['restock_eta']) : '—' ?></small></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
