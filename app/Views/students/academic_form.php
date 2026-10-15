<?php
/**
 * Academic-record form – used to "Edit Student Academic".
 * Receives: $student (array), $record (array), $courses (array)
 */
$action = url('students/' . $student['id'] . '/academics/' . $record['id']);
?>
<section class="card">
    <div class="card-header">
        <h2>Edit Student Academic</h2>
        <a href="<?= e(url('students/' . $student['id'])) ?>" class="btn btn-secondary btn-sm">&larr; Back to Student</a>
    </div>

    <p class="hint">
        Student: <strong><?= e(trim($student['last_name'] . ', ' . $student['first_name'])) ?></strong>
        (<?= e($student['student_no']) ?>)
    </p>

    <form method="post" action="<?= e($action) ?>" class="form-grid">
        <?= csrf_field() ?>

        <div class="form-group">
            <label>Department / Course <span class="req">*</span></label>
            <select name="course_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"
                        <?= (string) old('course_id', $record['course_id'] ?? '') === (string) $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['department_code'] . '/' . $c['code'] . ' — ' . $c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Semester <span class="req">*</span></label>
            <select name="semester" required>
                <?php foreach (\App\Models\StudentAcademicModel::SEMESTERS as $sem): ?>
                    <option value="<?= e($sem) ?>"
                        <?= old('semester', $record['semester'] ?? '') === $sem ? 'selected' : '' ?>>
                        <?= e($sem) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>School Year <span class="req">*</span></label>
            <input type="text" name="school_year" maxlength="9" placeholder="e.g. 2025-2026"
                   value="<?= e(old('school_year', $record['school_year'] ?? '')) ?>" required>
        </div>

        <div class="form-group">
            <label>Year <span class="req">*</span></label>
            <select name="year_level" required>
                <?php foreach (\App\Models\StudentAcademicModel::YEAR_LEVELS as $y): ?>
                    <option value="<?= e($y) ?>"
                        <?= old('year_level', $record['year_level'] ?? '') === $y ? 'selected' : '' ?>>
                        <?= e($y) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Beg. of Sem Status <span class="req">*</span></label>
            <select name="beg_status" required>
                <?php foreach (\App\Models\StudentAcademicModel::BEG_STATUSES as $b): ?>
                    <option value="<?= e($b) ?>"
                        <?= old('beg_status', $record['beg_status'] ?? '') === $b ? 'selected' : '' ?>>
                        <?= e($b) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>End of Sem Status <span class="req">*</span></label>
            <select name="end_status" required>
                <?php foreach (\App\Models\StudentAcademicModel::END_STATUSES as $en): ?>
                    <option value="<?= e($en) ?>"
                        <?= old('end_status', $record['end_status'] ?? '') === $en ? 'selected' : '' ?>>
                        <?= e($en) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions span-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= e(url('students/' . $student['id'])) ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>
