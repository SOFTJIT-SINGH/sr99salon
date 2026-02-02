<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$success = $error = '';
$student_id = $course_id = '';
$issue_date = date('Y-m-d');

// Get students and courses for dropdowns
try {
    $studentsStmt = Database::getInstance()->query("SELECT id, name, email FROM students ORDER BY name");
    $students = $studentsStmt->fetchAll();
    
    $coursesStmt = Database::getInstance()->query("SELECT id, title FROM courses WHERE status = 'active' ORDER BY title");
    $courses = $coursesStmt->fetchAll();
    
} catch (PDOException $e) {
    die('Error loading data: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    $issue_date = $_POST['issue_date'] ?? date('Y-m-d');
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    
    if (!$student_id || !$course_id) {
        $error = 'Please select student and course';
    } else {
        try {
            // Generate unique certificate number
            $prefix = 'SR99-CERT-';
            $lastStmt = Database::getInstance()->query("
                SELECT certificate_number FROM certificates 
                WHERE certificate_number LIKE '{$prefix}%' 
                ORDER BY id DESC LIMIT 1
            ");
            $last = $lastStmt->fetch();
            
            if ($last) {
                $lastNum = (int)str_replace($prefix, '', $last['certificate_number']);
                $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNum = '0001';
            }
            
            $certificate_number = $prefix . $newNum;
            
            // Generate PDF certificate (simple version)
            $pdf_path = null;
            require_once __DIR__ . '/../vendor/autoload.php'; // If using DomPDF
            
            // For now, just create database entry
            $stmt = Database::getInstance()->prepare("
                INSERT INTO certificates (student_id, course_id, certificate_number, issue_date, expiry_date, status, pdf_path)
                VALUES (?, ?, ?, ?, ?, 'issued', ?)
            ");
            $stmt->execute([$student_id, $course_id, $certificate_number, $issue_date, $expiry_date, $pdf_path]);
            
            header('Location: index.php?success=Certificate issued successfully: ' . $certificate_number);
            exit;
            
        } catch (PDOException $e) {
            $error = 'Error issuing certificate: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Issue Certificate - SR99 Admin</title>
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
            <h1>Issue Certificate</h1>
            <a href="index.php" class="btn-primary">← Back to Certificates</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label for="student_id">Select Student *</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student['id'] ?>" <?= $student_id == $student['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($student['name']) ?> (<?= htmlspecialchars($student['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="course_id">Select Course *</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>" <?= $course_id == $course['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="issue_date">Issue Date *</label>
                    <input type="date" id="issue_date" name="issue_date" value="<?= htmlspecialchars($issue_date) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="expiry_date">Expiry Date (optional)</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($expiry_date ?? '') ?>">
                    <small>Leave empty for lifetime certificate</small>
                </div>
                
                <button type="submit" class="btn-primary">Issue Certificate</button>
            </form>
        </div>
    </div>
</body>
</html>