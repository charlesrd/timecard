<?php


require_once("config.php");


// get the incoming variables
$in_company_ip_id = (isset($_POST['id'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['id'])) : "";


// error checking
if ( (!isset($userID)) || (!isset($adminID)) || ($in_company_ip_id == "") ) {exit;}


// delete the Company_IP_Address record
$q = $db_pdo->prepare("DELETE FROM `timecard`.`Company_IP_Address` WHERE `Company_IP_Address`.`id` = :company_ip_id");
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':company_ip_id' => $in_company_ip_id));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error deleting Company_IP_Address: " . $e->getMessage() . "</br>";
}



?>