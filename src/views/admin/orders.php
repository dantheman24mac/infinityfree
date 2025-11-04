<?php
/** @var array<int,array<string,mixed>> $orders */
/** @var array<int,array<string,mixed>> $shipments */
/** @var array<int,array<string,mixed>> $payments */
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="h3 mb-1">Orders</h2>
        <p class="text-muted mb-0">Track recent purchases, update statuses, and monitor fulfillment.</p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th>Email</th>
                <th class="text-end">Total</th>
                <th>Status</th>
                <th>Shipment</th>
                <th>Payment</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No orders yet.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <?php
                $orderId = (int)$order['id'];
                $shipment = $shipments[$orderId] ?? null;
                $payment = $payments[$orderId] ?? null;
                ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($order['order_reference']) ?></div>
                        <small class="text-muted">Placed <?= htmlspecialchars($order['placed_at']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                    <td><small><?= htmlspecialchars($order['email']) ?></small></td>
                    <td class="text-end"><?= htmlspecialchars(format_currency((float)$order['total'])) ?></td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary text-capitalize"><?= htmlspecialchars($order['status']) ?></span>
                    </td>
                    <td>
                        <?php if ($shipment): ?>
                            <small class="text-muted d-block">Provider: <?= htmlspecialchars($shipment['provider']) ?></small>
                            <small class="text-muted d-block">Tracking: <?= htmlspecialchars($shipment['tracking_number']) ?></small>
                        <?php else: ?>
                            <small class="text-muted">No shipment</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($payment): ?>
                            <small class="text-muted d-block">Method: <?= htmlspecialchars($payment['method']) ?></small>
                            <small class="text-muted d-block text-capitalize">Status: <?= htmlspecialchars($payment['status']) ?></small>
                        <?php else: ?>
                            <small class="text-muted">No payment</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <form method="post" action="orders.php" class="d-inline-flex align-items-center gap-2">
                            <input type="hidden" name="order_id" value="<?= $orderId ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (['pending','paid','shipped','completed','cancelled'] as $status): ?>
                                    <option value="<?= $status ?>" <?= $status === $order['status'] ? 'selected' : '' ?>>
                                        <?= ucfirst($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
