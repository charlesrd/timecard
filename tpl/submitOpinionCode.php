<?php


require_once("config.php");


// get the incoming variables
$code = str_replace(' ', '', $_POST["code"]);


// error checking
if ($code == "") {print "0"; exit;}


// get the user's password and full name
$sql = "SELECT IFNULL(C.code,0) AS code
		FROM Code C
		WHERE C.code = '$code'
		AND NOT EXISTS
		(
			SELECT *
			FROM Opinion_Code OC
			WHERE OC.code = C.code
		)";

$query = mysql_query($sql);
$r = mysql_fetch_object($query);
$found_code = $r->code;


// check the credentials
if ( ($found_code != "") && ($found_code != 0) )
{
    print "1";
}
else
{
	print "0";
}

?>
