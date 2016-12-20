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
                            <input class="startDate" type="hidden" id="startDate" name="startDate" value="2014-05-08" />
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