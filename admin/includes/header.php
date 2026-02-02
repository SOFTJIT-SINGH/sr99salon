<?php
require_once 'auth.php';
requireLogin();
?>
<div class="header">
    <div class="header-content">
        <div class="logo">
            <h1>SR99 Admin Panel</h1>
        </div>
        <div class="nav-menu">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../courses/index.php">Courses</a>
            <a href="../students/index.php">Students</a>
            <a href="../certificates/index.php">Certificates</a>
            <form method="POST" action="../logout.php" style="display: inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
</div>