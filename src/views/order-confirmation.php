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
                                        <span><?= htmlspecialchars(format_currency((float)$item['unit_price'])) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <dl class="row">
                                <dt class="col-6">Total paid</dt>
                                <dd class="col-6 text-end">
                                    <?= htmlspecialchars(format_currency((float)$order['total'])) ?>
                                </dd>
                                <dt class="col-6">EcoPoints awarded</dt>
                                <dd class="col-6 text-end">
                                    <?= (int)$order['eco_points_awarded'] ?> pts
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
