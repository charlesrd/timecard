<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";

// setup variables
$today = date('Y-m-d');
$employeeType = 1;


// IF the in_uid is not empty, reset the userID to in_uid
if ($in_uid != "")
{
    $userID = $in_uid;
}


// get the user information
$sql = "SELECT * FROM Employee E WHERE E.id = $userID";
$query = mysql_query($sql);
$user = mysql_fetch_object($query);

$firstName = $user->first_name;
$middleName = $user->middle_name;
$lastName = $user->last_name;
$email = $user->email;
$username = $user->username;
$password = $user->password;
$payrate = $user->payrate;
$salary = $user->salary;
$paytype = $user->paytype;
$fullTime = $user->full_time;
$companyID = $user->companyID;
$peachID = $user->peachID;
$status = $user->active;
$start_time = $user->start_time;
$daysBetweenReview = $user->days_between_review;
$personalDays = $user->personal_days;
$vacationDays = $user->vacation_days;
$startDate = $user->start_date;
$endDate = $user->end_date;
$birthDate = $user->birth_date;


// check if the employee is an administrator
$sql = "SELECT COUNT(*) AS count FROM Administrator A WHERE A.eid = $userID";
$query = mysql_query($sql);
$userCount = mysql_fetch_object($query);

if ($userCount->count > 0) {$employeeType = 2;}


// get the start time values
$START_TIME = explode(':', $start_time);
$start_time_hour = $START_TIME[0];
$start_time_minute = $START_TIME[1];


?>
<div class="modal fade" id="account-settings-modal" tabindex="-1" role="dialog" aria-labelledby="account-settings-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="account-settings-modal-title"><strong>Account Settings</strong></h2>
            </div>
            <div class="modal-body">
                <div id="account-settings-row" class="container-fluid">


                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#account-settings-user-tab" onclick="toggleAddItemTab()" aria-controls="account-settings-user-tab" role="tab" data-toggle="tab">User Settings</a></li>
                        <li role="presentation"><a href="#account-settings-files-tab" aria-controls="account-settings-files-tab" role="tab" data-toggle="tab">Files</a></li>
                        <li role="presentation"><a href="#account-settings-permissions-tab" aria-controls="account-settings-permissions-tab" role="tab" data-toggle="tab">Permissions</a></li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="account-settings-user-tab">


                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="firstNameLabel" for="firstName">First Name:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="firstName" name="firstName" class="alphanumeric" placeholder="First Name" value="<?php print $firstName; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="middleNameLabel" for="middleName">Middle Name:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="middleName" name="middleName" class="alphanumeric" placeholder="Middle Name" value="<?php print $middleName; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="lastNameLabel" for="lastName">Last Name:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="lastName" name="lastName" class="alphanumeric" placeholder="Last Name" value="<?php print $lastName; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="emailLabel" for="email">Email:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="email" name="email" class="account-settings-input" placeholder="Email" value="<?php print $email; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="usernameLabel" for="username">Username:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="username" name="username" class="alphanumeric" placeholder="Username" value="<?php print $username; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="passwordLabel" for="password">Password:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="password" id="password" name="password" class="account-settings-input" placeholder="Password" value="<?php print $password; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="confirmPasswordLabel" for="confirmPassword">Confirm Password:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="password" id="confirmPassword" name="confirmPassword" class="account-settings-input" placeholder="Password" value="<?php print $password; ?>" />
                                </div>
                            </div>
                            <div id="payrate-row" class="row payrate-row <?php if ($paytype != 1) {print 'hide';} ?>">
                                <div class="col-xs-5">
                                    <label id="payrateLabel" for="payrate">Hourly Payrate:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="payrate" id="payrate" name="payrate" class="decimal-numbers" placeholder="Hourly Payrate" value="<?php print $payrate; ?>" />
                                </div>
                            </div>
                            <div id="salary-row" class="row salary-row <?php if ($paytype != 2) {print 'hide';} ?>">
                                <div class="col-xs-5">
                                    <label id="salaryLabel" for="salary">Salary:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="salary" id="salary" name="salary" class="decimal-numbers" placeholder="Salary" value="<?php print $salary; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="scheduleLabel" for="schedule">Schedule:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="schedule" name="schedule" class="form-control select-type">
                                        <option value="0" <?php if ($fullTime == 0) {print 'selected="selected"';} ?> >Part-Time</option>
                                        <option value="1" <?php if ($fullTime == 1) {print 'selected="selected"';} ?> >Full-Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="paytypeLabel" for="paytype">Paytype:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="paytype" name="paytype" class="form-control select-type">
                                        <?php

                                        // get the paytypes
                                        $sql = "SELECT * FROM Paytype P ORDER BY P.name ASC";
                                        $query = mysql_query($sql);
                                        while ($record = mysql_fetch_object($query))
                                        {
                                            if ($paytype == $record->id) {print '<option value="'.$record->id.'" selected="selected">'.$record->name.'</option>';}
                                            else {print '<option value="'.$record->id.'">'.$record->name.'</option>';}
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="companyLabel" for="company">Company:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="company" name="company" class="form-control select-type">
                                        <?php

                                        // get all the companies that this administrator can choose
                                        $sql = "SELECT 
                                                C.id, C.name 
                                                FROM Company C, Company_Administrator CA 
                                                WHERE CA.aid = $adminID
                                                AND C.id = CA.cid
                                                ORDER BY C.name ASC";
                                        $query = mysql_query($sql);
                                        while ($record = mysql_fetch_object($query))
                                        {
                                            if ($companyID == $record->id) {print '<option value="'.$record->id.'" selected="selected">'.$record->name.'</option>';}
                                            else {print '<option value="'.$record->id.'">'.$record->name.'</option>';}
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="peachIDLabel" for="peachID">Peachtree ID:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="peachID" name="peachID" class="alphanumeric" placeholder="Peachtree ID" value="<?php print $peachID; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="employeeTypeLabel" for="employeeType">Employee Type:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="employeeType" name="employeeType" class="form-control select-type">
                                        <option value="1" <?php if ($employeeType == 1) {print 'selected="selected"';} ?> >Employee</option>
                                        <option value="2" <?php if ($employeeType == 2) {print 'selected="selected"';} ?> >Administrator</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="statusLabel" for="status">Employee Status:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="status" name="status" class="form-control select-type">
                                        <option value="1" <?php if ($status == 1) {print 'selected="selected"';} ?> >Active</option>
                                        <option value="0" <?php if ($status == 0) {print 'selected="selected"';} ?> >Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="statusLabel" for="start_time_hour">Start Time:</label>
                                </div>
                                <div class="col-xs-3">
                                    <select id="start_time_hour" name="start_time_hour" class="form-control select-type">
                                        <option value="00" <?php if ($start_time_hour == "00") {print 'selected="selected"';} ?> >00</option>
                                        <option value="01" <?php if ($start_time_hour == "01") {print 'selected="selected"';} ?> >01</option>
                                        <option value="02" <?php if ($start_time_hour == "02") {print 'selected="selected"';} ?> >02</option>
                                        <option value="03" <?php if ($start_time_hour == "03") {print 'selected="selected"';} ?> >03</option>
                                        <option value="04" <?php if ($start_time_hour == "04") {print 'selected="selected"';} ?> >04</option>
                                        <option value="05" <?php if ($start_time_hour == "05") {print 'selected="selected"';} ?> >05</option>
                                        <option value="06" <?php if ($start_time_hour == "06") {print 'selected="selected"';} ?> >06</option>
                                        <option value="07" <?php if ($start_time_hour == "07") {print 'selected="selected"';} ?> >07</option>
                                        <option value="08" <?php if ($start_time_hour == "08") {print 'selected="selected"';} ?> >08</option>
                                        <option value="09" <?php if ($start_time_hour == "09") {print 'selected="selected"';} ?> >09</option>
                                        <option value="10" <?php if ($start_time_hour == "10") {print 'selected="selected"';} ?> >10</option>
                                        <option value="11" <?php if ($start_time_hour == "11") {print 'selected="selected"';} ?> >11</option>
                                        <option value="12" <?php if ($start_time_hour == "12") {print 'selected="selected"';} ?> >12</option>
                                        <option value="13" <?php if ($start_time_hour == "13") {print 'selected="selected"';} ?> >13</option>
                                        <option value="14" <?php if ($start_time_hour == "14") {print 'selected="selected"';} ?> >14</option>
                                        <option value="15" <?php if ($start_time_hour == "15") {print 'selected="selected"';} ?> >15</option>
                                        <option value="16" <?php if ($start_time_hour == "16") {print 'selected="selected"';} ?> >16</option>
                                        <option value="17" <?php if ($start_time_hour == "17") {print 'selected="selected"';} ?> >17</option>
                                        <option value="18" <?php if ($start_time_hour == "18") {print 'selected="selected"';} ?> >18</option>
                                        <option value="19" <?php if ($start_time_hour == "19") {print 'selected="selected"';} ?> >19</option>
                                        <option value="20" <?php if ($start_time_hour == "20") {print 'selected="selected"';} ?> >20</option>
                                        <option value="21" <?php if ($start_time_hour == "21") {print 'selected="selected"';} ?> >21</option>
                                        <option value="22" <?php if ($start_time_hour == "22") {print 'selected="selected"';} ?> >22</option>
                                        <option value="23" <?php if ($start_time_hour == "23") {print 'selected="selected"';} ?> >23</option>
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <select id="start_time_minute" name="start_time_minute" class="form-control select-type">
                                        <option value="00" <?php if ($start_time_minute == "00") {print 'selected="selected"';} ?> >00</option>
                                        <option value="15" <?php if ($start_time_minute == "15") {print 'selected="selected"';} ?> >15</option>
                                        <option value="30" <?php if ($start_time_minute == "30") {print 'selected="selected"';} ?> >30</option>
                                        <option value="45" <?php if ($start_time_minute == "45") {print 'selected="selected"';} ?> >45</option>
                                    </select>
                                </div>
                                <div class="col-xs-1"></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="daysBetweenReviewLabel" for="daysBetweenReview">Days to Review:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="daysBetweenReview" name="daysBetweenReview" class="numbers-only" placeholder="0" value="<?php print $daysBetweenReview; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="personalDaysLabel" for="personalDays">Personal Days /yr:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="personalDays" name="personalDays" class="numbers-only" placeholder="0" value="<?php print $personalDays; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="vacationDaysLabel" for="vacationWeeks">Vacation Days /yr:</label>
                                </div>
                                <div class="col-xs-7">
                                    <input type="text" id="vacationDays" name="vacationDays" class="numbers-only" placeholder="0" value="<?php print $vacationDays; ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button id="start-date-modal-button" class="btn btn-default start-time-dropdown" onclick="toggle('#main-start-date',500)">
                                        <strong>Start Date:</strong> <span id="main-start-date-header" class="main-start-header"><?php $d = ($startDate != "0000-00-00") ? date("F j, Y", strtotime($startDate)) : ""; print $d; ?></span> <span id="start-date-caret" class="start-time-caret caret"></span>
                                    </button>
                                    <div id="main-start-date">
                                        <button class="btn btn-default start-time-dropdown" onclick="unknownStartDate()">Unknown Start Date</button>
                                        <div id="start-time-datepicker"></div>
                                        <input class="startDate" type="hidden" id="startDate" name="startDate" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button id="end-date-modal-button" class="btn btn-default end-time-dropdown" onclick="toggle('#main-end-date',500)">
                                        <strong>End Date:</strong> <span id="main-end-date-header" class="main-end-header"><?php $d = ($endDate != "0000-00-00") ? date("F j, Y", strtotime($endDate)) : ""; print $d; ?></span> <span id="end-date-caret" class="end-time-caret caret"></span>
                                    </button>
                                    <div id="main-end-date">
                                        <button class="btn btn-default end-time-dropdown" onclick="unknownEndDate()">Unknown End Date</button>
                                        <div id="end-time-datepicker"></div>
                                        <input class="endDate" type="hidden" id="endDate" name="endDate" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button id="birth-date-modal-button" class="btn btn-default birth-time-dropdown" onclick="toggle('#main-birth-date',500)">
                                        <strong>Birthday:</strong> <span id="main-birth-date-header" class="main-birth-header"><?php $d = ($birthDate != "0000-00-00") ? date("F j, Y", strtotime($birthDate)) : ""; print $d; ?></span> <span id="birth-date-caret" class="birth-time-caret caret"></span>
                                    </button>
                                    <div id="main-birth-date">
                                        <button class="btn btn-default birth-time-dropdown" onclick="unknownBirthDate()">Unknown Birthday</button>
                                        <div id="birth-time-datepicker"></div>
                                        <input class="birthDate" type="hidden" id="birthDate" name="birthDate" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <span id="span-errors"></span>
                                </div>
                            </div>


                        </div>


                        
                        <div role="tabpanel" class="tab-pane" id="account-settings-files-tab">




                            <form method="POST" action="http://amstimecard.com/tpl/upload.php" accept-charset="UTF-8" id="form-upload" name="form-upload" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div id="upload-message"></div>
                                    <div class="controls" id="dz-container">
                                        <div id="dz-upload" class="dropzone">
                                            <div class="fallback">
                                                <input multiple="true" accept=".doc, .DOC, .pdf, .PDF, .docx, .DOCX, .zip, .ZIP, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/zip" name="file[]" type="file">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="upload-employee-file-done" class="btn btn-sm btn-primary"><span class="fa fa-upload"></span> Upload</button>
                            </form>


                            

                        </div>


                        <div role="tabpanel" class="tab-pane" id="account-settings-permissions-tab">


                            <div class="row" style="margin-top: 10px;">
                                <div class="col-xs-5">
                                    <label id="adminBillingLabel" for="adminBilling">Admin - Billing:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="adminBilling" name="adminBilling" class="form-control select-type">
                                        <option value="0" <?php if ($user->admin_billing == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->admin_billing == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="adminIncidentReportsLabel" for="adminIncidentReports">Admin - Incident Reports:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="adminIncidentReports" name="adminIncidentReports" class="form-control select-type">
                                        <option value="0" <?php if ($user->admin_incident_reports == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->admin_incident_reports == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="adminMillingLabel" for="adminMilling">Admin - Milling:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="adminMilling" name="adminMilling" class="form-control select-type">
                                        <option value="0" <?php if ($user->admin_milling == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->admin_milling == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="adminQualityControlLabel" for="adminQualityControl">Admin - Quality Control:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="adminQualityControl" name="adminQualityControl" class="form-control select-type">
                                        <option value="0" <?php if ($user->admin_quality_control == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->admin_quality_control == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="adminShippingLabel" for="adminShipping">Admin - Shipping:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="adminShipping" name="adminShipping" class="form-control select-type">
                                        <option value="0" <?php if ($user->admin_shipping == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->admin_shipping == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="signsIncidentReportsLabel" for="signsIncidentReports">Signs Incident Reports:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="signsIncidentReports" name="signsIncidentReports" class="form-control select-type">
                                        <option value="0" <?php if ($user->incident_report_signed == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->incident_report_signed == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="salesCalendarActiveLabel" for="salesCalendarActive">Sales Calendar Active:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="salesCalendarActive" name="salesCalendarActive" class="form-control select-type">
                                        <option value="0" <?php if ($user->sales_calendar_active == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->sales_calendar_active == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="marketingDeptLabel" for="marketingDept">Marketing Department Employee:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="marketingDept" name="marketingDept" class="form-control select-type">
                                        <option value="0" <?php if ($user->marketing_dept == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->marketing_dept == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="dlpActiveLabel" for="dlpActive">DentalLabProfile Active:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="dlpActive" name="dlpActive" class="form-control select-type">
                                        <option value="0" <?php if ($user->dlp_active == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->dlp_active == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="dlpAdminLabel" for="dlpAdmin">DentalLabProfile Admin:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="dlpAdmin" name="dlpAdmin" class="form-control select-type">
                                        <option value="0" <?php if ($user->dlp_admin == 0) {print 'selected="selected"';} ?> >No</option>
                                        <option value="1" <?php if ($user->dlp_admin == 1) {print 'selected="selected"';} ?> >Yes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="overtimeAlertsLabel" for="overtimeAlerts">Overtime Alerts:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="overtimeAlerts" name="overtimeAlerts" class="form-control select-type">
                                        <option value="0" <?php if ($user->overtime_alerts == 0) {print 'selected="selected"';} ?> >Off</option>
                                        <option value="1" <?php if ($user->overtime_alerts == 1) {print 'selected="selected"';} ?> >On</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-5">
                                    <label id="enforceIPAddressLabel" for="enforceIPAddress">Enforce IP Address:</label>
                                </div>
                                <div class="col-xs-7">
                                    <select id="enforceIPAddress" name="enforceIPAddress" class="form-control select-type">
                                        <option value="0" <?php if ($user->enforce_ip_address == 0) {print 'selected="selected"';} ?> >Off</option>
                                        <option value="1" <?php if ($user->enforce_ip_address == 1) {print 'selected="selected"';} ?> >On</option>
                                    </select>
                                </div>
                            </div>
                            

                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveAccountSettings()" id="account-settings-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Account Settings Javascript -->
<script type="text/javascript">

    // prevent copy cut paste
    $('.account-settings-input, .numbers-only, .decimal-numbers, .alphanumeric').bind("copy cut paste",function(e) {
          e.preventDefault();
    });

    $(".select-type").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    $('#paytype').change(function() {

        // IF the paytype is Hourly, show the Hourly payrate input, hide the salary input
        if ($('#paytype').val() == 1)
        {
            hide('#salary-row', 500);
            hide('#payrate-row');
            $("#payrate-row").removeClass("hide");
            show('#payrate-row', 500);
        }
        // IF the paytype is Hourly, show the Hourly payrate input, hide the salary input
        else if ($('#paytype').val() == 2)
        {
            hide('#payrate-row', 500);
            hide('#salary-row');
            $("#salary-row").removeClass("hide");
            show('#salary-row', 500);
        }
        else{return;}
    });

    $(".account-settings-input").keydown(function (e) {

        var key = e.keyCode;

        // IF tab was pressed, return
        if (key == 9) {return;}

        // enable the done button
        $('.btn-done').removeAttr('disabled');
    });


    $("#start-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-start-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#start-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });


    $("#end-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.endDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-end-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#end-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });


    $("#birth-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        changeYear: true,
        yearRange: "-100:+0",
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.birthDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-birth-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#birth-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });


    // set the start date
    $('.startDate').val(<?php print '"'.$startDate.'"'; ?>);

    <?php if ($startDate != "0000-00-00"): ?>
    
    $('#start-time-datepicker').datepicker("setDate", new Date(<?php print date("Y,",strtotime($startDate)); print date("n",strtotime($startDate))-1; print date(",j",strtotime($startDate)); ?>));

    <?php endif; ?>

    
    // set the end date
    $('.endDate').val(<?php print '"'.$endDate.'"'; ?>);

    <?php if ($endDate != "0000-00-00"): ?>
    
    $('#end-time-datepicker').datepicker("setDate", new Date(<?php print date("Y,",strtotime($endDate)); print date("n",strtotime($endDate))-1; print date(",j",strtotime($endDate)); ?>));
    
    <?php endif; ?>


    // set the birth date
    $('.birthDate').val(<?php print '"'.$birthDate.'"'; ?>);

    <?php if ($birthDate != "0000-00-00"): ?>
    
    $('#birth-time-datepicker').datepicker("setDate", new Date(<?php print date("Y,",strtotime($birthDate)); print date("n",strtotime($birthDate))-1; print date(",j",strtotime($birthDate)); ?>));
    
    <?php endif; ?>


    // Only allow numbers for certain elements
    $(".numbers-only").keydown(function (e) {

        var key = e.keyCode;

        // IF tab was pressed, return
        if (key == 9) {return;}

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

        var key = e.keyCode;

        // IF tab was pressed, return
        if (key == 9) {return;}

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
        if (e.ctrlKey || e.altKey) {
            e.preventDefault();
        } else {
            var key = e.keyCode;

            // IF tab was pressed, return
            if (key == 9) {return;}

            if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
                e.preventDefault();
            }
            else // the key was allowed
            {
                if (e.shiftKey)
                {
                    if (!((key < 48 || key > 57) && (key < 96 || key > 105))) {
                        e.preventDefault();
                    }
                    else
                    {
                        // enable the done button
                        $('.btn-done').removeAttr('disabled');
                    }
                }
                else
                {
                    // enable the done button
                    $('.btn-done').removeAttr('disabled');
                }
            }
        }
    });





$(document).ready(function() {

    // Dropzone
    var maxParallelFiles = 500;
    Dropzone.autoDiscover = false;

    $("#dz-upload").dropzone({
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: maxParallelFiles,
        addRemoveLinks: true,
        createImageThumbnails: false,
        maxFiles: maxParallelFiles,
        acceptedFiles: "application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/zip,.zip,.ZIP,.pdf,.PDF,.doc,.DOC,.docx,.DOCX",
        url: "/tpl/upload.php",

        dictRemoveFile: "Delete",

        init: function() {
            dz = this;
            var uploadForm = $('#form-upload');
            var submitBtn = $("form#form-upload button[type=submit]");

            // Set vaidation options for guest upload form
            var validationOptions = {
                errorElement: 'div', //default input error message container
                errorClass: 'alert alert-danger', // default input error message class
                focusInvalid: false, // do not focus the last invalid input

                rules: {
                    "file[]": {
                        required: true
                    }
                },

                highlight: function(element, errorClass, validClass) {
                    $(element).removeClass(errorClass);
                },

                // Callback for handling actual submit when form is valid
                submitHandler: function(form) {
                    form.submit();
                }
            }

            dz.on("addedfile", function() {
                if (dz.files.length !== 0 && dz.files.length <= maxParallelFiles) {
                    $(".dropzone").css('overflow-y', 'scroll');
                }
            }).on("removedfile", function() {
                if (dz.files.length === 0) {
                    $(".dropzone").css('overflow-y', '');
                }
                if (dz.getRejectedFiles().length === 0) {
                    $("#rejected-files").slideUp(500);
                }
            });

            $("form#form-upload button[type=submit]").click(
                function(e) {
                    e.preventDefault(); // prevent default action of click event
                    e.stopPropagation(); // stop DOM propagation

                    uploadForm.validate(validationOptions);

                    if (uploadForm.valid()) {
                        // Process uploads if all validation has passed.
                        dz.processQueue();
                    }
                }
            );

            dz.on("sendingmultiple", function(file, xhr, formData) {
                formData.append('_token', $("input[name=_token]").val());
            });

            dz.on("successmultiple", function(file, response) {
                $("a.dz-remove").remove();
            });

            var count = 0;

            dz.on("completemultiple", function(file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0 && this.getRejectedFiles().length === 0)
                {
                    // set the variables
                    fileCount = 0;
                    totalFileCount = file.length;
                    currentFileName = file[fileCount].name;
                    fileNameArray.length = 0;
                    // alert("fileCount: "+fileCount);
                    // alert("totalFileCount: "+totalFileCount);
                    // alert("currentFileName: "+currentFileName);

                    // save the uploaded filenames to an array
                    for (var i=0; i<totalFileCount; i++)
                    {
                        fileNameArray.push(file[i].name);
                    }

                    // show the run sheet for each uploaded file one at a time
                    // getFile('run-sheet-new.php');

                    if (count == 0) {
                        $('<div class="alert alert-success lead text-muted text-center">Your files have been uploaded successfully. <a href="#" id="upload-more">Upload more?</a></div>').hide().appendTo('#upload-message').slideDown(500);

                        // $(document).on('click', '#upload-more', function(e) {
                        //     e.preventDefault();
                        //     e.stopPropagation();

                        //     dz.removeAllFiles();

                        //     $(".alert-success").slideUp(500, function() {
                        //         $(this).remove();
                        //     });

                        //     count = fileCount = totalFileCount = 0;
                        // });
                    }
                    count++;
                } else {
                    if (count == 0) {
                        $('<div class="alert alert-danger lead text-muted text-center" id="rejected-files">Please fix errors above and <a href="#" id="try-again">Try again?</a></div>').hide().appendTo('#upload-message').slideDown(500);

                        $(document).on('click', '#try-again', function(e) {
                            e.preventDefault();
                            e.stopPropagation();

                            dz.removeAllFiles();

                            $(".alert-danger").slideUp(500, function() {
                                $(this).remove();
                            });

                            count = fileCount = totalFileCount = 0;
                        });
                    }
                    count++;
                }
            });

        }
    });
    // END Dropzone

});



</script>