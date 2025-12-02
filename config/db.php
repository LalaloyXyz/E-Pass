<?php
$host = 'localhost:3306';
$db   = 'events';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements for security
    PDO::ATTR_PERSISTENT => true, // Enable persistent connections to reduce overhead
    PDO::ATTR_TIMEOUT => 5, // Connection timeout
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

// Retry logic for high load scenarios
$maxRetries = 3;
$retryDelay = 0.1; // 100ms
$pdo = null;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        // Set connection limits
        $pdo->exec("SET SESSION wait_timeout = 300");
        $pdo->exec("SET SESSION interactive_timeout = 300");
        break; // Success, exit retry loop
    } catch (PDOException $e) {
        if ($attempt === $maxRetries) {
            // Log error instead of die() to prevent site crash
            error_log("Database connection failed after $maxRetries attempts: " . $e->getMessage());
            http_response_code(503);
            die(json_encode(['error' => 'Service temporarily unavailable. Please try again later.']));
        }
        usleep($retryDelay * 1000000 * $attempt); // Exponential backoff
    }
}

// php -S localhost:8000 
// http://localhost/phpmyadmin/
// sudo ufw allow 8000
