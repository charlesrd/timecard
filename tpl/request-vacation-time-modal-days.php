<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";

// setup variables
$today = date('Y-m-d');
$daysRemaining = true;
$DAYS = array();


?>
<div class="modal fade" id="request-vacation-time-modal" tabindex="-1" role="dialog" aria-labelledby="request-vacation-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="request-vacation-time-modal-title"><strong>Choose Vacation Day(s):</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="request-vacation-time-row" class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-success" role="alert"><strong>Vacation Days Remaining in <span class="selected-year"></span>: &nbsp; <span id="vacation-days-remaining"></span></strong></div>
                            <div id="request-vacation-time-datepicker"></div>
                            <input class="startDate" type="hidden" id="startVacationDate" name="startVacationDate" value="" />
                            <span id="span-request-vacation-time"></span>
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

<!-- Internal Request Vacation Time Javascript -->
<script type="text/javascript">

    // setup variables
    var holidays = ['01-01', '07-04', '12-25'];
    var CURRENT_YEAR = new Date().getFullYear();
    var startDate;
    var endDate;


    // set the selected year to this year
    $(".selected-year").html(CURRENT_YEAR);

    
    // find the vacation weeks remaining for this user this year
    findVacationDaysRemaining(CURRENT_YEAR);


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

    $('#request-vacation-time-datepicker').datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
            endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
            var selectedDay = startDate.getDay();
            var selectedMonth = startDate.getMonth();
            var selectedYear = startDate.getFullYear();
            var cutoffDate = new Date();
            var daysAhead = 14;
            var dateAhead = daysAhead-1;
            cutoffDate.setDate(cutoffDate.getDate() + dateAhead);
            var errors = "";


            // find the vacation days remaining for this user for the selected year
            findVacationDaysRemaining(selectedYear);

            // set the selected year to the selected year
            $(".selected-year").html(selectedYear);

            // remove any errors that are showing
            $("#span-request-vacation-time").html('').show();

            // check IF the selected startDate is less than the cutoff date
            if (startDate < cutoffDate)
            {
                errors += '<div class="alert alert-danger"><strong>Please Select days at least '+daysAhead+' days in the future.</strong></div>';
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
                $('.startDate').val($.datepicker.formatDate("yy-mm-dd", startDate));
                $('.btn-done').removeAttr('disabled');
            }

            selectCurrentDay();
        },
        beforeShowDay: function(date) {
            var cssClass = '';
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';
            return [true, cssClass];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentDay();
        }
    });

    $('#request-vacation-time-datepicker .ui-datepicker-calendar tr').on('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('#request-vacation-time-datepicker .ui-datepicker-calendar tr').on('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });



</script>