<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_forgot_string = (isset($_POST['fs'])) ? str_replace(' ', '', $_POST['fs']) : "";

// remove the last character from the in_forgot_string
$in_forgot_string = substr($in_forgot_string, 0, -1);

// setup variables
$today = date('Y-m-d');
$IN_FORGOT = explode('|', $in_forgot_string);
$in_date = "";
$in_record_number = "";
$in_action = "";

// error checking
if ( ($userID != $in_uid) || (is_null($in_uid)) || (is_null($in_forgot_string)) ) {exit;}


foreach ($IN_FORGOT as $in_forgot)
{
    // get the values from the current in_forgot
    list($in_date, $in_record_number, $in_action) = explode(',', $in_forgot);
    $limit_start = $in_record_number-1;

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
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Hours` (`id`, `eid`, `clock_in`, `clock_out`, `date`) VALUES (NULL, :userID, NULL, NULL, :in_date)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
                $recordID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Employee_Hours (clock_in): " . $e->getMessage() . "</br>";
            }

            // INSERT the new Forgot_Hour (clock_in)
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Forgot_Hour` (`id`, `ehid`, `clock_in`, `clock_out`) VALUES (NULL, :recordID, 1, 0)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':recordID' => $recordID));
                $recordID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Forgot_Hours (clock_in): " . $e->getMessage() . "</br>";
            }
        }
        elseif ($in_action == "out")
        {
            // INSERT the new clock_out time
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Hours` (`id`, `eid`, `clock_in`, `clock_out`, `date`) VALUES (NULL, :userID, NULL, NULL, :in_date)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
                $recordID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Employee_Hours (clock_out): " . $e->getMessage() . "</br>";
            }

            // INSERT the new Forgot_Hour (clock_out)
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Forgot_Hour` (`id`, `ehid`, `clock_in`, `clock_out`) VALUES (NULL, :recordID, 0, 1)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':recordID' => $recordID));
                $recordID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Forgot_Hours (clock_in): " . $e->getMessage() . "</br>";
            }
        }
        else {exit;}
    }
    // IF recordCount >= in_record_number, UPDATE the clock_out time in the record 
    elseif ($recordCount >= $in_record_number)
    {
        // get the correct id of the Employee_Hours record to update
        $sql = "SELECT H.id 
                FROM Employee_Hours H 
                WHERE H.eid = $userID 
                AND H.date = '$in_date'
                ORDER BY H.id ASC 
                LIMIT $limit_start, 1";
        $query = mysql_query($sql);
        $record = mysql_fetch_object($query);
        $recordID = $record->id;

        // get the number of Forgot_Hour records for this Employee_Hour ID
        $sql = "SELECT COUNT(F.id) AS count 
                              FROM Forgot_Hour F 
                              WHERE F.ehid = $recordID";
        $query = mysql_query($sql);
        $forgot = mysql_fetch_object($query);
        $forgotCount = $forgot->count;

        if ($in_action == "in")
        {
            // update the time from the Employee_Hours
            $q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `Employee_Hours`.`clock_in` = NULL WHERE `Employee_Hours`.`id` = :id");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':id' => $recordID));
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error updating Employee_Hours (clock_in): " . $e->getMessage() . "</br>";
            }

            if ($forgotCount > 0)
            {
                // update the time from the Forgot_Hour (clock_in)
                $q = $db_pdo->prepare("UPDATE `timecard`.`Forgot_Hour` SET `Forgot_Hour`.`clock_in` = 1 WHERE `Forgot_Hour`.`ehid` = :id");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':id' => $recordID));
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error updating Forgot_Hour (clock_in): " . $e->getMessage() . "</br>";
                }
            }
            else
            {
                // INSERT the new Forgot_Hour (clock_in)
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Forgot_Hour` (`id`, `ehid`, `clock_in`, `clock_out`) VALUES (NULL, :recordID, 1, 0)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':recordID' => $recordID));
                    $recordID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new Forgot_Hours (clock_in): " . $e->getMessage() . "</br>";
                }
            }
        }
        elseif ($in_action == "out")
        {
            // update the time from the Employee_Hours
            $q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `Employee_Hours`.`clock_out` = NULL WHERE `Employee_Hours`.`id` = :id");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':id' => $recordID));
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error updating Employee_Hours (clock_out): " . $e->getMessage() . "</br>";
            }

            if ($forgotCount > 0)
            {
                // update the time from the Forgot_Hour (clock_out)
                $q = $db_pdo->prepare("UPDATE `timecard`.`Forgot_Hour` SET `Forgot_Hour`.`clock_out` = 1 WHERE `Forgot_Hour`.`ehid` = :id");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':id' => $recordID));
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error updating Forgot_Hour (clock_out): " . $e->getMessage() . "</br>";
                }
            }
            else
            {
                // INSERT the new Forgot_Hour (clock_out)
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Forgot_Hour` (`id`, `ehid`, `clock_in`, `clock_out`) VALUES (NULL, :recordID, 0, 1)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':recordID' => $recordID));
                    $recordID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new Forgot_Hours (clock_in): " . $e->getMessage() . "</br>";
                }
            }
        }
        else {exit;}
    }
    else {exit;}
}


// print the user-table
include('user-table.php');


?>