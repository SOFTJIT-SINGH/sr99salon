<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$success = $error = '';
$name = $email = $phone = '';
$enrollment_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $enrollment_date = $_POST['enrollment_date'] ?? date('Y-m-d');
    
    if (empty($name) || empty($email)) {
        $error = 'Name and email are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        try {
            $stmt = Database::getInstance()->prepare("
                INSERT INTO students (name, email, phone, enrollment_date)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $phone, $enrollment_date]);
            
            header('Location: index.php?success=Student added successfully');
            exit;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = 'Email already exists';
            } else {
                $error = 'Error adding student: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Student - SR99 Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Add New Student</h1>
            <a href="index.php" class="btn-primary">← Back to Students</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                </div>
                
                <div class="form-group">
                    <label for="enrollment_date">Enrollment Date *</label>
                    <input type="date" id="enrollment_date" name="enrollment_date" value="<?= htmlspecialchars($enrollment_date) ?>" required>
                </div>
                
                <button type="submit" class="btn-primary">Add Student</button>
            </form>
        </div>
    </div>
</body>
</html>