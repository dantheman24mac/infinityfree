<?php
/** @var array<int,array<string,mixed>> $categories */
/** @var array<int,array<string,mixed>> $products */
/** @var int|null $activeCategory */
?>
<section class="mb-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h2 class="h3 mb-2">Shop Sustainable Collections</h2>
            <p class="text-muted mb-0">Browse curated essentials across DragonStone’s eco-conscious categories.</p>
        </div>
        <form class="catalog-filter mt-3 mt-md-0" method="get">
            <input type="hidden" name="page" value="catalog">
            <select class="form-select" name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= $activeCategory === (int)$category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">No products found for the selected filter.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-success bg-opacity-10 text-success mb-2">
                                <?= htmlspecialchars($product['category_name']) ?>
                            </span>
                            <h4 class="card-title h5"><?= htmlspecialchars($product['name']) ?></h4>
                            <p class="text-muted flex-grow-1"><?= htmlspecialchars($product['summary']) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-end">
                                    <span class="fw-semibold d-block"><?= htmlspecialchars(format_price((float)$product['price'])) ?></span>
                                    <small class="text-muted">Base: <?= htmlspecialchars(format_currency((float)$product['price'])) ?></small>
                                </div>
                                <a class="btn btn-outline-success btn-sm" href="?page=product&amp;id=<?= (int)$product['id'] ?>">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

