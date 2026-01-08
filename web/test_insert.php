<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_config.php';
header('Content-Type: application/json');

try {
    $mysqli = getDBConnection();
    
    // Test data
    $name = 'Test Patient';
    $email = 'test@test.com';
    $phone = '1234567890';
    $date = '2026-01-10 10:00:00';
    $priority = 'normal';
    $position = 'end';
    
    $mysqli->begin_transaction();
    
    // Insert at the end
    $result = $mysqli->query("SELECT MAX(queue_position) as max_pos FROM patients WHERE status='waiting'");
    $row = $result->fetch_assoc();
    $queuePos = ($row['max_pos'] ?? 0) + 1;
    
    echo json_encode([
        'status' => 'debug',
        'queue_position' => $queuePos,
        'test' => 'Before insert'
    ]);
    
    $stmt = $mysqli->prepare("INSERT INTO patients (name,email,phone,date,priority,queue_position) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("sssssi",$name,$email,$phone,$date,$priority,$queuePos);
    
    if($stmt->execute()){
        $mysqli->commit();
        echo json_encode(['status'=>'success', 'inserted_id' => $stmt->insert_id]);
    } else {
        throw new Exception('Insert failed: ' . $stmt->error);
    }
    $stmt->close();
    $mysqli->close();
    
} catch(Exception $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
?>
