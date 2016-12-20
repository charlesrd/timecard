<?php


require_once("config.php");


// get the session variables
$START_DATE = $_SESSION['START_DATE'];


// setup variables
$today = date('Y-m-d');
$displayToday = date('F j, Y');
$start_date = "";
$end_date = "";
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
$rowCount = 0;
$totalTitle = "Total Employees:";

// get the incoming variables
$in_report = (isset($_GET['r'])) ? preg_replace("/[^-0-9]/", "", $_GET['r']) : "";
$in_sort_type = (isset($_GET['st'])) ? preg_replace("/[^0-9]/", "", $_GET['st']) : 2;
$in_sort_order = (isset($_GET['so'])) ? preg_replace("/[^0-1]/", "", $_GET['so']) : 1;
$in_date = (isset($_GET['d'])) ? preg_replace("/[^-0-9]/", "", $_GET['d']) : "";
$in_start_date = (isset($_GET['sd'])) ? preg_replace("/[^-0-9]/", "", $_GET['sd']) : "";
$in_end_date = (isset($_GET['ed'])) ? preg_replace("/[^-0-9]/", "", $_GET['ed']) : "";

// set the sort_order
$sort_order = ($in_sort_order == 1) ? "ASC" : "DESC";


// IF the in_report == 1, get the Employees on break in the company
if ($in_report == 1)
{
    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employees on Break";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Employees on Break</span><br><span class="table-sub-header">for '.$displayToday.'</span></td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Break Start Time</a>'.$CARET[3].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time');

    // get names and break start times of the Employees on break in the company
    $sql = "SELECT CONCAT(E.first_name,' ',E.last_name) AS name, B.start_time, C.name AS company
    FROM On_Break B, Employee E, Company C
    WHERE B.end_time IS NULL
    AND E.companyID = C.id 
    AND E.companyID IN 
    (
        SELECT CA.cid 
        FROM Company_Administrator CA
        WHERE CA.aid = $adminID
    )
    AND E.active = 1
    AND E.id = B.eid 
    AND B.date = '$today'
    ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EMPLOYEE[] = "<tr class=\"report-row\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td></tr>";
        
        // update the row counter
        $rowCount++;
    }
}
elseif ($in_report == 2) // IF the in_report == 2, get the Employees done for the day in the company
{
    // setup variables
    $totalTitleSpan = 5;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employees Done for the Day";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Employees Done for the Day</span><br><span class="table-sub-header">for '.$displayToday.'</span></td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].')">End Time</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].')">Total</a>'.$CARET[5].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'end_time', 'total');

    // get names, start/end times, company and total hours worked of the Employees done for the day in the company
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
                AND E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
                AND E.active = 1
                ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $EMPLOYEE[] = "<tr class=\"report-row\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td><td>".date("g:i a", strtotime($today." ".$user->end_time))."</td><td>".totalTime($user->total)."</td>";
    
        // update the row counter
        $rowCount++;
    }
}
elseif ($in_report == 3) // IF the in_report == 3, get the Working Employees in the company
{
    // setup variables
    $totalTitleSpan = 9;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Active Employees";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Working Employees</span><br><span class="table-sub-header">for '.$displayToday.'</span></td></tr>';

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].')">Out</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].')">In</a>'.$CARET[5].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',6,'.$SORT_ORDER[6].')">Out</a>'.$CARET[6].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',7,'.$SORT_ORDER[7].')">In</a>'.$CARET[7].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',8,'.$SORT_ORDER[8].')">Out</a>'.$CARET[8].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',9,'.$SORT_ORDER[9].')">Total</a>'.$CARET[9].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'out1', 'in2', 'out2', 'in3', 'out3', 'total');

    // get id, name, company, all times, and total hours of the working Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company, 
            H.clock_in AS start_time, 
            (
                SELECT H4.clock_out 
                FROM Employee_Hours H4 
                WHERE H4.date = '$today'
                AND H4.eid = E.id
                ORDER BY H4.eid ASC
                LIMIT 1
            ) AS out1,
            (
                SELECT H5.clock_in 
                FROM Employee_Hours H5 
                WHERE H5.date = '$today'
                AND H5.eid = E.id
                ORDER BY H5.eid ASC
                LIMIT 1, 1
            ) AS in2,
            (
                SELECT H6.clock_out 
                FROM Employee_Hours H6 
                WHERE H6.date = '$today'
                AND H6.eid = E.id
                ORDER BY H6.eid ASC
                LIMIT 1, 1
            ) AS out2,
            (
                SELECT H7.clock_in 
                FROM Employee_Hours H7 
                WHERE H7.date = '$today'
                AND H7.eid = E.id
                ORDER BY H7.eid ASC
                LIMIT 2, 2
            ) AS in3,
            (
                SELECT H8.clock_out 
                FROM Employee_Hours H8 
                WHERE H8.date = '$today'
                AND H8.eid = E.id
                ORDER BY H8.eid ASC
                LIMIT 2, 2
            ) AS out3,
            (
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS total
                FROM Employee_Hours H2
                WHERE H2.date = '$today' 
                AND H2.eid = E.id
                AND H2.clock_out IS NOT NULL
            ) AS total
                FROM Employee E, Employee_Hours H, Company C
                WHERE H.clock_in = (
                                    SELECT H3.clock_in 
                                    FROM Employee_Hours H3 
                                    WHERE H3.date = '$today'
                                    AND H3.eid = E.id
                                    ORDER BY H3.eid ASC
                                    LIMIT 1
                                 )
                AND E.id NOT IN (
                                    SELECT D.eid 
                                    FROM Is_Done D 
                                    WHERE D.date = '$today'
                                    ORDER BY D.eid ASC
                                )
                AND H.date = '$today'
                AND E.companyID = C.id 
                AND E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
                AND E.active = 1
                ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $out1 = ($user->out1 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out1));
        $in2 = ($user->in2 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->in2));
        $out2 = ($user->out2 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out2));
        $in3 = ($user->in3 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->in3));
        $out3 = ($user->out3 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out3));
        $EMPLOYEE[] = "<tr class=\"report-row\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".date("g:i a", strtotime($today." ".$user->start_time))."</td><td>".$out1."</td><td>".$in2."</td><td>".$out2."</td><td>".$in3."</td><td>".$out3."</td><td>".totalTime($user->total)."</td>";
    
        // update the row counter
        $rowCount++;
    }
}
elseif ($in_report == 4) // IF the in_report == 4, get All Employee Daily Activity in the company
{
    // setup variables
    $totalTitleSpan = 9;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | All Employee Daily Activity";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">All Daily Activity of Active Employees</span><br><span class="table-sub-header">for '.$displayToday.'</span></td></tr>';
    
    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].')">Out</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].')">In</a>'.$CARET[5].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',6,'.$SORT_ORDER[6].')">Out</a>'.$CARET[6].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',7,'.$SORT_ORDER[7].')">In</a>'.$CARET[7].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortPrintReport('.$in_report.',8,'.$SORT_ORDER[8].')">Out</a>'.$CARET[8].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',9,'.$SORT_ORDER[9].')">Total</a>'.$CARET[9].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'out1', 'in2', 'out2', 'in3', 'out3', 'total');

    // get id, name, company, all times, and total hours of all Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company,
            (
                SELECT H1.clock_in 
                FROM Employee_Hours H1 
                WHERE H1.date = '$today'
                AND H1.eid = E.id
                ORDER BY H1.eid ASC
                LIMIT 1
            ) AS start_time,
            (
                SELECT H4.clock_out 
                FROM Employee_Hours H4 
                WHERE H4.date = '$today'
                AND H4.eid = E.id
                ORDER BY H4.eid ASC
                LIMIT 1
            ) AS out1,
            (
                SELECT H5.clock_in 
                FROM Employee_Hours H5 
                WHERE H5.date = '$today'
                AND H5.eid = E.id
                ORDER BY H5.eid ASC
                LIMIT 1, 1
            ) AS in2,
            (
                SELECT H6.clock_out 
                FROM Employee_Hours H6 
                WHERE H6.date = '$today'
                AND H6.eid = E.id
                ORDER BY H6.eid ASC
                LIMIT 1, 1
            ) AS out2,
            (
                SELECT H7.clock_in 
                FROM Employee_Hours H7 
                WHERE H7.date = '$today'
                AND H7.eid = E.id
                ORDER BY H7.eid ASC
                LIMIT 2, 2
            ) AS in3,
            (
                SELECT H8.clock_out 
                FROM Employee_Hours H8 
                WHERE H8.date = '$today'
                AND H8.eid = E.id
                ORDER BY H8.eid ASC
                LIMIT 2, 2
            ) AS out3,
            (
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS total
                FROM Employee_Hours H2
                WHERE H2.date = '$today' 
                AND H2.eid = E.id
                AND H2.clock_out IS NOT NULL
            ) AS total
                FROM Employee E, Company C
                WHERE E.companyID = C.id 
                AND E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
                AND E.active = 1
                ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $start_time = ($user->start_time == NULL) ? "" : date("g:i a", strtotime($date." ".$user->start_time));
        $out1 = ($user->out1 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out1));
        $in2 = ($user->in2 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->in2));
        $out2 = ($user->out2 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out2));
        $in3 = ($user->in3 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->in3));
        $out3 = ($user->out3 == NULL) ? "" : date("g:i a", strtotime($date." ".$user->out3));
        $EMPLOYEE[] = "<tr class=\"report-row\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td class=\"no-print\">".$start_time."</td><td class=\"no-print\">".$out1."</td><td class=\"no-print\">".$in2."</td><td class=\"no-print\">".$out2."</td><td class=\"no-print\">".$in3."</td><td class=\"no-print\">".$out3."</td><td>".totalTime($user->total)."</td>";
    
        // update the row counter
        $rowCount++;
    }
}
elseif ($in_report == 5) // IF the in_report == 5, get the Forgotten Hours in the company
{
    // setup variables
    $totalTitleSpan = 5;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Forgotten Hours";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Forgotten Hours</span></td></tr>';
    
    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Date</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].')">IN</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].')">Out</a>'.$CARET[5].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'date', 'clock_in', 'clock_out');

    // SELECT 1: get the list of all forgotten hours
    // SELECT 2: get the list of all forgotten hours from previous dates not listed in the Forgot_Hours table
    $sql = "SELECT CONCAT(E.first_name,' ',E.last_name) AS name,  
            C.name AS company, 
            H.date, 
            F.clock_in, 
            F.clock_out, 
            E.id 
            FROM Employee E, Company C, Employee_Hours H, Forgot_Hour F 
            WHERE E.companyID = C.id 
            AND E.companyID IN 
            (
                SELECT CA.cid 
                FROM Company_Administrator CA
                WHERE CA.aid = $adminID
            )
            AND E.active = 1
            AND H.id = F.ehid 
            AND H.eid = E.id 
            AND ( 
                    F.clock_out != 0 
                    OR F.clock_in != 0 
                ) 
            UNION 
            SELECT CONCAT(E.first_name,' ',E.last_name) AS name,  
            C.name AS company, 
            H.date,
            H.clock_in, 
            H.clock_out, 
            E.id 
            FROM Employee E, Company C, Employee_Hours H
            WHERE E.companyID = C.id 
            AND E.active = 1
            AND H.eid = E.id 
            AND H.eid IN
            (
                SELECT 
                E.id
                FROM Employee E
                WHERE E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
            )
            AND (
                    H.clock_in IS NULL
                    OR H.clock_out IS NULL
                )
            AND H.date < '$today'
            ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $inForgot = ( ($user->clock_in == 1) || ($user->clock_in == NULL) ) ? "forgot" : "";
        $outForgot = ( ($user->clock_out == 1) || ($user->clock_out == NULL) ) ? "forgot" : "";
        $EMPLOYEE[] = "<tr class=\"report-row forgot-report\" onclick=\"changeUser(".$user->id.",'".$user->date."')\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".$user->date."</td><td class=\"".$inForgot."\"></td><td class=\"".$outForgot."\"></td>";
    
        // update the row counter
        $rowCount++;
    }

    // get the list of all forgotten hours from previous dates not listed in the Forgot_Hours table
    // $sql = "SELECT CONCAT(E.first_name,' ',E.last_name) AS name, 
    //         H.date, 
    //         C.name AS company, 
    //         H.clock_in, 
    //         H.clock_out, 
    //         E.id 
    //         FROM Employee E, Company C, Employee_Hours H
    //         WHERE E.companyID = C.id 
    //         AND H.eid = E.id 
    //         AND (
    //                 H.clock_in IS NULL
    //                 OR H.clock_out IS NULL
    //             )
    //         AND H.date < '$today'
    //         ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // $query = mysql_query($sql);
    // while ($user = mysql_fetch_object($query))
    // {
    //     $inForgot = ($user->clock_in == NULL) ? "forgot" : "";
    //     $outForgot = ($user->clock_out == NULL) ? "forgot" : "";
    //     $EMPLOYEE[] = "<tr class=\"report-row forgot-report\" onclick=\"changeUser(".$user->id.",'".$user->date."')\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".$user->date."</td><td class=\"".$inForgot."\"></td><td class=\"".$outForgot."\"></td>";
    
    //     // update the row counter
    //     $rowCount++;
    // }
}
elseif ($in_report == 6) // IF the in_report == 6, get All Employee Hours from a given week in the company
{
    // get the correct start and end dates
    if ($in_date == "")
    {
        // IF the session START_DATE exists, use it
        if (isset($START_DATE))
        {
           list($start_date, $end_date) = x_week_range($START_DATE);
        }
        else // ELSE, get the start and end dates of the current week
        {
            list($start_date, $end_date) = x_week_range($today);
        }
    }
    else
    {
        // get the start and end dates from the week of the in_date
        list($start_date, $end_date) = x_week_range($in_date);
    }

    // set the session start date
    $_SESSION['START_DATE'] = $start_date;

    // correctly display the start and end date
    $modifiedStartDate = date('F j', strtotime($start_date));
    $modifiedEndDate = date('F j, Y', strtotime($end_date));

    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | All Employee Hours from the week of ".$modifiedStartDate." to ".$modifiedEndDate;
    $changeWeekLink = '<a id="change-week-link" href="#" onclick="setupModal(\'change-week-week\')" data-toggle="modal" data-target="#change-week-modal">Change Week</a>';
    $printButton = '<span id="print-report-button" onclick="printReport(\'6\')" class="glyphicon glyphicon-print"></span>';
    $printInstructions = '<button id="print-instructions" onclick="window.print()" class="btn btn-info no-print shake"><span class="glyphicon glyphicon-print"></span> Click Here or Type CONTROL+P to print this table.</button>';
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header"><span class="table-header">All Employee Hours</span><br><span class="table-sub-header"> during the week of '.$modifiedStartDate.' to '.$modifiedEndDate.'</span></td></tr>';
    
    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Total</a>'.$CARET[3].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'total');

    // get id, name, company, total hours, and forgot status of all Employees for a given week in the company
    // NOTE: the forgot status checks whether the employee has any clock in or clock out values that aren't filled in yet
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company, 
            ( 
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS totalRegularTime 
                FROM Employee_Hours H2 
                WHERE H2.date >= '$start_date' 
                AND H2.date <= '$end_date' 
                AND H2.eid = E.id 
                AND H2.clock_out IS NOT NULL 
            ) AS totalRegularTime, 
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( V.time ) ) ) AS totalVacationTime
                FROM Vacation_Time V
                WHERE V.date >= '$start_date' 
                AND V.date <= '$end_date' 
                AND V.eid = E.id
            ) AS totalVacationTime,
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( H.time ) ) ) AS totalHolidayTime
                FROM Holiday_Time H
                WHERE H.date >= '$start_date' 
                AND H.date <= '$end_date' 
                AND H.eid = E.id
            ) AS totalHolidayTime,
            (
                SELECT 
                SEC_TO_TIME( SUM( TIME_TO_SEC( P.time ) ) ) AS totalPersonalTime
                FROM Personal_Time P
                WHERE P.date >= '$start_date' 
                AND P.date <= '$end_date' 
                AND P.eid = E.id
            ) AS totalPersonalTime,
            ( 
                SELECT COUNT(*) AS forgot
                FROM Employee_Hours H3 
                WHERE H3.date >= '$start_date' 
                AND H3.date <= '$end_date' 
                AND H3.eid = E.id 
                AND (
                        H3.clock_out IS NULL
                        OR H3.clock_in IS NULL
                    ) 
            ) AS forgot 
                FROM Employee E, Company C 
                WHERE E.companyID = C.id 
                AND E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
                ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $forgot = ($user->forgot > 0) ? "forgot" : "";
        $forgotMessage = ($forgot != "") ? "(INCOMPLETE)" : "";
        $totalRegularTime = (is_null($user->totalRegularTime)) ? "00:00:00" : $user->totalRegularTime;
        $totalVacationTime = (is_null($user->totalVacationTime)) ? "00:00:00" : $user->totalVacationTime;
        $totalHolidayTime = (is_null($user->totalHolidayTime)) ? "00:00:00" : $user->totalHolidayTime;
        $totalPersonalTime = (is_null($user->totalPersonalTime)) ? "00:00:00" : $user->totalPersonalTime;

        // get the total time worked
        $sql2 = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( '$totalRegularTime' ) + TIME_TO_SEC( '$totalVacationTime' ) + TIME_TO_SEC( '$totalHolidayTime' ) + TIME_TO_SEC( '$totalPersonalTime' ) ) ) AS totalTime";
        $q0 = mysql_query($sql2);
        $total = mysql_fetch_object($q0);
        $totalTime = $total->totalTime;

        if ($totalTime != "00:00:00")
        {
            $EMPLOYEE[] = "<tr class=\"report-row\"><td class=\"".$forgot."\">".$user->name."</td><td class=\"".$forgot."\">".$user->company."</td><td class=\"".$forgot."\">".$forgotMessage." ".totalTimeCalculated($totalTime)."</td>";
        
            // update the row counter
            $rowCount++;
        }
    }
}
elseif ($in_report == 7) // IF the in_report == 7, get the Personal Day Requests in the company
{
    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Personal Day Requests";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Personal Day Requests</span><br><span class="table-sub-header">after '.$displayToday.'</span></td></tr>';
    $totalTitle = "Total Requests:";

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].')">Date</a>'.$CARET[3].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'date');

    // get names, company and requested personal dates of the Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company,
            P.date
            FROM Employee E, Company C, Personal_Time P
            WHERE E.id = P.eid
            AND P.date > '$today'
            AND E.companyID = C.id 
            AND E.companyID IN 
            (
                SELECT CA.cid 
                FROM Company_Administrator CA
                WHERE CA.aid = $adminID
            )
            AND E.active = 1
            ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $modifiedDate = preg_replace('/-/', ' ', $user->date, 1);
        
        $EMPLOYEE[] = "<tr class=\"report-row cursor-pointer\"<a href=\"#\" onclick=\"changeUser(".$user->id.",'".$user->date."')\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".$modifiedDate."</td>";
        
        // update the row counter
        $rowCount++;
    }
}
elseif ($in_report == 8) // IF the in_report == 8, "Do Payroll" from the in_start_date to the in_end_date (same as report 6 except with the dates chosen by the user)
{
    if ( ($in_start_date == "") || ($in_end_date == "") )
    {
        // IF the session START_DATE and END_DATE exists, use them
        if ( (isset($START_DATE)) && (isset($END_DATE)) )
        {
            $start_date = $START_DATE;
            $end_date = $END_DATE;
        }
        else // ELSE, get the start and end dates of the current week
        {
            list($start_date, $end_date) = x_week_range($today);
        }
    }
    else
    {
        // ELSE, use the in_start_date and in_end_date
        $start_date = $in_start_date;
        $end_date = $in_end_date;
    }

    // find how many days are between the start and end dates
    $is2weeks = false;
    $date1 = new DateTime($start_date);
    $date2 = new DateTime($end_date);
    $totalDays = 1+$date2->diff($date1)->format("%a");
    
    $nextSunday = date('Y-m-d', strtotime('next sunday '. $start_date));
    $date1 = new DateTime($nextSunday);

    if ( ($end_date >= $nextSunday) && ($totalDays >= 2) && ($totalDays <= 7) )
    {
        $is2weeks = true;
    }

    $is2weeks = ( ($totalDays >= 8) && ($totalDays <= 14) ) ? true : $is2weeks;

    // set the session start and end dates
    $_SESSION['START_DATE'] = $start_date;
    $_SESSION['END_DATE'] = $end_date;

    // correctly display the start and end date
    $modifiedStartDate = date('F j', strtotime($start_date));
    $modifiedEndDate = date('F j, Y', strtotime($end_date));

    // setup variables
    $totalTitleSpan = ($is2weeks) ? 8 : 6;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | All Employee Hours from the week of ".$modifiedStartDate." to ".$modifiedEndDate;
    $printInstructions = '<button id="print-instructions" onclick="window.print()" class="btn btn-info no-print shake"><span class="glyphicon glyphicon-print"></span> Click Here or Type CONTROL+P to print this table.</button>';
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header"><span class="table-header">All Employee Hours</span><br><span class="table-sub-header"> from '.$modifiedStartDate.' to '.$modifiedEndDate.'</span></td></tr>';
    
    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td><a href="#" onclick="sortPrintReport('.$in_report.',1,'.$SORT_ORDER[1].',\''.$start_date.'\',\''.$end_date.'\')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',2,'.$SORT_ORDER[2].',\''.$start_date.'\',\''.$end_date.'\')">Company</a>'.$CARET[2].'</td>';

    if ($is2weeks)
    {
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].',\''.$start_date.'\',\''.$end_date.'\')">Total Week 1</a>'.$CARET[3].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].',\''.$start_date.'\',\''.$end_date.'\')">Total Week 2</a>'.$CARET[4].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].',\''.$start_date.'\',\''.$end_date.'\')">Total Regular</a>'.$CARET[5].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',6,'.$SORT_ORDER[6].',\''.$start_date.'\',\''.$end_date.'\')">Total Overtime</a>'.$CARET[6].'</td>';
        $tableHeader .= '<td>Last Review Date</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',7,'.$SORT_ORDER[7].',\''.$start_date.'\',\''.$end_date.'\')">Total</a>'.$CARET[7].'</td>';
    }
    else
    {
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',3,'.$SORT_ORDER[3].',\''.$start_date.'\',\''.$end_date.'\')">Total Regular</a>'.$CARET[3].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',4,'.$SORT_ORDER[4].',\''.$start_date.'\',\''.$end_date.'\')">Total Overtime</a>'.$CARET[4].'</td>';
        $tableHeader .= '<td>Last Review Date</td>';
        $tableHeader .= '<td><a href="#" onclick="sortPrintReport('.$in_report.',5,'.$SORT_ORDER[5].',\''.$start_date.'\',\''.$end_date.'\')">Total</a>'.$CARET[5].'</td>';
    }

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'total');

    // setup variables
    $daysRemaining = $totalDays;
    $daysToNextSunday = 0;
    $weekCount = 1;
    $TOTAL_HOURS = array();
    $TOTAL_WEEK_HOURS = array();
    $TOTAL_REGULAR_HOURS = array();
    $TOTAL_OVERTIME_HOURS = array();
    $forgot = "";

    while ($daysRemaining > 0)
    {
        // setup loop variables
        $start_date = ($weekCount == 1) ? $start_date : $nextSunday;

        if ($weekCount == 1)
        {
            $start_date_datetime = new DateTime($start_date);

            $nextSaturday = date('Y-m-d', strtotime('next saturday '. $start_date));
            $nextSunday = date('Y-m-d', strtotime('next sunday '. $start_date));

            $datetime = new DateTime($nextSunday);
            $daysToNextSunday = $start_date_datetime->diff($datetime)->format('%d');
        }
        else
        {
            $nextSaturday = $datetime->modify('+6 days')->format("Y-m-d");
            $nextSunday = $datetime->modify('+1 day')->format("Y-m-d");
            $daysToNextSunday = 7;
        }

        // IF the end_date >= nextSaturday is >= 0, then set $end_date = $nextSaturday
        if ($_SESSION["END_DATE"] >= $nextSaturday)
        {
            $end_date = $nextSaturday;
        }
        // ELSE, set $end_date = $_SESSION["END_DATE"]
        else 
        {
            $end_date = $_SESSION["END_DATE"];
        }

        // print "<br>";
        // print "weekCount: ".$weekCount."<br>";
        // print "start_date: ".$start_date."<br>";
        // print "end_date: ".$end_date."<br>";
        // print "nextSaturday: ".$nextSaturday."<br>";
        // print "nextSunday: ".$nextSunday."<br>";
        // print "daysToNextSunday: ".$daysToNextSunday."<br>";
        // print "totalDays: ".$totalDays."<br>";
        // print "daysRemaining: ".$daysRemaining."<br>";



        // get id, name, company, total hours, and forgot status of all Employees for a given time period in the company
        // NOTE: the forgot status checks whether the employee has any clock in or clock out values that aren't filled in yet
        $sql = "SELECT E.id, 
                CONCAT(E.first_name,' ',E.last_name) AS name, 
                C.name AS company, 
                ( 
                    SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS totalRegularTime 
                    FROM Employee_Hours H2 
                    WHERE H2.date >= '$start_date' 
                    AND H2.date <= '$end_date' 
                    AND H2.eid = E.id 
                    AND H2.clock_out IS NOT NULL 
                ) AS totalRegularTime, 
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( V.time ) ) ) AS totalVacationTime
                    FROM Vacation_Time V
                    WHERE V.date >= '$start_date' 
                    AND V.date <= '$end_date' 
                    AND V.eid = E.id
                ) AS totalVacationTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( H.time ) ) ) AS totalHolidayTime
                    FROM Holiday_Time H
                    WHERE H.date >= '$start_date' 
                    AND H.date <= '$end_date' 
                    AND H.eid = E.id
                ) AS totalHolidayTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( P.time ) ) ) AS totalPersonalTime
                    FROM Personal_Time P
                    WHERE P.date >= '$start_date' 
                    AND P.date <= '$end_date' 
                    AND P.eid = E.id
                ) AS totalPersonalTime,
                ( 
                    SELECT COUNT(*) AS forgot
                    FROM Employee_Hours H3 
                    WHERE H3.date >= '$start_date' 
                    AND H3.date <= '$end_date' 
                    AND H3.eid = E.id 
                    AND (
                            H3.clock_out IS NULL
                            OR H3.clock_in IS NULL
                        ) 
                ) AS forgot 
                FROM Employee E, Company C 
                WHERE E.companyID = C.id 
                AND E.companyID IN 
                (
                    SELECT CA.cid 
                    FROM Company_Administrator CA
                    WHERE CA.aid = $adminID
                )
                ORDER BY $SORT_TYPE[$in_sort_type], E.peachID $sort_order";

        // set the row counter
        $rowCount = 0;

        $query = mysql_query($sql);
        while ($user = mysql_fetch_object($query))
        {
            $EID[] = $user->id;
            if ( ($weekCount == 1) || ($is2weeks) )
            {
                $forgot = ($user->forgot > 0) ? "forgot" : "";
                $forgotMessage = ($forgot != "") ? "(INCOMPLETE)" : "";
            }
            $totalRegularTime = (is_null($user->totalRegularTime)) ? "00:00:00" : $user->totalRegularTime;
            $totalVacationTime = (is_null($user->totalVacationTime)) ? "00:00:00" : $user->totalVacationTime;
            $totalHolidayTime = (is_null($user->totalHolidayTime)) ? "00:00:00" : $user->totalHolidayTime;
            $totalPersonalTime = (is_null($user->totalPersonalTime)) ? "00:00:00" : $user->totalPersonalTime;

            // get the total time worked
            $sql2 = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( '$totalRegularTime' ) + TIME_TO_SEC( '$totalVacationTime' ) + TIME_TO_SEC( '$totalHolidayTime' ) + TIME_TO_SEC( '$totalPersonalTime' ) ) ) AS totalTime";
            $q0 = mysql_query($sql2);
            $total = mysql_fetch_object($q0);
            $totalTime = $total->totalTime;

            $TOTAL_WEEK_HOURS = floatval(totalTimeCalculated($totalTime));
            // print "TOTAL_WEEK_HOURS: ".$TOTAL_WEEK_HOURS;

            if ($weekCount == 1)
            {
                $TOTAL_HOURS[] = $TOTAL_WEEK_HOURS;
                if ($TOTAL_WEEK_HOURS > 40)
                {
                    $TOTAL_REGULAR_HOURS[] = 40;
                    $TOTAL_OVERTIME_HOURS[] = ($TOTAL_WEEK_HOURS - 40);
                }
                else
                {
                    $TOTAL_REGULAR_HOURS[] = $TOTAL_WEEK_HOURS;
                    $TOTAL_OVERTIME_HOURS[] = 0;
                }
            }
            else
            {
                $TOTAL_HOURS[$rowCount] += $TOTAL_WEEK_HOURS;
                if ($TOTAL_WEEK_HOURS > 40)
                {
                    $TOTAL_REGULAR_HOURS[$rowCount] += 40;
                    $TOTAL_OVERTIME_HOURS[$rowCount] += ($TOTAL_WEEK_HOURS - 40);
                }
                else
                {
                    $TOTAL_REGULAR_HOURS[$rowCount] += $TOTAL_WEEK_HOURS;
                }
            }

            if ($is2weeks)
            {
                if ($weekCount == 1)
                {
                    $EMPLOYEE[] = "<tr class=\"report-row\"><td class=\"".$forgot."\"><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$_SESSION['START_DATE']."')\">".$user->name."</a></td><td class=\"".$forgot."\">".$user->company."</td><td class=\"".$forgot."\">".$forgotMessage." ".number_format(floatval(totalTimeCalculated($totalTime)), 1, '.', '')."</td>";
                }
                else
                {
                    $EMPLOYEE[$rowCount] .= "<td class=\"".$forgot."\">".$forgotMessage." ".number_format(floatval(totalTimeCalculated($totalTime)), 1, '.', '')."</td><td>".number_format((float)($TOTAL_REGULAR_HOURS[$rowCount]), 1, '.', '')."</td><td>".number_format((float)($TOTAL_OVERTIME_HOURS[$rowCount]), 1, '.', '')."</td><td>&nbsp;</td><td>".number_format((float)($TOTAL_HOURS[$rowCount]), 1, '.', '')."</td>";
                }
            }
            else
            {
                if ($weekCount == 1)
                {
                    $EMPLOYEE[] = "<tr class=\"report-row\"><td class=\"".$forgot."\"><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$_SESSION['START_DATE']."')\">".$user->name."</a></td><td class=\"".$forgot."\">".$user->company."</td><td>".number_format((float)($TOTAL_REGULAR_HOURS[$rowCount]), 1, '.', '')."</td><td>".number_format((float)($TOTAL_OVERTIME_HOURS[$rowCount]), 1, '.', '')."</td><td>&nbsp;</td><td class=\"".$forgot."\">".$forgotMessage." ".number_format((float)($TOTAL_HOURS[$rowCount]), 1, '.', '')."</td>";
                }
                else
                {
                    $EMPLOYEE[$rowCount] = "<tr class=\"report-row\"><td class=\"".$forgot."\"><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$_SESSION['START_DATE']."')\">".$user->name."</a></td><td class=\"".$forgot."\">".$user->company."</td><td>".number_format((float)($TOTAL_REGULAR_HOURS[$rowCount]), 1, '.', '')."</td><td>".number_format((float)($TOTAL_OVERTIME_HOURS[$rowCount]), 1, '.', '')."</td><td>&nbsp;</td><td class=\"".$forgot."\">".$forgotMessage." ".number_format((float)($TOTAL_HOURS[$rowCount]), 1, '.', '')."</td>";
                }
            }

            // update the row counter
            $rowCount++;
        }


        // update the daysRemaining
        $daysRemaining -= $daysToNextSunday;
        $weekCount++;
    }

    // remove the EMPLOYEE rows that don't have any hours
    $count = 0;
    foreach ($TOTAL_HOURS as $t)
    {
        if ($t == 0.0)
        {
            unset($EMPLOYEE[$count]);
        }

        $count++;
    }
}
else // ELSE, no report for the incoming type selected, so exit
{
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">

    <link rel="icon" type="image/png" href="../img/clock.png" />
    <title>AMS Timecard | AmericaSmiles &amp; United Dental Resources Hour Time Tracker</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AmericaSmiles Dental Technologies, Inc &amp; United Dental Resources - Timecard Program.  Easily login to clock in, clock out, view timecard history, and more.">
    <meta name="author" content="AmericaSmiles &amp; United Dental Resources">

    <!-- CSS stylesheets -->
    <link media="all" type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link media="all" type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="http://amstimecard.com/css/timecard.css">

    <!-- Javascript Files -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script type="text/javascript" src="http://amstimecard.com/js/timecard.js"></script>
</head>

<body>
    <?php print $printInstructions; ?>
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
            print '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>'.$totalTitle.'</h3></td><td class="text-center"><h3>'.count($EMPLOYEE).'</h3>';

            // print the hidden inputs
            print '<input type="hidden" class="hide" id="pageTitleNew" name="pageTitleNew" value="'.$pageTitle.'" />';
            print '</td></tr>';

            ?>
        </tbody>
    </table>

    <!-- Print Report Interal Javascript -->
    <script type="text/javascript">
        $(document).ready(function(){
            // automatically print the page on load
            $('#print-instructions').trigger('click');
        });

    </script>

    <style type="text/css">

    @media print {
        .table tr:nth-child(odd) td {
                background-color: #e0e0e0 !important;
        }

        .table thead td {
                background-color: #fff !important;
        }

        .table-stripe {
                background-color: #ffffff !important;
        }
    }

    </style>

    <?php if ($rowCount >= 10): ?>
        <style type="text/css">
        #user-table-div {
            overflow: auto;
        }
        </style>
    <?php endif; ?>

</body>
</html>