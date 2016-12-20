<?php


require_once("config.php");


// get the incoming variables
$in_vtrs = (isset($_POST['vtrs'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['vtrs'])) : "";

// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_vtrs)) ) {exit;}


// update the Request Vacation Time Status session value
$_SESSION['vtrs'] = $in_vtrs;



?>