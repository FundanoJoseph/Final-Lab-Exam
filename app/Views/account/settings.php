<?php
/**
 * Settings – account info + change password form.
 */
$user = current_user();
?>
<section class="card">
    <div class="card-header">
        <h2>My Account</h2>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Full Name</span>
            <span class="detail-value"><?= e($user['full_name']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Username</span>
            <span class="detail-value"><?= e($user['username']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Role</span>
            <span class="detail-value"><?= e(ucfirst($user['role'])) ?></span>
        </div>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <h2>Change Password</h2>
    </div>

    <form method="post" action="<?= e(url('settings/password')) ?>" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="current_password">Current Password <span class="req">*</span></label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password <span class="req">*</span></label>
            <input type="password" id="new_password" name="new_password" minlength="6" required>
            <small>At least <?= (int) \App\Core\Config::get('security.password_min_length', 6) ?> characters.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password <span class="req">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
        </div>

        <div class="form-actions span-2">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </div>
    </form>
</section>
