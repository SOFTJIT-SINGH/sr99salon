<?php
declare(strict_types=1);
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SR99 Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <div class="container">
        <h1 class="mb-20">Dashboard</h1>
        
        <div class="stats-grid">
            <?php
            try {
                // Total courses
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses");
                $totalCourses = $stmt->fetch()['total'];
                
                // Active courses
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses WHERE status = 'active'");
                $activeCourses = $stmt->fetch()['total'];
                
                // Total students
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM students");
                $totalStudents = $stmt->fetch()['total'];
                
                // Total certificates
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM certificates WHERE status = 'issued'");
                $totalCertificates = $stmt->fetch()['total'];
                
            } catch (PDOException $e) {
                die('Error loading stats: ' . $e->getMessage());
            }
            ?>
            
            <div class="stat-card">
                <p>Total Courses</p>
                <h3><?= $totalCourses ?></h3>
            </div>
            
            <div class="stat-card">
                <p>Active Courses</p>
                <h3><?= $activeCourses ?></h3>
            </div>
            
            <div class="stat-card">
                <p>Total Students</p>
                <h3><?= $totalStudents ?></h3>
            </div>
            
            <div class="stat-card">
                <p>Certificates Issued</p>
                <h3><?= $totalCertificates ?></h3>
            </div>
        </div>
        
        <div class="card">
            <h2>Recent Activity</h2>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</p>
        </div>
    </div>
</body>
</html>