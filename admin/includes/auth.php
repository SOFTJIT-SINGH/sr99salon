<?php
declare(strict_types=1);
session_start();

function requireLogin(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function isAdmin(): bool {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']);
}

function getCurrentAdminId(): ?int {
    return $_SESSION['admin_id'] ?? null;
}

function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Session timeout: 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: /admin/login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();