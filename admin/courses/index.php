<?php
declare(strict_types=1);
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

try {
    // Get total count
    $countStmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses");
    $total = (int)$countStmt->fetch()['total'];
    $totalPages = ceil($total / $perPage);
    
    // Get courses
    $stmt = Database::getInstance()->prepare("
        SELECT * FROM courses 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die('Error loading courses: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include Tailwind Header -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - SR99 Admin Panel</title>
    
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
                            500: '#ffa000',
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
        
        .table-row:hover {
            background: #fff9f0;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header.php'; ?>
    
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-darkgray-900">Courses Management</h1>
                <p class="text-darkgray-500 mt-1">Manage all academy courses</p>
            </div>
            <a href="add.php" class="btn-primary inline-flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-primary-600 hover:to-primary-700 transition-all transform hover:scale-[1.02] shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Course
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><?= htmlspecialchars($_GET['success']) ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Courses Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-darkgray-200">
                    <thead class="bg-darkgray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">Fees (₹)</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-darkgray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-darkgray-200">
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-darkgray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-darkgray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-lg font-medium">No courses found</p>
                                        <p class="text-sm">Add your first course to get started</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr class="table-row">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-darkgray-900"><?= $course['id'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <?php if ($course['image']): ?>
                                                <img src="../uploads/courses/<?= htmlspecialchars($course['image']) ?>" 
                                                     alt="" class="w-12 h-12 object-cover rounded-lg">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-darkgray-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-darkgray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-medium text-darkgray-900"><?= htmlspecialchars($course['title']) ?></p>
                                                <p class="text-xs text-darkgray-500 mt-1 line-clamp-1"><?= htmlspecialchars(substr($course['description'], 0, 50)) ?>...</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-darkgray-700"><?= htmlspecialchars($course['duration']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-darkgray-900">₹<?= number_format($course['fees'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($course['status'] === 'active'): ?>
                                            <span class="badge bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="edit.php?id=<?= $course['id'] ?>" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-xs font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                            <a href="delete.php?id=<?= $course['id'] ?>" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors text-xs font-medium"
                                               onclick="return confirm('Are you sure you want to delete this course?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-darkgray-200 flex items-center justify-between">
                    <div class="text-sm text-darkgray-600">
                        Showing <?= (($page - 1) * $perPage) + 1 ?> to <?= min($page * $perPage, $total) ?> of <?= $total ?> courses
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" 
                               class="inline-flex items-center px-3 py-2 border border-darkgray-300 rounded-lg text-sm font-medium text-darkgray-700 bg-white hover:bg-darkgray-50">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <div class="flex items-center gap-1">
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>" 
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-medium <?= $i == $page ? 'bg-primary-500 text-white' : 'bg-white text-darkgray-700 hover:bg-darkgray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" 
                               class="inline-flex items-center px-3 py-2 border border-darkgray-300 rounded-lg text-sm font-medium text-darkgray-700 bg-white hover:bg-darkgray-50">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    </main>
</div>
</body>
</html>