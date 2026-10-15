<?php
/**
 * Dashboard – summary of lists + recent students.
 * Receives: $counts (array), $recentStudents (array)
 */
?>
<section class="summary-grid">
    <div class="summary-card">
        <span class="summary-label">Departments</span>
        <span class="summary-value"><?= (int) $counts['departments'] ?></span>
        <a href="<?= e(url('departments')) ?>" class="summary-link">View list &rarr;</a>
    </div>
    <div class="summary-card">
        <span class="summary-label">Courses</span>
        <span class="summary-value"><?= (int) $counts['courses'] ?></span>
        <a href="<?= e(url('courses')) ?>" class="summary-link">View list &rarr;</a>
    </div>
    <div class="summary-card">
        <span class="summary-label">Students</span>
        <span class="summary-value"><?= (int) $counts['students'] ?></span>
        <a href="<?= e(url('students')) ?>" class="summary-link">View list &rarr;</a>
    </div>
    <div class="summary-card accent">
        <span class="summary-label">Active Students</span>
        <span class="summary-value"><?= (int) $counts['active'] ?></span>
        <a href="<?= e(url('students')) ?>" class="summary-link">View list &rarr;</a>
    </div>
    <div class="summary-card">
        <span class="summary-label">Academic Records</span>
        <span class="summary-value"><?= (int) $counts['academics'] ?></span>
        <a href="<?= e(url('students')) ?>" class="summary-link">Browse students &rarr;</a>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <h2>Recently Added Students</h2>
        <a href="<?= e(url('students/create')) ?>" class="btn btn-primary btn-sm">+ Add New Student</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Student No.</th>
                <th>Name</th>
                <th>Program</th>
                <th>Status</th>
                <th>Added</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($recentStudents)): ?>
                <tr>
                    <td colspan="7" class="empty-row">No students recorded yet. Start by adding one.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentStudents as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($s['student_no']) ?></td>
                        <td><?= e(trim($s['last_name'] . ', ' . $s['first_name'])) ?></td>
                        <td><?= e($s['course_name'] ?? '—') ?></td>
                        <td><?= status_badge($s['status']) ?></td>
                        <td><?= e(format_datetime($s['created_at'])) ?></td>
                        <td>
                            <a class="btn btn-secondary btn-sm" href="<?= e(url('students/' . $s['id'])) ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
