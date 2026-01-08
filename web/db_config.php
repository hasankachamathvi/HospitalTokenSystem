<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'webuserh');
define('DB_PASS', '1234');
define('DB_NAME', 'hospital');

// Create database connection
function getDBConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($mysqli->connect_error) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $mysqli->connect_error
        ]));
    }
    
    // Set charset to utf8mb4
    $mysqli->set_charset("utf8mb4");
    
    return $mysqli;
}
?>
