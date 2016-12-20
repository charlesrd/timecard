<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";
$in_functions = (isset($_POST['fnct'])) ? preg_replace("/[^0-9]/", "", $_POST['fnct']) : "";

// setup variables
$today = date('Y-m-d');
$weeksRemaining = true;
$WEEKS = array();
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$EMPLOYEE_TIME = array();
$OVERTIME_ID = array();
$OVERTIME_NAME = array();
$OVERTIME_TIME = array();


// check whether or not to include the timecard functions
if ($in_functions == 1)
{
    // include functions
    // include("timecard_functions.php");
}


// get the start and end dates for this week
list($start_date, $end_date) = x_week_range($today);

// get the list of all the active Employees from this company, sorted by first name
$sql = "SELECT 
        E.id,
        CONCAT(E.first_name,' ',E.last_name) AS name
        FROM Employee E
        WHERE E.companyID IN 
        (
            SELECT CA.cid 
            FROM Company_Administrator CA
            WHERE CA.aid = $adminID
        )
        AND E.active = 1
        ORDER BY E.first_name ASC";

$query = mysql_query($sql);

while($user = mysql_fetch_object($query))
{
    $EMPLOYEE_ID[] = $user->id;
    $EMPLOYEE_NAME[] = $user->name;
}

// get the total hours for this week for each employee in the company
$i = 0;
foreach ($EMPLOYEE_ID as $eid)
{
    $sql = "SELECT
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) )
                FROM Employee_Hours H
                WHERE H.date >= '$start_date'
                AND H.date <= '$end_date' 
                AND H.eid = $eid
                AND H.clock_out IS NOT NULL
            ) AS totalRegularTime,
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( V.time ) ) ) AS totalVacationTime
                FROM Vacation_Time V
                WHERE V.date >= '$start_date'
                AND V.date <= '$end_date' 
                AND V.eid = $eid
            ) AS totalVacationTime,
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( HT.time ) ) ) AS totalHolidayTime
                FROM Holiday_Time HT
                WHERE HT.date >= '$start_date'
                AND HT.date <= '$end_date' 
                AND HT.eid = $eid
            ) AS totalHolidayTime,
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( PT.time ) ) ) AS totalPersonalTime
                FROM Personal_Time PT
                WHERE PT.date >= '$start_date'
                AND PT.date <= '$end_date' 
                AND PT.eid = $eid
            ) AS totalPersonalTime";

    $query = mysql_query($sql);
    $total = mysql_fetch_object($query);
    $totalRegularTime = (is_null($total->totalRegularTime)) ? "00:00:00" : $total->totalRegularTime;
    $totalVacationTime = (is_null($total->totalVacationTime)) ? "00:00:00" : $total->totalVacationTime;
    $totalHolidayTime = (is_null($total->totalHolidayTime)) ? "00:00:00" : $total->totalHolidayTime;
    $totalPersonalTime = (is_null($total->totalPersonalTime)) ? "00:00:00" : $total->totalPersonalTime;

    $totalRegularTimeWeek = (is_null($total->totalRegularTime)) ? "" : totalTime($total->totalRegularTime);
    $totalVacationTimeWeek = (is_null($total->totalVacationTime)) ? "" : totalTime($total->totalVacationTime);
    $totalHolidayTimeWeek = (is_null($total->totalHolidayTime)) ? "" : totalTime($total->totalHolidayTime);
    $totalPersonalTimeWeek = (is_null($total->totalPersonalTime)) ? "" : totalTime($total->totalPersonalTime);

    $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( '$totalRegularTime' ) + TIME_TO_SEC( '$totalVacationTime' ) + TIME_TO_SEC( '$totalHolidayTime' ) + TIME_TO_SEC( '$totalPersonalTime' ) ) ) AS totalTime";

    $query = mysql_query($sql);
    $total = mysql_fetch_object($query);
    $totalTimeWeek = ($total->totalTime == "") ? "" : totalTime($total->totalTime);
    $EMPLOYEE_TIME[] = $totalTimeWeek;

    // split the total time for the week
    if ($total->totalTime != "")
    {
        $totalTimeWeekArray = explode(":", $total->totalTime);
        $totalTimeWeekHours = $totalTimeWeekArray[0];
        $totalTimeWeekMinutes = $totalTimeWeekArray[1];
        $totalTimeWeekSeconds = $totalTimeWeekArray[2];
    }
    else
    {
        $totalTimeWeekHours = 0;
        $totalTimeWeekMinutes = 0;
        $totalTimeWeekSeconds = 0;
    }

    // determine if the employee is on track to get overtime hours this week
    // checks IF the employee has over 32 hours OR they have 32 hours and some minutes
    if ( (intval($totalTimeWeekHours) > 32) || ((intval($totalTimeWeekHours) == 32) && (intval($totalTimeWeekMinutes) > 0)) )
    {
        $OVERTIME_ID[] = $eid;
        $OVERTIME_NAME[] = $EMPLOYEE_NAME[$i];
        $OVERTIME_TIME[] = $totalTimeWeek;
    }

    // update the counter
    $i++;
}





?>
<div class="modal fade" id="warning-messages-modal" tabindex="-1" role="dialog" aria-labelledby="warning-messages-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                <h2 class="modal-title" id="warning-messages-modal-title"><span class="fa fa-warning"></span> <strong>Attention:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="warning-messages-row" class="row">
                        <div class="col-xs-12">

                            <!-- Overtime Hours Alert Table -->
                            <table id="overtime-hours-table" class="table table-striped table-bordered table-hover text-center">
                                <thead>
                                    <tr class="warning">
                                        <td colspan="2">Overtime Hours Alert</td>
                                    </tr>
                                    <tr class="info">
                                        <td>Name</td>
                                        <td>Hours</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $i = 0;
                                    // print the Employee table records
                                    foreach ($OVERTIME_ID as $oid)
                                    {
                                        print '<tr><td><a href="#" onclick="changeUser('.$oid.')">'.$OVERTIME_NAME[$i].'</a></td><td>'.$OVERTIME_TIME[$i].'</td></tr>';

                                        // update the counter
                                        $i++;
                                    }

                                    // print the Employee total as the table footer 
                                    print '<tr class="success table-total"><td class="text-right"><h3>Total Employees:</h3></td><td class="text-center"><h3>'.count($OVERTIME_ID).'</h3></td></tr>';

                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" onclick="closeModal()" id="warning-messages-done" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-thumbs-up"></span> OK</a>
            </div>
        </div>
    </div>
</div>

<!-- Internal Admin Warning Messages Javascript -->
<script type="text/javascript">

$(document).ready(function() {

$(document).keypress(function(e) {
    if(e.which == 13) {
        e.preventDefault();
        return false;
    }
});

<?php 

if (count($OVERTIME_ID) > 0)
{
    // show the warning-message modal
    print 'setTimeout(function ()
    {
        $("#warning-messages-modal").modal(
        {
            backdrop: \'static\',
            show: true
        });
    }, 1000);';
}

 ?>



});

</script>