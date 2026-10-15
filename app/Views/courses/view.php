<?php
/**
 * Course details ("View Course Details").
 * Receives: $course (array), $studentCount (int), $academicCount (int)
 */
?>
<section class="card">
    <div class="card-header">
        <h2>Course Details</h2>
        <div class="header-actions">
            <a class="btn btn-warning btn-sm" href="<?= e(url('courses/' . $course['id'] . '/edit')) ?>">Edit</a>
            <form method="post" action="<?= e(url('courses/' . $course['id'] . '/delete')) ?>"
                  class="inline-form" data-confirm="Delete this course? This cannot be undone.">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
            <a href="<?= e(url('courses')) ?>" class="btn btn-secondary btn-sm">&larr; Back to List</a>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Course Code</span>
            <span class="detail-value"><?= e($course['code']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Course Name</span>
            <span class="detail-value"><?= e($course['name']) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Department</span>
            <span class="detail-value">
                <a href="<?= e(url('departments/' . $course['department_id'])) ?>">
                    <?= e($course['department_code'] . ' — ' . $course['department_name']) ?>
                </a>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Enrolled Students</span>
            <span class="detail-value"><?= (int) $studentCount ?></span>
        </div>
        <div class="detail-item span-2">
            <span class="detail-label">Description</span>
            <span class="detail-value"><?= e($course['description'] ?: '—') ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Created</span>
            <span class="detail-value"><?= e(format_datetime($course['created_at'])) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Last Updated</span>
            <span class="detail-value"><?= e(format_datetime($course['updated_at'])) ?></span>
        </div>
    </div>
</section>
