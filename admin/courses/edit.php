<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$error = $success = '';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php?error=Invalid course ID');
    exit;
}

// Get course data
try {
    $stmt = Database::getInstance()->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        header('Location: index.php?error=Course not found');
        exit;
    }
} catch (PDOException $e) {
    die('Error loading course: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $fees = trim($_POST['fees'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    if (empty($title) || empty($duration) || empty($fees)) {
        $error = 'Title, duration, and fees are required';
    } elseif (!is_numeric($fees) || $fees <= 0) {
        $error = 'Invalid fees amount';
    } else {
        try {
            $image = $course['image']; // Keep existing image
            
            // Handle new image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
                $maxSize = 2 * 1024 * 1024;
                
                if (!in_array($_FILES['image']['type'], $allowed)) {
                    $error = 'Invalid image type';
                } elseif ($_FILES['image']['size'] > $maxSize) {
                    $error = 'Image too large';
                } else {
                    // Delete old image if exists
                    if ($course['image'] && file_exists(__DIR__ . '/../uploads/courses/' . $course['image'])) {
                        unlink(__DIR__ . '/../uploads/courses/' . $course['image']);
                    }
                    
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = 'course_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $target = __DIR__ . '/../uploads/courses/' . $image;
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $error = 'Failed to upload image';
                    }
                }
            }
            
            if (!$error) {
                $stmt = Database::getInstance()->prepare("
                    UPDATE courses 
                    SET title = ?, description = ?, duration = ?, fees = ?, image = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $description, $duration, $fees, $image, $status, $id]);
                
                header('Location: index.php?success=Course updated successfully');
                exit;
            }
            
        } catch (PDOException $e) {
            $error = 'Error updating course: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Course - SR99 Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Edit Course</h1>
            <a href="index.php" class="btn-primary">← Back to Courses</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($course['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($course['description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration *</label>
                    <input type="text" id="duration" name="duration" value="<?= htmlspecialchars($course['duration']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="fees">Fees (₹) *</label>
                    <input type="number" id="fees" name="fees" step="0.01" value="<?= htmlspecialchars($course['fees']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Current Image</label>
                    <?php if ($course['image']): ?>
                        <img src="../uploads/courses/<?= htmlspecialchars($course['image']) ?>" 
                             alt="Course image" style="max-width: 200px; margin-top: 10px;">
                    <?php else: ?>
                        <p>No image uploaded</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="image">Replace Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Leave empty to keep current image</small>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?= $course['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $course['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary">Update Course</button>
            </form>
        </div>
    </div>
</body>
</html>