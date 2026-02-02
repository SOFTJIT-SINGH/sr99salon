<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

try {
    $countStmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM students");
    $total = (int)$countStmt->fetch()['total'];
    $totalPages = ceil($total / $perPage);
    
    $stmt = Database::getInstance()->prepare("
        SELECT * FROM students 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die('Error loading students: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Students - SR99 Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Students</h1>
            <a href="add.php" class="btn-primary">Add New Student</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="6" class="text-center">No students found</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $student['id'] ?></td>
                                    <td><?= htmlspecialchars($student['name']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= htmlspecialchars($student['phone']) ?></td>
                                    <td><?= date('d M Y', strtotime($student['enrollment_date'])) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $student['id'] ?>" class="btn-sm btn-warning">Edit</a>
                                        <a href="delete.php?id=<?= $student['id'] ?>" 
                                           class="btn-sm btn-danger"
                                           onclick="return confirm('Delete this student?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
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