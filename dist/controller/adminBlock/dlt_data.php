
<?php

require_once __DIR__ . '/admin_init.php';

// delete_handler.php mein
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = (int) $_POST['user_id'];
        $result = $admin->delete($userId); // <-- Sirf DELETE query chalegi
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