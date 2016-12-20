<?php


require_once("config.php");


// get the incoming variables
$in_message = $_POST['message'];


// error checking
if ($in_message == "") {exit;}


// insert the Employee Message record
$sql = "INSERT INTO `timecard`.`Employee_Message` 
        (
            `id`,
            `eid`,
            `message`,
            `created_at`
        )
        VALUES (NULL, :eid, :message, NOW())";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':eid' => $userID, ':message' => $in_message));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting Employee_Message: " . $e->getMessage() . "</br>";
}



?>