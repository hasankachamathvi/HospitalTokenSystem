<?php
// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$date = $_POST['date'] ?? '';
$priority = $_POST['priority'] ?? 'normal'; // normal, vip, emergency
$position = $_POST['position'] ?? 'end'; // start, end, after

// Convert datetime
if($date) {
    $date = str_replace('T', ' ', $date);
    if(strlen($date) == 16) { 
        $date .= ':00';
    }
}

if(!$name || !$email || !$phone || !$date){
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

$mysqli->begin_transaction();

try {
    // Automatic priority-based positioning
    if($priority === 'emergency') {
        // Emergency- Insert at the front 
        $mysqli->query("UPDATE patients SET queue_position = queue_position + 1 WHERE status='waiting'");
        $queuePos = 1;
    } 
    elseif($priority === 'vip') {
        // VIP- Insert after emergency patients but before normal patients
        $result = $mysqli->query("SELECT MAX(queue_position) as max_pos FROM patients WHERE status='waiting' AND priority='emergency'");
        $row = $result->fetch_assoc();
        $afterEmergency = $row['max_pos'] ?? 0;
        
        // Shift normal patients down
        $mysqli->query("UPDATE patients SET queue_position = queue_position + 1 WHERE status='waiting' AND queue_position > $afterEmergency");
        $queuePos = $afterEmergency + 1;
    }
    else {
        // Normal- Insert at the end 
        $result = $mysqli->query("SELECT MAX(queue_position) as max_pos FROM patients WHERE status='waiting'");
        $row = $result->fetch_assoc();
        $queuePos = ($row['max_pos'] ?? 0) + 1;
    }

    $stmt = $mysqli->prepare("INSERT INTO patients (name,email,phone,date,priority,queue_position) VALUES (?,?,?,?,?,?)");
    if(!$stmt) {
        throw new Exception('Prepare failed: ' . $mysqli->error);
    }
    
    $stmt->bind_param("sssssi",$name,$email,$phone,$date,$priority,$queuePos);
    
    if($stmt->execute()){
        $mysqli->commit();
        echo json_encode(['status'=>'success','id'=>$stmt->insert_id]);
    } else {
        throw new Exception('Insert failed: ' . $stmt->error);
    }
    $stmt->close();
} catch(Exception $e) {
    $mysqli->rollback();
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}

$mysqli->close();
?>