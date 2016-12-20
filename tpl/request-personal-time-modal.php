<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";

// setup variables
$today = date('Y-m-d');
$weeksRemaining = true;
$WEEKS = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;


// get the user information
$sql = "SELECT *
        FROM Employee E
        WHERE E.id = $userID";

$query = mysql_query($sql);
$r = mysql_fetch_object($query);


?>
<div class="modal fade" id="request-personal-time-modal" tabindex="-1" role="dialog" aria-labelledby="request-personal-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="request-personal-time-modal-title"><strong>Personal Days</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#add-personal-time-tab" onclick="toggleAddItemTab()" aria-controls="add-personal-time-tab" role="tab" data-toggle="tab">Add New</a></li>
                        <li role="presentation"><a href="#requested-personal-time-tab" onclick="toggleAddItemTab()" aria-controls="requested-personal-time-tab" role="tab" data-toggle="tab">Requested Personal Days</a></li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="add-personal-time-tab">

                            <div id="request-personal-time-row" class="row">
                                <div class="col-xs-12">
                                    <br>
                                    <p><strong>Personal Days Used: &nbsp; <span id="personal-days-used"></span></strong></p>
                                    <div class="alert alert-success" role="alert">
                                        <strong>Personal Days Remaining: &nbsp; <span id="personal-days-remaining"></span></strong><br>
                                        <strong>Anniversary Date: &nbsp; <?php print date("M j",strtotime($r->start_date)); ?></strong>
                                    </div>
                                    <div id="request-personal-time-datepicker"></div>
                                </div>
                            </div>

                            <div id="request-personal-time-time-row" class="row"> 
                                <div class="col-xs-12">
                                    <button id="request-personal-time-modal-button" class="btn btn-default personal-time-dropdown" onclick="toggle('#main-personal-time',500)">
                                        <strong>Choose a Time:</strong> <span id="main-personal-time-header" class="main-personal-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="personal-time-caret" class="caret"></span>
                                    </button>
                                    <div id="main-personal-time">
                                        <div class="row">
                                            <div class="col-xs-4">
                                                <select id="personal-time-hour" name="personal-time-hour" class="form-control input-lg select-time">
                                                    <?php
                                                    $default = $DEFAULT_HR;
                                                    for ($i=1; $i<=8; $i++)
                                                    {
                                                        if ($i == $default) {print '<option value="'.$i.'" selected="selected">'.$i.'</option>';}
                                                        else {print '<option value="'.$i.'">'.$i.'</option>';}
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-2 personal-time-time-label"><span>hr</span></div>
                                            <div class="col-xs-4">
                                                <select id="personal-time-minute" name="personal-time-minute" class="form-control input-lg select-time">
                                                    <?php
                                                    for ($i=0; $i<=45; $i+=15)
                                                    {
                                                        print '<option value="'.$i.'">'.$i.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-2 personal-time-time-label"><span>min</span></div>
                                        </div>
                                    </div>

                                    <input class="startDate" type="hidden" id="startPersonalDate" name="startPersonalDate" value="" />
                                </div>
                            </div>

                            <div id="request-personal-time-note-label-row" class="row">
                                <div class="col-xs-12 margin-top-10">
                                    <label class="control-label" for="personal-time-note">Note: </label>
                                </div>
                            </div>

                            <div id="request-personal-time-note-row" class="row">
                                <div class="col-xs-12">
                                    <textarea id="personal-time-note" rows="3" class="form-control input-lg" placeholder="Type your note here..." value=""></textarea>
                                    <span id="span-request-personal-time"></span>
                                </div>
                            </div>

                        </div>

                        <div role="tabpanel" class="tab-pane" id="requested-personal-time-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Employee Note</th>
                                            <th>Admin Note</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                    $STATUS = array("Pending", "Approved", "Disapproved");
                                    $STATUS_CLASS = array("", "success", "danger");

                                    // get the requested personal days
                                    // status: 0 = Not yet reviewed, 1 = Approved, 2 = Disapproved
                                    $sql = "SELECT R.id,
                                            R.time,
                                            R.date,
                                            R.status,
                                            R.employee_note,
                                            R.admin_note
                                            FROM Employee E, Request_Personal_Time R
                                            WHERE E.id = R.eid
                                            AND E.id = $userID
                                            ORDER BY R.date DESC";

                                    $query = mysql_query($sql);
                                    while ($personal_day = mysql_fetch_object($query))
                                    {
                                        $time = explode(':', $personal_day->time);

                                        $remove_button = ($personal_day->status == 0) ? '<button class="btn btn-xs btn-danger" onclick="removePersonalRequestTime('.$personal_day->id.')"><span class="fa fa-remove"></span></button>' : '';

                                        print '<tr id="personal-request-'.$personal_day->id.'" class="'.$STATUS_CLASS[$personal_day->status].'">
                                                  <td>'.$remove_button.'</td>
                                                  <td>'.date("M j Y", strtotime($personal_day->date)).'</td>
                                                  <td>'.str_replace(" 0 min", "", totaltime($personal_day->time)).'</td>
                                                  <td>'.$personal_day->employee_note.'</td>
                                                  <td>'.$personal_day->admin_note.'</td>
                                                  <td>'.$STATUS[$personal_day->status].'</td>
                                               </tr>';
                                    }

                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="requestPersonalTime()" id="request-personal-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-child fa-lg"></span> Request Personal Time</button>
            </div>
        </div>
    </div>
</div>

<!-- Internal Request Personal Time Javascript -->
<script type="text/javascript">
    
    // setup variables
    var holidays = ['01-01', '07-04', '12-25'];
    var CURRENT_YEAR = new Date().getFullYear();
    var CURRENT_DATE = new Date().toISOString().slice(0, 10);
    var DEFAULT_HR = 8;
    var DEFAULT_MIN = 0;
    var ANNIVERSARY_DATE = <?php print "'".date("m-d",strtotime($r->start_date))."'"; ?>


    $("#personal-time-hour").change(function() {

        if ($("#personal-time-hour").val() == 8)
        {
            $("#personal-time-minute").val(0);
            $("#personal-time-minute").attr("disabled", true);
        }
        else
        {
            $("#personal-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#personal-time-hour').val();
        $('#main-personal-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#personal-time-minute').change(function() {
        DEFAULT_MIN = $('#personal-time-minute').val();
        $('#main-personal-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $('#personal-time-modal-button').click(function() {
        if ($('#personal-time-caret').hasClass('caret-reversed'))
        {
            $('#personal-time-caret').removeClass('caret-reversed');
        }
        else {$('#personal-time-caret').addClass('caret-reversed');}
    });


    // set the selected year to this year
    $(".selected-year").html(CURRENT_YEAR);

    
    // find the personal days remaining for this user this year
    findPersonalDaysRemaining(CURRENT_DATE);


    // find the personal days used for this user this year
    findPersonalDaysUsed(CURRENT_DATE);


    // find the floating holidays for this year
    findFloatingHolidays();

    function findFloatingHolidays(year)
    {
        // setup variables
        var sd = new Date();
        year = arguments[0] || sd.getFullYear();
        var holiday = "";


        /* Memorial Day */
        var d = new Date(year, 4, 31);

        // get the last Monday in the month
        while (d.getDay() !== 1) {d.setDate(d.getDate()-1);}

        // add the holiday to the holiday array
        holiday = (format2Digit(d.getMonth()+1))+'-'+(format2Digit(d.getDate()));
        holidays.push(holiday);


        /* Labor Day */
        var d = new Date(year, 8, 1);

        // get the first Monday in the month
        while (d.getDay() !== 1) {d.setDate(d.getDate()+1);}

        // add the holiday to the holiday array
        holiday = (format2Digit(d.getMonth()+1))+'-'+(format2Digit(d.getDate()));
        holidays.push(holiday);


        /* Thanksgiving Day */
        var d = new Date(year, 10, 1);

        // get the first Thursday in the month
        while (d.getDay() !== 4) {d.setDate(d.getDate()+1);}

        // get the fourth Thursday in the month
        d.setDate(d.getDate()+21);

        // add the holiday to the holiday array
        holiday = (format2Digit(d.getMonth()+1))+'-'+(format2Digit(d.getDate()));
        holidays.push(holiday);
    }

    function format2Digit(n) {return n < 10 ? '0' + n : n;}


    $("#request-personal-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            var selectedDate = $.datepicker.formatDate("yy-mm-dd", date);
            var selectedDay = date.getDay();
            var selectedMonth = date.getMonth();
            var selectedYear = date.getFullYear();
            var cutoffDate = new Date();
            var daysAhead = 14;
            var dateAhead = daysAhead-1;
            cutoffDate.setDate(cutoffDate.getDate() + dateAhead);
            var errors = "";

            // find the personal days remaining for this user for the selected date
            findPersonalDaysRemaining(selectedDate);

            // find the personal days used for the selected year
            findPersonalDaysUsed(selectedDate);

            // clear the holidays array
            while(holidays.length > 0) {holidays.pop();}

            // add the static holidays
            holidays.push('01-01');
            holidays.push('07-04');
            holidays.push('12-25');

            // add the floating holidays for the selected year
            findFloatingHolidays(selectedYear);

            // set the selected year to the selected year
            $(".selected-year").html(selectedYear);

            // remove any errors that are showing
            $("#span-request-personal-time").html('').show();

            // check IF the selected date is less than the cutoff date
            if (date < cutoffDate)
            {
                errors += '<div class="alert alert-danger"><strong>Please Select a Date at least '+daysAhead+' days in the future.</strong></div>';
            }
            
            // check IF the selected date is a weekend
            // if (!( (selectedDay > 0) && (selectedDay < 6) ))
            // {
            //     errors += '<div class="alert alert-danger"><strong>Weekends can&apos;t be selected for Personal Time.</strong></div>';
            // }

            // check IF the selected date is one day BEFORE a holiday
            var dayBeforeHoliday = new Date();
            dayBeforeHoliday.setFullYear(selectedYear, selectedMonth, date.getDate() + 1);
            var selectedDate = $.datepicker.formatDate("mm-dd", dayBeforeHoliday);

            console.log("Day Before: "+selectedDate);

            if ($.inArray(selectedDate, holidays) >= 0)
            {
                errors += '<div class="alert alert-danger"><strong>The Selected Date can&apos;t be one day before a Holiday.</strong></div>';
            }

            // check IF the selected date is a holiday
            var selectedDate = $.datepicker.formatDate("mm-dd", date);

            console.log("Day: "+selectedDate);

            if ($.inArray(selectedDate, holidays) >= 0)
            {
                errors += '<div class="alert alert-danger"><strong>The Selected Date can&apos;t be a Holiday.</strong></div>';
            }

            // check IF the selected date is one day AFTER a holiday
            var dayAfterHoliday = new Date();
            dayAfterHoliday.setFullYear(selectedYear, selectedMonth, date.getDate() - 1);
            var selectedDate = $.datepicker.formatDate("mm-dd", dayAfterHoliday);

            console.log("Day After: "+selectedDate);

            if ($.inArray(selectedDate, holidays) >= 0)
            {
                errors += '<div class="alert alert-danger"><strong>The Selected Date can&apos;t be one day after a Holiday.</strong></div>';
            }

            console.log("holidays: "+holidays);

            // IF there are any errors, display them
            if (errors != "")
            {
                $(errors).hide().appendTo('#span-request-personal-time').slideDown(500);
                
                // disable the done buttons
                $('.btn-done').attr("disabled", true);
            }
            else // ELSE, choose the selected date
            {
                selectedDate = $.datepicker.formatDate("yy-mm-dd", date);
                $('.startDate').val(selectedDate);
                $('.btn-done').removeAttr('disabled');
            }
        }
    });

</script>