<?php
/** @var array<int,array<string,mixed>> $subscriptions */
/** @var array<int,array<string,mixed>> $eligibleProducts */
?>
<section class="mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h2 class="h3 mb-2">Subscription Hub</h2>
            <p class="text-muted mb-0">Manage recurring deliveries for your essentials and monitor upcoming renewals.</p>
        </div>
        <a class="btn btn-success mt-3 mt-md-0" href="?page=catalog&filter=subscription">Browse subscription-ready products</a>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <?php if (empty($subscriptions)): ?>
                <div class="alert alert-info">No active subscriptions yet. Start with our curated bundles or build your own from the catalog.</div>
            <?php else: ?>
                <?php foreach ($subscriptions as $subscription): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-3">
                                <div>
                                    <h3 class="h5 mb-1"><?= htmlspecialchars($subscription['name']) ?></h3>
                                    <p class="text-muted mb-0">
                                        Customer: <?= htmlspecialchars($subscription['first_name'] . ' ' . $subscription['last_name']) ?> ·
                                        <?= htmlspecialchars($subscription['email']) ?>
                                    </p>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success mt-3 mt-lg-0 text-capitalize">
                                    <?= htmlspecialchars($subscription['status']) ?> – <?= htmlspecialchars($subscription['interval_unit']) ?>
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Estimated Points</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($subscription['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                            <td class="text-end"><?= htmlspecialchars(format_currency((float)$item['price'])) ?></td>
                                            <td class="text-end"><?= (int)$item['sustainability_score'] * (int)$item['quantity'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-muted small">Next renewal: <?= htmlspecialchars($subscription['next_renewal']) ?></span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">EcoPoints per cycle: <?= (int)$subscription['estimated_points'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="h6 mb-0">Build your own subscription</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Prototype preview: select a product below to start a recurring delivery plan. Full workflow will connect to subscription APIs in production.</p>
                    <form method="get" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                        <input type="hidden" name="page" value="product">
                        <div class="mb-3">
                            <label class="form-label" for="subscription_product">Choose product</label>
                            <select class="form-select" id="subscription_product" name="id">
                                <?php foreach ($eligibleProducts as $product): ?>
                                    <option value="<?= (int)$product['id'] ?>">
                                        <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars(format_currency((float)$product['price'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-success w-100">Configure subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
