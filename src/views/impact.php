<?php
/** @var array<int,array<string,mixed>> $impactHighlights */
/** @var int $activeChallenges */
/** @var int $approvedStories */
/** @var int $ecoPointTotal */
/** @var float $avgProductFootprint */
?>
<section class="mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h2 class="display-6 fw-semibold">Impact Dashboard</h2>
            <p class="lead text-muted mb-0">
                Track how DragonStone products reduce waste, inspire community action, and generate EcoPoints rewards for planet-first lifestyles.
            </p>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-success border-opacity-25">
                <div class="card-body">
                    <h3 class="h6 text-uppercase text-muted">Average product footprint</h3>
                    <p class="display-6 fw-semibold mb-0"><?= number_format($avgProductFootprint, 2) ?> kg CO₂e</p>
                    <small class="text-muted">Per unit across products with verified lifecycle data.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted fs-6 mb-1">EcoPoints awarded</p>
                    <p class="display-6 fw-semibold mb-0"><?= number_format($ecoPointTotal) ?></p>
                    <small class="text-muted">Points earned for sustainable actions in the storefront and community.</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted fs-6 mb-1">Active challenges</p>
                    <p class="display-6 fw-semibold mb-0"><?= $activeChallenges ?></p>
                    <small class="text-muted">Community events encouraging plastic-free swaps and mindful consumption.</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted fs-6 mb-1">Approved stories</p>
                    <p class="display-6 fw-semibold mb-0"><?= $approvedStories ?></p>
                    <small class="text-muted">Member submissions sharing tips, progress, and measurable wins.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4 mb-0">Product impact highlights</h3>
        <a class="btn btn-sm btn-outline-success" href="?page=catalog">Shop verified products</a>
    </div>
    <?php if (empty($impactHighlights)): ?>
        <div class="alert alert-info">Impact metrics will populate as soon as products are tagged with sustainability data.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Impact metric</th>
                    <th class="text-end">EcoPoints insight</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($impactHighlights as $highlight): ?>
                    <tr>
                        <td><?= htmlspecialchars($highlight['product']) ?></td>
                        <td><?= htmlspecialchars($highlight['category']) ?></td>
                        <td><?= htmlspecialchars($highlight['impact']) ?></td>
                        <td class="text-end"><span class="badge bg-primary bg-opacity-10 text-primary"><?= (int)$highlight['points'] ?> pts</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="mb-5">
    <div class="card shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h3 class="h5 mb-1">Share your impact</h3>
                <p class="text-muted mb-0">Join a challenge, post your sustainable swaps, and help the community learn faster.</p>
            </div>
            <a class="btn btn-success mt-3 mt-md-0" href="?page=community">Go to Community Hub</a>
        </div>
    </div>
</section>
