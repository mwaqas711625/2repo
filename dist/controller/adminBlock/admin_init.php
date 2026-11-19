<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/../admin.php';

use Admin\Admin;

if (!isset($_SESSION['user']['role_id'])) {
    exit('Access denied: Please login first.');
}

$stmtRole = $pdo->prepare("SELECT role_name FROM roles WHERE role_id = ?");
$stmtRole->execute([$_SESSION['user']['role_id']]);
$userRole = strtolower(trim((string) $stmtRole->fetchColumn()));

if ($userRole !== 'admin' && $userRole !== 'superadmin') {
    exit('Access denied: Only Admin can access this.');
}

$sessionEmail = trim((string) ($_SESSION['user']['email'] ?? ''));
$stmtAdmin = $pdo->prepare('SELECT user_id FROM users WHERE email = ? AND role_id = ? LIMIT 1');
$stmtAdmin->execute([$sessionEmail, (int) $_SESSION['user']['role_id']]);
$adminId = (int) $stmtAdmin->fetchColumn();
if ($adminId <= 0) {
    http_response_code(403);
    exit('Access denied: Unable to verify the admin account.');
}

$admin = new Admin($pdo, $adminId);