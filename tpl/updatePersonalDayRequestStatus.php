<?php


require_once("config.php");


// get the incoming variables
$in_review_id = (isset($_POST['review_id'])) ? str_replace(' ', '', $_POST['review_id']) : "";
$in_status = (isset($_POST['status'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['status'])) : "";


// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_review_id)) ) {exit;}


// get information about this personal day request
$sql = "SELECT *
		FROM Request_Personal_Time R
		WHERE R.id = $in_review_id";

$query = mysql_query($sql);
$request = mysql_fetch_object($query);
$request_eid = $request->eid;
$request_time = $request->time;
$request_date = $request->date;


// update the Request_Personal_Time record
$sql = "UPDATE `timecard`.`Request_Personal_Time` 
        SET `Request_Personal_Time`.`status` = :status
        WHERE `Request_Personal_Time`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':status' => $in_status, ':id' => $in_review_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee `Request_Personal_Time`.`status`: " . $e->getMessage() . "</br>";
}


// IF the in_status == 1 (Approved) OR in_status == 2 (Dissapproved), then remove the old Personal_Time records (if any exist)
if ( ($in_status == 1) || ($in_status == 2) )
{
	$q = $db_pdo->prepare("DELETE FROM `timecard`.`Personal_Time` WHERE `Personal_Time`.`eid` = :request_eid AND `Personal_Time`.`date` = :request_date");
	try {
	    $db_pdo->beginTransaction();
	    $q->execute(array(':request_eid' => $request_eid, ':request_date' => $request_date));
	    $db_pdo->commit();
	} catch(PDOExecption $e) {
	    $db_pdo->rollback();
	    print "Error deleting Personal_Time: " . $e->getMessage() . "</br>";
	}
}

// IF the in_status == 1 (Approved), then insert the new Personal_Time record
if ($in_status == 1)
{
    $q = $db_pdo->prepare("INSERT INTO `timecard`.`Personal_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :request_eid, :request_time, :request_date)");
    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':request_eid' => $request_eid, ':request_time' => $request_time, ':request_date' => $request_date));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error inserting new Personal_Time: " . $e->getMessage() . "</br>";
    }
}

?>