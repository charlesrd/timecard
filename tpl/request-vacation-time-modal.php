<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";

// setup variables
$today = date('Y-m-d');
$daysRemaining = true;
$DAYS = array();


// get the user information
$sql = "SELECT *
        FROM Employee E
        WHERE E.id = $userID";

$query = mysql_query($sql);
$r = mysql_fetch_object($query);


?>
<div class="modal fade" id="request-vacation-time-modal" tabindex="-1" role="dialog" aria-labelledby="request-vacation-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="request-vacation-time-modal-title"><strong>Choose a day(s):</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#add-vacation-time-tab" onclick="toggleAddItemTab()" aria-controls="add-vacation-time-tab" role="tab" data-toggle="tab">Add New</a></li>
                        <li role="presentation"><a href="#requested-vacation-time-tab" onclick="toggleAddItemTab()" aria-controls="requested-vacation-time-tab" role="tab" data-toggle="tab">Requested Vacation Days</a></li>
                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="add-vacation-time-tab">

                            <div id="request-vacation-time-row" class="row">

                                <div class="col-xs-12">
                                    <br>
                                    <p><strong>Vacation Days Used: &nbsp; <span id="vacation-days-used"></span></strong></p>
                                    <div class="alert alert-success" role="alert">
                                        <strong>Vacation Days Remaining: &nbsp; <span id="vacation-days-remaining"></span></strong><br>
                                        <strong>Anniversary Date: &nbsp; <?php print date("M j",strtotime($r->start_date)); ?></strong>
                                    </div>
                                    <div id="request-vacation-time-datepicker"></div>
                                    <input class="startDate" type="hidden" id="startVacationDate" name="startVacationDate" value="" />
                                    <span id="span-request-vacation-time"></span>
                                </div>

                                <div class="col-xs-12 margin-top-10">
                                    <label class="control-label" for="vacation-employee-note">Note: </label>
                                </div>

                                <div class="col-xs-12">
                                    <textarea id="vacation-employee-note" rows="3" class="form-control input-lg" placeholder="Type your note here..." value=""></textarea>
                                </div>

                            </div>

                        </div>

                        <div role="tabpanel" class="tab-pane" id="requested-vacation-time-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Date</th>
                                            <th>Employee Note</th>
                                            <th>Admin Note</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php

                                    $STATUS = array("Pending", "Approved", "Disapproved");
                                    $STATUS_CLASS = array("", "success", "danger");

                                    // get the requested vacation days
                                    // status: 0 = Not yet reviewed, 1 = Approved, 2 = Disapproved
                                    $sql = "SELECT R.id,
                                            (
                                                SELECT GROUP_CONCAT(D.date SEPARATOR ',') AS dates
                                                FROM Request_Vacation_Date D
                                                WHERE D.rvtid = R.id
                                            ) AS dates,
                                            R.status,
                                            R.employee_note,
                                            R.admin_note
                                            FROM Employee E, Request_Vacation_Time R
                                            WHERE E.id = R.eid
                                            AND E.id = $userID
                                            ORDER BY R.id DESC";

                                    $query = mysql_query($sql);
                                    while ($vacation_day = mysql_fetch_object($query))
                                    {
                                        $time = explode(':', $vacation_day->time);

                                        $DATES = explode(',', $vacation_day->dates);
                                        $dates = "";
                                        foreach ($DATES as $d)
                                        {
                                            $dates .= date("M j Y", strtotime($d))."<br>";
                                        }

                                        rtrim($dates, "<br>");

                                        $remove_button = ($vacation_day->status == 0) ? '<button class="btn btn-xs btn-danger" onclick="removeVacationRequestTime('.$vacation_day->id.')"><span class="fa fa-remove"></span></button>' : '';

                                        print '<tr id="vacation-request-'.$vacation_day->id.'" class="'.$STATUS_CLASS[$vacation_day->status].'">
                                                  <td>'.$remove_button.'</td>
                                                  <td>'.$dates.'</td>
                                                  <td>'.$vacation_day->employee_note.'</td>
                                                  <td>'.$vacation_day->admin_note.'</td>
                                                  <td>'.$STATUS[$vacation_day->status].'</td>
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
                <button type="button" onclick="requestVacationTime()" id="request-vacation-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-sun-o fa-lg"></span> Request Vacation Time</button>
            </div>
        </div>
    </div>
</div>

<?php

// get the number of vacation days remaining for this user for this in_year
$sql = "SELECT E.vacation_days-COUNT(*) AS count 
        FROM Vacation_Time V, Employee E 
        WHERE V.eid = $userID
        AND V.eid = E.id
        AND V.date LIKE '".date('Y')."%'";

$query = mysql_query($sql);
$vacationTime = mysql_fetch_object($query);
$vacationDaysRemaining = ($vacationTime->count < 0) ? 0 : $vacationTime->count;


?>

<!-- Internal Request Vacation Time Javascript -->
<script type="text/javascript">

    // setup variables
    var holidays = ['01-01', '07-04', '12-25'];
    var CURRENT_YEAR = new Date().getFullYear();
    var CURRENT_DATE = new Date().toISOString().slice(0, 10);
    var DATES = [];
    var SELECTED_DATES = [];
    var startDate;
    var endDate;


    // set the selected year to this year
    $(".selected-year").html(CURRENT_YEAR);

    
    // find the vacation days remaining for this user this year
    findVacationDaysRemaining(CURRENT_DATE);


    // find the vacation days used for this user this year
    findVacationDaysUsed(CURRENT_DATE);


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


    var selectCurrentDay = function() {
        window.setTimeout(function () {
            $('#request-vacation-time-datepicker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }




    $('#request-vacation-time-datepicker').multiDatesPicker({
        dateFormat: "yy-mm-dd",
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            var selectedDate = $.datepicker.formatDate("yy-mm-dd", date);
            var selectedDay = date.getDay();
            var selectedMonth = date.getMonth();
            var selectedYear = date.getFullYear();
            var cutoffDate = new Date();
            var daysAhead = 30;
            var dateAhead = daysAhead-1;
            cutoffDate.setDate(cutoffDate.getDate() + dateAhead);
            var errors = "";
            

            // add or remove the selected date from the arrays
            if ($.inArray(selectedDate, SELECTED_DATES) !== -1)
            {
                SELECTED_DATES = jQuery.grep(SELECTED_DATES,function(e) {return e != selectedDate;});
                DATES.splice(DATES.indexOf(date), 1);
            }
            else
            {
                SELECTED_DATES.push(selectedDate);
                DATES.push(date);
            }


            // find the vacation days remaining for this user for the selected year
            findVacationDaysRemaining(selectedDate);

            // find the vacation days used for the selected year
            var days_used = findVacationDaysUsed(selectedDate);

            // set the selected year to the selected year
            $(".selected-year").html(selectedYear);

            // remove any errors that are showing
            $("#span-request-vacation-time").html('').show();


            // check IF any selected date is less than the cutoff date
            for (var i = 0; i < DATES.length; i++)
            {
                if (DATES[i] < cutoffDate)
                {
                    errors += '<div class="alert alert-danger"><strong>Please Select Dates at least '+daysAhead+' days in the future.</strong></div>';
                }
            }


            console.log("days_used: "+days_used);

            
            // check IF the days_used is equal to zero
            if (days_used == "0")
            {
                errors = '<div class="alert alert-danger"><strong>Please select days only in one anniversary year timeframe.</strong></div>';
            }


            // IF there are any errors, display them
            if (errors != "")
            {
                $(errors).hide().appendTo('#span-request-vacation-time').slideDown(500);
                
                // disable the done buttons
                $('.btn-done').attr("disabled", true);
            }
            else // ELSE, choose the selected date
            {
                $('.startDate').val($(this).multiDatesPicker('value'));
                $('.btn-done').removeAttr('disabled');
            }

            // selectCurrentDay();
        }
    });

</script>