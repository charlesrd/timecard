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
	// get the number of vacation days remaining for this user for this in_year
	$sql = "SELECT E.vacation_days-COUNT(*) AS count
			FROM Vacation_Time V, Employee E 
	        WHERE V.eid = $userID
	        AND V.eid = E.id
	        AND V.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
}
else
{
	// get the number of vacation days remaining for this user for this in_year
	$sql = "SELECT E.vacation_days-COUNT(*) AS count
			FROM Vacation_Time V, Employee E 
	        WHERE V.eid = $userID
	        AND V.eid = E.id
	        AND V.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
}


$query = mysql_query($sql);
$vacationTime = mysql_fetch_object($query);
$vacationTimeCount = ($vacationTime->count < 0) ? 0 : $vacationTime->count;

print $vacationTimeCount;

?>