<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "hospital");
if($mysqli->connect_error) {
    die(json_encode(['status'=>'error','message'=>'DB connection failed']));
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$date = $_POST['date'] ?? '';

if(!$name || !$email || !$phone || !$date){
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO patients (name,email,phone,date) VALUES (?,?,?,?)");
$stmt->bind_param("ssss",$name,$email,$phone,$date);
if($stmt->execute()){
    echo json_encode(['status'=>'success']);
}else{
    echo json_encode(['status'=>'error','message'=>'Insert failed']);
}
$stmt->close();
$mysqli->close();
?>
