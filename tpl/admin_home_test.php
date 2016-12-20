<?php


require_once("config.php");


// setup variables
$today = date('Y-m-d');
$showClockIn = true;
$weeksRemaining = true;
$pageTitle = "";
$start_date = "";
$end_date = "";
$cuid = "";
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;
global $VIEW_REPORT;

// get the user's username
$sql = "SELECT * FROM Employee E WHERE E.id = $userID";
$query = mysql_query($sql);
$user = mysql_fetch_object($query);

$username = $user->username;

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
                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs"></span> Settings <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="setupModal('add-user')"><span class="fa fa-plus"></span><span class="glyphicon glyphicon-user"></span> Add User</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('account-settings')"><span class="fa fa-wrench"></span> Account Settings</a></li>
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
                        <?php 
                            // include("user-table.php"); 
                            $VIEW_REPORT = 4;
                            include("view-report.php");
                        ?>
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
                    <div class="row">
                        <div class="col-xs-12 col-sm-2">
                            <a href="#" class="thumbnail text-center" onclick="setupModal('change-user')" title="Change the user to view their timecards">
                                <span class="glyphicon glyphicon-user"></span>
                                <h3 class="text-center"><strong>Change User</strong></h3>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <a href="#" class="thumbnail text-center" onclick="setupModal('view-report-type')" data-toggle="modal" data-target="#view-report-modal" title="View the current employee timecard reports">
                                <span class="glyphicon glyphicon-list-alt"></span>
                                <h3 class="text-center"><strong>View Reports</strong></h3>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <div id="add-vacation">
                                <div id="vacation-time-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('vacation-time-user')" data-toggle="modal" data-target="#vacation-time-modal" title="Add vacation time for a chosen employee">
                                        <span class="fa fa-sun-o"></span>
                                        <h3 id="vacation-time-button" class="text-center"><strong>Add Vacation</strong></h3>
                                    </a>
                                </div>
                            </div>
                            <div id="edit-vacation">
                                <div id="edit-vacation-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('edit-vacation')" data-toggle="modal" data-target="#edit-vacation-modal" title="Edit the chosen vacation time">
                                        <span class="fa fa-sun-o"></span>
                                        <h3 id="edit-vacation-button" class="text-center"><strong>Edit Vacation</strong></h3>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <div id="clock-time">
                                <a id="clock-in" href="#" class="<?php if (!$showClockIn) {print "hide ";} ?>thumbnail text-center" onclick="clockIn()" title="Clock in to start recording your time">
                                    <span class="fa fa-clock-o"></span>
                                    <h3 class="text-center"><strong>Clock in</strong></h3>
                                </a>
                                <a id="clock-out" href="#" class="<?php if ($showClockIn) {print "hide ";} ?>thumbnail text-center" onclick="setupModal('clock-out-reason')" data-toggle="modal" data-target="#clock-out-modal" title="Clock out to stop recording your time">
                                    <span class="fa fa-clock-o"></span>
                                    <h3 class="text-center"><strong>Clock out</strong></h3>
                                </a>
                            </div>
                            <div id="edit-time">
                                <a href="#" class="thumbnail text-center" onclick="setupModal('edit-time')" data-toggle="modal" data-target="#edit-time-modal" title="Click here to edit the selected time">
                                    <span class="fa fa-clock-o"></span>
                                    <h3 class="text-center"><strong>Edit Time</strong></h3>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <div id="view-history-thumbnail-container">
                                <a href="#" class="thumbnail text-center" onclick="setupModal('view-history-week')" data-toggle="modal" data-target="#view-history-modal" title="View your history of your weekly timecards">
                                    <span class="fa fa-bar-chart-o"></span>
                                    <h3 class="text-center"><strong>View History</strong></h3>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <a href="#" class="thumbnail text-center" onclick="logout()" title="Logout of your timecard">
                                <span class="fa fa-sign-out"></span>
                                <h3 class="text-center"><strong>Logout</strong></h3>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Modal Dialogs -->

<!-- Edit Time Modal -->
<div class="modal fade" id="edit-time-modal" tabindex="-1" role="dialog" aria-labelledby="edit-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="edit-time-modal-title"><strong>Select a Time:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="edit-time-row" class="row">
                        <div class="col-xs-4 col-sm-4 col-label">
                            <label class="modal-label"><h4><strong>Hour:</strong></h4></label>
                            <select id="edit-time-hour" name="edit-time-hour" class="form-control input-lg select-time">
                                <?php
                                for ($i=1; $i<=12; $i++)
                                {
                                    print '<option value="'.$i.'">'.$i.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-label">
                            <label class="modal-label"><h4><strong>Min:</strong></h4></label>
                            <select id="edit-time-minute" name="edit-time-minute" class="form-control input-lg select-time">
                                <?php
                                for ($i=0; $i<=59; $i++)
                                {
                                    if ($i < 10) {print '<option value="'.$i.'">0'.$i.'</option>';}
                                    else {print '<option value="'.$i.'">'.$i.'</option>';}
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-label">
                            <label class="modal-label"><h4><strong>Daytime:</strong></h4></label>
                            <select id="edit-time-daytime" name="edit-time-daytime" class="form-control input-lg select-time">
                                <option value="0">AM</option>
                                <option value="1">PM</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deleteTime()" id="edit-time-delete" class="btn btn-lg btn-danger"><span class="fa fa-trash-o"></span> Delete</button>
                <button type="button" onclick="saveTime()" id="edit-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-floppy-o"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Vacation Modal -->
<div class="modal fade" id="edit-vacation-modal" tabindex="-1" role="dialog" aria-labelledby="edit-vacation-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="edit-vacation-modal-title"><strong>Select a Time:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="edit-vacation-row" class="row">
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Hour:</strong></h4></label>
                            <input type="text" id="edit-vacation-hour" name="edit-vacation-hour" class="form-control input-lg numbers-only" />
                        </div>
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Min:</strong></h4></label>
                            <input type="text" id="edit-vacation-minute" name="edit-vacation-minute" class="form-control input-lg numbers-only" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deleteVacationTime()" id="edit-vacation-delete" class="btn btn-lg btn-danger"><span class="fa fa-trash-o"></span> Delete</button>
                <button type="button" onclick="addVacationTime(1)" id="edit-vacation-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-floppy-o"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Vacation Time Modal -->
<div class="modal fade" id="vacation-time-modal" tabindex="-1" role="dialog" aria-labelledby="vacation-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="vacation-time-modal-title"><strong>Add Vacation Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="vacation-date-modal-button" class="btn btn-default vacation-time-dropdown" onclick="toggle('#main-vacation-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-vacation-date-header" class="main-vacation-header"><?php print date("F j, Y"); ?></span> <span id="vacation-date-caret" class="vacation-time-caret caret"></span>
                            </button>
                            <div id="main-vacation-date">
                                <div id="vacation-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startVacationDate" name="startVacationDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="vacation-time-modal-button" class="btn btn-default vacation-time-dropdown" onclick="toggle('#main-vacation-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-vacation-time-header" class="main-vacation-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="vacation-time-caret" class="caret"></span>
                            </button>
                            <div id="main-vacation-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="vacation-time-hour" name="vacation-time-hour" class="form-control input-lg select-time">
                                            <?php
                                            $default = $DEFAULT_HR;
                                            for ($i=1; $i<=24; $i++)
                                            {
                                                if ($i == $default) {print '<option value="'.$i.'" selected="selected">'.$i.'</option>';}
                                                else {print '<option value="'.$i.'">'.$i.'</option>';}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 vacation-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="vacation-time-minute" name="vacation-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 vacation-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="vacation-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="vacation-time-user" name="vacation-time-user" class="form-control input-lg select-time">
                                <option id="vacation-time-user-0" value="0" selected="selected">All Employees</option>
                                <?php

                                    // get the list of all the Employees in the company, sorted by first name
                                    $sql = "SELECT E.id,
                                            CONCAT(E.first_name,' ',E.last_name) AS name
                                            FROM Employee E
                                            WHERE E.companyID IN
                                            (
                                                SELECT CA.cid
                                                FROM Company_Administrator CA
                                                WHERE CA.aid = $adminID
                                            )
                                            ORDER BY E.first_name ASC";

                                    $query = mysql_query($sql);

                                    while($user = mysql_fetch_object($query))
                                    {
                                        $EMPLOYEE_ID[] = $user->id;
                                        $EMPLOYEE_NAME[] = $user->name;
                                        print '<option id="vacation-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="vacation-time-employee-list" class="row">
                        <div id="employee-list-table-div" class="col-xs-12">
                            <table id="employee-list-table" class="table table-hover table-striped text-center">
                                <thead>
                                    <tr><td class="table-col-sm">hr</td><td class="table-col-sm">min</td><td>Name</td></tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        // prepare the defaults for printing
                                        if ($DEFAULT_MIN == 0) {$DEFAULT_MIN = "";}

                                        $count = 0;
                                        foreach ($EMPLOYEE_NAME as $name)
                                        {
                                            print '<tr>
                                                    <td class="table-col-sm"><input id="employee-list-hr-'.$count.'" name="employee-list-hr-'.$count.'" type="text" placeholder="0" class="employee-list-hr numbers-only" value="'.$DEFAULT_HR.'" /></td>
                                                    <td class="table-col-sm"><input id="employee-list-min-'.$count.'" name="employee-list-min-'.$count.'" type="text" placeholder="0" class="employee-list-min numbers-only" value="'.$DEFAULT_MIN.'" /></td>
                                                    <td class="employee-list-name">
                                                     <span id="employee-list-name-'.$count.'">'.$name.'</span>
                                                     <input id="employee-list-id-'.$count.'" name="employee-list-id-'.$count.'" type="hidden" class="employee-list-id" value="'.$EMPLOYEE_ID[$count].'" />
                                                    </td>
                                                   </tr>';
                                            $count++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addVacationTime(0)" id="vacation-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-sun-o"></span> Add Vacation Time</button>
            </div>
        </div>
    </div>
</div>

<!-- Holiday Time Modal -->
<div class="modal fade" id="holiday-time-modal" tabindex="-1" role="dialog" aria-labelledby="holiday-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="holiday-time-modal-title"><strong>Add Holiday Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="holiday-date-modal-button" class="btn btn-default holiday-time-dropdown" onclick="toggle('#main-holiday-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-holiday-date-header" class="main-holiday-header"><?php print date("F j, Y"); ?></span> <span id="holiday-date-caret" class="holiday-time-caret caret"></span>
                            </button>
                            <div id="main-holiday-date">
                                <div id="holiday-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startHolidayDate" name="startHolidayDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="holidayholiday-time-modal-button" class="btn btn-default holiday-time-dropdown" onclick="toggle('#main-holiday-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-holiday-time-header" class="main-holiday-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="holiday-time-caret" class="caret"></span>
                            </button>
                            <div id="main-holiday-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="holiday-time-hour" name="holiday-time-hour" class="form-control input-lg select-time">
                                            <?php
                                            $default = $DEFAULT_HR;
                                            for ($i=1; $i<=24; $i++)
                                            {
                                                if ($i == $default) {print '<option value="'.$i.'" selected="selected">'.$i.'</option>';}
                                                else {print '<option value="'.$i.'">'.$i.'</option>';}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 holiday-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="holiday-time-minute" name="holiday-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 holiday-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="holiday-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="holiday-time-user" name="holiday-time-user" class="form-control input-lg select-time">
                                <option id="holiday-time-user-0" value="0" selected="selected">All Employees</option>
                                <?php

                                    // get the list of all the Employees in the company, sorted by first name
                                    $sql = "SELECT E.id,
                                            CONCAT(E.first_name,' ',E.last_name) AS name
                                            FROM Employee E
                                            WHERE E.companyID IN
                                            (
                                                SELECT CA.cid
                                                FROM Company_Administrator CA
                                                WHERE CA.aid = $adminID
                                            )
                                            ORDER BY E.first_name ASC";

                                    $query = mysql_query($sql);

                                    while($user = mysql_fetch_object($query))
                                    {
                                        $EMPLOYEE_ID[] = $user->id;
                                        $EMPLOYEE_NAME[] = $user->name;
                                        print '<option id="vacation-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="holiday-time-employee-list" class="row">
                        <div id="employee-list-table-div" class="col-xs-12">
                            <table id="employee-list-table" class="table table-hover table-striped text-center">
                                <thead>
                                    <tr><td class="table-col-sm">hr</td><td class="table-col-sm">min</td><td>Name</td></tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        // prepare the defaults for printing
                                        if ($DEFAULT_MIN == 0) {$DEFAULT_MIN = "";}

                                        $count = 0;
                                        foreach ($EMPLOYEE_NAME as $name)
                                        {
                                            print '<tr>
                                                    <td class="table-col-sm"><input id="employee-list-hr-'.$count.'" name="employee-list-hr-'.$count.'" type="text" placeholder="0" class="employee-list-hr numbers-only" value="'.$DEFAULT_HR.'" /></td>
                                                    <td class="table-col-sm"><input id="employee-list-min-'.$count.'" name="employee-list-min-'.$count.'" type="text" placeholder="0" class="employee-list-min numbers-only" value="'.$DEFAULT_MIN.'" /></td>
                                                    <td class="employee-list-name">
                                                     <span id="employee-list-name-'.$count.'">'.$name.'</span>
                                                     <input id="employee-list-id-'.$count.'" name="employee-list-id-'.$count.'" type="hidden" class="employee-list-id" value="'.$EMPLOYEE_ID[$count].'" />
                                                    </td>
                                                   </tr>';
                                            $count++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addVacationTime(0)" id="holiday-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-sun-o"></span> Add Vacation Time</button>
            </div>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="view-report-modal" tabindex="-1" role="dialog" aria-labelledby="view-report-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="view-report-modal-title"><strong>Choose a report:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-1" name="view-report-type" class="input-lg" type="radio" value="1" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-1-label" class="modal-label label-md" for="view-report-1"><h4><strong>Employees on Break</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-2" name="view-report-type" class="input-lg" type="radio" value="2" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-2-label" class="modal-label label-md" for="view-report-2"><h4><strong>Employees Done for the Day</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-3" name="view-report-type" class="input-lg" type="radio" value="3" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-3-label" class="modal-label label-md" for="view-report-3"><h4><strong>Active Employees</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-4" name="view-report-type" class="input-lg" type="radio" value="4" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-4-label" class="modal-label label-md" for="view-report-4"><h4><strong>All Employee Daily Activity</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-5" name="view-report-type" class="input-lg" type="radio" value="5" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-5-label" class="modal-label label-md" for="view-report-5"><h4><strong>Forgotten Hours</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-6" name="view-report-type" class="input-lg" type="radio" value="6" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-6-label" class="modal-label label-md" for="view-report-6"><h4><strong>All Employee Hours for the Week</strong></h4></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="viewReport()" id="view-report-done" class="btn btn-lg btn-primary btn-done"><span class="glyphicon glyphicon-list-alt"></span> View Report</button>
            </div>
        </div>
    </div>
</div>

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

<!-- Change User Modal -->
<div id="change-user-modal-container"><?php include("change-user-modal.php"); ?></div>

<!-- View History Modal -->
<div id="view-history-modal-container"><?php include("view-history-modal.php"); ?></div>

<!-- Change Week Modal -->
<div id="change-week-modal-container"><?php include("change-week-modal.php"); ?></div>

<!-- Account Settings Modal -->
<div id="account-settings-modal-container"><?php include("account-settings-modal.php"); ?></div>

<!-- Add User Modal -->
<div id="add-user-modal-container"></div>

<!-- Hidden Inputs -->
<input type="hidden" id="uid" name="uid" value="<?php print $userID; ?>">
<input type="hidden" id="aid" name="aid" value="<?php print $adminID; ?>">
<input type="hidden" id="uname" name="uname" value="<?php print $uname; ?>">
<input type="hidden" id="pageTitle" name="pageTitle" value="<?php print $pageTitle; ?>">

<!-- Extra CSS -->
<link media="all" type="text/css" rel="stylesheet" href="http://americasmiles.com/css/timecard_admin.css">

<!-- Internal Javascript -->
<script type="text/javascript">
$(document).ready(function(){

    // setup variables
    var startDate;
    var endDate;
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    month = format2Digit(month);
    day = format2Digit(day);
    var today = d.getFullYear()+"-"+month+"-"+day;
    var DEFAULT_HR = 8;
    var DEFAULT_MIN = 0;
    var DEFAULT_NAME_ID = 0;

    function resize()
    {
        var topNav = 40;
        var bottomNav = 110;
        var smView = 768;
        var originalHeight = $("user-container").height();
        var browserHeight = $(window).height();
        var browserWidth = $(window).width();

        if (browserWidth <= smView) {bottomNav = 150;}

        var newHeight = browserHeight-topNav-bottomNav;

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

    $(".select-time").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    $(".select-type").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active');
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

    $("#vacation-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-vacation-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#vacation-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    $("#vacation-time-hour").change(function() {

        if ($("#vacation-time-hour").val() == 24)
        {
            $("#vacation-time-minute").val(0);
            $("#vacation-time-minute").attr("disabled", true);
        }
        else
        {
            $("#vacation-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#vacation-time-hour').val();
        $('#main-vacation-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#vacation-time-minute').change(function() {
        DEFAULT_MIN = $('#vacation-time-minute').val();
        $('#main-vacation-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $("#vacation-time-user").change(function() {

        // IF All Employees is selected
        if ($("#vacation-time-user").val() == 0)
        {
            $('#vacation-time-employee-list').show(500);
        }
        else
        {
            $('#vacation-time-employee-list').hide(500);
        }
    });

    $('#vacation-date-modal-button').click(function() {
        if ($('#vacation-date-caret').hasClass('caret-reversed'))
        {
            $('#vacation-date-caret').removeClass('caret-reversed');
        }
        else {$('#vacation-date-caret').addClass('caret-reversed');}
    });

    $('#vacation-time-modal-button').click(function() {
        if ($('#vacation-time-caret').hasClass('caret-reversed'))
        {
            $('#vacation-time-caret').removeClass('caret-reversed');
        }
        else {$('#vacation-time-caret').addClass('caret-reversed');}
    });

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

    // Only allow numbers for certain elements
    $(".numbers-only").keydown(function (e) {

        // console.log("key: "+e.keyCode);

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        else // the key was allowed
        {
            // enable the done button
            $('.btn-done').removeAttr('disabled');
        }
    });

    // Only allow decimal numbers for certain elements
    $(".decimal-numbers").keydown(function (e) {

        // console.log("key: "+e.keyCode);

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        else // the key was allowed
        {
            // enable the done button
            $('.btn-done').removeAttr('disabled');
        }
    });

    // Only allow alphanumeric for certain elements
    $('.alphanumeric').keydown(function (e) {
        if (e.shiftKey || e.ctrlKey || e.altKey) {
            e.preventDefault();
        } else {
            var key = e.keyCode;
            if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                e.preventDefault();
            }
            else // the key was allowed
            {
                // enable the done button
                $('.btn-done').removeAttr('disabled');
            }
        }
    });

});
</script>