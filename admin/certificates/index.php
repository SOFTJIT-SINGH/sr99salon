<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

try {
    $countStmt = Database::getInstance()->query("
        SELECT COUNT(*) as total 
        FROM certificates c
        JOIN students s ON c.student_id = s.id
        JOIN courses co ON c.course_id = co.id
    ");
    $total = (int)$countStmt->fetch()['total'];
    $totalPages = ceil($total / $perPage);
    
    $stmt = Database::getInstance()->prepare("
        SELECT c.*, s.name as student_name, s.email as student_email, co.title as course_title
        FROM certificates c
        JOIN students s ON c.student_id = s.id
        JOIN courses co ON c.course_id = co.id
        ORDER BY c.issue_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $certificates = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die('Error loading certificates: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates - SR99 Admin Panel</title>
    
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
            <h1>Certificates</h1>
            <a href="issue.php" class="btn-primary">Issue Certificate</a>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Certificate #</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Issue Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($certificates)): ?>
                            <tr><td colspan="7" class="text-center">No certificates found</td></tr>
                        <?php else: ?>
                            <?php foreach ($certificates as $cert): ?>
                                <tr>
                                    <td><?= $cert['id'] ?></td>
                                    <td><?= htmlspecialchars($cert['certificate_number']) ?></td>
                                    <td><?= htmlspecialchars($cert['student_name']) ?><br><small><?= htmlspecialchars($cert['student_email']) ?></small></td>
                                    <td><?= htmlspecialchars($cert['course_title']) ?></td>
                                    <td><?= date('d M Y', strtotime($cert['issue_date'])) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $cert['status'] === 'issued' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($cert['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cert['pdf_path']): ?>
                                            <a href="download.php?id=<?= $cert['id'] ?>" class="btn-sm btn-primary">Download</a>
                                        <?php endif; ?>
                                        <?php if ($cert['status'] === 'issued'): ?>
                                            <a href="revoke.php?id=<?= $cert['id'] ?>" 
                                               class="btn-sm btn-danger"
                                               onclick="return confirm('Revoke this certificate?')">Revoke</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn-sm">&laquo; Previous</a>
                    <?php endif; ?>
                    <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn-sm">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>