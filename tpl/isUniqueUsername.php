<?php


require_once("config.php");


// get the incoming variables
$uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_username = (isset($_POST['un'])) ? strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $_POST['un'])) : "";


// error checking (username must be 3 characters or longer)
if ( ($uid == "") || ($in_username == "") || (strlen($in_username) < 3) ) {print "0"; exit;}


// get the employee's original username
$sql = "SELECT E.username FROM Employee E WHERE E.id = $userID";
$query = mysql_query($sql);
$user = mysql_fetch_object($query);
$username = $user->username;

// IF the in_username == username, then the employee didn't change their username, return true
if ($in_username == $username)
{
	print "1";
	exit;
}
else // ELSE, check IF the in_username is already taken
{
	// get the usernameCount for this username from all other employees
	$sql = "SELECT COUNT(*) AS count FROM Employee E WHERE E.username = '$in_username' AND E.id != $userID";
	$query = mysql_query($sql);
	$user = mysql_fetch_object($query);
	$usernameCount = $user->count;

	// IF the usernameCount > 0, then username is taken, return false
	if ($usernameCount > 0)
	{
		print "0";
		exit;
	}
	else // ELSE, the username is not taken, return true
	{
		print "1";
		exit;
	}
}


?>