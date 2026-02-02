<?php
require_once 'config/database.php';

$username = 'admin';
$password = 'ChangeThisPassword123!'; // CHANGE THIS IMMEDIATELY
$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = Database::getInstance()->prepare("
        INSERT INTO admin_users (username, password_hash, role)
        VALUES (?, ?, 'super_admin')
    ");
    $stmt->execute([$username, $password_hash]);
    echo "Admin user created successfully!\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
    echo "CHANGE THE PASSWORD IMMEDIATELY!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>