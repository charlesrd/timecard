<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_action = (isset($_POST['a'])) ? str_replace(' ', '', $_POST['a']) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";
$in_record_number = (isset($_POST['r'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['r'])) : "";
$in_hour = (isset($_POST['hour'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['hour'])) : "";
$in_minute = (isset($_POST['minute'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['minute'])) : "";
$in_daytime = (isset($_POST['daytime'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['daytime'])) : "";

// setup variables
$today = date('Y-m-d');
$limit_start = $in_record_number-1;

// error checking
if ( ($userID != $in_uid) || (is_null($in_uid)) || (is_null($in_action)) || (is_null($in_date)) || (is_null($in_record_number)) || ($in_record_number > 3) || ($in_record_number <= 0) || (is_null($in_hour)) || ($in_hour > 12) || ($in_hour <= 0) || (is_null($in_minute)) || ($in_minute > 59) || ($in_minute < 0) || (is_null($in_daytime)) || ($in_daytime < 0) || ($in_daytime > 1) ) {exit;}


// correct the hour if the daytime is AM and the hour = 12
if ($in_daytime == 0)
{
    if ($in_hour == 12) {$in_hour = 0;}
}
// correct the hour if the daytime is PM
elseif ($in_daytime == 1)
{
    if ($in_hour != 12) {$in_hour += 12;} 
}
else {exit;}


// correct the hour if the in_hour < 10
$in_hour = ($in_hour < 10) ? "0".$in_hour : $in_hour;
$in_minute = ($in_minute < 10) ? "0".$in_minute : $in_minute;

// set the clock_in and clock_out times
$clock_in = $in_hour.":".$in_minute.":00";
$clock_out = $in_hour.":".$in_minute.":00";


// get the number of Employee_Hours records for this employee on this date
$query = mysql_query("SELECT COUNT(H.id) AS count 
                      FROM Employee_Hours H 
                      WHERE H.eid = $userID 
                      AND H.date = '$in_date'");
$record = mysql_fetch_object($query);
$recordCount = $record->count;


// IF the recordCount < in_record_number, INSERT the new time record
if ($recordCount < $in_record_number)
{
    if ($in_action == "in")
    {
        // INSERT the new clock_in time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Hours` (`id`, `eid`, `clock_in`, `clock_out`, `date`) VALUES (NULL, :userID, :clock_in, NULL, :in_date)");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':clock_in' => $clock_in, ':in_date' => $in_date));
            $recordID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Employee_Hours (clock_in): " . $e->getMessage() . "</br>";
        }
    }
    elseif ($in_action == "out")
    {
        // INSERT the new clock_out time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Hours` (`id`, `eid`, `clock_in`, `clock_out`, `date`) VALUES (NULL, :userID, NULL, :clock_out, :in_date)");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':clock_out' => $clock_out, ':in_date' => $in_date));
            $recordID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Employee_Hours (clock_out): " . $e->getMessage() . "</br>";
        }
    }
}
// IF recordCount >= in_record_number, UPDATE the clock_out time in the record 
elseif ($recordCount >= $in_record_number)
{
    // get the correct id of the Employee_Hours record to update
    $query = mysql_query("SELECT H.id 
                          FROM Employee_Hours H 
                          WHERE H.eid = $userID 
                          AND H.date = '$in_date'
                          ORDER BY H.id ASC 
                          LIMIT $limit_start, 1");
    $record = mysql_fetch_object($query);
    $recordID = $record->id;

    if ($in_action == "in")
    {
        // update the time from the Employee_Hours
        $q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `Employee_Hours`.`clock_in` = :clock_in WHERE `Employee_Hours`.`id` = :id");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':clock_in' => $clock_in, ':id' => $recordID));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error updating Employee_Hours (clock_in): " . $e->getMessage() . "</br>";
        }

        // update the time from the Forgot_Hour (clock_in)
        $q = $db_pdo->prepare("UPDATE `timecard`.`Forgot_Hour` SET `Forgot_Hour`.`clock_in` = 0 WHERE `Forgot_Hour`.`ehid` = :id");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':id' => $recordID));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error updating Forgot_Hour (clock_in): " . $e->getMessage() . "</br>";
        }
    }
    elseif ($in_action == "out")
    {
        // update the time from the Employee_Hours
        $q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `Employee_Hours`.`clock_out` = :clock_out WHERE `Employee_Hours`.`id` = :id");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':clock_out' => $clock_out, ':id' => $recordID));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error updating Employee_Hours (clock_out): " . $e->getMessage() . "</br>";
        }

        // update the time from the Forgot_Hour (clock_out)
        $q = $db_pdo->prepare("UPDATE `timecard`.`Forgot_Hour` SET `Forgot_Hour`.`clock_out` = 0 WHERE `Forgot_Hour`.`ehid` = :id");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':id' => $recordID));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error updating Forgot_Hour (clock_out): " . $e->getMessage() . "</br>";
        }
    }
    else {exit;}
}
else {exit;}


// print the user-table
include('user-table.php');


?>