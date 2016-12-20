<?php


require_once("config.php");


// get the incoming variables
$in_company_id = (isset($_POST['c'])) ? preg_replace("/[^0-9]/", "", $_POST['c']) : "";
$in_breaks = (isset($_POST['b'])) ? preg_replace("/[^0-9-]/", "", $_POST['b']) : "";
$in_breaks = ($_POST['b'] == "") ? 0 : $in_breaks;


// error checking
if ( ($adminID == "") || ($in_company_id == "") || ($in_breaks == "") ) {exit;}


// update the Company record
$sql = "UPDATE `timecard`.`Company` 
        SET `Company`.`breaks_per_day` = :breaks
        WHERE `Company`.`id` = :company_id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':breaks' => $in_breaks, ':company_id' => $in_company_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Company: " . $e->getMessage() . "</br>";
}



?>