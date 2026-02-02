<?php
declare(strict_types=1);
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include Tailwind Header from above -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SR99 Admin Panel</title>
    
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
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Welcome back, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</h2>
                    <p class="mt-2 opacity-90">Here's what's happening with your academy today.</p>
                </div>
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <span class="text-4xl font-bold"><?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            try {
                // Total courses
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses");
                $totalCourses = $stmt->fetch()['total'];
                
                // Active courses
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM courses WHERE status = 'active'");
                $activeCourses = $stmt->fetch()['total'];
                
                // Total students
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM students");
                $totalStudents = $stmt->fetch()['total'];
                
                // Total certificates
                $stmt = Database::getInstance()->query("SELECT COUNT(*) as total FROM certificates WHERE status = 'issued'");
                $totalCertificates = $stmt->fetch()['total'];
                
            } catch (PDOException $e) {
                die('Error loading stats: ' . $e->getMessage());
            }
            ?>
            
            <!-- Total Courses -->
            <div class="stat-card rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-primary-100 rounded-lg">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-darkgray-500">Total</span>
                </div>
                <h3 class="text-3xl font-bold text-darkgray-900"><?= $totalCourses ?></h3>
                <p class="text-sm text-darkgray-600 mt-1">Courses</p>
            </div>
            
            <!-- Active Courses -->
            <div class="stat-card rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-darkgray-500">Active</span>
                </div>
                <h3 class="text-3xl font-bold text-darkgray-900"><?= $activeCourses ?></h3>
                <p class="text-sm text-darkgray-600 mt-1">Active Courses</p>
            </div>
            
            <!-- Total Students -->
            <div class="stat-card rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-darkgray-500">Enrolled</span>
                </div>
                <h3 class="text-3xl font-bold text-darkgray-900"><?= $totalStudents ?></h3>
                <p class="text-sm text-darkgray-600 mt-1">Students</p>
            </div>
            
            <!-- Certificates Issued -->
            <div class="stat-card rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-darkgray-500">Issued</span>
                </div>
                <h3 class="text-3xl font-bold text-darkgray-900"><?= $totalCertificates ?></h3>
                <p class="text-sm text-darkgray-600 mt-1">Certificates</p>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-darkgray-900">Recent Activity</h2>
                <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View All</a>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-darkgray-50 rounded-lg">
                    <div class="p-2 bg-primary-100 rounded-lg">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-darkgray-900">New course added</p>
                        <p class="text-sm text-darkgray-500 mt-1">Advanced Hair Styling Techniques</p>
                        <p class="text-xs text-darkgray-400 mt-2">2 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4 p-4 bg-darkgray-50 rounded-lg">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-darkgray-900">New student enrolled</p>
                        <p class="text-sm text-darkgray-500 mt-1">Priya Sharma joined Hair Care Course</p>
                        <p class="text-xs text-darkgray-400 mt-2">5 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4 p-4 bg-darkgray-50 rounded-lg">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-darkgray-900">Certificate issued</p>
                        <p class="text-sm text-darkgray-500 mt-1">Certificate #SR99-CERT-045 to Rohan Kumar</p>
                        <p class="text-xs text-darkgray-400 mt-2">Yesterday</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </main>
</div>
</body>
</html>