<?php
/**
 * Department form – shared by "Add New Department" and "Update Department".
 * Receives: $department (array|null)
 */
$isEdit = $department !== null;
$action = $isEdit ? url('departments/' . $department['id']) : url('departments');
?>
<section class="card">
    <div class="card-header">
        <h2><?= $isEdit ? 'Update Department' : 'Add New Department' ?></h2>
        <a href="<?= e(url('departments')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
    </div>

    <form method="post" action="<?= e($action) ?>" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="code">Department Code <span class="req">*</span></label>
            <input type="text" id="code" name="code" maxlength="20"
                   value="<?= e(old('code', $department['code'] ?? '')) ?>"
                   placeholder="e.g. CCS" required>
            <small>Letters, numbers and dashes only.</small>
        </div>

        <div class="form-group">
            <label for="name">Department Name <span class="req">*</span></label>
            <input type="text" id="name" name="name" maxlength="100"
                   value="<?= e(old('name', $department['name'] ?? '')) ?>"
                   placeholder="e.g. College of Computer Studies" required>
        </div>

        <div class="form-group span-2">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" maxlength="255"
                      placeholder="Short description (optional)"><?= e(old('description', $department['description'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions span-2">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Add Department' ?></button>
            <a href="<?= e(url('departments')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>
