<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$success = $error = '';
$title = $description = $duration = $fees = '';
$status = 'active';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $fees = trim($_POST['fees'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Validation
    if (empty($title) || empty($duration) || empty($fees)) {
        $error = 'Title, duration, and fees are required';
    } elseif (!is_numeric($fees) || $fees <= 0) {
        $error = 'Invalid fees amount';
    } else {
        try {
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!in_array($_FILES['image']['type'], $allowed)) {
                    $error = 'Invalid image type. Only JPG/PNG allowed.';
                } elseif ($_FILES['image']['size'] > $maxSize) {
                    $error = 'Image too large. Max 2MB.';
                } else {
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
                    INSERT INTO courses (title, description, duration, fees, image, status)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $description, $duration, $fees, $image, $status]);
                
                header('Location: index.php?success=Course added successfully');
                exit;
            }
            
        } catch (PDOException $e) {
            $error = 'Error adding course: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Course - SR99 Admin</title>
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
            <h1>Add New Course</h1>
            <a href="index.php" class="btn-primary">← Back to Courses</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration (e.g., "3 months", "6 weeks") *</label>
                    <input type="text" id="duration" name="duration" value="<?= htmlspecialchars($duration) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="fees">Fees (₹) *</label>
                    <input type="number" id="fees" name="fees" step="0.01" value="<?= htmlspecialchars($fees) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Course Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Max 2MB. JPG/PNG only.</small>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary">Add Course</button>
            </form>
        </div>
    </div>
</body>
</html>