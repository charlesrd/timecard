<?php


require_once("config.php");


// get the incoming variables
$in_company_id = (isset($_POST['c'])) ? preg_replace("/[^0-9]/", "", $_POST['c']) : "";
$in_ip_address = (isset($_POST['ip'])) ? $_POST['ip'] : "";


// error checking
if ( ($adminID == "") || (is_null($in_ip_address)) ) {exit;}


// add the new Company_IP_Address record
$sql = "INSERT INTO `timecard`.`Company_IP_Address` (cid, ip_address) VALUES (:company_id, INET_ATON('".$in_ip_address."'))";
$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':company_id' => $in_company_id));
    $newCompanyIPID = $db_pdo->lastInsertId();
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting Company_IP_Address: " . $e->getMessage() . "</br>";
}


print $newCompanyIPID;


?>