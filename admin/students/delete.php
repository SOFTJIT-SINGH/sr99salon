<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?error=Invalid student ID');
    exit;
}

try {
    $stmt = Database::getInstance()->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: index.php?success=Student deleted successfully');
    exit;
    
} catch (PDOException $e) {
    header('Location: index.php?error=Error deleting student');
    exit;
}