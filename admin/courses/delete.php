<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?error=Invalid course ID');
    exit;
}

try {
    // Get course to delete image
    $stmt = Database::getInstance()->prepare("SELECT image FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();
    
    // Delete course
    $stmt = Database::getInstance()->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete image file
    if ($course['image'] && file_exists(__DIR__ . '/../uploads/courses/' . $course['image'])) {
        unlink(__DIR__ . '/../uploads/courses/' . $course['image']);
    }
    
    header('Location: index.php?success=Course deleted successfully');
    exit;
    
} catch (PDOException $e) {
    header('Location: index.php?error=Error deleting course: ' . urlencode($e->getMessage()));
    exit;
}