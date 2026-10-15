<?php
/**
 * 403 / access-denied page.
 * Receives: $message (optional string)
 */
?>
<div class="card error-card">
    <h2><?= e($title ?? 'Access Denied') ?></h2>
    <p><?= e($message ?? 'You do not have permission to perform this action.') ?></p>
    <a class="btn btn-primary" href="<?= e(url('')) ?>">&larr; Back to Dashboard</a>
</div>
