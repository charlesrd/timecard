<?php

require_once("config.php");

// get the incoming variables
$username = str_replace(' ', '', $_POST["u"]);
$password = str_replace(' ', '', $_POST["p"]);

// error checking
if ( ($username == "") || ($password == "") ) {print "0"; exit;}

// setup variables
$USERNAME = "";
$PASSWORD = "";
$loggedIn = false;


// get the user's password and full name
$query = mysql_query("SELECT id,password, CONCAT(first_name, ' ', last_name) AS name FROM Employee WHERE `username` = '$username'");
$user = mysql_fetch_object($query);
$PASSWORD = $user->password;
$uid = $user->id;
$uName = $user->name;

// error checking
if ( ($PASSWORD == "") || ($uid == "") ) {print "0"; exit;}

// check the credentials
if ($password == $PASSWORD)
{
    $userID = $uid;
    $_SESSION["uid"] = $userID;
    $_SESSION["uname"] = $uName;
    $loggedIn = true;
}

if ($loggedIn)
{
	// check if this user is an Administrator
	$query = mysql_query("SELECT id FROM Administrator WHERE `eid` = $userID");
	$admin = mysql_fetch_object($query);
	$adminID = $admin->id;

	// IF the user is an Administrator, load the Admin Homepage
	if (!is_null($adminID))
	{
		$_SESSION["aid"] = $adminID;
		include("admin_home.php");
	}
	else // ELSE, load the User Homepage
	{
		include("user_home.php");
	}
}
else
{
    print "0";
}

?>
