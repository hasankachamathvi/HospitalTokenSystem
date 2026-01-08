<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$mysqli = getDBConnection();
$id = $_POST['id'] ?? 0;

if($id){
    $mysqli->begin_transaction();
    try {
        //Get the queue position of the patient to be served
        $result = $mysqli->query("SELECT queue_position FROM patients WHERE id=$id AND status='waiting'");
        if($result && $row = $result->fetch_assoc()) {
            $servedPos = $row['queue_position'];
            
            // set patient as served and remove 
            $stmt = $mysqli->prepare("UPDATE patients SET status='served', queue_position=NULL WHERE id=?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->close();
            
            // Update positions of patients after the served one (shift left)
            $mysqli->query("UPDATE patients SET queue_position = queue_position - 1 WHERE queue_position > $servedPos AND status='waiting'");
            
            $mysqli->commit();
            echo json_encode(['status'=>'success','message'=>'Patient served and removed from queue']);
        } 
        else 
        {
            throw new Exception('Patient not found in waiting queue');
        }
    } 
    catch(Exception $e) 
    {
        $mysqli->rollback();
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}
 else {
    echo json_encode(['status'=>'error','message'=>'Invalid patient ID']);
}

$mysqli->close();
?>
