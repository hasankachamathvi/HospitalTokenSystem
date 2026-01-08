<?php
// Database configuration for InfinityFree
// REPLACE THESE VALUES with your InfinityFree database credentials

define('DB_HOST', 'sql123.infinityfree.net');  // Replace with your MySQL hostname from InfinityFree
define('DB_USER', 'epiz_12345678');            // Replace with your database username
define('DB_PASS', 'YourPassword');             // Replace with your database password
define('DB_NAME', 'epiz_12345678_hospital');   // Replace with your database name

// Create database connection
function getDBConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($mysqli->connect_error) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $mysqli->connect_error,
            'host' => DB_HOST,
            'user' => DB_USER,
            'database' => DB_NAME
        ]));
    }
    
    // Set charset to utf8mb4
    $mysqli->set_charset("utf8mb4");
    
    return $mysqli;
}
?>
