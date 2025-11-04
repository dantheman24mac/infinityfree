<?php
/** @var array<int,array<string,mixed>> $items */
/** @var float $cartTotal */
?>
<section class="mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h2 class="h3 mb-2">Your Cart</h2>
            <p class="text-muted mb-0">Review products before completing your sustainable purchase.</p>
        </div>
        <?php if (!empty($items)): ?>
            <form method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                <input type="hidden" name="action" value="cart_clear">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?page=cart') ?>">
                <button class="btn btn-outline-secondary btn-sm" type="submit">Clear cart</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="alert alert-info mt-4">
            Your cart is currently empty. Explore the <a href="?page=catalog">catalog</a> to add products.
        </div>
    <?php else: ?>
        <div class="card shadow-sm mt-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col" class="text-center">Quantity</th>
                        <th scope="col" class="text-end">Unit Price</th>
                        <th scope="col" class="text-end">Subtotal</th>
                        <th scope="col" class="text-end">EcoPoints</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($item['product']['name']) ?></div>
                                <div class="text-muted small">SKU: <?= htmlspecialchars($item['product']['sku']) ?></div>
                            </td>
                            <td class="text-center" style="max-width: 140px;">
                                <input type="number" class="form-control" min="1" name="items[<?= (int)$item['product_id'] ?>]"
                                       value="<?= (int)$item['quantity'] ?>" form="cartUpdateForm">
                            </td>
                            <td class="text-end"><?= htmlspecialchars(format_currency((float)$item['unit_price'])) ?></td>
                            <td class="text-end"><?= htmlspecialchars(format_currency((float)$item['subtotal'])) ?></td>
                            <td class="text-end"><?= (int)$item['estimated_points'] ?></td>
                            <td class="text-end">
                                <form method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                                    <input type="hidden" name="action" value="cart_remove">
                                    <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?page=cart') ?>">
                                    <button class="btn btn-link text-danger p-0" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="text-muted">Estimated EcoPoints: <strong><?= array_sum(array_column($items, 'estimated_points')) ?></strong></div>
                <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
                    <div class="fs-5 fw-semibold">Total: <?= htmlspecialchars(format_currency($cartTotal)) ?></div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" type="submit" form="cartUpdateForm" name="action" value="cart_update">
                            Update quantities
                        </button>
                        <a class="btn btn-success" href="?page=checkout">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
        <form id="cartUpdateForm" method="post" action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/" class="d-none">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '?page=cart') ?>">
        </form>
    <?php endif; ?>
</section>
