<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";

// setup variables
$today = date('Y-m-d');
$weeksRemaining = true;
$WEEKS = array();


if ($in_uid != "") // IF the in_uid is not empty, reset the userID to in_uid
{
    $userID = $in_uid;

    // include functions
    // include("timecard_functions.php");
}

?>
<div class="modal fade" id="change-week-modal" tabindex="-1" role="dialog" aria-labelledby="change-week-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="change-week-modal-title"><strong>Choose a week:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="change-week-row" class="row">
                        <div class="col-xs-12">
                            <div class="week-picker"></div>
                            <input type="hidden" id="startDate" name="startDate" value="2014-05-08" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="changeWeek()" id="change-week-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-bar-chart-o"></span> Change Week</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Javascript -->
<script type="text/javascript">
$(document).ready(function(){

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
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
            $('#startDate').val($.datepicker.formatDate( dateFormat, startDate, inst.settings ));

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

    $('.week-picker .ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
    $('.week-picker .ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
    
    $('.week-picker .ui-datepicker-calendar tr').click(function(){
        $('.btn-done').removeAttr('disabled');
    });
});
</script>