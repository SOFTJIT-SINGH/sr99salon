<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?error=Invalid certificate ID');
    exit;
}

try {
    $stmt = Database::getInstance()->prepare("UPDATE certificates SET status = 'revoked' WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: index.php?success=Certificate revoked successfully');
    exit;
    
} catch (PDOException $e) {
    header('Location: index.php?error=Error revoking certificate');
    exit;
}