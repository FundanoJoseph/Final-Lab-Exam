<?php
/**
 * Department details ("View Department").
 * Receives: $department (array), $courses (array)
 */
?>
<section class="card">
    <div class="card-header">
        <h2>Department Details</h2>
        <div class="header-actions">
            <a class="btn btn-warning btn-sm" href="<?= e(url('departments/' . $department['id'] . '/edit')) ?>">Edit</a>
            <form method="post" action="<?= e(url('departments/' . $department['id'] . '/delete')) ?>"
                  class="inline-form" data-confirm="Delete this department? This cannot be undone.">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
            <a href="<?= e(url('departments')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Code</span>
            <span class="detail-value"><?= e($department['code']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Name</span>
            <span class="detail-value"><?= e($department['name']) ?></span>
        </div>
        <div class="detail-item span-2">
            <span class="detail-label">Description</span>
            <span class="detail-value"><?= e($department['description'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Created</span>
            <span class="detail-value"><?= e(format_datetime($department['created_at'])) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Last Updated</span>
            <span class="detail-value"><?= e(format_datetime($department['updated_at'])) ?></span>
        </div>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <h2>Courses in this Department</h2>
        <a href="<?= e(url('courses/create')) ?>" class="btn btn-primary btn-sm">+ Add New Course</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Course Name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($courses)): ?>
                <tr><td colspan="4" class="empty-row">No courses under this department yet.</td></tr>
            <?php else: ?>
                <?php foreach ($courses as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($c['code']) ?></strong></td>
                        <td><?= e($c['name']) ?></td>
                        <td>
                            <a class="btn btn-secondary btn-sm" href="<?= e(url('courses/' . $c['id'])) ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
