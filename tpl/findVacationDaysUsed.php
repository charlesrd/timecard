<?php


require_once("config.php");


// get the incoming variables
$in_selected_date = (isset($_POST['d'])) ? strtolower(preg_replace("/[^0-9-]/", "", $_POST['d'])) : "";

// setup variables
$vacationTimeUsed = "";
$vacationDays = "";
$vacationDaysCount = 0;


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


// get the number of vacation days remaining for this user for this in_selected_date
if ($selected_date < $selected_year_anniversary_date)
{
	$sql = "SELECT *
	        FROM Vacation_Time V
	        WHERE V.eid = $userID
	        AND V.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
}
else
{
	$sql = "SELECT *
	        FROM Vacation_Time V
	        WHERE V.eid = $userID
	        AND V.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
}


$query = mysql_query($sql);
while ($vacationTime = mysql_fetch_object($query))
{
	if ($vacationDaysCount == 0) {$vacationDays .= date('M j', strtotime($vacationTime->date));}
	else {$vacationDays .= ', '.date('M j', strtotime($vacationTime->date));}

	$vacationDaysCount++;
}

$vacationTimeUsed = ($vacationDaysCount == 0) ? '0' : '<a href="#" onclick="showVacationDays()">'.$vacationDaysCount.'</a><div id="show-vacation-days-used" class="display:none;">'.$vacationDays.'</div>';

print $vacationTimeUsed;

?>