<?php


require_once("config.php");


// setup variables
$today = date('Y-m-d');
$showClockIn = true;
$hideClockTime = 0;
$weeksRemaining = true;
$pageTitle = "";
$start_date = "";
$end_date = "";
$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];
$COMPANY_IP_ADDRESS = array();


// get the company ip address
$sql = "SELECT INET_NTOA(C.ip_address) AS ip_address
        FROM Company_IP_Address C, Employee E
        WHERE C.cid = E.companyID";

$query = mysql_query($sql);
while($company = mysql_fetch_object($query))
{
    $COMPANY_IP_ADDRESS[] = $company->ip_address;
}


// get the user's information
$sql = "SELECT * FROM Employee E WHERE E.id = $userID";
$query = mysql_query($sql);
$user = mysql_fetch_object($query);
$username = $user->username;
$start_time = $user->start_time;


// get the user's Next Employee Review Scheduled Date
$sql = "SELECT 
        ER.id,
        ER.date,
        ER.created_at,
        ER.updated_at
        FROM Employee E, Employee_Review ER 
        WHERE E.id = $userID
        AND E.id = ER.eid
        ORDER BY ER.date DESC
        LIMIT 1";

$query = mysql_query($sql);
$review = mysql_fetch_object($query);
$reviewID = $review->id;


// get the user's Next Employee Review Notes
$sql = "SELECT ERN.note,
        (
            SELECT CONCAT(E2.first_name,' ',E2.last_name) AS admin_name
            FROM Administrator A, Employee E2
            WHERE ERN.aid = A.id
            AND A.eid = E2.id
        ) AS admin_name
        FROM Employee E, Employee_Review ER, Employee_Review_Note ERN 
        WHERE E.id = $userID
        AND ER.id = $reviewID
        AND E.id = ER.eid
        AND ER.id = ERN.erid";
        
$reviewNotesQuery = mysql_query($sql);


?>
<nav id="top-nav" class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <h1 id="header-title"><a href="http://americasmiles.com/timecard" class="navbar-brand"><span class="glyphicon glyphicon-time"></span> AMS Timecard</a></h1>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right" id="top-links">

                <?php if ( (!in_array($IP_ADDRESS, $COMPANY_IP_ADDRESS)) && ($user->enforce_ip_address == 1) ): ?>

                <li><a href="#" class="bg-danger remote-access-link"><span class="fa fa-road"></span> Remote Access Mode</a></li>

                <?php endif; ?>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-envelope"></span> Message <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="setupModal('employee-message')"><span class="fa fa-plus fa-lg"></span> New Message</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-lock"></span> Security <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#"><span class="fa fa-key"></span> Employee Key: <h4 class="has-key"><?php $hasKey = ($user->has_key == 1) ? '<div class="text-success">YES <span class="fa fa-smile-o"></span></div>' : '<div class="text-danger">NO <span class="fa fa-ban"></span></div>'; print $hasKey; ?></h4></a></li>
                        <li class="<?php $a = ($user->alarm_code == "") ? "hide" : ""; print $a; ?> settings"><a href="#"><span class="fa fa-volume-up"></span> Employee Alarm Code: <h4 class="alarm-code"><?php print $user->alarm_code; ?></h4></a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-eye"></span> Review <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#"><span class="fa fa-calendar"></span> Next Review Date: <h4 class="text-center"><strong><?php print date("D, M. d Y", strtotime($review->date)); ?></strong></h4></a></li>
                        <li class="settings">
                            <a href="#">
                                <span class="fa fa-edit"></span> Review Notes:
                            </a>
                        </li>
                                <?php

                                $reviewNotesCount = 0;

                                // print out the review notes
                                while ($reviewNotes = mysql_fetch_object($reviewNotesQuery))
                                {
                                    print '<li class="settings user-review-note" id="user-review-note-'.$reviewID.'"><div class="alert alert-info"><i>'.$reviewNotes->note.'</i><h6 class="text-right"> &ndash; '.$reviewNotes->admin_name.'</h6></div></li>';
                                    $reviewNotesCount++;
                                }

                                if ($reviewNotesCount == 0)
                                {
                                    print '<li class="settings"><a href="#"><h5>None</h5></a></li>';
                                }

                                ?>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs"></span> Settings <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#">Start Time: <h4 class="alarm-code"><?php print date("g:i a", strtotime($user->start_time)); ?></h4></a></li>
                        <li class="settings"><a href="#">Anniversary Date: <h5><?php print ucwords(date("M jS, Y", strtotime($user->start_date))); ?></h5></a></li>
                        <li class="<?php $a = ($user->personal_days == 0) ? "hide" : ""; print $a; ?> settings"><a href="#" onclick="setupModal('request-personal-time')"><span class="fa fa-child fa-lg"></span> Request Personal Time</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('request-vacation-time')"><span class="fa fa-sun-o fa-lg"></span> Request Vacation Time</a></li>
                        <li class="divider"></li>
                        <li class="settings text-center">
                            <a href="#">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <span id="user-icon" class="glyphicon glyphicon-user"></span>
                                        </div>
                                        <div class="col-xs-8">
                                            <h5 class="text-center">Logged in as</h5>
                                            <h4 class="text-center"><?php print $username; ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li class="settings"><a href="#" onclick="logout()"><span class="fa fa-sign-out"></span> Log out</a></li>
                    </ul>
                </li>

            </ul>
        </div><!-- /.navbar-collapse -->

    </div><!-- /.container -->
</nav>

<div id="user-container" class="container">

    <div class="row">
        <div class="col-md-12">
            <div id="user-table-row" class="row">
                <div id="user-table-col" class="col-md-12">
                    <div id="user-table-div" class="table-responsive">
                        <?php include("user-table.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>


<footer>
    <div class="navbar navbar-inverse navbar-fixed-bottom">
        <div class="container">
            <div class="navbar-header">
                <div class="container">
                    <div id="user-action-row" class="row">
                        <?php if ( (in_array($IP_ADDRESS, $COMPANY_IP_ADDRESS)) || ($user->enforce_ip_address == 0) ): ?>
                        <div class="col-xs-12 col-sm-3">
                            <div id="clock-time" <?php if ($hideClockTime == 1) {print 'class="hide" style="display: none;"';} ?>>
                                <a id="clock-in" href="#" class="<?php if (!$showClockIn) {print "hide ";} ?>thumbnail text-center" title="Clock in to start recording your time">
                                    <span class="fa fa-clock-o"></span>
                                    <h3 class="text-center"><strong>Clock in</strong></h3>
                                </a>
                                <a id="clock-out" href="#" class="<?php if ($showClockIn) {print "hide ";} ?>thumbnail text-center" onclick="setupModal('clock-out-reason')" data-toggle="modal" data-target="#clock-out-modal" title="Clock out to stop recording your time">
                                    <span class="fa fa-clock-o"></span>
                                    <h3 class="text-center"><strong>Clock out</strong></h3>
                                </a>

                                <!-- Internal Javascript -->
                                <script type="text/javascript">
                                $(document).ready(function(){

                                     $("#clock-in").one('click', function (event) {  
                                           event.preventDefault();
                                           clockIn();
                                     });

                                });
                                </script>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <a href="#" id="forgot-thumbnail" class="thumbnail text-center" onclick="showForgotOverlay()" title="Click here if you forgot to clock in or clock out">
                                <span class="fa fa-frown-o"></span>
                                <h3 class="text-center"><strong>Oops I forgot</strong></h3>
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="col-xs-12 col-sm-3">
                            <a href="#" class="thumbnail text-center" onclick="setupModal('view-history-week')" data-toggle="modal" data-target="#view-history-modal" title="View your history of your weekly timecards">
                                <span class="fa fa-bar-chart-o"></span>
                                <h3 class="text-center"><strong>View History</strong></h3>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                            <a href="#" class="thumbnail text-center" onclick="logout()" title="Logout of your timecard">
                                <span class="fa fa-sign-out"></span>
                                <h3 class="text-center"><strong>Logout</strong></h3>
                            </a>
                        </div>
                    </div>
                    <?php if ( (in_array($IP_ADDRESS, $COMPANY_IP_ADDRESS)) || ($user->enforce_ip_address == 0) ): ?>
                    <div id="forgot-row" class="row">
                        <div class="col-xs-8">
                            <span id="forgot-message">Please select the time(s) above that you forgot to clock in or clock out:</span>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-lg btn-default" onclick="hideForgotOverlay()">Cancel</button>
                            <button type="button" onclick="saveForgot()" id="forgot-done" class="btn btn-lg btn-danger btn-done"><span class="fa fa-envelope-o"></span> Send to Management</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Modal Dialogs -->

<!-- Clock Out Modal -->
<div class="modal fade" id="clock-out-modal" tabindex="-1" role="dialog" aria-labelledby="clock-out-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="clock-out-modal-title"><strong>Clock out?</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="clock-out-break" name="clock-out-reason" class="input-lg" type="radio" value="0" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="clock-out-break-label" class="modal-label" for="clock-out-break"><h4><strong>I&apos;m going on break</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="clock-out-done" name="clock-out-reason" class="input-lg" type="radio" value="1" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="clock-out-done-label" class="bg-danger modal-label" for="clock-out-done"><h4><strong>I&apos;m done for the day</strong></h4></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="clockOut()" id="clock-out-done" class="btn btn-lg btn-primary btn-done"><span class="glyphicon glyphicon-time"></span> Clock out</button>
            </div>
        </div>
    </div>
</div>

<!-- Warning Messages Modal -->
<div class="modal fade" id="warning-messages-modal" tabindex="-1" role="dialog" aria-labelledby="warning-messages-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="warning-messages-modal-title"><span class="fa fa-warning"></span> <strong>Attention:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="warning-messages-row" class="row">
                        <div class="col-xs-12">
                            <div id="missing-hours-alert" class="alert alert-warning warning-message">
                                <span id="forgot-text" class="warning-alert-text">You have missing clock in or clock out times. Please see management to correct these errors.</span>
                            </div>
                            <div id="overtime-hours-alert" class="alert alert-warning warning-message">
                                <span id="overtime-text" class="warning-alert-text">You have over 32 hours so far this week. Please see management to find out your starting time for tomorrow.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" id="warning-messages-done" class="btn btn-lg btn-danger btn-done"><span class="glyphicon glyphicon-thumbs-up"></span> OK</button>
            </div>
        </div>
    </div>
</div>

<!-- View History Modal -->
<div id="view-history-modal-container"><?php include("view-history-modal.php"); ?></div>

<!-- Request Personal Time Modal -->
<div id="request-personal-time-modal-container"><?php include("request-personal-time-modal.php"); ?></div>

<!-- Request Vacation Time Modal -->
<div id="request-vacation-time-modal-container"><?php include("request-vacation-time-modal.php"); ?></div>

<!-- Employee Message Modal -->
<div id="employee-message-modal-container"><?php include("employee-message-modal.php"); ?></div>


<!-- Hidden Inputs -->
<input type="hidden" id="uid" name="uid" value="<?php print $userID; ?>">
<input type="hidden" id="uname" name="uname" value="<?php print $uname; ?>">
<input type="hidden" id="pageTitle" name="pageTitle" value="<?php print $pageTitle; ?>">

<!-- Internal Javascript -->
<script type="text/javascript">
$(document).ready(function(){

    function resize()
    {
        var topNav = 40;
        var bottomNav = 110;
        var smView = 768;
        var originalHeight = $("user-container").height();
        var browserHeight = $(window).height();
        var browserWidth = $(window).width();

        if (browserWidth <= smView) {bottomNav = 70;}

        var newHeight = browserHeight-topNav-bottomNav;

        <?php if ( (!in_array($IP_ADDRESS, $COMPANY_IP_ADDRESS)) && ($user->enforce_ip_address == 1) ): ?>
        var topNav = 70;
        var text = "Only timecard reports can be seen while Remote Access Mode is on.";
        var remoteMessage = $("#remote-mode-top-message");
        if (browserWidth > smView) {remoteMessage.html(text);}
        else {remoteMessage.html('');}
        <?php endif; ?>

        $("#user-container").css("height", newHeight);
        $("#user-container").css("width", browserWidth);
        $("#user-table-div").css("height", newHeight-50);
        $("#user-table-div").css("width", browserWidth-30);
    }

    resize();
    
    $(window).resize(function(){
        resize();
    });

    $('input:radio').change(function(){
        $('.btn-done').removeAttr('disabled');
    });

    // IF the user has any warning messages, display the warning message modal
    if ( ($('#missingHours').val() === "1") || ($('#overtimeHours').val() === "1") )
    {
        // show the warning-message modal
        setTimeout(function ()
        {
            // hide all the warning messages
            $('.warning-message').hide();

            // setup variables
            var d = new Date();
            var day = d.getDay();

            // only show the warning messages that apply
            if ($('#missingHours').val() === "1") {$('#missing-hours-alert').show();}


            <?php if ($user->overtime_alerts == 1): ?>

            if ( ($('#overtimeHours').val() === "1") && (day <= 5) ) {$('#overtime-hours-alert').show();}

            $("#warning-messages-modal").modal(
            {
                backdrop: 'static',
                show: true
            });

            <?php endif; ?>


        }, 1000);
    }

    var startDate;
    var endDate;

    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }

    $('.week-picker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", startDate));
            $('.btn-done').removeAttr('disabled');

            selectCurrentWeek();
        },
        beforeShowDay: function(date) {
            var cssClass = '';
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
    });

    $('.week-picker .ui-datepicker-calendar tr').on('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.week-picker .ui-datepicker-calendar tr').on('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });


});
</script>