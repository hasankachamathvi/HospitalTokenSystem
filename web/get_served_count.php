<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();
$result = $mysqli->query("SELECT COUNT(*) as count FROM patients WHERE status='served'");
$row = $result->fetch_assoc();
echo json_encode(['count' => $row['count']]);
$mysqli->close();
?>
