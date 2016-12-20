<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_action = (isset($_POST['a'])) ? str_replace(' ', '', $_POST['a']) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";
$in_record_number = (isset($_POST['r'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['r'])) : "";

// setup variables
$today = date('Y-m-d');
$limit_start = $in_record_number-1;


// error checking
if ( ($userID != $in_uid) || ($in_uid == "") || ($in_action == "") || ($in_date == "") || ($in_record_number == "") || ($in_record_number > 3) || ($in_record_number <= 0) ) {exit;}


// get the correct id of the Employee_Hours record to delete
$query = mysql_query("SELECT H.id 
                      FROM Employee_Hours H 
                      WHERE H.eid = $userID 
                      AND H.date = '$in_date'
                      ORDER BY H.id ASC 
                      LIMIT $limit_start, 1");
$record = mysql_fetch_object($query);
$recordID = $record->id;

// IF the in_action == 'in', delete the entire record (including the clock_out time!)
if ($in_action == "in")
{
    // delete the record from the Forgot_Hour
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Forgot_Hour` WHERE `Forgot_Hour`.`ehid` = :ehid");

    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':ehid' => $recordID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Forgot_Hour: " . $e->getMessage() . "</br>";
    }

    // delete the time from the Employee_Hours
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Employee_Hours` WHERE `Employee_Hours`.`id` = :id");

    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':id' => $recordID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Employee_Hours: " . $e->getMessage() . "</br>";
    }
}
// IF the in_action == 'out', update the clock_out time in the record 
elseif ($in_action == "out")
{
    // delete the record from the Forgot_Hour
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Forgot_Hour` WHERE `Forgot_Hour`.`ehid` = :ehid");

    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':ehid' => $recordID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Forgot_Hour: " . $e->getMessage() . "</br>";
    }
    
    // update the time from the Employee_Hours
    $q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `Employee_Hours`.`clock_out` = NULL WHERE `Employee_Hours`.`id` = :id");
    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':id' => $recordID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error updating Employee_Hours: " . $e->getMessage() . "</br>";
    }
}
else {exit;}


// print the user-table
include('user-table.php');


?>