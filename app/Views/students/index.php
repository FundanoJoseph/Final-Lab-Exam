<?php
/**
 * Student list ("List All Students").
 * Receives: $students (array), $searchTerm (string)
 */
?>
<section class="card">
    <div class="card-header">
        <h2>All Students</h2>
        <div class="header-actions">
            <form method="get" action="<?= e(url('students')) ?>" class="search-form">
                <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search name or student no...">
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <a href="<?= e(url('students/create')) ?>" class="btn btn-primary btn-sm">+ Add New Student</a>
        </div>
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
                <th>Records</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
                <tr><td colspan="7" class="empty-row">No students found.</td></tr>
            <?php else: ?>
                <?php foreach ($students as $i => $s): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($s['student_no']) ?></strong></td>
                        <td><?= e(trim($s['last_name'] . ', ' . $s['first_name'] . ' ' . $s['middle_name'])) ?></td>
                        <td><?= e($s['course_code'] ?? '—') ?></td>
                        <td><?= status_badge($s['status']) ?></td>
                        <td><?= (int) $s['academic_count'] ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= e(url('students/' . $s['id'])) ?>">View</a>
                            <a class="btn btn-warning btn-sm" href="<?= e(url('students/' . $s['id'] . '/edit')) ?>">Edit</a>
                            <form method="post" action="<?= e(url('students/' . $s['id'] . '/delete')) ?>"
                                  class="inline-form" data-confirm="Delete this student and ALL academic records? This cannot be undone.">
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
    <p class="table-foot">Showing <?= count($students) ?> entr<?= count($students) === 1 ? 'y' : 'ies' ?></p>
</section>
