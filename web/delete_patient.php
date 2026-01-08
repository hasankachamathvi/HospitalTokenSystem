<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();
$id = $_POST['id'] ?? 0;

if($id){
    $mysqli->begin_transaction();
    try {
        // Get the queue position of the patient to be deleted
        $result = $mysqli->query("SELECT queue_position FROM patients WHERE id=$id AND status='waiting'");
        if($result && $row = $result->fetch_assoc()) {
            $deletedPos = $row['queue_position'];
            
            // Delete the patient 
            $stmt = $mysqli->prepare("DELETE FROM patients WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->close();
            
            // Update positions of patients after the deleted one (shift left)
            $mysqli->query("UPDATE patients SET queue_position = queue_position - 1 WHERE queue_position > $deletedPos AND status='waiting'");
            
            $mysqli->commit();
            echo json_encode(['status'=>'success','message'=>'Patient deleted and queue updated']);
        } else {
            throw new Exception('Patient not found in waiting queue');
        }
    } catch(Exception $e) {
        $mysqli->rollback();
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
} else {
    echo json_encode(['status'=>'error','message'=>'Invalid patient ID']);
}

$mysqli->close();
?>
