<?php
/** @var array<string,mixed> $product */
/** @var array<int,array<string,string>> $impactMetrics */
/** @var array<int,string> $tags */
?>
<section class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=catalog">Catalog</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <span class="badge bg-success bg-opacity-10 text-success mb-3">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    <h2 class="h3"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="lead text-muted"><?= htmlspecialchars($product['summary']) ?></p>
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <span class="display-6 fw-semibold"><?= htmlspecialchars(format_currency((float)$product['price'], 'USD')) ?></span>
                        <?php if ((int)$product['subscription_eligible'] === 1): ?>
                            <span class="badge bg-success text-white">Subscription eligible</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($tags)): ?>
                        <div class="mt-4">
                            <?php foreach ($tags as $tag): ?>
                                <span class="badge bg-secondary bg-opacity-10 text-success me-1"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4">
                        <form class="d-flex flex-column flex-md-row gap-2 align-items-md-center" method="post"
                              action="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/">
                            <input type="hidden" name="action" value="cart_add">
                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentUrl ?? '?page=product&id=' . (int)$product['id']) ?>">
                            <div class="input-group" style="max-width: 200px;">
                                <span class="input-group-text">Qty</span>
                                <input type="number" name="quantity" min="1" value="1" class="form-control">
                            </div>
                            <button class="btn btn-success flex-grow-1 flex-md-grow-0" type="submit">Add to cart</button>
                            <?php if ((int)$product['subscription_eligible'] === 1): ?>
                                <a class="btn btn-outline-success" href="<?= htmlspecialchars(rtrim($baseUrl, '/')) ?>/?page=subscriptions&amp;product=<?= (int)$product['id'] ?>">
                                    Start subscription
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5">Sustainability metrics</h3>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($impactMetrics as $metric): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($metric['metric_label']) ?>:</strong>
                                <?= htmlspecialchars($metric['metric_value']) ?>
                                <?php if (!empty($metric['baseline_comparison'])): ?>
                                    <span class="text-muted d-block small">
                                        Baseline: <?= htmlspecialchars($metric['baseline_comparison']) ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5">EcoPoints insight</h3>
                    <p class="text-muted">
                        Estimated EcoPoints reward: <strong><?= (int)$product['estimated_points'] ?> points</strong>.
                        Earn extra points by sharing this product in the community hub.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
