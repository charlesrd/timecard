<?php


require_once("config.php");


// get the incoming variables
$in_review_id = (isset($_POST['erid'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['erid'])) : "";
$in_review_note_id = (isset($_POST['ernid'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['ernid'])) : "";

// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_review_id)) || (is_null($in_review_note_id)) ) {exit;}


// delete the Employee_Review_Note record
$q = $db_pdo->prepare("DELETE FROM `timecard`.`Employee_Review_Note` WHERE `Employee_Review_Note`.`id` = :review_note_id");
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':review_note_id' => $in_review_note_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error deleting Employee_Review_Note: " . $e->getMessage() . "</br>";
}


// update the Employee_Review record
$sql = "UPDATE `timecard`.`Employee_Review` 
        SET `Employee_Review`.`updated_at` = NOW()
        WHERE `Employee_Review`.`id` = :review_id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':review_id' => $in_review_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee_Review: " . $e->getMessage() . "</br>";
}



?>