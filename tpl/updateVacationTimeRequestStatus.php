<?php


require_once("config.php");


// get the incoming variables
$in_request_id = (isset($_POST['status'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['request_id'])) : "";
$in_status = (isset($_POST['status'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['status'])) : "";
$in_dates = (isset($_POST['dates'])) ? str_replace(' ', '', $_POST['dates']) : "";
$in_hours = (isset($_POST['hours'])) ? str_replace(' ', '', $_POST['hours']) : "";


// remove the ending delimiter
$in_dates = rtrim($in_dates, "||");
$in_hours = rtrim($in_hours, "||");


// setup variables
$today = date('Y-m-d');
$count = 0;
$REQUESTED_DATES = explode("||", $in_dates);
$REQUESTED_HOURS = explode("||", $in_hours);


// error checking
if ( (!isset($userID)) || (!isset($adminID)) || ($in_dates == "") || ($in_hours == "") ) {exit;}


// get information about this vacation time request
$sql = "SELECT *
		FROM Request_Vacation_Time R
		WHERE R.id = $in_request_id";

$query = mysql_query($sql);
$request = mysql_fetch_object($query);
$request_eid = $request->eid;


// update the Request_Vacation_Time record
$sql = "UPDATE `timecard`.`Request_Vacation_Time` 
        SET `Request_Vacation_Time`.`status` = :status
        WHERE `Request_Vacation_Time`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':status' => $in_status, ':id' => $in_request_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee `Request_Vacation_Time`.`status`: " . $e->getMessage() . "</br>";
}


// foreach vacation date that is requested, remove any existing vacation time on that date
foreach ($REQUESTED_DATES as $request_date)
{
    if ($request_date != "")
    {
        $q = $db_pdo->prepare("DELETE FROM `timecard`.`Vacation_Time` WHERE `Vacation_Time`.`eid` = :request_eid AND `Vacation_Time`.`date` = :request_date");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':request_eid' => $request_eid, ':request_date' => $request_date));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error deleting Vacation_Time: " . $e->getMessage() . "</br>";
        }
    }
}


// IF the in_status == 1 (Approved), then insert the new Vacation_Time record
if ($in_status == 1)
{
    // foreach vacation date that is requested, add the vacation time on that date
    foreach ($REQUESTED_DATES as $request_date)
    {
        $request_time = ($REQUESTED_HOURS[$count] == "") ? "00:00:00" : convertDecimalTimeToDBTime($REQUESTED_HOURS[$count]);

        if ($request_date != "")
        {
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Vacation_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :request_eid, :request_time, :request_date)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':request_eid' => $request_eid, ':request_time' => $request_time, ':request_date' => $request_date));
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Vacation_Time: " . $e->getMessage() . "</br>";
            }
        }

        // update counter
        $count++;
    }
}










// functions

function convertDecimalTimeToDBTime($dec)
{
    // start by converting to seconds
    $seconds = ($dec * 3600);
    // we're given hours, so let's get those the easy way
    $hours = floor($dec);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    return lz($hours).":".lz($minutes).":".lz($seconds);
}

// lz = leading zero
function lz($num)
{
    return (strlen($num) < 2) ? "0{$num}" : $num;
}





?>