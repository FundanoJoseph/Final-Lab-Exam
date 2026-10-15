<?php
/**
 * Student details + academic history ("View Student Details" /
 * "List Student's Academic History").
 * Receives: $student (array), $records (array), $courses (array)
 */
?>
<!-- ===== Student details ============================================ -->
<section class="card">
    <div class="card-header">
        <h2>Student Details</h2>
        <div class="header-actions">
            <a class="btn btn-warning btn-sm" href="<?= e(url('students/' . $student['id'] . '/edit')) ?>">Edit</a>
            <button type="button" class="btn btn-danger btn-sm" onclick="window.print()">Print</button>
            <a href="<?= e(url('students')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
        </div>
    </div>

    <div class="student-head">
        <div class="student-head-main">
            <p class="student-no"><?= e($student['student_no']) ?></p>
            <h3 class="student-name"><?= e(trim($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'])) ?></h3>
        </div>
        <div class="student-head-side">
            <span class="detail-label">Status</span>
            <?= status_badge($student['status']) ?>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Gender</span>
            <span class="detail-value"><?= e($student['gender'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Civil Status</span>
            <span class="detail-value"><?= e($student['civil_status'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Date of Birth</span>
            <span class="detail-value"><?= e(format_date($student['date_of_birth'])) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Contact #</span>
            <span class="detail-value"><?= e($student['contact_no'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value"><?= e($student['email'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Program / Course</span>
            <span class="detail-value">
                <?php if (!empty($student['course_name'])): ?>
                    <a href="<?= e(url('courses/' . $student['course_id'])) ?>">
                        <?= e($student['department_code'] . '/' . $student['course_code']) ?>
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </span>
        </div>
        <div class="detail-item span-2">
            <span class="detail-label">Present Address</span>
            <span class="detail-value"><?= e($student['present_address'] ?: 'This is my sample present address.') ?></span>
        </div>
        <div class="detail-item span-2">
            <span class="detail-label">Permanent Address</span>
            <span class="detail-value"><?= e($student['permanent_address'] ?: 'This is my sample permanent address.') ?></span>
        </div>
    </div>

    <!-- Update Student's Status -->
    <form method="post" action="<?= e(url('students/' . $student['id'] . '/status')) ?>" class="status-form">
        <?= csrf_field() ?>
        <label for="status" class="detail-label">Update Student's Status:</label>
        <select id="status" name="status">
            <?php foreach (\App\Models\StudentModel::STATUSES as $st): ?>
                <option value="<?= e($st) ?>" <?= $student['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
    </form>

    <!-- Delete Student Information and Records -->
    <form method="post" action="<?= e(url('students/' . $student['id'] . '/delete')) ?>"
          class="danger-zone" data-confirm="Delete this student's information and ALL academic records? This cannot be undone.">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger">Delete Student Information and Records</button>
    </form>
</section>

<!-- ===== Academic history =========================================== -->
<section class="card">
    <div class="card-header">
        <h2>Academic History</h2>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="academic-form">
            + Add Academic Record
        </button>
    </div>

    <!-- Add New Student Academic (hidden until the button is pressed) -->
    <div id="academic-form" class="collapsible">
        <form method="post" action="<?= e(url('students/' . $student['id'] . '/academics')) ?>" class="form-grid compact">
            <?= csrf_field() ?>

            <div class="form-group">
                <label>Department / Course <span class="req">*</span></label>
                <select name="course_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['department_code'] . '/' . $c['code'] . ' — ' . $c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Semester <span class="req">*</span></label>
                <select name="semester" required>
                    <?php foreach (\App\Models\StudentAcademicModel::SEMESTERS as $sem): ?>
                        <option value="<?= e($sem) ?>"><?= e($sem) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>School Year <span class="req">*</span></label>
                <input type="text" name="school_year" placeholder="e.g. 2025-2026" maxlength="9" required>
            </div>

            <div class="form-group">
                <label>Year <span class="req">*</span></label>
                <select name="year_level" required>
                    <?php foreach (\App\Models\StudentAcademicModel::YEAR_LEVELS as $y): ?>
                        <option value="<?= e($y) ?>"><?= e($y) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Beg. of Sem Status <span class="req">*</span></label>
                <select name="beg_status" required>
                    <?php foreach (\App\Models\StudentAcademicModel::BEG_STATUSES as $b): ?>
                        <option value="<?= e($b) ?>"><?= e($b) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>End of Sem Status <span class="req">*</span></label>
                <select name="end_status" required>
                    <?php foreach (\App\Models\StudentAcademicModel::END_STATUSES as $en): ?>
                        <option value="<?= e($en) ?>"><?= e($en) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions span-2">
                <button type="submit" class="btn btn-primary">Add Academic Record</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Department/Name</th>
                <th>Semester/School Yr.</th>
                <th>Year</th>
                <th>Beg. of Sem Status</th>
                <th>End of Sem Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($records)): ?>
                <tr><td colspan="7" class="empty-row">No academic records yet.</td></tr>
            <?php else: ?>
                <?php foreach ($records as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if (!empty($r['course_code'])): ?>
                                <a href="<?= e(url('courses/' . $r['course_id'])) ?>">
                                    <?= e($r['department_code'] . '/' . $r['course_code']) ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= e($r['semester'] . ' ' . $r['school_year']) ?></td>
                        <td><?= e($r['year_level']) ?></td>
                        <td><?= status_badge($r['beg_status']) ?></td>
                        <td><?= status_badge($r['end_status']) ?></td>
                        <td class="actions">
                            <a class="btn btn-warning btn-sm"
                               href="<?= e(url('students/' . $student['id'] . '/academics/' . $r['id'] . '/edit')) ?>">Edit</a>
                            <form method="post"
                                  action="<?= e(url('students/' . $student['id'] . '/academics/' . $r['id'] . '/delete')) ?>"
                                  class="inline-form" data-confirm="Delete this academic record?">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="table-foot">Showing 1 to <?= count($records) ?> of <?= count($records) ?> entr<?= count($records) === 1 ? 'y' : 'ies' ?></p>
</section>
