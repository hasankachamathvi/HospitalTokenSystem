<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$date = $_POST['date'] ?? '';

// Convert datetime-local format to MySQL datetime format
if($date) {
    $date = str_replace('T', ' ', $date);
    if(strlen($date) == 16) { // If format is "2026-01-10 10:00", add seconds
        $date .= ':00';
    }
}

if(!$name || !$email || !$phone || !$date){
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

// Regular bookings are always normal priority and go to the end
$result = $mysqli->query("SELECT MAX(queue_position) as max_pos FROM patients WHERE status='waiting'");
$row = $result->fetch_assoc();
$queuePos = ($row['max_pos'] ?? 0) + 1;

$stmt = $mysqli->prepare("INSERT INTO patients (name,email,phone,date,priority,queue_position) VALUES (?,?,?,?,?,?)");
$priority = 'normal';
$stmt->bind_param("sssssi",$name,$email,$phone,$date,$priority,$queuePos);
if($stmt->execute()){
    echo json_encode(['status'=>'success']);
}else{
    echo json_encode(['status'=>'error','message'=>'Insert failed: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
