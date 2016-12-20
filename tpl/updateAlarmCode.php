<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_alarm_code = (isset($_POST['ac'])) ? $_POST['ac'] : "";


// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_uid)) || (is_null($in_alarm_code)) ) {exit;}


// update the Employee record
$sql = "UPDATE `timecard`.`Employee` 
        SET `Employee`.`alarm_code` = :alarm_code
        WHERE `Employee`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':alarm_code' => $in_alarm_code, ':id' => $in_uid));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee `alarm_code`: " . $e->getMessage() . "</br>";
}


?>