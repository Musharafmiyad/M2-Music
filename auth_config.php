<?php
// Database configuration for authentication system
define('AUTH_DB_SERVER', 'localhost');
define('AUTH_DB_USERNAME', 'root');
define('AUTH_DB_PASSWORD', '');
define('AUTH_DB_NAME', 'm2music');

// Attempt to connect to MySQL database
try {
    $auth_pdo = new PDO("mysql:host=" . AUTH_DB_SERVER . ";dbname=" . AUTH_DB_NAME, AUTH_DB_USERNAME, AUTH_DB_PASSWORD);
    // Set the PDO error mode to exception
    $auth_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>