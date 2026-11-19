<?php
require_once __DIR__ . '/admin_init.php';

// register_handler.php ya POST block mein
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $result = $admin->post($_POST, $_FILES); // <-- Sirf POST / INSERT query chalegi
        $_SESSION['success'] = $result['message'];
        header("Location: ../../pages/admin/RegDr.php");
        exit();
    } catch (Throwable $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: ../../pages/admin/RegDr.php");
        exit();
    }
}
?>