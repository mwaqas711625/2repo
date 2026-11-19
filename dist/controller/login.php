<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
// print_r($email);
// print_r($password);
// die();
require_once __DIR__ . '/../database/config.php';

$stmt = $pdo->prepare(
    'SELECT user_id, email, password_hash, role_id, is_active FROM users WHERE email = :email LIMIT 1'
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
// print_r($user);
if (
    $user
    // && password_verify($password, (string) $user['password_hash'])
    && (int) $user['role_id'] >= 0
    && (int) $user['is_active'] === 1
) {
    session_start();
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'user_id' => (int) $user['user_id'],
        'email' => $user['email'],
        'role_id' => (int) $user['role_id'],
    ];

    header('Location: ../dashboard.php');
    exit;
}

header('Location: ../../index.php?error=1');
exit;
?>
