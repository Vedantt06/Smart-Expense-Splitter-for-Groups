<?php
// api/db.php
$host = 'localhost';
$db_name = 'smart_expense_db';
$username = 'root';
$password = ''; // Default XAMPP password is empty

try {
    $conn = new PDO("mysql:host={$host};port=3307;dbname={$db_name}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Allow CORS for local development if needed, but since it's same domain it's fine.
    // session_start() is needed for authentication state
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch(PDOException $exception) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $exception->getMessage() . '. Please ensure you have imported database.sql in phpMyAdmin.']);
    exit();
}
?>
