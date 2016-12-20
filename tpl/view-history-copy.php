<?php


require_once("config.php");


// setup variables
$today = date('Y-m-d');
$pageTitle = "";
$tableHeader = "";
$totalColSpan = 0;
$EID = array();
$EMPLOYEE = array();
$SORT_TYPE = array();
$SORT_ORDER = array();
$sort_order = "ASC";
$CARET = array();
$caret = "";

// get the incoming variables
$in_report = (isset($_POST['r'])) ? preg_replace("/[^-0-9]/", "", $_POST['r']) : "";
$in_sort_type = (isset($_POST['st'])) ? preg_replace("/[^0-9]/", "", $_POST['st']) : 1;
$in_sort_order = (isset($_POST['so'])) ? preg_replace("/[^0-1]/", "", $_POST['so']) : 1;

// set the sort_order
$sort_order = ($in_sort_order == 1) ? "ASC" : "DESC";


// IF the in_report == 1, get the Employees on break
if ($in_report == 1)
{
    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employees on Break";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'">Employees on Break</td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Break Start Time</a>'.$CARET[3].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time');

    // get names and break start times of the Employees on break
    $sql = "SELECT CONCAT(E.first_name,' ',E.last_name) AS name, B.start_time, C.name AS company
    FROM On_Break B, Employee E, Company C
    WHERE B.end_time IS NULL
    AND E.companyID = C.id 
    AND E.id = B.eid 
    AND B.date = '$today'
    ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EMPLOYEE[] = "<tr><td>".$user->name."</td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td></tr>";
    }
}
elseif ($in_report == 2) // IF the in_report == 2, get the Employees gone for the day
{
    // setup variables
    $totalTitleSpan = 5;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employees Gone for the Day";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'">Employees Gone for the Day</td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">End Time</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">Total</a>'.$CARET[5].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'end_time', 'total');

    // get names, start/end times, company and total hours worked of the Employees gone for the day
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            H.clock_in AS start_time, 
            D.end_time, 
            C.name AS company,
            (
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS total
                FROM Employee_Hours H2
                WHERE H2.date = '$today' 
                AND H2.eid = E.id
                AND H2.clock_out IS NOT NULL
            ) AS total
                FROM Is_Done D, Employee E, Employee_Hours H, Company C
                WHERE E.id = D.eid 
                AND H.clock_in = (
                                    SELECT H3.clock_in 
                                    FROM Employee_Hours H3 
                                    WHERE H3.date = '$today'
                                    AND H3.eid = E.id
                                    ORDER BY H3.eid ASC
                                    LIMIT 1
                                 )
                AND H.date = D.date
                AND D.date = '$today'
                AND E.companyID = C.id 
                ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $EMPLOYEE[] = "<tr><td>".$user->name."</td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td><td>".date("g:i a", strtotime($today." ".$user->end_time))."</td><td>".totalTime($user->total)."</td>";
    }

    // $i = 0;
    // foreach ($EID as $userID)
    // {
    //     // total hours worked for the current date
    //     $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) ) AS totalTime
    //             FROM Employee_Hours H
    //             WHERE H.date = '$today' 
    //             AND H.eid = $userID
    //             AND H.clock_out IS NOT NULL";

    //     $query = mysql_query($sql);
    //     $total = mysql_fetch_object($query);
    //     $totalTimeDay = totalTime($total->totalTime);

    //     // finish creating the employee record in the table
    //     $EMPLOYEE[$i] .= "<td>".$totalTimeDay."</td></tr>";

    //     // update the counter
    //     $i++;
    // }
}
elseif ($in_report == 3) // IF the in_report == 3, get the Active Employees
{
    // setup variables
    $totalTitleSpan = 9;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Active Employees";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'">Active Employees</td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Out</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">In</a>'.$CARET[5].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',6,'.$SORT_ORDER[6].')">Out</a>'.$CARET[6].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',7,'.$SORT_ORDER[7].')">In</a>'.$CARET[7].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',8,'.$SORT_ORDER[8].')">Out</a>'.$CARET[8].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',9,'.$SORT_ORDER[9].')">End Time</a>'.$CARET[9].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',10,'.$SORT_ORDER[10].')">Total</a>'.$CARET[10].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'out1', 'in2', 'out2', 'in3', 'out3', 'end_time', 'total');

    // get id, name, company, and all times of the active Employees
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company, 
            H.clock_in AS start_time, 
            (
                SELECT H4.clock_out 
                FROM Employee_Hours H4 
                WHERE H4.date = '2014-05-16'
                AND H4.eid = E.id
                ORDER BY H4.eid ASC
                LIMIT 1
            ) AS out1,
            (
                SELECT H5.clock_in 
                FROM Employee_Hours H5 
                WHERE H5.date = '2014-05-16'
                AND H5.eid = E.id
                ORDER BY H5.eid ASC
                LIMIT 1, 1
            ) AS in2,
            (
                SELECT H6.clock_out 
                FROM Employee_Hours H6 
                WHERE H6.date = '2014-05-16'
                AND H6.eid = E.id
                ORDER BY H6.eid ASC
                LIMIT 1, 1
            ) AS out2,
            (
                SELECT H7.clock_in 
                FROM Employee_Hours H7 
                WHERE H7.date = '2014-05-16'
                AND H7.eid = E.id
                ORDER BY H7.eid ASC
                LIMIT 2, 2
            ) AS in3,
            (
                SELECT H8.clock_out 
                FROM Employee_Hours H8 
                WHERE H8.date = '2014-05-16'
                AND H8.eid = E.id
                ORDER BY H8.eid ASC
                LIMIT 2, 2
            ) AS out3,
            D.end_time, 
            (
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS total
                FROM Employee_Hours H2
                WHERE H2.date = '2014-05-16' 
                AND H2.eid = E.id
                AND H2.clock_out IS NOT NULL
            ) AS total
                FROM Is_Done D, Employee E, Employee_Hours H, Company C
                WHERE E.id = D.eid 
                AND H.clock_in = (
                                    SELECT H3.clock_in 
                                    FROM Employee_Hours H3 
                                    WHERE H3.date = '2014-05-16'
                                    AND H3.eid = E.id
                                    ORDER BY H3.eid ASC
                                    LIMIT 1
                                 )
                AND H.date = D.date
                AND D.date = '2014-05-16'
                AND E.companyID = C.id 
                ORDER BY name ASC";
                
    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $EMPLOYEE[] = "<tr><td>".$user->name."</td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td><td>".date("g:i a", strtotime($today." ".$user->out1))."</td><td>".date("g:i a", strtotime($today." ".$user->in2))."</td><td>".date("g:i a", strtotime($today." ".$user->out2))."</td><td>".date("g:i a", strtotime($today." ".$user->in3))."</td><td>".date("g:i a", strtotime($today." ".$user->end_time))."</td><td>".totalTime($user->total)."</td>";
    }

    // $i = 0;
    // foreach ($EID as $userID)
    // {
    //     // get the CLOCK_IN and CLOCK_OUT times
    //     $sql = "SELECT H.clock_in, H.clock_out
    //     FROM Employee_Hours H
    //     WHERE H.eid = $userID 
    //     AND H.date = '$today'
    //     ORDER BY H.clock_in ASC";

    //     $count = 0;
    //     $CLOCK_IN = [];
    //     $CLOCK_OUT = [];
    //     $query = mysql_query($sql);
    //     while ($time = mysql_fetch_object($query))
    //     {
    //         // add the current clock_in and clock_out times to an array
    //         $CLOCK_IN[$count] = date("g:i a", strtotime($date." ".$time->clock_in));
    //         $CLOCK_OUT[$count] = ($time->clock_out == "00:00:00") ? "" : date("g:i a", strtotime($date." ".$time->clock_out));
    //         $count++;
    //     }

    //     // total hours worked for the current date
    //     $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) ) AS totalTime
    //             FROM Employee_Hours H
    //             WHERE H.date = '$today' 
    //             AND H.eid = $userID
    //             AND H.clock_out IS NOT NULL";

    //     $query = mysql_query($sql);
    //     $total = mysql_fetch_object($query);
    //     $totalTimeDay = totalTime($total->totalTime);

    //     // setup the variable names
    //     $start_time = $CLOCK_IN[0];
    //     $break_1_start = $CLOCK_OUT[0];
    //     $break_1_end = $CLOCK_IN[1];
    //     $break_2_start = $CLOCK_OUT[1];
    //     $break_2_end = $CLOCK_IN[2];
    //     $end_time = $CLOCK_OUT[2];

    //     // finish creating the employee record in the table
    //     $EMPLOYEE[$i] .= "<td>".$break_1_start."</td><td>".$break_1_end."</td><td>".$break_2_start."</td><td>".$break_2_end."</td><td>".$end_time."</td><td>".$totalTimeDay."</td></tr>";

    //     // update the counter
    //     $i++;
    // }
}
// IF the in_report == 1, get All Employees
elseif ($in_report == 4)
{
    // setup variables
    $totalTitleSpan = 9;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | All Employees";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'">All Employees</td></tr>';
    $tableHeader = "<td>Name</td><td>Company</td><td>Start Time</td><td>Out</td><td>In</td><td>Out</td><td>In</td><td>End Time</td><td>Total</td>";

    // get name and company of all the Employees
    $sql = "SELECT E.id, CONCAT(E.first_name,' ',E.last_name) AS name, C.name AS company
    FROM Employee E, Company C
    WHERE E.companyID = C.id 
    ORDER BY E.first_name ASC";

    $count = 0;
    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $EMPLOYEE[] = "<tr><td>".$user->name."</td><td>".$user->company."</td>";
    }

    $i = 0;
    foreach ($EID as $userID)
    {
        // get the CLOCK_IN and CLOCK_OUT times
        $sql = "SELECT H.clock_in, H.clock_out
        FROM Employee_Hours H
        WHERE H.eid = $userID 
        AND H.date = '$today'
        ORDER BY H.clock_in ASC";

        $count = 0;
        $CLOCK_IN = [];
        $CLOCK_OUT = [];
        $query = mysql_query($sql);
        while ($time = mysql_fetch_object($query))
        {
            // add the current clock_in and clock_out times to an array
            $CLOCK_IN[$count] = date("g:i a", strtotime($date." ".$time->clock_in));
            $CLOCK_OUT[$count] = ($time->clock_out == "00:00:00") ? "" : date("g:i a", strtotime($date." ".$time->clock_out));
            $count++;
        }

        // total hours worked for the current date
        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) ) AS totalTime
                FROM Employee_Hours H
                WHERE H.date = '$today' 
                AND H.eid = $userID
                AND H.clock_out IS NOT NULL";

        $query = mysql_query($sql);
        $total = mysql_fetch_object($query);
        $totalTimeDay = totalTime($total->totalTime);

        // setup the variable names
        $start_time = $CLOCK_IN[0];
        $break_1_start = $CLOCK_OUT[0];
        $break_1_end = $CLOCK_IN[1];
        $break_2_start = $CLOCK_OUT[1];
        $break_2_end = $CLOCK_IN[2];
        $end_time = $CLOCK_OUT[2];

        // finish creating the employee record in the table
        $EMPLOYEE[$i] .= "<td>".$start_time."</td><td>".$break_1_start."</td><td>".$break_1_end."</td><td>".$break_2_start."</td><td>".$break_2_end."</td><td>".$end_time."</td><td>".$totalTimeDay."</td></tr>";

        // update the counter
        $i++;
    }
}
else // ELSE, no report for the incoming type selected, so exit
{
    exit;
}

?>
<table id="user-table" class="table table-striped table-bordered table-hover text-center">
    <thead>
        <tr class="info">
            <?php print $tableTitle; ?>
        </tr>
        <tr class="info">
            <?php print $tableHeader; ?>
        </tr>
    </thead>
    <tbody>
        <?php

        // print the Employee table records
        foreach ($EMPLOYEE as $e)
        {
            print $e;
        }

        // print the Employee total as the table footer 
        print '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>Total Employees:</h3></td><td class="text-center"><h3>'.count($EMPLOYEE).'</h3>';

        // print the hidden inputs
        print '<input type="hidden" class="hide" id="pageTitleNew" name="pageTitleNew" value="'.$pageTitle.'" />';
        print '</td></tr>';

        ?>
    </tbody>
</table>