<?php
/**
 * 500 / generic error page. Sensitive details are written to
 * logs/app.log instead of being shown here.
 */
?>
<div class="card error-card">
    <h2><?= e($title ?? 'Something Went Wrong') ?></h2>
    <p><?= e($message ?? 'An unexpected error occurred. It has been logged and will be investigated.') ?></p>
    <a class="btn btn-primary" href="<?= e(url('')) ?>">&larr; Back to Dashboard</a>
</div>
