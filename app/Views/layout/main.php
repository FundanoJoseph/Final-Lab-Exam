<?php
/**
 * Main application layout: sidebar + top bar + content area.
 * Receives: $title (string), $content (HTML string)
 */
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> · <?= e(\App\Core\Config::get('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<div class="app-shell">

    <?php require __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="app-main">
        <header class="topbar">
            <h1 class="topbar-title"><?= e($title ?? '') ?></h1>
            <div class="topbar-user">
                <div class="user-chip">
                    <span class="user-avatar"><?= e(strtoupper(substr(current_user()['full_name'] ?? 'U', 0, 1))) ?></span>
                    <div class="user-meta">
                        <span class="user-role">Administrator</span>
                        <span class="user-name"><?= e(current_user()['username'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <?php foreach (Session::pullFlashes() as $flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </main>

        <footer class="app-footer">
            <span>&copy; <?= date('Y') ?> Student Information System · Final Lab Exam</span>
        </footer>
    </div>
</div>

<script src="<?= e(url('assets/js/app.js')) ?>"></script>
</body>
</html>
