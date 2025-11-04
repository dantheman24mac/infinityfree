<?php
/** @var string $message */
?>
<section class="py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h2 class="display-5 fw-semibold mb-3">Not found</h2>
            <p class="text-muted"><?= htmlspecialchars($message ?? 'The requested resource could not be found.') ?></p>
            <a class="btn btn-success mt-3" href="/assignment-ecommerce/prototype/public/">Return to homepage</a>
        </div>
    </div>
</section>

