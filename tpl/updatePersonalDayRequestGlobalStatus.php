<?php


require_once("config.php");


// get the incoming variables
$in_pdrs = (isset($_POST['pdrs'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['pdrs'])) : "";

// setup variables
$today = date('Y-m-d');

// error checking
if ( (!isset($userID)) || (!isset($adminID)) || (is_null($in_pdrs)) ) {exit;}


// update the Personal Day Review Status session value
$_SESSION['pdrs'] = $in_pdrs;



?>