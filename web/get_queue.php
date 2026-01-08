<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();
$result = $mysqli->query("SELECT * FROM patients WHERE status='waiting' ORDER BY queue_position ASC");
$patients = [];
while($row = $result->fetch_assoc()){
    $patients[] = $row;
}
echo json_encode($patients);
$mysqli->close();
?>
