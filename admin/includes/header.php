<?php
require_once 'auth.php';
requireLogin();
?>
<!-- Sidebar -->
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="sidebar w-64 bg-white h-full fixed left-0 top-0 z-50">
        <div class="p-6 border-b border-darkgray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-xl">SR</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-darkgray-900">SR99 Admin</h1>
                    <p class="text-xs text-darkgray-500">Salon & Academy</p>
                </div>
            </div>
        </div>
        
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="../dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full transition-all <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary-50 text-primary-700 font-medium' : 'text-darkgray-700 hover:bg-darkgray-50' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li>
                    <a href="../courses/index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full transition-all <?= strpos($_SERVER['PHP_SELF'], 'courses') !== false ? 'bg-primary-50 text-primary-700 font-medium' : 'text-darkgray-700 hover:bg-darkgray-50' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Courses</span>
                    </a>
                </li>
                
                <li>
                    <a href="../students/index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full transition-all <?= strpos($_SERVER['PHP_SELF'], 'students') !== false ? 'bg-primary-50 text-primary-700 font-medium' : 'text-darkgray-700 hover:bg-darkgray-50' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Students</span>
                    </a>
                </li>
                
                <li>
                    <a href="../certificates/index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full transition-all <?= strpos($_SERVER['PHP_SELF'], 'certificates') !== false ? 'bg-primary-50 text-primary-700 font-medium' : 'text-darkgray-700 hover:bg-darkgray-50' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Certificates</span>
                    </a>
                </li>
                
                <li class="mt-6 pt-4 border-t border-darkgray-100">
                    <div class="px-4 py-2 text-xs font-semibold text-darkgray-500 uppercase">
                        Account
                    </div>
                </li>
                
                <li>
                    <a href="../profile.php" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full text-darkgray-700 hover:bg-darkgray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile</span>
                    </a>
                </li>
                
                <li>
                    <form method="POST" action="../logout.php">
                        <button type="submit" class="flex items-center gap-3 px-4 py-3 rounded-lg w-full text-darkgray-700 hover:bg-red-50 hover:text-red-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <!-- Top Bar -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-darkgray-900">
                    <?php
                    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
                    $titles = [
                        'dashboard' => 'Dashboard',
                        'index' => ucwords(str_replace('_', ' ', dirname($_SERVER['PHP_SELF']))),
                        'add' => 'Add New ' . ucwords(str_replace('_', ' ', dirname($_SERVER['PHP_SELF']))),
                        'edit' => 'Edit ' . ucwords(str_replace('_', ' ', dirname($_SERVER['PHP_SELF']))),
                        'login' => 'Login'
                    ];
                    echo $titles[$currentPage] ?? 'SR99 Admin Panel';
                    ?>
                </h1>
                <p class="text-darkgray-500 mt-1">Manage your salon and academy efficiently</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-medium text-darkgray-700">
                        <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
                    </p>
                    <p class="text-xs text-darkgray-500">
                        <?= ucfirst($_SESSION['admin_role'] ?? 'admin') ?>
                    </p>
                </div>
                
                <div class="w-10 h-10 bg-darkgray-200 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-darkgray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>