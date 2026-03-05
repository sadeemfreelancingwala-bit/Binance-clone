<?php
// db.php - Database connection for CryptoX Pro Exchange
// Using PDO for enhanced security with prepared statements
 
$host = 'localhost';
$db   = 'rsk56_rsk56_12';
$user = 'rsk56_rsk56_12';
$pass = '123456';
$charset = 'utf8mb4';
 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
 
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // For production, you might want to log this and show a generic message
     die("Database connection failed: " . $e->getMessage());
}
 
// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
/**
 * Basic CSRF Protection helper
 */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
 
function check_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("CSRF validation failed.");
    }
}
 
/**
 * Helper function for input sanitization
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
 
/**
 * Helper to check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
 
/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
