<?php


require_once("config.php");


// get the incoming variables
$in_ertf = (isset($_POST['ertf'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['ertf'])) : "";

// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_ertf)) ) {exit;}


// update the Employee Review Time Frame session value
$_SESSION['ertf'] = $in_ertf;



?>