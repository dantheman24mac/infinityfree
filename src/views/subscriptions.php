<?php
/** @var array<int,array<string,mixed>> $subscriptions */
/** @var array<int,array<string,mixed>> $eligibleProducts */
/** @var int $selectedProductId */
/** @var bool $hasCustomerContext */
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
            <?php if (!$hasCustomerContext): ?>
                <div class="alert alert-info">Identify yourself by placing an order or creating a subscription to view your renewal history.</div>
            <?php elseif (empty($subscriptions)): ?>
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
                                <div class="d-flex flex-column text-lg-end mt-3 mt-lg-0">
                                    <span class="badge bg-success bg-opacity-10 text-success text-capitalize">
                                        <?= htmlspecialchars($subscription['status']) ?> – <?= htmlspecialchars($subscription['interval_unit']) ?>
                                    </span>
                                    <small class="text-muted">Currency: <?= htmlspecialchars($subscription['currency_code'] ?? active_currency()) ?></small>
                                </div>
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
                                            <td class="text-end">
                                                <?= htmlspecialchars(format_price((float)$item['price'])) ?>
                                                <small class="text-muted d-block">Base: <?= htmlspecialchars(format_currency((float)$item['price'])) ?></small>
                                            </td>
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
                    <p class="text-muted small">Choose a replenishment plan, tell us who to deliver to, and we will auto-renew every cycle.</p>
                    <form method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                        <input type="hidden" name="action" value="subscription_create">
                        <input type="hidden" name="redirect" value="?page=subscriptions">
                        <div class="mb-3">
                            <label class="form-label" for="subscription_name">Plan name</label>
                            <input type="text" class="form-control" id="subscription_name" name="subscription_name" placeholder="Monthly essentials" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="subscription_product">Choose product</label>
                            <select class="form-select" id="subscription_product" name="product_id" required>
                                <?php foreach ($eligibleProducts as $product): ?>
                                    <option value="<?= (int)$product['id'] ?>" <?= $selectedProductId === (int)$product['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars(format_price((float)$product['price'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="subscription_quantity">Quantity</label>
                                <input type="number" min="1" value="1" class="form-control" id="subscription_quantity" name="quantity">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="subscription_interval">Interval</label>
                                <select class="form-select" id="subscription_interval" name="interval_unit">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="sub_first_name">First name</label>
                                <input type="text" class="form-control" id="sub_first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sub_last_name">Last name</label>
                                <input type="text" class="form-control" id="sub_last_name" name="last_name" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="sub_email">Email</label>
                                <input type="email" class="form-control" id="sub_email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sub_phone">Phone</label>
                                <input type="text" class="form-control" id="sub_phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="sub_city">City</label>
                                <input type="text" class="form-control" id="sub_city" name="city">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="sub_country">Country</label>
                                <input type="text" class="form-control" id="sub_country" name="country">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label" for="reward_points">EcoPoints to redeem per cycle (optional)</label>
                            <input type="number" min="0" class="form-control" id="reward_points" name="subscription_reward_points" value="0">
                            <small class="text-muted">Points convert using your active currency (<?= htmlspecialchars(active_currency()) ?>).</small>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-4">Schedule subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
