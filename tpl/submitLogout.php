<?php

session_start();

// unset the session variables
$_SESSION["uid"] = "";
$_SESSION["aid"] = "";
unset($_SESSION["uid"]);
unset($_SESSION["aid"]);

include("login.php");

?>