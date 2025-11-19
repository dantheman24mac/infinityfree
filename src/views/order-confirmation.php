<?php
/** @var array<string,mixed> $order */
?>
<section class="py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <?php if (empty($order)): ?>
                <div class="alert alert-warning">
                    We couldn't find your order confirmation. Please view your <a href="?page=catalog">catalog</a> and try again.
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h3 mb-3">Thank you for supporting sustainable living!</h2>
                        <p class="text-muted">
                            Your order <strong><?= htmlspecialchars($order['order_reference']) ?></strong> has been recorded.
                            A confirmation email has been sent to <?= htmlspecialchars($order['email']) ?>.
                        </p>
                        <div class="text-start mt-4">
                            <h3 class="h5">Order summary</h3>
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach ($order['items'] ?? [] as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>
                                            <?= htmlspecialchars($item['name']) ?>
                                            <small class="text-muted">&times; <?= (int)$item['quantity'] ?></small>
                                        </span>
                                        <span>
                                            <?= htmlspecialchars(format_currency((float)($item['unit_price_display'] ?? $item['unit_price']), $order['currency_code'] ?? active_currency())) ?>
                                            <small class="text-muted d-block">Base: <?= htmlspecialchars(format_currency((float)$item['unit_price'])) ?></small>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php
                            $carbon = \DragonStone\Services\CarbonCalculator::forItems($order['items'] ?? []);
                            $treeDays = \DragonStone\Services\CarbonCalculator::treeOffsetDays($carbon);
                            $commuteKm = \DragonStone\Services\CarbonCalculator::commuteKilometers($carbon);
                            ?>
                            <dl class="row">
                                <dt class="col-6">Subtotal (base)</dt>
                                <dd class="col-6 text-end"><?= htmlspecialchars(format_currency((float)($order['subtotal'] ?? $order['total']))) ?></dd>
                                <dt class="col-6">Discounts</dt>
                                <dd class="col-6 text-end text-success">−<?= htmlspecialchars(format_currency((float)($order['discount_total'] ?? 0.0))) ?></dd>
                                <dt class="col-6">Total (<?= htmlspecialchars($order['currency_code'] ?? active_currency()) ?>)</dt>
                                <dd class="col-6 text-end">
                                    <?= htmlspecialchars(format_currency((float)($order['total_converted'] ?? $order['total']), $order['currency_code'] ?? active_currency())) ?>
                                    <small class="text-muted d-block">Base: <?= htmlspecialchars(format_currency((float)$order['total'])) ?></small>
                                </dd>
                                <?php if (!empty($order['eco_points_redeemed'])): ?>
                                    <dt class="col-6">EcoPoints redeemed</dt>
                                    <dd class="col-6 text-end text-success">−<?= (int)$order['eco_points_redeemed'] ?> pts</dd>
                                <?php endif; ?>
                                <dt class="col-6">EcoPoints awarded</dt>
                                <dd class="col-6 text-end">
                                    <?= (int)$order['eco_points_awarded'] ?> pts
                                </dd>
                                <dt class="col-6">Order footprint</dt>
                                <dd class="col-6 text-end">
                                    <?= htmlspecialchars(format_carbon($carbon)) ?>
                                    <small class="text-muted d-block">≈ <?= number_format($treeDays, 1) ?> tree-days · <?= number_format($commuteKm, 1) ?> km</small>
                                </dd>
                            </dl>
                        </div>
                        <a class="btn btn-success mt-3" href="?page=catalog">Continue shopping</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
