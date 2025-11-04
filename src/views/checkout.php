<?php
/** @var array<int,array<string,mixed>> $items */
/** @var float $cartTotal */
/** @var int $totalPoints */
?>
<section class="mb-5">
    <h2 class="h3 mb-3">Checkout</h2>
    <p class="text-muted">Complete your order and earn EcoPoints for every sustainable choice.</p>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Your cart is empty. <a href="?page=catalog">Return to the catalog</a> to add products.</div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 mb-3">Contact details</h3>
                        <form method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                            <input type="hidden" name="action" value="checkout_submit">
                            <input type="hidden" name="redirect" value="?page=order-confirmation">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="first_name">First name</label>
                                    <input class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="last_name">Last name</label>
                                    <input class="form-control" id="last_name" name="last_name" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input class="form-control" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="city">City</label>
                                    <input class="form-control" id="city" name="city">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="country">Country</label>
                                    <input class="form-control" id="country" name="country">
                                </div>
                            </div>
                            <hr class="my-4">
                            <h3 class="h5 mb-3">Payment & delivery</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="payment_method">Payment method</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="card" selected>Credit / Debit Card</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="shipping_provider">Shipping provider</label>
                                    <select class="form-select" id="shipping_provider" name="shipping_provider" required>
                                        <option value="DragonStone Green Logistics" selected>DragonStone Green Logistics</option>
                                        <option value="EcoShip">EcoShip</option>
                                        <option value="SolarExpress">SolarExpress</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <div class="text-muted small">By placing the order you acknowledge delivery within 3–5 business days.</div>
                                <button type="submit" class="btn btn-success">Place order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <strong>Order summary</strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($item['product']['name']) ?></div>
                                        <small class="text-muted">Qty <?= (int)$item['quantity'] ?> × <?= htmlspecialchars(format_currency((float)$item['unit_price'])) ?></small>
                                    </div>
                                    <span><?= htmlspecialchars(format_currency((float)$item['subtotal'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <dl class="row mb-0">
                            <dt class="col-6">Total</dt>
                            <dd class="col-6 text-end fw-semibold"><?= htmlspecialchars(format_currency($cartTotal)) ?></dd>
                            <dt class="col-6">EcoPoints to earn</dt>
                            <dd class="col-6 text-end fw-semibold"><?= (int)$totalPoints ?> pts</dd>
                        </dl>
                    </div>
                    <div class="card-footer text-muted small">
                        Carbon savings data will appear in the Impact Dashboard after launch.
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
