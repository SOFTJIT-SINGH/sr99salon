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
        <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff9f0',
                            100: '#fff3e0',
                            200: '#ffe0b2',
                            300: '#ffcc80',
                            400: '#ffb74d',
                            500: '#ffa000', // Golden Orange
                            600: '#ff9100',
                            700: '#ff6f00',
                            800: '#e65100',
                            900: '#bf360c'
                        },
                        darkgray: {
                            50: '#f8f9fa',
                            100: '#e9ecef',
                            200: '#dee2e6',
                            300: '#ced4da',
                            400: '#adb5bd',
                            500: '#6c757d',
                            600: '#495057',
                            700: '#343a40',
                            800: '#212529',
                            900: '#171819'
                        }
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .sidebar {
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        .sidebar:hover {
            box-shadow: 0 0 30px rgba(255, 160, 0, 0.1);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 160, 0, 0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, white 0%, #fff9f0 100%);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(255, 160, 0, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ffa000 0%, #ff9100 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 160, 0, 0.4);
        }
        
        .table-row:hover {
            background: #fff9f0;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        
        .modal.show {
            display: block;
        }
    </style>
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