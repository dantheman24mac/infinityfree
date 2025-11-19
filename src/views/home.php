<?php
/** @var array<int,array<string,mixed>> $featuredCategories */
/** @var array<int,array<string,mixed>> $featuredProducts */
/** @var array<int,array<string,mixed>> $impactHighlights */
?>
<section class="row align-items-center mb-5 hero">
    <div class="col-md-6">
        <h2 class="display-6 fw-semibold">Sustainable living, curated for you.</h2>
        <p class="lead text-muted">
            Discover low-impact essentials vetted by the DragonStone founders. Track your carbon savings
            and earn EcoPoints every time you shop or share with the community.
        </p>
        <a class="btn btn-success btn-lg me-2" href="?page=catalog">Start Shopping</a>
        <a class="btn btn-outline-success btn-lg" href="?page=community">Explore Eco Challenges</a>
    </div>
    <div class="col-md-6 text-center">
        <img src="/assignment-ecommerce/prototype/public/assets/img/hero-placeholder.svg" class="img-fluid"
             alt="DragonStone sustainable products preview">
    </div>
</section>

<section class="mb-5">
    <h3 class="h4 mb-3">Featured Categories</h3>
    <div class="row g-4">
        <?php foreach ($featuredCategories as $category): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title h5"><?= htmlspecialchars($category['name']) ?></h4>
                        <p class="card-text text-muted"><?= htmlspecialchars($category['description'] ?? 'Explore this collection.') ?></p>
                        <a href="?page=catalog&amp;category=<?= (int)$category['id'] ?>" class="stretched-link">Browse category</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-5">
    <h3 class="h4 mb-3">Featured Products</h3>
    <div class="row g-4">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <span class="badge bg-success bg-opacity-10 text-success mb-2">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </span>
                        <h4 class="card-title h5"><?= htmlspecialchars($product['name']) ?></h4>
                        <p class="card-text text-muted"><?= htmlspecialchars($product['summary']) ?></p>
                        <p class="fw-semibold mb-2">
                            <?= htmlspecialchars(format_price((float)$product['price'])) ?>
                            <small class="text-muted d-block">Base: <?= htmlspecialchars(format_currency((float)$product['price'])) ?></small>
                        </p>
                        <a class="btn btn-outline-success btn-sm" href="?page=product&amp;id=<?= (int)$product['id'] ?>">View details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-5">
    <h3 class="h4 mb-3">Latest Community Challenges</h3>
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-0 text-muted">Community challenge feed will display here once integrated.</p>
        </div>
    </div>
</section>

<section class="cta bg-light p-4 rounded-3">
    <h3 class="h4">Track your carbon footprint</h3>
    <p class="mb-3 text-muted">
        The DragonStone carbon calculator compares your purchases with conventional alternatives to quantify
        CO₂ savings. Prototype integration currently highlights example metrics from the product catalog.
    </p>
    <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#carbonModal">
        View Carbon Calculator Demo
    </button>
</section>

<div class="modal fade" id="carbonModal" tabindex="-1" aria-labelledby="carbonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carbonModalLabel">Carbon Savings Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Impact Metric</th>
                            <th>EcoPoints Reward (est.)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($impactHighlights as $highlight): ?>
                            <tr>
                                <td><?= htmlspecialchars($highlight['product']) ?></td>
                                <td><?= htmlspecialchars($highlight['category']) ?></td>
                                <td><?= htmlspecialchars($highlight['impact']) ?></td>
                                <td><?= (int)$highlight['points'] ?> pts</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="?page=impact" class="btn btn-success">View Impact Dashboard</a>
            </div>
        </div>
    </div>
</div>

