<?php


require_once("config.php");


// get the incoming variables
$in_id = (isset($_POST['id'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['id'])) : "";


// error checking
if ( (!isset($userID)) || ($in_id == "") ) {exit;}


// make sure this user owns this request personal time record
$sql = "SELECT RPT.eid
		FROM Request_Personal_Time RPT
		WHERE RPT.id = $in_id";

$query = mysql_query($sql);
$request = mysql_fetch_object($query);


if ($request->eid == $userID)
{
	// delete the Request_Personal_Time record
	$q = $db_pdo->prepare("DELETE FROM `timecard`.`Request_Personal_Time` WHERE `Request_Personal_Time`.`id` = :in_id");
	try {
	    $db_pdo->beginTransaction();
	    $q->execute(array(':in_id' => $in_id));
	    $db_pdo->commit();
	} catch(PDOExecption $e) {
	    $db_pdo->rollback();
	    print "Error deleting Request_Personal_Time: " . $e->getMessage() . "</br>";
	}
}



?>