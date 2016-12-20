<?php


require_once("config.php");


// setup variables
$clock_out = date('H:i:s');
$today = date('Y-m-d');
$timeCount = 0;

// get the incoming variables
$uid = str_replace(' ', '', $_POST["uid"]);
$reason = preg_replace("/[^-0-9]/", "", $_POST['r']);

// error check the userID
if ($uid != $userID) {exit;}


// count the number of entries with a blank clock_out in the Employee_Hours table for this userID today
$sql = "SELECT COUNT(*) AS count
		FROM Employee_Hours H 
		WHERE H.eid = $userID
		AND H.clock_out IS NULL
		AND H.date = '$today'";

$query = mysql_query($sql);
$time = mysql_fetch_object($query);
$timeCount = $time->count;

// IF timeCount is > 0, then UPDATE the existing record
if ($timeCount > 0)
{
	// UPDATE the clock_out time
	$q = $db_pdo->prepare("UPDATE `timecard`.`Employee_Hours` SET `clock_out` = :clock_out WHERE `Employee_Hours`.`clock_out` IS NULL AND `Employee_Hours`.`eid` = :eid AND `Employee_Hours`.`date` = :today");
	try {
	    $db_pdo->beginTransaction();
	    $q->execute(array(':clock_out' => $clock_out, ':eid' => $userID, ':today' => $today));
	    $db_pdo->commit();
	} catch(PDOExecption $e) {
	    $db_pdo->rollback();
	    print "Error updating Employee_Hours: " . $e->getMessage() . "</br>";
	}

	// IF reason = 0, then INSERT the user into the On_Break table
	if ($reason == 0)
	{
		$q = $db_pdo->prepare("INSERT INTO `timecard`.`On_Break` (`id`, `eid`, `start_time`, `end_time`, `date`) VALUES (NULL, :eid, :start_time, NULL, :today)");
		try {
		    $db_pdo->beginTransaction();
		    $q->execute(array(':eid' => $userID, ':start_time' => $clock_out, ':today' => $today));
		    $db_pdo->commit();
		} catch(PDOExecption $e) {
		    $db_pdo->rollback();
		    print "Error inserting new On_Break: " . $e->getMessage() . "</br>";
		}
	}
	elseif ($reason == 1) // IF reason = 1, then INSERT the user into the Is_Done table
	{
		$q = $db_pdo->prepare("INSERT INTO `timecard`.`Is_Done` (`id`, `eid`, `end_time`, `date`) VALUES (NULL, :eid, :end_time, :today)");
		try {
		    $db_pdo->beginTransaction();
		    $q->execute(array(':eid' => $userID, ':end_time' => $clock_out, ':today' => $today));
		    $db_pdo->commit();
		} catch(PDOExecption $e) {
		    $db_pdo->rollback();
		    print "Error inserting new Is_Done: " . $e->getMessage() . "</br>";
		}
	}
	else {exit;}
}
else // ELSE, user is not clocked in yet, so exit
{
	// exit;
}


// include the user table
include("user-table.php");

?>
