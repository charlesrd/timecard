<?php

// setup url variables
$url = $_SERVER["SERVER_NAME"];
$page = $_SERVER["REQUEST_URI"]; 

// start the session
session_start();

// include files
include('timecard_functions.php');

// setup variables
$INC = "tpl/";
$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

// database variables
$DB_HOST = "localhost";
$DB_USER = "";
$DB_PASS = "";
$DB_NAME = "timecard";

// get the session variables
$globalDate = ($_SESSION['date'] == "") ? date('Y-m-d') : preg_replace("/[^-0-9]/", "", $_SESSION['date']);
$userID = preg_replace("/[^-0-9]/", "", $_SESSION['uid']);
$adminID = preg_replace("/[^-0-9]/", "", $_SESSION['aid']);
$uname = $_SESSION['uname'];

// connect to the Database
$db = mysql_connect($DB_HOST, $DB_USER, $DB_PASS) or die ("Could not connect to database");
mysql_select_db($DB_NAME,$db) or die ("Could not select database");

// connect to the database with PDO
$db_pdo = new PDO('mysql:host='.$DB_HOST.';dbname='.$DB_NAME, $DB_USER, $DB_PASS);
$db_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


