<?php
/** @var array<int,array<string,mixed>> $products */
/** @var array<int,array<string,mixed>> $categories */
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="h3 mb-1">Catalog Management</h2>
        <p class="text-muted mb-0">Review and update DragonStone products, categories, and sustainability metrics.</p>
    </div>
    <button class="btn btn-success mt-3 mt-md-0" type="button" data-bs-toggle="modal" data-bs-target="#createProductModal">
        Add new product
    </button>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h3 class="h6 mb-0">Product catalog</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Subscription</th>
                    <th class="text-center">Sustainability score</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                            <small class="text-muted">SKU: <?= htmlspecialchars($product['sku']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td class="text-end"><?= htmlspecialchars(format_currency((float)$product['price'])) ?></td>
                        <td class="text-center">
                            <?= (int)$product['subscription_eligible'] === 1 ? '<span class="badge bg-success bg-opacity-10 text-success">Yes</span>' : '<span class="badge bg-secondary bg-opacity-10 text-muted">No</span>' ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= (int)$product['sustainability_score'] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal"
                                    data-product='<?= json_encode($product, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'>
                                Edit
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductLabel">Add new product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Full create/update workflow will connect to REST endpoints in production. For the prototype, capture Form submission for documentation.</p>
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product name</label>
                            <input class="form-control" placeholder="Eco Bamboo Cutlery Set">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input class="form-control" placeholder="DS-KIT...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" placeholder="39.99">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Summary</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sustainability score</label>
                            <input type="number" class="form-control" value="85">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="newSubscriptionEligible">
                                <label class="form-check-label" for="newSubscriptionEligible">Subscription eligible</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Save prototype entry</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductLabel">Edit product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm">
                    <input type="hidden" id="editProductId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product name</label>
                            <input class="form-control" id="editProductName">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input class="form-control" id="editProductSku">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="editProductCategory">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="editProductPrice">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Summary</label>
                            <textarea class="form-control" rows="2" id="editProductSummary"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" id="editProductDescription"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sustainability score</label>
                            <input type="number" class="form-control" id="editProductScore">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="editProductSubscription">
                                <label class="form-check-label" for="editProductSubscription">Subscription eligible</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('editProductModal')?.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const product = button?.dataset.product ? JSON.parse(button.dataset.product) : null;
        if (!product) {
            return;
        }

        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editProductSku').value = product.sku;
        document.getElementById('editProductCategory').value = product.category_id;
        document.getElementById('editProductPrice').value = product.price;
        document.getElementById('editProductSummary').value = product.summary;
        document.getElementById('editProductDescription').value = product.description || '';
        document.getElementById('editProductScore').value = product.sustainability_score;
        document.getElementById('editProductSubscription').checked = parseInt(product.subscription_eligible, 10) === 1;
    });
</script>
