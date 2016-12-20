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

// get the admin's username
$sql = "SELECT E.username FROM Employee E, Administrator A WHERE A.id = $adminID AND E.id = A.eid";
$query = mysql_query($sql);
$admin = mysql_fetch_object($query);

$ADMIN_USERNAME = $admin->username;

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
                    <a href="#" id="actionsButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-bolt"></span> Actions <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings">
                            <div id="clock-time">
                                <a id="clock-in" href="#" class="<?php if (!$showClockIn) {print "hide ";} ?>text-center" onclick="clockIn()" title="Clock in to start recording your time"><span class="fa fa-clock-o"></span> Clock in</a>
                                <a id="clock-out" href="#" class="<?php if ($showClockIn) {print "hide ";} ?>text-center" onclick="setupModal('clock-out-reason')" data-toggle="modal" data-target="#clock-out-modal" title="Clock out to stop recording your time"><span class="fa fa-clock-o"></span> Clock out</a>
                            </div>
                            <div id="edit-time" class="text-center" data-toggle="modal" data-target="#edit-time-modal" title="Click here to edit the selected time">
                                <a href="#" class="text-center" onclick="setupModal('edit-time')"><span class="fa fa-edit"></span> Edit Time</a>
                            </div>
                        </li>
                        <li id="edit-sick" class="settings">
                            <div class="text-center" data-toggle="modal" data-target="#edit-sick-modal" title="Click here to edit the selected sick time">
                                <a href="#" class="text-center" onclick="setupModal('edit-sick')"><span class="fa fa-edit"></span> <span class="fa fa-bed"></span> Edit Sick Time</a>
                            </div>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-plus"></span> Add <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="setupModal('holiday-time')"><span class="fa fa-gift"></span> Holiday Time</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('personal-time')"><span class="fa fa-child"></span> Personal Time</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('vacation-time')"><span class="fa fa-sun-o"></span> Vacation Time</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('sick-time')"><span class="fa fa-bed"></span> Sick Time</a></li>
                        <li class="divider"></li>
                        <li class="settings"><a href="#" onclick="setupModal('add-user')"><span class="fa fa-plus"></span><span class="glyphicon glyphicon-user"></span> New User</a></li>
                    </ul>
                </li>


                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-lock"></span> Security <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="viewReport(10)"><span class="fa fa-volume-up"></span> Employee Alarm Codes</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(9)"><span class="fa fa-key"></span> Employees with Keys</a></li>
                    </ul>
                </li>


                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-list-alt"></span> Reports <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="viewReport(4)"><span class="fa fa-history"></span> All Daily Activity of Active Employees</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(6)"><span class="fa fa-calendar"></span> All Employee Hours for the Week</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('do-payroll')" id="do-payroll-link" data-toggle="modal" data-target="#do-payroll-modal"><span class="fa fa-usd"></span> Do Payroll</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(2)"><span class="fa fa-car"></span> Employees Done for the Day</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(1)"><span class="fa fa-headphones"></span> Employees on Break</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(11)"><span class="fa fa-eye"></span> Employee Review Schedule</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(5)"><span class="fa fa-frown-o"></span> Forgotten Hours</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(7)"><span class="fa fa-child"></span> Personal Day Requests</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(12)"><span class="fa fa-sun-o"></span> Vacation Requests</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('admin-warning-messages')"><span class="fa fa-warning"></span> Warning Messages</a></li>
                        <li class="settings"><a href="#" onclick="viewReport(3)"><span class="fa fa-briefcase"></span> Working Employees</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" id="taskButton" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs"></span> Settings <b class="fa fa-caret-down"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="settings"><a href="#" onclick="setupModal('account-settings')"><span class="fa fa-wrench"></span> Account Settings</a></li>
                        <!-- <li class="settings"><a href="#" onclick="setupModal('ip-address')"><span class="fa fa-info-circle"></span> Company IP Address</a></li> -->
                        <li class="settings"><a href="#" onclick="setupModal('company-settings')"><span class="fa fa-info-circle"></span> Company Settings</a></li>
                        <li class="settings"><a href="#" onclick="setupModal('message-board')"><span class="fa fa-comment-o"></span> Message Board</a></li>
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
                                            <h4 class="text-center"><?php print $ADMIN_USERNAME; ?></h4>
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
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('vacation-time')" title="Add vacation time for a chosen employee">
                                        <span class="fa fa-sun-o"></span>
                                        <h3 id="vacation-time-button" class="text-center"><strong>Vacation Time</strong></h3>
                                    </a>
                                </div>
                            </div>
                            <div id="edit-vacation">
                                <div id="edit-vacation-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('edit-vacation')" data-toggle="modal" data-target="#edit-vacation-modal" title="Edit the chosen vacation time">
                                        <span class="fa fa-edit"></span>
                                        <h3 id="edit-vacation-button" class="text-center"><strong>Edit Vacation</strong></h3>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <div id="add-holiday">
                                <div id="holiday-time-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('holiday-time')" title="Add holiday time for all employees">
                                        <span class="fa fa-gift"></span>
                                        <h3 id="holiday-time-button" class="text-center"><strong>Holiday Time</strong></h3>
                                    </a>
                                </div>
                            </div>
                            <div id="edit-holiday">
                                <div id="edit-holiday-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('edit-holiday')" data-toggle="modal" data-target="#edit-holiday-modal" title="Edit the chosen holiday time">
                                        <span class="fa fa-edit"></span>
                                        <h3 id="edit-holiday-button" class="text-center"><strong>Edit Holiday</strong></h3>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2">
                            <div id="add-personal">
                                <div id="personal-time-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('personal-time')" title="Add personal time for a chosen employee">
                                        <span class="fa fa-child"></span>
                                        <h3 id="personal-time-button" class="text-center"><strong>Personal Time</strong></h3>
                                    </a>
                                </div>
                            </div>
                            <div id="edit-personal">
                                <div id="edit-personal-thumbnail-container">
                                    <a href="#" class="thumbnail text-center" onclick="setupModal('edit-personal')" data-toggle="modal" data-target="#edit-personal-modal" title="Edit the chosen personal time">
                                        <span class="fa fa-edit"></span>
                                        <h3 id="edit-personal-button" class="text-center"><strong>Edit Personal</strong></h3>
                                    </a>
                                </div>
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
                        <!-- <div class="col-xs-12 col-sm-2">
                            <a href="#" class="thumbnail text-center" onclick="logout()" title="Logout of your timecard">
                                <span class="fa fa-sign-out"></span>
                                <h3 class="text-center"><strong>Logout</strong></h3>
                            </a>
                        </div> -->
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

<!-- Edit Holiday Modal -->
<div class="modal fade" id="edit-holiday-modal" tabindex="-1" role="dialog" aria-labelledby="edit-holiday-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="edit-holiday-modal-title"><strong>Select a Time:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="edit-holiday-row" class="row">
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Hour:</strong></h4></label>
                            <input type="text" id="edit-holiday-hour" name="edit-holiday-hour" class="form-control input-lg numbers-only" />
                        </div>
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Min:</strong></h4></label>
                            <input type="text" id="edit-holiday-minute" name="edit-holiday-minute" class="form-control input-lg numbers-only" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deleteHolidayTime()" id="edit-holiday-delete" class="btn btn-lg btn-danger"><span class="fa fa-trash-o"></span> Delete</button>
                <button type="button" onclick="addHolidayTime(1)" id="edit-holiday-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-floppy-o"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Personal Modal -->
<div class="modal fade" id="edit-personal-modal" tabindex="-1" role="dialog" aria-labelledby="edit-personal-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="edit-personal-modal-title"><strong>Select a Time:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="edit-personal-row" class="row">
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Hour:</strong></h4></label>
                            <input type="text" id="edit-personal-hour" name="edit-personal-hour" class="form-control input-lg numbers-only" />
                        </div>
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Min:</strong></h4></label>
                            <input type="text" id="edit-personal-minute" name="edit-personal-minute" class="form-control input-lg numbers-only" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deletePersonalTime()" id="edit-personal-delete" class="btn btn-lg btn-danger"><span class="fa fa-trash-o"></span> Delete</button>
                <button type="button" onclick="addPersonalTime(1)" id="edit-personal-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-floppy-o"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Sick Modal -->
<div class="modal fade" id="edit-sick-modal" tabindex="-1" role="dialog" aria-labelledby="edit-sick-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="edit-sick-modal-title"><strong>Select a Time:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="edit-sick-row" class="row">
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Hour:</strong></h4></label>
                            <input type="text" id="edit-sick-hour" name="edit-sick-hour" class="form-control input-lg numbers-only" />
                        </div>
                        <div class="col-xs-6 col-sm-6 col-label">
                            <label class="modal-label"><h4><strong>Min:</strong></h4></label>
                            <input type="text" id="edit-sick-minute" name="edit-sick-minute" class="form-control input-lg numbers-only" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-label">
                            <label class="modal-label"><h4><strong>Absence Type:</strong></h4></label>
                            <select id="edit-sick-time-absence" name="edit-sick-time-absence" class="form-control input-lg">
                                <option value="0">Excused</option>
                                <option value="1">Unexcused</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deleteSickTime()" id="edit-sick-delete" class="btn btn-lg btn-danger"><span class="fa fa-trash-o"></span> Delete</button>
                <button type="button" onclick="addSickTime(1)" id="edit-sick-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-floppy-o"></span> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Do Payroll Modal -->
<div class="modal fade" id="do-payroll-modal" tabindex="-1" role="dialog" aria-labelledby="do-payroll-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="do-payroll-modal-title"><strong>Do Payroll</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="payroll-start-date-modal-button" class="btn btn-default do-payroll-dropdown" onclick="toggle('#main-payroll-start-date',500)">
                                <strong>Choose the Start Date:</strong> <span id="main-payroll-start-date-header" class="main-payroll-start-header"><?php print date("F j, Y"); ?></span> <span id="payroll-start-date-caret" class="do-payroll-caret caret"></span>
                            </button>
                            <div id="main-payroll-start-date">
                                <div id="do-payroll-start-datepicker"></div>
                                <input class="startDate" type="hidden" id="startPayrollDate" name="startPayrollDate" value="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="payroll-end-date-modal-button" class="btn btn-default do-payroll-dropdown" onclick="toggle('#main-payroll-end-date',500)">
                                <strong>Choose the End Date:</strong> <span id="main-payroll-end-date-header" class="main-payroll-end-header"><?php print date("F j, Y"); ?></span> <span id="payroll-end-date-caret" class="do-payroll-caret caret"></span>
                            </button>
                            <div id="main-payroll-end-date">
                                <div id="do-payroll-end-datepicker"></div>
                                <input class="endDate" type="hidden" id="endPayrollDate" name="endPayrollDate" value="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="viewReport(8)" id="do-payroll-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-usd"></span> Do Payroll</button>
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
                                    <input id="view-report-4" name="view-report-type" class="input-lg" type="radio" value="4" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-4-label" class="modal-label label-md" for="view-report-4"><h4><strong>All Daily Activity of Active Employees</strong></h4></label>
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
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-do-payroll" name="view-report-type" class="input-lg" type="radio" value="do-payroll" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-do-payroll-label" class="modal-label label-md" for="view-report-do-payroll"><h4><strong>Do Payroll</strong></h4></label>
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
                                    <input id="view-report-11" name="view-report-type" class="input-lg" type="radio" value="11" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-11-label" class="modal-label label-md" for="view-report-11"><h4><strong>Employee Review Schedule</strong></h4></label>
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
                                    <input id="view-report-7" name="view-report-type" class="input-lg" type="radio" value="7" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-7-label" class="modal-label label-md" for="view-report-7"><h4><strong>Personal Day Requests</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-admin-warning-messages" name="view-report-type" class="input-lg" type="radio" value="admin-warning-messages" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-admin-warning-messages-label" class="modal-label label-md" for="view-report-admin-warning-messages"><h4><strong>Warning Messages</strong></h4></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2 col-radio">
                                    <input id="view-report-3" name="view-report-type" class="input-lg" type="radio" value="3" />
                                </div>
                                <div class="col-xs-10 col-label">
                                    <label id="view-report-3-label" class="modal-label label-md" for="view-report-3"><h4><strong>Working Employees</strong></h4></label>
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

<!-- Change Day Modal -->
<div id="change-day-modal-container"><?php include("change-day-modal.php"); ?></div>

<!-- Change Week Modal -->
<div id="change-week-modal-container"><?php include("change-week-modal.php"); ?></div>

<!-- Account Settings Modal -->
<div id="account-settings-modal-container"><?php include("account-settings-modal.php"); ?></div>

<!-- Add User Modal -->
<div id="add-user-modal-container"></div>

<!-- Message Board Modal -->
<div id="message-board-modal-container"><?php include("message-board-modal.php"); ?></div>

<!-- IP Address Modal -->
<!-- <div id="ip-address-modal-container"><?php //include("ip-address-modal.php"); ?></div> -->

<!-- Company Settings Modal -->
<div id="company-settings-modal-container"><?php include("company-settings-modal.php"); ?></div>

<!-- Admin Warning Messages Modal -->
<div id="admin-warning-messages-modal-container"><?php include("admin-warning-messages-modal.php"); ?></div>

<!-- Holiday Time Modal -->
<div id="holiday-time-modal-container"><?php include("holiday-time-modal.php"); ?></div>

<!-- Personal Time Modal -->
<div id="personal-time-modal-container"><?php include("personal-time-modal.php"); ?></div>

<!-- Vacation Time Modal -->
<div id="vacation-time-modal-container"><?php include("vacation-time-modal.php"); ?></div>

<!-- Sick Time Modal -->
<div id="sick-time-modal-container"><?php include("sick-time-modal.php"); ?></div>

<!-- Hidden Inputs -->
<input type="hidden" id="uid" name="uid" value="<?php print $userID; ?>">
<input type="hidden" id="aid" name="aid" value="<?php print $adminID; ?>">
<input type="hidden" id="uname" name="uname" value="<?php print $uname; ?>">
<input type="hidden" id="pageTitle" name="pageTitle" value="<?php print $pageTitle; ?>">

<!-- Extra CSS -->
<link media="all" type="text/css" rel="stylesheet" href="http://amstimecard.com/css/timecard_admin.css">

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

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

    // set the start date to today for everything with the endDate class
    $('.endDate').val(today);

    // setup the start datepicker for the do-payroll modal
    $("#do-payroll-start-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-payroll-start-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#payroll-start-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    // setup the end datepicker for the do-payroll modal
    $("#do-payroll-end-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.endDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-payroll-end-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#payroll-end-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });


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