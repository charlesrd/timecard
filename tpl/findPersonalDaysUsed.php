<?php


require_once("config.php");


// get the incoming variables
$in_selected_date = (isset($_POST['d'])) ? strtolower(preg_replace("/[^0-9-]/", "", $_POST['d'])) : "";

// setup variables
$personalTimeUsed = "";
$personalDays = "";
$personalDaysCount = 0;


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
	$sql = "SELECT *
	        FROM Personal_Time P
	        WHERE P.eid = $userID
	        AND P.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
}
else
{
	// get the number of personal days remaining for this user for this in_year
	$sql = "SELECT *
	        FROM Personal_Time P
	        WHERE P.eid = $userID
	        AND P.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
}


$query = mysql_query($sql);
while ($personalTime = mysql_fetch_object($query))
{
	if ($personalDaysCount == 0) {$personalDays .= date('M j', strtotime($personalTime->date));}
	else {$personalDays .= ', '.date('M j', strtotime($personalTime->date));}

	$personalDaysCount++;
}

$personalTimeUsed = ($personalDaysCount == 0) ? '0' : '<a href="#" onclick="showPersonalDays()">'.$personalDaysCount.'</a><div id="show-personal-days-used" class="display:none;">'.$personalDays.'</div>';

print $personalTimeUsed;

?>