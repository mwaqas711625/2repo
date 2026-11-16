
<?php

// Database Configuration

$host = "localhost";
$dbname = "hms";
$username = "root";
$password = "";

try {

    // Create Database Connection
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    // Set PDO Error Mode
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Default Fetch Mode
    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    // Log actual error on server
    error_log($e->getMessage());

    // Show user-friendly error
    http_response_code(500);

    die("Database Connection Failed. Please try again later.");

}

?>
