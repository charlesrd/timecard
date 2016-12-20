<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_has_key = (isset($_POST['hk'])) ? intval(preg_replace("/[^0-1]/", "", $_POST['hk'])) : "";


// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_uid)) || (is_null($in_has_key)) ) {exit;}


// update the Employee record
$sql = "UPDATE `timecard`.`Employee` 
        SET `Employee`.`has_key` = :has_key
        WHERE `Employee`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':has_key' => $in_has_key, ':id' => $in_uid));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee `has_key`: " . $e->getMessage() . "</br>";
}


?>