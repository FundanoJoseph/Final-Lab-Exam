<?php
/**
 * Login page.
 */
?>
<div class="login-card">
    <div class="login-brand">
        <div class="brand-logo big">SIS</div>
        <h1>Student Information System</h1>
        <p>Sign in with your administrator account</p>
    </div>

    <form method="post" action="<?= e(url('login')) ?>" autocomplete="off">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= e(old('username')) ?>" placeholder="Enter your username"
                   required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>
</div>
