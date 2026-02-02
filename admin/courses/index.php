<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireLogin();

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

try {
    // Get total count
    $countStmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses");
    $total = (int)$countStmt->fetch()['total'];
    $totalPages = ceil($total / $perPage);
    
    // Get courses
    $stmt = Database::getInstance()->prepare("
        SELECT * FROM courses 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die('Error loading courses: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Courses - SR99 Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Courses</h1>
            <a href="add.php" class="btn-primary">Add New Course</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Duration</th>
                            <th>Fees (₹)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No courses found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= $course['id'] ?></td>
                                    <td>
                                        <?php if ($course['image']): ?>
                                            <img src="../uploads/courses/<?= htmlspecialchars($course['image']) ?>" 
                                                 alt="" class="course-thumb">
                                        <?php endif; ?>
                                        <?= htmlspecialchars($course['title']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($course['duration']) ?></td>
                                    <td>₹<?= number_format($course['fees'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $course['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($course['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?= $course['id'] ?>" class="btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?id=<?= $course['id'] ?>" 
                                           class="btn-sm btn-danger"
                                           onclick="return confirm('Delete this course?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn-sm">&laquo; Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn-sm">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>