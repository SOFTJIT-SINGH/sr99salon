<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die('Invalid certificate ID');
}

try {
    $stmt = Database::getInstance()->prepare("
        SELECT c.*, s.name as student_name, s.email as student_email, co.title as course_title
        FROM certificates c
        JOIN students s ON c.student_id = s.id
        JOIN courses co ON c.course_id = co.id
        WHERE c.id = ?
    ");
    $stmt->execute([$id]);
    $cert = $stmt->fetch();
    
    if (!$cert || !$cert['pdf_path']) {
        die('Certificate PDF not found');
    }
    
    $filePath = __DIR__ . '/../uploads/certificates/' . $cert['pdf_path'];
    
    if (!file_exists($filePath)) {
        die('Certificate file not found on server');
    }
    
    // Force download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $cert['certificate_number'] . '.pdf"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
    
} catch (PDOException $e) {
    die('Error downloading certificate: ' . $e->getMessage());
}