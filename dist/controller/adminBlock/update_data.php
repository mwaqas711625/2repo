<?php
require_once __DIR__ . '/admin_init.php';
// update_handler.php mein
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = (int) $_POST['user_id'];
        $result = $admin->update($userId, $_POST, $_FILES); // <-- Sirf UPDATE query chalegi
        $_SESSION['success'] = $result['message'];
        header("Location: ../../pages/admin/RegDr.php");
        exit();
    } catch (Throwable $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: ../../pages/admin/RegDr.php");
        exit();
    }
}