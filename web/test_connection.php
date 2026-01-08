<?php
require_once 'db_config.php';
header('Content-Type: application/json');

// Test database connection
try {
    $mysqli = getDBConnection();
    
    // Check if database exists
    $dbCheck = $mysqli->query("SELECT DATABASE()");
    $currentDB = $dbCheck->fetch_row()[0];
    
    // Check if patients table exists
    $tableCheck = $mysqli->query("SHOW TABLES LIKE 'patients'");
    $tableExists = $tableCheck->num_rows > 0;
    
    // Count records
    $countResult = $mysqli->query("SELECT COUNT(*) as total FROM patients");
    $count = $countResult->fetch_assoc()['total'];
    
    // Get sample records
    $sampleResult = $mysqli->query("SELECT * FROM patients LIMIT 5");
    $samples = [];
    while($row = $sampleResult->fetch_assoc()) {
        $samples[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'connection' => 'Connected successfully',
        'database' => $currentDB,
        'table_exists' => $tableExists,
        'total_records' => $count,
        'sample_data' => $samples
    ], JSON_PRETTY_PRINT);
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
