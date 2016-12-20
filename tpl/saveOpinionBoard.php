<?php


require_once("config.php");


// get the incoming variables
$in_code = str_replace(' ', '', $_POST["code"]);
$in_self_opinion = $_POST['self_opinion'];
$in_company_opinion = $_POST['company_opinion'];


// insert the Opinion Board record
$sql = "INSERT INTO `timecard`.`Opinion` 
        (
            `id`,
            `self_opinion`,
            `company_opinion`,
            `created_at`
        )
        VALUES (NULL, :self_opinion, :company_opinion, NOW())";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':self_opinion' => $in_self_opinion, ':company_opinion' => $in_company_opinion));
    $newOpinionID = $db_pdo->lastInsertId();
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting Opinion: " . $e->getMessage() . "</br>";
}


// insert the Opinion_Code record
$sql = "INSERT INTO `timecard`.`Opinion_Code` (`id`, `oid`, `code`) VALUES (NULL, :opinion_id, :code)";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':code' => $in_code, ':opinion_id' => $newOpinionID));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting Opinion_Code: " . $e->getMessage() . "</br>";
}



?>