<?php
/**
 * Course list ("List All Courses").
 * Receives: $courses (array), $searchTerm (string)
 */
?>
<section class="card">
    <div class="card-header">
        <h2>All Courses</h2>
        <div class="header-actions">
            <form method="get" action="<?= e(url('courses')) ?>" class="search-form">
                <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search course...">
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <a href="<?= e(url('courses/create')) ?>" class="btn btn-primary btn-sm">+ Add New Course</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Course Name</th>
                <th>Department</th>
                <th>Students</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($courses)): ?>
                <tr><td colspan="6" class="empty-row">No courses found.</td></tr>
            <?php else: ?>
                <?php foreach ($courses as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($c['code']) ?></strong></td>
                        <td><?= e($c['name']) ?></td>
                        <td><?= e($c['department_name']) ?></td>
                        <td><?= (int) $c['student_count'] ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= e(url('courses/' . $c['id'])) ?>">View</a>
                            <a class="btn btn-warning btn-sm" href="<?= e(url('courses/' . $c['id'] . '/edit')) ?>">Edit</a>
                            <form method="post" action="<?= e(url('courses/' . $c['id'] . '/delete')) ?>"
                                  class="inline-form" data-confirm="Delete this course? This cannot be undone.">
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
    <p class="table-foot">Showing <?= count($courses) ?> entr<?= count($courses) === 1 ? 'y' : 'ies' ?></p>
</section>
