<?php
/** @var string $message */
?>
<section class="py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Access denied</h2>
                    <p class="text-muted"><?= htmlspecialchars($message ?? 'You do not have permission to view this section.') ?></p>
                    <a class="btn btn-outline-success" href="/assignment-ecommerce/prototype/public/admin/dashboard.php">Return to dashboard</a>
                </div>
            </div>
        </div>
    </div>
</section>
