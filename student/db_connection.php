<?php
// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');  // Replace with your database username
define('DB_PASSWORD', '');  // Replace with your database password
define('DB_NAME', 'ulra');

// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset to UTF8
    $conn->set_charset("utf8");
    
    // Set timezone (adjust as needed)
    $conn->query("SET time_zone = '+05:30'");  // Indian Standard Time
    
} catch (Exception $e) {
    // Log error (in production, log to file instead of displaying)
    die("Error: " . $e->getMessage());
}

// Function to safely close the database connection
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Function to escape and sanitize input
function sanitizeInput($conn, $input) {
    if (is_array($input)) {
        return array_map(function($item) use ($conn) {
            return mysqli_real_escape_string($conn, $item);
        }, $input);
    }
    return mysqli_real_escape_string($conn, $input);
}

// Register shutdown function to close connection
register_shutdown_function('closeConnection', $conn);
?>