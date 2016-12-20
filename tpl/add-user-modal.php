<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";

// setup variables
$today = date('Y-m-d');
$employeeType = 1;


?>
<div class="modal fade" id="add-user-modal" tabindex="-1" role="dialog" aria-labelledby="add-user-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="add-user-modal-title"><strong>Add User</strong></h2>
            </div>
            <div class="modal-body">
                <div id="add-user-row" class="container-fluid">
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="firstNameLabel" for="firstName">First Name:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="text" id="firstName" name="firstName" class="alphanumeric" placeholder="First Name" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="middleNameLabel" for="middleName">Middle Name:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="text" id="middleName" name="middleName" class="alphanumeric" placeholder="Middle Name" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="lastNameLabel" for="lastName">Last Name:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="text" id="lastName" name="lastName" class="alphanumeric" placeholder="Last Name" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="emailLabel" for="email">Email:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="text" id="email" name="email" class="add-user-input" placeholder="Email" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="usernameLabel" for="username">Username:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="text" id="username" name="username" class="alphanumeric" placeholder="Username" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="passwordLabel" for="password">Password:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="password" id="password" name="password" class="add-user-input" placeholder="Password" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="confirmPasswordLabel" for="confirmPassword">Confirm Password:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="password" id="confirmPassword" name="confirmPassword" class="add-user-input" placeholder="Password" value="" />
                        </div>
                    </div>
                    <div id="payrate-row" class="row payrate-row">
                        <div class="col-xs-5">
                            <label id="payrateLabel" for="payrate">Hourly Payrate:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="payrate" id="payrate" name="payrate" class="decimal-numbers" placeholder="Hourly Payrate" value="" />
                        </div>
                    </div>
                    <div id="salary-row" class="row salary-row hide">
                        <div class="col-xs-5">
                            <label id="salaryLabel" for="salary">Salary:</label>
                        </div>
                        <div class="col-xs-7">
                            <input type="salary" id="salary" name="salary" class="decimal-numbers" placeholder="Salary" value="" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="scheduleLabel" for="schedule">Schedule:</label>
                        </div>
                        <div class="col-xs-7">
                            <select id="schedule" name="schedule" class="form-control select-type">
                                <option value="0">Part-Time</option>
                                <option value="1">Full-Time</option>
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
                            <label id="employeeTypeLabel" for="employeeType">Employee Type:</label>
                        </div>
                        <div class="col-xs-7">
                            <select id="employeeType" name="employeeType" class="form-control select-type">
                                <option value="1">Employee</option>
                                <option value="2">Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-5">
                            <label id="statusLabel" for="start_time_hour">Start Time:</label>
                        </div>
                        <div class="col-xs-3">
                            <select id="start_time_hour" name="start_time_hour" class="form-control select-type">
                                <option value="00">00</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09" selected="selected">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <select id="start_time_minute" name="start_time_minute" class="form-control select-type">
                                <option value="00">00</option>
                                <option value="30">30</option>
                            </select>
                        </div>
                        <div class="col-xs-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="start-date-modal-button" class="btn btn-default start-time-dropdown" onclick="toggle('#main-start-date',500)">
                                <strong>Start Date:</strong> <span id="main-start-date-header" class="main-start-header"><?php print date("F j, Y"); ?></span> <span id="start-date-caret" class="start-time-caret caret"></span>
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
                                <strong>End Date:</strong> <span id="main-end-date-header" class="main-end-header"></span> <span id="end-date-caret" class="end-time-caret caret"></span>
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
                                <strong>Birthday:</strong> <span id="main-birth-date-header" class="main-birth-header"></span> <span id="birth-date-caret" class="birth-time-caret caret"></span>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addUser()" id="add-user-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Add User Javascript -->
<script type="text/javascript">

    // setup variables
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    month = format2Digit(month);
    day = format2Digit(day);
    var today = d.getFullYear()+"-"+month+"-"+day;

    // prevent copy cut paste
    $('.add-user-input, .numbers-only, .decimal-numbers, .alphanumeric').bind("copy cut paste",function(e) {
          e.preventDefault();
    });

    $(".select-type").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    $('#paytype').change(function() {

        // IF the paytype is Hourly, show the Hourly payrate input, hide the salary input
        if ($('#paytype').val() == 1)
        {
            hide('.salary-row', 500);
            hide('.payrate-row');
            $(".payrate-row").removeClass("hide");
            show('.payrate-row', 500);
        }
        // IF the paytype is Hourly, show the Hourly payrate input, hide the salary input
        else if ($('#paytype').val() == 2)
        {
            hide('.payrate-row', 500);
            hide('.salary-row');
            $(".salary-row").removeClass("hide");
            show('.salary-row', 500);
        }
        else{return;}
    });

    $(".add-user-input").keydown(function (e) {

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


    // set the start date to today
    $('.startDate').val(today);

    // set the end date to nothing
    $('.endDate').val("0000-00-00");

    // set the birth date to nothing
    $('.birthDate').val("0000-00-00");


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

</script>