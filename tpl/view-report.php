<?php


require_once("config.php");


// get the session variables
$START_DATE = $_SESSION['START_DATE'];
$END_DATE = $_SESSION['END_DATE'];


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
$in_report = (isset($_POST['r'])) ? preg_replace("/[^-0-9]/", "", $_POST['r']) : "";
$in_sort_type = (isset($_POST['st'])) ? preg_replace("/[^0-9]/", "", $_POST['st']) : 1;
$in_sort_order = (isset($_POST['so'])) ? preg_replace("/[^0-1]/", "", $_POST['so']) : 1;
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";
$in_start_date = (isset($_POST['sd'])) ? preg_replace("/[^-0-9]/", "", $_POST['sd']) : "";
$in_end_date = (isset($_POST['ed'])) ? preg_replace("/[^-0-9]/", "", $_POST['ed']) : "";


// set the globalDate
if ($in_date != "")
{
    $_SESSION['date'] = $in_date;
}

// get the globalDate
$globalDate = preg_replace("/[^-0-9]/", "", $_SESSION['date']);
if ($globalDate == "") {$globalDate = $today;}


// IF the global $VIEW_REPORT exists, use it instead
$in_report = (isset($VIEW_REPORT)) ? $VIEW_REPORT : $in_report;

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
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Break Start Time</a>'.$CARET[3].'</td>';

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
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">End Time</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">Total</a>'.$CARET[5].'</td>';

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
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Out</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">In</a>'.$CARET[5].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',6,'.$SORT_ORDER[6].')">Out</a>'.$CARET[6].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',7,'.$SORT_ORDER[7].')">In</a>'.$CARET[7].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',8,'.$SORT_ORDER[8].')">Out</a>'.$CARET[8].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',9,'.$SORT_ORDER[9].')">Total</a>'.$CARET[9].'</td>';

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
    $displayDate = date('F j, Y', strtotime($globalDate));
    $pageTitle = "AMS Timecard | All Employee Daily Activity";
    $changeDayLink = '<a href="#" class="header-link" onclick="setupModal(\'change-day\')" data-toggle="modal" data-target="#change-day-modal">Change Day</a>';
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><div class="container"><div class="row"><div class="col-xs-2"></div><div class="col-xs-7"><span class="table-header">All Daily Activity of Active Employees</span><br><span class="table-sub-header">for '.$displayDate.'</span></div><div class="col-xs-3 text-left">'.$changeDayLink.'</div></div></div></td></tr>';


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
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Start Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Out</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">In</a>'.$CARET[5].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',6,'.$SORT_ORDER[6].')">Out</a>'.$CARET[6].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',7,'.$SORT_ORDER[7].')">In</a>'.$CARET[7].'</td>';
    $tableHeader .= '<td class="no-print"><a href="#" onclick="sortReport('.$in_report.',8,'.$SORT_ORDER[8].')">Out</a>'.$CARET[8].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',9,'.$SORT_ORDER[9].')">Total</a>'.$CARET[9].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'start_time', 'out1', 'in2', 'out2', 'in3', 'out3', 'total');

    // get id, name, company, all times, and total hours of all Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company,
            (
                SELECT H1.clock_in 
                FROM Employee_Hours H1 
                WHERE H1.date = '$globalDate'
                AND H1.eid = E.id
                ORDER BY H1.eid ASC
                LIMIT 1
            ) AS start_time,
            (
                SELECT H4.clock_out 
                FROM Employee_Hours H4 
                WHERE H4.date = '$globalDate'
                AND H4.eid = E.id
                ORDER BY H4.eid ASC
                LIMIT 1
            ) AS out1,
            (
                SELECT H5.clock_in 
                FROM Employee_Hours H5 
                WHERE H5.date = '$globalDate'
                AND H5.eid = E.id
                ORDER BY H5.eid ASC
                LIMIT 1, 1
            ) AS in2,
            (
                SELECT H6.clock_out 
                FROM Employee_Hours H6 
                WHERE H6.date = '$globalDate'
                AND H6.eid = E.id
                ORDER BY H6.eid ASC
                LIMIT 1, 1
            ) AS out2,
            (
                SELECT H7.clock_in 
                FROM Employee_Hours H7 
                WHERE H7.date = '$globalDate'
                AND H7.eid = E.id
                ORDER BY H7.eid ASC
                LIMIT 2, 1
            ) AS in3,
            (
                SELECT H8.clock_out 
                FROM Employee_Hours H8 
                WHERE H8.date = '$globalDate'
                AND H8.eid = E.id
                ORDER BY H8.eid ASC
                LIMIT 2, 1
            ) AS out3,
            (
                SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H2.clock_out ) - TIME_TO_SEC( H2.clock_in ) ) ) AS total
                FROM Employee_Hours H2
                WHERE H2.date = '$globalDate' 
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
    $tableHeader = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Date</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">IN</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">Out</a>'.$CARET[5].'</td>';

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
        $modifiedDate = preg_replace('/-/', ' ', $user->date, 1);

        $EMPLOYEE[] = "<tr class=\"report-row forgot-report\" onclick=\"changeUser(".$user->id.",'".$user->date."')\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$user->company."</td><td>".$modifiedDate."</td><td class=\"".$inForgot."\"></td><td class=\"".$outForgot."\"></td>";
    
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
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><div class="container"><div class="row"><div class="col-xs-9"><span class="table-header">All Employee Hours</span><br><span class="table-sub-header"> during the week of '.$modifiedStartDate.' to '.$modifiedEndDate.'</span></div><div class="col-xs-3 text-left">'.$changeWeekLink.' '.$printButton.'</div></div></div></td></tr>';
    
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
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Total</a>'.$CARET[3].'</td>';

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
            $EMPLOYEE[] = "<tr class=\"report-row\"><td class=\"".$forgot."\"><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$start_date."')\">".$user->name."</a></td><td class=\"".$forgot."\">".$user->company."</td><td class=\"".$forgot."\">".$forgotMessage." ".totalTimeCalculated($totalTime)."</td>";
            
            // update the row counter
            $rowCount++;
        }
    }
}
elseif ($in_report == 7) // IF the in_report == 7, get the Personal Day Requests in the company
{
    // setup variables
    $personalDayRequestStatus = (isset($_SESSION['pdrs'])) ? preg_replace("/[^0-9]/", "", $_SESSION['pdrs']) : 0;
    $personalDayRequestValues = array('Not Reviewed','Approved','Disapproved');

    $personalDayRequestCount = 0;
    $personalDayRequestOptions = "";
    foreach ($personalDayRequestValues as $value)
    {
        $personalDayRequestOptions .= ($personalDayRequestStatus == $personalDayRequestCount) ? '<option value="'.$personalDayRequestCount.'" selected="selected">'.$value.'</option>' : '<option value="'.$personalDayRequestCount.'">'.$value.'</option>';
        $personalDayRequestCount++;
    }

    $totalTitleSpan = 7;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Personal Day Requests";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Personal Day Requests</span></td></tr>';
    $tableTitle .= '<tr>
                        <td colspan="'.$totalTitleSpan.'">
                            <div class="row">
                                <div class="col-xs-4">
                                    <h5 class="text-right"><strong>Select a Status:</strong></h5>
                                </div>
                                <div class="col-xs-8 col-sm-4">
                                    <select class="form-control" id="personal-day-request-status" name="personal-day-request-status" onchange="updatePersonalDayRequestGlobalStatus()">
                                        '.$personalDayRequestOptions.'
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>';
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
    $tableHeader  = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Time</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Date</a>'.$CARET[4].'</td>';
    $tableHeader .= '<td>Employee Note</td>';
    $tableHeader .= '<td>Admin Note</td>';
    $tableHeader .= '<td>Status</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'time', 'date');

    // get names, company and requested personal dates of the Employees in the company
    // status: 0 = Not yet reviewed, 1 = Approved, 2 = Disapproved
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company,
            R.time,
            R.id AS review_id,
            R.date,
            R.status,
            R.employee_note,
            R.admin_note
            FROM Employee E, Company C, Request_Personal_Time R
            WHERE E.id = R.eid
            AND E.companyID = C.id 
            AND R.status = $personalDayRequestStatus
            AND E.companyID IN 
            (
                SELECT CA.cid 
                FROM Company_Administrator CA
                WHERE CA.aid = $adminID
            )
            AND E.active = 1
            AND YEAR(R.date) > 2015
            AND R.date != '0000-00-00'
            ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $modifiedDate = preg_replace('/-/', ' ', $user->date, 1);


        $select_status  = '<select class="form-control status" id="select_status_'.$user->review_id.'" name="select_status_'.$user->review_id.'" onchange="updatePersonalDayRequestStatus('.$user->review_id.')">';
        $select_status .= ($user->status == 0) ? '<option value="0" selected="selected">Not reviewed</option>' : '<option value="0">Not yet reviewed</option>';
        $select_status .= ($user->status == 1) ? '<option class="bg-success" value="1" selected="selected">Approved</option>' : '<option class="bg-success" value="1">Approved</option>';
        $select_status .= ($user->status == 2) ? '<option class="bg-danger" value="2" selected="selected">Disapproved</option>' : '<option class="bg-danger" value="2">Disapproved</option>';
        $select_status .= '</select>';


        $EMPLOYEE[] = "<tr class=\"report-row\"><td><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$user->date."')\">".$user->name."</a></td><td>".$user->company."</td><td>".totalTime($user->time)."</td><td>".date("D, M. d Y", strtotime($user->date))."</td><td>".$user->employee_note."</td><td><input data-id=\"".$user->review_id."\" id=\"input_admin_note_".$user->review_id."\" type=\"text\" placeholder=\"Type a note...\" class=\"form-control admin-note\" value=\"".$user->admin_note."\" /></td><td>".$select_status."</td>";
        
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
    $printButton = '<span id="print-report-button" onclick="printReport(\'8\',\''.$start_date.'\',\''.$end_date.'\')" class="glyphicon glyphicon-print"></span>';
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><div class="container"><div class="row"><div class="col-xs-9"><span class="table-header">All Employee Hours</span><br><span class="table-sub-header"> from '.$modifiedStartDate.' to '.$modifiedEndDate.'</span></div><div class="col-xs-3 text-left"> '.$printButton.'</div></div></div></td></tr>';
    
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

    if ($is2weeks)
    {
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Total Week 1</a>'.$CARET[3].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Total Week 2</a>'.$CARET[4].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">Total Regular</a>'.$CARET[5].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',6,'.$SORT_ORDER[6].')">Total Overtime</a>'.$CARET[6].'</td>';
        $tableHeader .= '<td>Last Review Date</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',7,'.$SORT_ORDER[7].')">Total</a>'.$CARET[7].'</td>';
    }
    else
    {
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Total Regular</a>'.$CARET[3].'</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Total Overtime</a>'.$CARET[4].'</td>';
        $tableHeader .= '<td>Last Review Date</td>';
        $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',5,'.$SORT_ORDER[5].')">Total</a>'.$CARET[5].'</td>';
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
                    ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

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
elseif ($in_report == 9) // IF the in_report == 9, get the Employees with Keys in the company
{
    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employees with Keys";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Employees with Keys</span><br><span class="table-sub-header">on '.$displayToday.'</span></td></tr>';
    $totalTitle = "Total Employees with Keys:";
    $customTableFooter = 1;

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td>#</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Has Key?</a>'.$CARET[2].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'has_key');

    // get names and has_key values of the Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            E.has_key
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
    $rowCount = 1;
    $employeesWithKeyCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;


        $select_has_key = '<select class="form-control has-key" id="select_has_key_'.$user->id.'" name="select_has_key_'.$user->id.'" onchange="updateHasKey('.$user->id.')">';
        $select_has_key_values = ($user->has_key == 1) ? '<option class="bg-success" value="1" selected="selected">Yes</option><option class="bg-danger" value="0">No</option>' : '<option class="bg-success" value="1">Yes</option><option class="bg-danger" value="0" selected="selected">No</option>';
        $select_has_key .= $select_has_key_values;
        $select_has_key .= '</select>';


        if ($user->has_key == 1) {$employeesWithKeyCount++;}


        $EMPLOYEE[] = "<tr class=\"report-row\"><td>".$rowCount."</td><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td>".$select_has_key."</td>";
    
        // update the row counter
        $rowCount++;
    }

    $tableFooter = '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>'.$totalTitle.'</h3></td><td class="text-center"><h3 id="tableTotalValue">'.$employeesWithKeyCount.'</h3>';
}
elseif ($in_report == 10) // IF the in_report == 10, get the Employees Alarm Codes in the company
{
    // setup variables
    $totalTitleSpan = 3;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employee Alarm Codes";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Employee Alarm Codes</span><br><span class="table-sub-header">on '.$displayToday.'</span></td></tr>';
    $totalTitle = "Total Employees with Alarm Codes:";
    $customTableFooter = 1;

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td>#</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Alarm Code</a>'.$CARET[2].'</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'has_key');

    // get names and alarm codes of the Employees in the company
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            E.alarm_code
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
    $rowCount = 1;
    $employeesWithAlarmCodesCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;

        if ($user->alarm_code != "") {$employeesWithAlarmCodesCount++;}

        $EMPLOYEE[] = "<tr class=\"report-row\"><td>".$rowCount."</td><td><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td><input data-id=\"".$user->id."\" id=\"input_alarm_code_".$user->id."\" type=\"text\" class=\"input-lg form-control alarm-code\" value=\"".$user->alarm_code."\" /></td>";
    
        // update the row counter
        $rowCount++;
    }

    $tableFooter = '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>'.$totalTitle.'</h3></td><td class="text-center"><h3 id="tableTotalValue">'.$employeesWithAlarmCodesCount.'</h3>';
}
elseif ($in_report == 11) // IF the in_report == 11, get the Employee Review Schedule in the company
{
    // setup variables
    $employeeReviewTimeFrame = (isset($_SESSION['ertf'])) ? preg_replace("/[^0-9]/", "", $_SESSION['ertf']) : 2;
    $timeFrameValues = array('All Employee Reviews','Missed Employee Reviews','Upcoming Employee Reviews');

    $timeFrameCount = 0;
    $timeFrameOptions = "";
    foreach ($timeFrameValues as $value)
    {
        $timeFrameOptions .= ($employeeReviewTimeFrame == $timeFrameCount) ? '<option value="'.$timeFrameCount.'" selected="selected">'.$value.'</option>' : '<option value="'.$timeFrameCount.'">'.$value.'</option>';
        $timeFrameCount++;
    }


    $totalTitleSpan = 7;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Employee Review Schedule";
    $tableTitle  = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Employee Review Schedule</span><br><span class="table-sub-header">for '.$displayToday.'</span></td></tr>';
    $tableTitle .= '<tr>
                        <td colspan="'.$totalTitleSpan.'">
                            <div class="row">
                                <div class="col-xs-4">
                                    <h5 class="text-right"><strong>Select Review Type:</strong></h5>
                                </div>
                                <div class="col-xs-8 col-sm-4">
                                    <select class="form-control" id="employee-review-timeframe" name="employee-review-timeframe" onchange="updateEmployeeReviewTimeFrame()">
                                        '.$timeFrameOptions.'
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>';
    $totalTitle = "Total Employee Reviews:";
    $customTableFooter = 1;

    // set default starting sort values
    $in_sort_type = (isset($_POST['st'])) ? preg_replace("/[^0-9]/", "", $_POST['st']) : 3;

    // setup the SORT_ORDER array
    // FILLS the SORT_ORDER array with totalTitleSpan number of values and sets the value to 1 (0 = DESC, 1 = ASC)
    $SORT_ORDER = array_fill(1, $totalTitleSpan, 1);
    $in_sort_order = ($in_sort_order == 1) ? 0 : 1;
    $SORT_ORDER[$in_sort_type] = $in_sort_order;

    // setup the CARET array
    $CARET = array_fill(1, $totalTitleSpan, "");
    $CARET[$in_sort_type] = ($in_sort_order == "ASC") ? '<span class="caret caret-reversed"></span>' : '<span class="caret"></span>';

    // create the table header
    $tableHeader = '<td>#</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',3,'.$SORT_ORDER[3].')">Review Date</a>'.$CARET[3].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',4,'.$SORT_ORDER[4].')">Reviewed</a>'.$CARET[4].'</td>';
    // $tableHeader .= '<td>Notes</td>';
    $tableHeader .= '<td></td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'review_date');

    // setup the EMPLOYEE_REVIEW_TIMEFRAME array
    $EMPLOYEE_REVIEW_TIMEFRAME = array("!= '0000-00-00 00:00:00'","< CURDATE() AND ER.reviewed = 0",">= CURDATE()");

    $DISTINCT = ($employeeReviewTimeFrame == 1) ? "DISTINCT" : "";

    // get names, company, review_id, review_date of the Employees in the company
    $sql = "SELECT $DISTINCT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name,
            C.name AS company,
            ER.id AS review_id,
            ER.date AS review_date,
            ER.reviewed AS reviewed
            FROM Employee E, Company C, Employee_Review ER
            WHERE E.companyID = C.id 
            AND E.id = ER.eid
            AND E.companyID IN 
            (
                SELECT CA.cid 
                FROM Company_Administrator CA
                WHERE CA.aid = $adminID
            )
            AND E.active = 1
            AND ER.date $EMPLOYEE_REVIEW_TIMEFRAME[$employeeReviewTimeFrame]
            ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";

    // set the row counter
    $rowCount = 1;
    $employeeReviewCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;


        $reviewDateHTML = '
            <button id="review-date-button-'.$user->review_id.'" class="btn btn-block btn-default employee-review-dropdown" data-id="'.$user->review_id.'" onclick="toggle(\'#main-review-date-'.$user->review_id.'\',500)">
                <span id="main-review-date-header-'.$user->review_id.'" class="main-review-header">'.date("F j, Y", strtotime($user->review_date)).'</span> <span id="review-date-caret-'.$user->review_id.'" class="employee-review-caret caret"></span>
            </button>
            <div id="main-review-date-'.$user->review_id.'" class="employee-review-datepicker-container">
                <div id="employee-review-datepicker-'.$user->review_id.'" data-id="'.$user->review_id.'" class="employee-review-datepicker"></div>
                <input class="startDate" type="hidden" id="review-date-'.$user->review_id.'" name="review-date-'.$user->review_id.'" value="'.date("Y-m-d", strtotime($user->review_date)).'" />
            </div>
            <script>
            $(document).ready(function(){
                $("#employee-review-datepicker-'.$user->review_id.'").datepicker( {
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    onSelect: function(dateText, inst) { 
                        var date = $(this).datepicker("getDate");
                        var reviewID = $(this).data("id");
                        $("#review-date-"+reviewID).val($.datepicker.formatDate("yy-mm-dd", date));
                        $("#main-review-date-header-"+reviewID).html($.datepicker.formatDate("MM d, yy", date));
                        $("#review-date-button-"+reviewID).trigger("click");
                    }
                });

                $("#employee-review-datepicker-'.$user->review_id.'").datepicker("setDate", new Date('.date("Y", strtotime($user->review_date)).','.(date("n", strtotime($user->review_date))-1).','.date("j", strtotime($user->review_date)).'));
                
                console.log("date: "+$("#employee-review-datepicker-'.$user->review_id.'").datepicker("getDate"));
            });
            </script>
        ';


        $reviewedSelectHTML  = '<select id="reviewed-select-'.$user->review_id.'" class="form-control reviewed-select">';
        $reviewedSelectHTML .= ($user->reviewed == 0) ? '<option value="0" selected="selected">No</option><option value="1">Yes</option>' : '<option value="0">No</option><option value="1" selected="selected">Yes</option>';
        $reviewedSelectHTML .= '</select>';

        $reviewNoteHTML = '<input type="text" id="review-note-'.$user->review_id.'" class="form-control review-note-input" placeholder="Type Note..." />';

        $reviewSaveButtonHTML = '<div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <button data-id="'.$user->review_id.'" onclick="saveEmployeeReview('.$user->review_id.')" id="review-save-button-'.$user->review_id.'" class="btn btn-block btn-primary review-note-button">Save</button>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="review-save-message" id="review-save-message-'.$user->review_id.'"></div>
                                    </div>
                                 </div>
        ';

        $missedReview = ($user->reviewed == 0) ? ' missed-review' : '';

        // $EMPLOYEE[] = "<tr class=\"report-row".$missedReview."\"><td class=\"report-cell\">".$rowCount."</td><td class=\"report-cell\"><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td><td class=\"report-cell\">".$user->company."</td><td class=\"report-cell\">".$reviewDateHTML."</td><td class=\"report-cell\">".$reviewedSelectHTML."</td><td class=\"report-cell\">".$reviewNoteHTML."</td><td class=\"report-cell\">".$reviewSaveButtonHTML."</td>";
        
        $EMPLOYEE[] = "<tr class=\"report-row".$missedReview."\">
                           <td class=\"report-cell\">".$rowCount."</td>
                           <td class=\"report-cell\"><a href=\"#\" onclick=\"changeUser(".$user->id.")\">".$user->name."</a></td>
                           <td class=\"report-cell\">".$user->company."</td>
                           <td class=\"report-cell\">".$reviewDateHTML."</td>
                           <td class=\"report-cell\">".$reviewedSelectHTML."</td>
                           <td class=\"report-cell\"><a href=\"#\" onclick=\"setupModal('employee-review')\" id=\"employee-review-link\" data-toggle=\"modal\" data-target=\"#employee-review-modal\">Edit</a></td>";

        // update the counter
        $employeeReviewCount++;

        // get the current employee's Next Employee Review Notes
        $sql = "SELECT ERN.id,
                ERN.note,
                (
                    SELECT CONCAT(E2.first_name,' ',E2.last_name) AS admin_name
                    FROM Administrator A, Employee E2
                    WHERE ERN.aid = A.id
                    AND A.eid = E2.id
                ) AS admin_name
                FROM Employee E, Employee_Review ER, Employee_Review_Note ERN 
                WHERE E.id = $user->id
                AND ER.id = $user->review_id
                AND E.id = ER.eid
                AND ER.id = ERN.erid";

        $reviewNotesQuery = mysql_query($sql);

        // print out the review notes
        while ($reviewNotes = mysql_fetch_object($reviewNotesQuery))
        {
            $reviewNoteDeleteButton = '<button data-id="'.$reviewNotes->id.'" onclick="deleteEmployeeReviewNote('.$user->review_id.','.$reviewNotes->id.')" id="review-note-button-'.$reviewNotes->id.'" class="btn btn-sm btn-danger"><span class="fa fa-times"></span></button>';
            $EMPLOYEE[] = '<tr class="report-row"><td></td><td colspan="'.($totalTitleSpan-2).'"><span class="admin-review-note"><i>'.$reviewNotes->note.'</i></span></td><td>'.$reviewNoteDeleteButton.'</td>';
        }


        // update the row counter
        $rowCount++;
    }

    $tableFooter = '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>'.$totalTitle.'</h3></td><td class="text-center"><h3 id="tableTotalValue">'.$employeeReviewCount.'</h3>';
}
elseif ($in_report == 12) // IF the in_report == 12, get the Vacation Time Requests in the company
{
    // setup variables
    $vacationTimeRequestStatus = (isset($_SESSION['vtrs'])) ? preg_replace("/[^0-9]/", "", $_SESSION['vtrs']) : 0;
    $vacationTimeRequestValues = array('Not Reviewed','Approved','Disapproved');

    $vacationTimeRequestCount = 0;
    $vacationTimeRequestOptions = "";
    foreach ($vacationTimeRequestValues as $value)
    {
        $vacationTimeRequestOptions .= ($vacationTimeRequestStatus == $vacationTimeRequestCount) ? '<option value="'.$vacationTimeRequestCount.'" selected="selected">'.$value.'</option>' : '<option value="'.$vacationTimeRequestCount.'">'.$value.'</option>';
        $vacationTimeRequestCount++;
    }

    $totalTitleSpan = 6;
    $totalColSpan = $totalTitleSpan-1;
    $pageTitle = "AMS Timecard | Vacation Requests";
    $tableTitle = '<tr><td colspan="'.$totalTitleSpan.'"><span class="table-header">Vacation Requests</span></td></tr>';
    $tableTitle .= '<tr>
                        <td colspan="'.$totalTitleSpan.'">
                            <div class="row">
                                <div class="col-xs-4">
                                    <h5 class="text-right"><strong>Select a Status:</strong></h5>
                                </div>
                                <div class="col-xs-8 col-sm-4">
                                    <select class="form-control" id="vacation-time-request-status" name="vacation-time-request-status" onchange="updateVacationTimeRequestGlobalStatus()">
                                        '.$vacationTimeRequestOptions.'
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>';
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
    $tableHeader  = '<td><a href="#" onclick="sortReport('.$in_report.',1,'.$SORT_ORDER[1].')">Name</a>'.$CARET[1].'</td>';
    $tableHeader .= '<td><a href="#" onclick="sortReport('.$in_report.',2,'.$SORT_ORDER[2].')">Company</a>'.$CARET[2].'</td>';
    $tableHeader .= '<td>Requested Dates</td>';
    $tableHeader .= '<td>Employee Note</td>';
    $tableHeader .= '<td>Admin Note</td>';
    $tableHeader .= '<td>Status</td>';

    // setup the SORT_TYPE array
    $SORT_TYPE = array('', 'name', 'company', 'dates');

    // get names, company and requested vacation dates of the Employees in the company
    // status: 0 = Not yet reviewed, 1 = Approved, 2 = Disapproved
    $sql = "SELECT E.id, 
            CONCAT(E.first_name,' ',E.last_name) AS name, 
            C.name AS company,
            R.id AS request_id,
            (
                SELECT GROUP_CONCAT(RVD.date SEPARATOR '|') AS dates
                FROM Request_Vacation_Date RVD
                WHERE RVD.rvtid = R.id
                ORDER BY RVD.date ASC
            ) AS dates,
            R.status,
            R.employee_note,
            R.admin_note
            FROM Employee E, Company C, Request_Vacation_Time R
            WHERE E.id = R.eid
            AND E.companyID = C.id 
            AND R.status = $vacationTimeRequestStatus
            AND E.companyID IN 
            (
                SELECT CA.cid 
                FROM Company_Administrator CA
                WHERE CA.aid = $adminID
            )
            AND E.active = 1
            HAVING `dates` != '0000-00-00'
            ORDER BY $SORT_TYPE[$in_sort_type] $sort_order";


    // set the row counter
    $rowCount = 0;

    $query = mysql_query($sql);
    while ($user = mysql_fetch_object($query))
    {
        $EID[] = $user->id;
        $modifiedDate = preg_replace('/-/', ' ', $user->date, 1);


        $select_status  = '<select class="form-control status" id="select_status_'.$user->request_id.'" name="select_status_'.$user->request_id.'" onchange="updateVacationTimeRequestStatus('.$user->request_id.')">';
        $select_status .= ($user->status == 0) ? '<option value="0" selected="selected">Not reviewed</option>' : '<option value="0">Not yet reviewed</option>';
        $select_status .= ($user->status == 1) ? '<option class="bg-success" value="1" selected="selected">Approved</option>' : '<option class="bg-success" value="1">Approved</option>';
        $select_status .= ($user->status == 2) ? '<option class="bg-danger" value="2" selected="selected">Disapproved</option>' : '<option class="bg-danger" value="2">Disapproved</option>';
        $select_status .= '</select>';


        if ( (is_null($user->dates)) || ($user->dates == "") ) {$PRINT_REQUESTED_DATES = "";}
        else
        {
            $REQUESTED_DATES = explode('|', $user->dates);

            $PRINT_REQUESTED_DATES = '<table class="table table-striped table-hover table-bordered">';

            foreach ($REQUESTED_DATES as $rdate)
            {
                // IF the displayed status is "Not Reviewed", allow the hours to be typed in
                if ($vacationTimeRequestStatus == 0)
                {
                    $PRINT_REQUESTED_DATES .= '<tr>
                                                 <td>'.date("D, M. d Y", strtotime($rdate)).'</td>
                                                 <td><input type="text" value="8.0" placeholder="0.0" data-date="'.$rdate.'" class="request-hours-'.$user->request_id.'" style="max-width: 40px;" /> hours</td>
                                               </tr>';
                }
                else
                {
                    $sql = "SELECT VT.time
                            FROM Vacation_Time VT
                            WHERE VT.eid = $user->id
                            AND VT.date = '$rdate'";
                    
                    $qdate = mysql_query($sql);
                    $vacation = mysql_fetch_object($qdate);
                    $time = $vacation->time;

                    $TIME = explode(":", $time);
                    $hours = $TIME[0];
                    $minutes = $TIME[1];
                    $hours_minutes = number_format($hours+($minutes/60), 1);

                    $PRINT_REQUESTED_DATES .= '<tr>
                                                 <td>'.date("D, M. d Y", strtotime($rdate)).'</td>
                                                 <td>'.$hours_minutes.'<input type="text" value="8.0" placeholder="0.0" data-date="'.$rdate.'" class="hide request-hours-'.$user->request_id.'" style="max-width: 40px;" /> hrs</td>
                                               </tr>';
                }
            }

            $PRINT_REQUESTED_DATES .= '</table>';
        }


        $EMPLOYEE[] = "<tr class=\"report-row\">
                         <td><a href=\"#\" onclick=\"changeUser(".$user->id.",'".$user->date."')\">".$user->name."</a></td>
                         <td>".$user->company."</td>
                         <td>".$PRINT_REQUESTED_DATES."</td>
                         <td>".$user->employee_note."</td>
                         <td><input data-id=\"".$user->request_id."\" id=\"input_admin_note_".$user->request_id."\" type=\"text\" placeholder=\"Type a note...\" class=\"form-control admin-note\" value=\"".$user->admin_note."\" /></td>
                         <td>".$select_status."</td>";
        
        // update the row counter
        $rowCount++;
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
            print $e."</tr>";
        }

        // print the Employee total as the table footer

        if ($customTableFooter != 1)
        {
            print '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>'.$totalTitle.'</h3></td><td class="text-center"><h3>'.count($EMPLOYEE).'</h3>';
        }
        else
        {
            print $tableFooter;
        }

        // print the hidden inputs
        print '<input type="hidden" class="hide" id="pageTitleNew" name="pageTitleNew" value="'.$pageTitle.'" />';
        print '</td></tr>';

        ?>
    </tbody>
</table>

<style type="text/css">

    <?php if ($rowCount >= 10): ?>
        #user-table-div {
            overflow: auto;
        }
    <?php endif; ?>


    <?php if ($in_report == 12): ?>
        #user-table-div {
            overflow-y: auto;
        }
    <?php endif; ?>


    .employee-review-datepicker .ui-state-active {
        background: #00cc00;
        color: #fff;
        font-weight: 700;
    }

</style>


<!-- Internal Javascript -->
<script type="text/javascript">
$(document).ready(function(){


    <?php if ($in_report == 7): ?>
        $(".admin-note").on('input propertychange paste', function () {
            var review_id = $(this).data("id");
            var admin_note = $(this).val();
            updatePersonalDayAdminNote(review_id,admin_note);
        });
    <?php endif; ?>


    <?php if ($in_report == 10): ?>
        $('.alarm-code').on('input propertychange paste', function() {
            updateAlarmCode($(this).data("id"));
        });
    <?php endif; ?>


    <?php if ($in_report == 11): ?>
        // setup variables
        var startDate;
        var endDate;
        var d = new Date();
        var month = d.getMonth()+1;
        var day = d.getDate();
        month = format2Digit(month);
        day = format2Digit(day);
        var today = d.getFullYear()+"-"+month+"-"+day;

        $('.employee-review-dropdown').click(function() {

            var reviewID = $(this).data("id");

            if ($('#review-date-caret-'+reviewID).hasClass('caret-reversed'))
            {
                $('#review-date-caret-'+reviewID).removeClass('caret-reversed');
            }
            else {$('#review-date-caret-'+reviewID).addClass('caret-reversed');}
        });

    <?php endif; ?>


    <?php if ($in_report == 12): ?>
        $(".admin-note").on('input propertychange paste', function () {
            var request_id = $(this).data("id");
            var admin_note = $(this).val();
            updateVacationTimeAdminNote(request_id,admin_note);
        });
    <?php endif; ?>



});
</script>