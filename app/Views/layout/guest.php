<?php
/**
 * Guest layout (login page) – no sidebar, centred card.
 * Receives: $title (string), $content (HTML string)
 */
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sign In') ?> · <?= e(\App\Core\Config::get('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body class="guest-body">
<div class="guest-wrap">
    <?php foreach (Session::pullFlashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endforeach; ?>

    <?= $content ?>

    <p class="guest-foot">&copy; <?= date('Y') ?> Student Information System</p>
</div>
</body>
</html>
