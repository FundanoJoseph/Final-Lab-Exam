<?php
/**
 * Sidebar navigation (shared by every page of the main layout).
 */
use App\Core\Session;
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">SIS</div>
        <div class="brand-text">
            <strong>Student Info</strong>
            <span>Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= e(url('')) ?>" class="nav-link">
            <span class="nav-icon">&#9632;</span> Dashboard
        </a>

        <p class="nav-section">Students</p>
        <a href="<?= e(url('students/create')) ?>" class="nav-link">
            <span class="nav-icon">+</span> Add Student
        </a>
        <a href="<?= e(url('students')) ?>" class="nav-link">
            <span class="nav-icon">&#9776;</span> Student List
        </a>

        <p class="nav-section">Academics</p>
        <a href="<?= e(url('courses')) ?>" class="nav-link">
            <span class="nav-icon">&#9776;</span> Course List
        </a>
        <a href="<?= e(url('departments')) ?>" class="nav-link">
            <span class="nav-icon">&#9776;</span> Department List
        </a>
        <a href="<?= e(url('departments/create')) ?>" class="nav-link">
            <span class="nav-icon">+</span> Add Dept
        </a>
        <a href="<?= e(url('courses/create')) ?>" class="nav-link">
            <span class="nav-icon">+</span> Add Course
        </a>

        <p class="nav-section">System</p>
        <a href="<?= e(url('settings')) ?>" class="nav-link">
            <span class="nav-icon">&#9881;</span> Settings
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="post" action="<?= e(url('logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-logout">&#10140; Log out</button>
        </form>
    </div>
</aside>
