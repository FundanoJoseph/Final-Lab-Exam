<?php
/**
 * Student form – shared by "Add New Student" and "Update Student Details".
 * Receives: $student (array|null), $courses (array)
 */
$isEdit = $student !== null;
$action = $isEdit ? url('students/' . $student['id']) : url('students');
?>
<section class="card">
    <div class="card-header">
        <h2><?= $isEdit ? 'Update Student Details' : 'Add New Student' ?></h2>
        <a href="<?= e(url('students')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
    </div>

    <form method="post" action="<?= e($action) ?>" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="student_no">Student No. <span class="req">*</span></label>
            <input type="text" id="student_no" name="student_no" maxlength="30"
                   value="<?= e(old('student_no', $student['student_no'] ?? '')) ?>"
                   placeholder="e.g. 221040603807" required>
        </div>

        <div class="form-group">
            <label for="status">Status <span class="req">*</span></label>
            <select id="status" name="status" required>
                <?php foreach (\App\Models\StudentModel::STATUSES as $st): ?>
                    <option value="<?= e($st) ?>"
                        <?= old('status', $student['status'] ?? 'Active') === $st ? 'selected' : '' ?>>
                        <?= e($st) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="first_name">First Name <span class="req">*</span></label>
            <input type="text" id="first_name" name="first_name" maxlength="50"
                   value="<?= e(old('first_name', $student['first_name'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" maxlength="50"
                   value="<?= e(old('middle_name', $student['middle_name'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name <span class="req">*</span></label>
            <input type="text" id="last_name" name="last_name" maxlength="50"
                   value="<?= e(old('last_name', $student['last_name'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender <span class="req">*</span></label>
            <select id="gender" name="gender" required>
                <?php foreach (['Male', 'Female'] as $g): ?>
                    <option value="<?= e($g) ?>"
                        <?= old('gender', $student['gender'] ?? '') === $g ? 'selected' : '' ?>>
                        <?= e($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="civil_status">Civil Status</label>
            <select id="civil_status" name="civil_status">
                <?php foreach (['Single', 'Married', 'Widowed', 'Separated'] as $cs): ?>
                    <option value="<?= e($cs) ?>"
                        <?= old('civil_status', $student['civil_status'] ?? 'Single') === $cs ? 'selected' : '' ?>>
                        <?= e($cs) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth <span class="req">*</span></label>
            <input type="date" id="date_of_birth" name="date_of_birth"
                   value="<?= e(old('date_of_birth', $student['date_of_birth'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label for="contact_no">Contact #</label>
            <input type="text" id="contact_no" name="contact_no" maxlength="20"
                   value="<?= e(old('contact_no', $student['contact_no'] ?? '')) ?>"
                   placeholder="e.g. 0917-123-4567">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" maxlength="100"
                   value="<?= e(old('email', $student['email'] ?? '')) ?>"
                   placeholder="e.g. juan@email.com">
        </div>

        <div class="form-group">
            <label for="course_id">Program / Course</label>
            <select id="course_id" name="course_id">
                <option value="">-- None --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"
                        <?= (string) old('course_id', $student['course_id'] ?? '') === (string) $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['code'] . ' — ' . $c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group span-2">
            <label for="present_address">Present Address</label>
            <textarea id="present_address" name="present_address" rows="2" maxlength="255"><?= e(old('present_address', $student['present_address'] ?? '')) ?></textarea>
        </div>

        <div class="form-group span-2">
            <label for="permanent_address">Permanent Address</label>
            <textarea id="permanent_address" name="permanent_address" rows="2" maxlength="255"><?= e(old('permanent_address', $student['permanent_address'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions span-2">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Add Student' ?></button>
            <a href="<?= e(url('students')) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>
