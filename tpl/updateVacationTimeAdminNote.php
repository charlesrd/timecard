<?php


require_once("config.php");


// get the incoming variables
$in_request_id = (isset($_POST['request_id'])) ? str_replace(' ', '', $_POST['request_id']) : "";
$in_admin_note = (isset($_POST['admin_note'])) ? $_POST['admin_note'] : "";


// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_request_id)) || (is_null($in_admin_note)) ) {exit;}


// update the Request_Vacation_Time record
$sql = "UPDATE `timecard`.`Request_Vacation_Time` 
        SET `Request_Vacation_Time`.`admin_note` = :admin_note
        WHERE `Request_Vacation_Time`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':admin_note' => $in_admin_note, ':id' => $in_request_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Request_Vacation_Time `admin_note`: " . $e->getMessage() . "</br>";
}


?>