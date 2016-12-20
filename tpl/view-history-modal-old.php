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
<div class="modal fade" id="view-history-modal" tabindex="-1" role="dialog" aria-labelledby="view-history-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="view-history-modal-title"><strong>Choose a week:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="view-history-row" class="row">
                        <?php

                            // get the oldest date this user worked
                            $sql = "SELECT H.date
                                    FROM Employee_Hours H
                                    WHERE H.eid = $userID
                                    ORDER BY H.date ASC
                                    LIMIT 1";
                            $query = mysql_query($sql);
                            $record = mysql_fetch_object($query);
                            $START_DATE = $record->date;

                            // get the newest date this user worked
                            $sql = "SELECT H.date
                                    FROM Employee_Hours H
                                    WHERE H.eid = $userID
                                    ORDER BY H.date DESC
                                    LIMIT 1";
                            $query = mysql_query($sql);
                            $record = mysql_fetch_object($query);
                            $END_DATE = $record->date;

                            // IF the user doesn't have any hours worked yet, set weeksRemaining to false
                            if ( ($START_DATE == "") || ($END_DATE == "") ) {$weeksRemaining = false;}
                            else // ELSE get the start and end dates for the user's first week
                            {
                                // get the dates of the starting week that was worked
                                list($start_date, $end_date) = x_week_range($START_DATE);
                                $start_date_modified = preg_replace('/-/', ' ', $start_date, 1);
                                $end_date_modified = preg_replace('/-/', ' ', $end_date, 1);
                            }

                            $i = 1;
                            while ($weeksRemaining)
                            {
                                $WEEKS[] = '<div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-2 col-radio">
                                                    <input id="view-history-week-'.$i.'" name="view-history-week" class="input-lg" type="radio" value="'.$start_date.'" />
                                                </div>
                                                <div class="col-xs-10 col-label">
                                                    <label id="view-history-week-'.$i.'-label" class="modal-label" for="view-history-week-'.$i.'"><h4><strong>'.$start_date_modified.' to '.$end_date_modified.'</strong></h4></label>
                                                </div>
                                            </div>
                                        </div>';

                                // setup variables
                                $end_date_plus_1 = date('Y-m-d', strtotime($end_date." + 1 day"));
                                
                                // get the dates of the starting week that was worked
                                list($start_date, $end_date) = x_week_range($end_date_plus_1);
                                $start_date_modified = preg_replace('/-/', ' ', $start_date, 1);
                                $end_date_modified = preg_replace('/-/', ' ', $end_date, 1);

                                // update the counter
                                $i++;

                                // IF the new current start_date > the END_DATE, then break the loop
                                if (strtotime($start_date) > strtotime($END_DATE)) {$weeksRemaining = false;}
                            }

                            // print out the user's weeks from newest to oldest
                            $WEEKS_SIZE = count($WEEKS);
                            for ($i=$WEEKS_SIZE-1; $i>=0; $i--)
                            {
                                // IF this is the newest week worked, change the start and end date wording
                                if (($i+1) == $WEEKS_SIZE)
                                {
                                    $WEEKS[$i] = preg_replace("/<strong>(.*)<\/strong>/","<strong>Newest Week</strong>", $WEEKS[$i]);
                                }

                                print $WEEKS[$i];
                            }

                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="viewHistory()" id="view-history-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-bar-chart-o"></span> View History</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Javascript -->
<script type="text/javascript">
$(document).ready(function(){

    $('input:radio').change(function(){
        $('.btn-done').removeAttr('disabled');
    });
});
</script>