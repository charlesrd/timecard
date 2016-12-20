<?php


require_once("config.php");


// setup variables
$clock_in = date('H:i:s');
$today = date('Y-m-d');
$timeCount = 0;

// get the incoming variables
$uid = str_replace(' ', '', $_POST["uid"]);

// error check the userID
if ($uid != $userID) {exit;}


// count the number of Employee_Hours records for this user today
$sql = "SELECT COUNT(*) AS count
		FROM Employee_Hours EH
		WHERE EH.eid = $userID
		AND EH.date = '$today'";

$query = mysql_query($sql);
$records = mysql_fetch_object($query);
$recordsCount = $records->count;


// get the number of Company breaks for this user
$sql = "SELECT C.breaks_per_day
		FROM Company C, Employee E
		WHERE E.id = $userID
		AND C.id = E.companyID";

$query = mysql_query($sql);
$company = mysql_fetch_object($query);
$breaks_per_day = $company->breaks_per_day;


if ( ($recordsCount < ($breaks_per_day+1)) || ($breaks_per_day < 0) )
{
	// INSERT the new clock_in time
	$q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Hours` (`id`, `eid`, `clock_in`, `clock_out`, `date`) VALUES (NULL, :userID, :clock_in, NULL, :today)");
	try {
	    $db_pdo->beginTransaction();
	    $q->execute(array(':userID' => $userID, ':clock_in' => $clock_in, ':today' => $today));
	    $labID = $db_pdo->lastInsertId();
	    $db_pdo->commit();
	} catch(PDOExecption $e) {
	    $db_pdo->rollback();
	    print "Error inserting new lab: " . $e->getMessage() . "</br>";
	}

	// count the number of entries with a blank end_time in the On_Break table for this userID today
	$sql = "SELECT COUNT(*) AS count
			FROM On_Break B 
			WHERE B.eid = $userID
			AND B.end_time IS NULL
			AND B.date = '$today'";

	$query = mysql_query($sql);
	$time = mysql_fetch_object($query);
	$timeCount = $time->count;

	// IF timeCount is > 0, then UPDATE the existing record
	if ($timeCount > 0)
	{
		// UPDATE the end_time for the user's break
		$q = $db_pdo->prepare("UPDATE `timecard`.`On_Break` SET `end_time` = :clock_in WHERE `On_Break`.`end_time` IS NULL AND `On_Break`.`eid` = :eid AND `On_Break`.`date` = :today");
		try {
		    $db_pdo->beginTransaction();
		    $q->execute(array(':clock_in' => $clock_in, ':eid' => $userID, ':today' => $today));
		    $db_pdo->commit();
		} catch(PDOExecption $e) {
		    $db_pdo->rollback();
		    print "Error updating On_Break: " . $e->getMessage() . "</br>";
		}
	}
}


// include the user table
include("user-table.php");


?>
