<?php


require_once("config.php");


// get the incoming variables
$in_selected_date = (isset($_POST['d'])) ? strtolower(preg_replace("/[^0-9-]/", "", $_POST['d'])) : "";


// error checking
if ( ($in_selected_date == "") ) {print "Invalid Year"; exit;}


// setup variables
$selected_date = $in_selected_date." 00:00:00";
$selected_year = date("Y",strtotime($in_selected_date));


// get the anniversary date for the user
$sql = "SELECT *
        FROM Employee E
        WHERE E.id = $userID";

$query = mysql_query($sql);
$r = mysql_fetch_object($query);
$selected_year_anniversary_date = date("Y", strtotime($in_selected_date)).date("-m-d",strtotime($r->start_date))." 00:00:00";



if ($selected_date < $selected_year_anniversary_date)
{
	// get the number of personal days remaining for this user for this in_year
	$sql = "SELECT E.personal_days-COUNT(*) AS count 
	        FROM Personal_Time P, Employee E 
	        WHERE P.eid = $userID
	        AND P.eid = E.id
	        AND P.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
}
else
{
	// get the number of personal days remaining for this user for this in_year
	$sql = "SELECT E.personal_days-COUNT(*) AS count 
	        FROM Personal_Time P, Employee E 
	        WHERE P.eid = $userID
	        AND P.eid = E.id
	        AND P.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
}


$query = mysql_query($sql);
$personalTime = mysql_fetch_object($query);
$personalTimeCount = $personalTime->count;

print $personalTimeCount;

?>