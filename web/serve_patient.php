<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "hospital");
$id = $_POST['id'] ?? 0;
if($id){
    $stmt = $mysqli->prepare("UPDATE patients SET status='served' WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}
$mysqli->close();
echo json_encode(['status'=>'success']);
?>
