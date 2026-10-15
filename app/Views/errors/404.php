<?php
/**
 * 404 / missing-record page.
 * Receives: $message (optional string)
 */
?>
<div class="card error-card">
    <h2><?= e($title ?? 'Page Not Found') ?></h2>
    <p><?= e($message ?? 'The page you are looking for does not exist.') ?></p>
    <a class="btn btn-primary" href="<?= e(url('')) ?>">&larr; Back to Dashboard</a>
</div>
