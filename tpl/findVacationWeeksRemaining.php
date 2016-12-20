<?php


require_once("config.php");


// get the incoming variables
$in_year = (isset($_POST['y'])) ? strtolower(preg_replace("/[^0-9]/", "", $_POST['y'])) : "";


// error checking
if ( ($in_year == "") ) {print "Invalid Year"; exit;}


// get the number of vacation days remaining for this user for this in_year
$query = mysql_query("SELECT 1-COUNT(*) AS count 
                      FROM Request_Vacation_Time V 
                      WHERE V.eid = $userID
                      AND V.start_date LIKE '$in_year%'");
$vacationTime = mysql_fetch_object($query);
$vacationTimeCount = $vacationTime->count;

print $vacationTimeCount;

?>