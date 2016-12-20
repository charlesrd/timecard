<?php

require_once(__DIR__."/config.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">

    <link rel="icon" type="image/png" href="../img/clock.png" />
    <title>AMS Timecard | AmericaSmiles &amp; United Dental Resources Hour Time Tracker</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AmericaSmiles Dental Technologies, Inc &amp; United Dental Resources - Timecard Program.  Easily login to clock in, clock out, view timecard history, and more.">
    <meta name="author" content="AmericaSmiles &amp; United Dental Resources">

    <script type="text/javascript">
    if (!window.console) window.console = {};
    if (!window.console.log) window.console.log = function () { };
    </script>

    <!-- CSS stylesheets -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="http://amstimecard.com/css/timecard.css?ver=1465401825">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">


    <!-- Javascript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="http://amstimecard.com/js/timecard.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="http://amstimecard.com/js/jquery-ui.multidatespicker.js"></script>

    <script src="http://amstimecard.com/assets/jquery-validation/dist/jquery.validate.min.js?ver=<?=time();?>"></script>
    <script src="http://amstimecard.com/assets/dropzone/downloads/dropzone.min.js?ver=<?=time();?>"></script>

</head>

<body>

    <div class="container-fluid" id="main">
        <div class="container-main">

            <?php

            if ($userID == "") {include($INC.'login.php');}
            elseif ($adminID != "") {include($INC.'admin_home.php');}
            else {include($INC.'user_home.php');}
            
            ?>
        
        </div>

        <div class="clearfix"></div>
    </div>


</body>
</html>