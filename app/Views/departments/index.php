<?php
/**
 * Department list ("List All Department").
 * Receives: $departments (array), $searchTerm (string)
 */
?>
<section class="card">
    <div class="card-header">
        <h2>All Departments</h2>
        <div class="header-actions">
            <form method="get" action="<?= e(url('departments')) ?>" class="search-form">
                <input type="text" name="q" value="<?= e($searchTerm) ?>" placeholder="Search department...">
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <a href="<?= e(url('departments/create')) ?>" class="btn btn-primary btn-sm">+ Add New Department</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Department Name</th>
                <th>Description</th>
                <th>Courses</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($departments)): ?>
                <tr><td colspan="6" class="empty-row">No departments found.</td></tr>
            <?php else: ?>
                <?php foreach ($departments as $i => $d): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($d['code']) ?></strong></td>
                        <td><?= e($d['name']) ?></td>
                        <td><?= e($d['description'] ?: '—') ?></td>
                        <td><?= (int) $d['course_count'] ?></td>
                        <td class="actions">
                            <a class="btn btn-secondary btn-sm" href="<?= e(url('departments/' . $d['id'])) ?>">View</a>
                            <a class="btn btn-warning btn-sm" href="<?= e(url('departments/' . $d['id'] . '/edit')) ?>">Edit</a>
                            <form method="post" action="<?= e(url('departments/' . $d['id'] . '/delete')) ?>"
                                  class="inline-form" data-confirm="Delete this department? This cannot be undone.">
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
    <p class="table-foot">Showing <?= count($departments) ?> entr<?= count($departments) === 1 ? 'y' : 'ies' ?></p>
</section>
