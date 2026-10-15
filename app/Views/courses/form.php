<?php
/**
 * Course form – shared by "Add New Course" and "Update Course Details".
 * Receives: $course (array|null), $departments (array)
 */
$isEdit = $course !== null;
$action = $isEdit ? url('courses/' . $course['id']) : url('courses');
?>
<section class="card">
    <div class="card-header">
        <h2><?= $isEdit ? 'Update Course Details' : 'Add New Course' ?></h2>
        <a href="<?= e(url('courses')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
    </div>

    <form method="post" action="<?= e($action) ?>" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="department_id">Department <span class="req">*</span></label>
            <select id="department_id" name="department_id" required>
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= (int) $d['id'] ?>"
                        <?= (string) old('department_id', $course['department_id'] ?? '') === (string) $d['id'] ? 'selected' : '' ?>>
                        <?= e($d['code'] . ' — ' . $d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="code">Course Code <span class="req">*</span></label>
            <input type="text" id="code" name="code" maxlength="20"
                   value="<?= e(old('code', $course['code'] ?? '')) ?>"
                   placeholder="e.g. BSIT" required>
        </div>

        <div class="form-group span-2">
            <label for="name">Course Name <span class="req">*</span></label>
            <input type="text" id="name" name="name" maxlength="120"
                   value="<?= e(old('name', $course['name'] ?? '')) ?>"
                   placeholder="e.g. Bachelor of Science in Information Technology" required>
        </div>

        <div class="form-group span-2">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3" maxlength="255"
                      placeholder="Short description (optional)"><?= e(old('description', $course['description'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions span-2">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Add Course' ?></button>
            <a href="<?= e(url('courses')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>
