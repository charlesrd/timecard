<?php


require_once("config.php");


// get the incoming variables
$in_title = $_POST['title'];
$in_message = $_POST['message'];

// error checking
if ( ($adminID == "") || (is_null($in_title)) || (is_null($in_message)) ) {exit;}


// update the Message Board record
$sql = "UPDATE `timecard`.`Company_Message` 
        SET `Company_Message`.`title` = :title,
        `Company_Message`.`message` = :message
        WHERE `Company_Message`.`cid` IN (
                                            SELECT `Company_Administrator`.`cid` 
                                            FROM `timecard`.`Company_Administrator`
                                            WHERE `Company_Administrator`.`aid` = :adminID
                                         )";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':title' => $in_title, ':message' => $in_message, ':adminID' => $adminID));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Company_Message: " . $e->getMessage() . "</br>";
}



?>