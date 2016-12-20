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
<div class="modal fade" id="change-day-modal" tabindex="-1" role="dialog" aria-labelledby="change-day-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="change-day-modal-title"><strong>Choose a Day:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="view-history-row" class="row">
                        <div class="col-xs-12">
                            <div id="change-day-datepicker"></div>
                            <input class="startDate" type="hidden" id="startDayDate" name="startDayDate" value="2014-05-08" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="changeDay()" id="change-day-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-bar-chart-o"></span> Change Day</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Change Day Javascript -->
<script type="text/javascript">

    // setup variables
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    month = format2Digit(month);
    day = format2Digit(day);
    var today = d.getFullYear()+"-"+month+"-"+day;


    $("#change-day-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('.btn-done').removeAttr('disabled');
        }
    });


    // set the start date to today for everything with the startDate class
    // $('.startDate').val(today);

</script>