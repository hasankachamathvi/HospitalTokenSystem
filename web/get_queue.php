<?php
header('Content-Type: application/json');
$mysqli = new mysqli("localhost", "root", "", "hospital");
$result = $mysqli->query("SELECT * FROM patients WHERE status='waiting' ORDER BY created_at ASC");
$patients = [];
while($row = $result->fetch_assoc()){
    $patients[] = $row;
}
echo json_encode($patients);
$mysqli->close();
?>
