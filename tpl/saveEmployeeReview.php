<?php


require_once("config.php");


// get the incoming variables
$in_review_id = (isset($_POST['erid'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['erid'])) : "";
$in_review_date = (isset($_POST['erd'])) ? $_POST['erd']." 12:00:00" : "";
$in_reviewed = (isset($_POST['err'])) ? $_POST['err'] : "";
$in_review_note = (isset($_POST['ern'])) ? $_POST['ern'] : "";


// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_review_id)) || ($in_reviewed == "") || (is_null($in_review_date)) ) {exit;}


// update the Employee_Review record
$sql = "UPDATE `timecard`.`Employee_Review` 
        SET `Employee_Review`.`date` = :review_date,
        `Employee_Review`.`reviewed` = :reviewed,
        `Employee_Review`.`updated_at` = NOW()
        WHERE `Employee_Review`.`id` = :review_id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':review_date' => $in_review_date, ':reviewed' => $in_reviewed, ':review_id' => $in_review_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee_Review: " . $e->getMessage() . "</br>";
}


// IF there is a new note, then add a new Employee_Review_Note
if ($in_review_note != "")
{
	$q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Review_Note` (`id`, `erid`, `aid`, `note`) VALUES (NULL, :review_id, :admin_id, :review_note)");
	try {
	    $db_pdo->beginTransaction();
	    $q->execute(array(':review_id' => $in_review_id, ':admin_id' => $adminID, ':review_note' => $in_review_note));
	    $db_pdo->commit();
	} catch(PDOExecption $e) {
	    $db_pdo->rollback();
	    print "Error inserting new Employee_Review_Note: " . $e->getMessage() . "</br>";
	}
}


?>