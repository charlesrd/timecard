<?php


require_once("config.php");


// get the incoming variables
$uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST["uid"]) : "";
$in_date = (isset($_POST['d'])) ? preg_replace("/[^-0-9]/", "", $_POST['d']) : "";
$in_uid = (isset($_POST['n'])) ? str_replace(' ', '', $_POST["n"]) : "";

$in_vacation_hour = (isset($_POST['vh'])) ? preg_replace("/[^0-9]/", "", $_POST['vh']) : "";
$in_vacation_minute = (isset($_POST['vm'])) ? preg_replace("/[^0-9]/", "", $_POST['vm']) : "";
$in_vacation_note = (isset($_POST['vn'])) ? $_POST['vn'] : "";
$in_vacation_employee_list_hour = (isset($_POST['velh'])) ? preg_replace("/[^\|0-9]/", "", $_POST['velh']) : "";
$in_vacation_employee_list_minute = (isset($_POST['velm'])) ? preg_replace("/[^\|0-9]/", "", $_POST['velm']) : "";
$in_vacation_employee_list_id = (isset($_POST['velid'])) ? preg_replace("/[^\|0-9]/", "", $_POST['velid']) : "";
$in_vacation_edit = (isset($_POST['ve'])) ? preg_replace("/[^0-9]/", "", $_POST['ve']) : "";

$in_holiday_hour = (isset($_POST['hh'])) ? preg_replace("/[^0-9]/", "", $_POST['hh']) : "";
$in_holiday_minute = (isset($_POST['hm'])) ? preg_replace("/[^0-9]/", "", $_POST['hm']) : "";
$in_holiday_employee_list_hour = (isset($_POST['helh'])) ? preg_replace("/[^\|0-9]/", "", $_POST['helh']) : "";
$in_holiday_employee_list_minute = (isset($_POST['helm'])) ? preg_replace("/[^\|0-9]/", "", $_POST['helm']) : "";
$in_holiday_employee_list_id = (isset($_POST['helid'])) ? preg_replace("/[^\|0-9]/", "", $_POST['helid']) : "";
$in_holiday_edit = (isset($_POST['he'])) ? preg_replace("/[^0-9]/", "", $_POST['he']) : "";

$in_personal_hour = (isset($_POST['ph'])) ? preg_replace("/[^0-9]/", "", $_POST['ph']) : "";
$in_personal_minute = (isset($_POST['pm'])) ? preg_replace("/[^0-9]/", "", $_POST['pm']) : "";
$in_personal_employee_list_hour = (isset($_POST['pelh'])) ? preg_replace("/[^\|0-9]/", "", $_POST['pelh']) : "";
$in_personal_employee_list_minute = (isset($_POST['pelm'])) ? preg_replace("/[^\|0-9]/", "", $_POST['pelm']) : "";
$in_personal_employee_list_id = (isset($_POST['pelid'])) ? preg_replace("/[^\|0-9]/", "", $_POST['pelid']) : "";
$in_personal_edit = (isset($_POST['pe'])) ? preg_replace("/[^0-9]/", "", $_POST['pe']) : "";

$in_sick_hour = (isset($_POST['sh'])) ? preg_replace("/[^0-9]/", "", $_POST['sh']) : "";
$in_sick_minute = (isset($_POST['sm'])) ? preg_replace("/[^0-9]/", "", $_POST['sm']) : "";
$in_sick_employee_list_hour = (isset($_POST['selh'])) ? preg_replace("/[^\|0-9]/", "", $_POST['selh']) : "";
$in_sick_employee_list_minute = (isset($_POST['selm'])) ? preg_replace("/[^\|0-9]/", "", $_POST['selm']) : "";
$in_sick_employee_list_id = (isset($_POST['selid'])) ? preg_replace("/[^\|0-9]/", "", $_POST['selid']) : "";
$in_sick_absence = (isset($_POST['sat'])) ? preg_replace("/[^0-9]/", "", $_POST['sat']) : "";
$in_sick_edit = (isset($_POST['se'])) ? preg_replace("/[^0-9]/", "", $_POST['se']) : "";

$in_personal_date = (isset($_POST['pd'])) ? preg_replace("/[^-0-9]/", "", $_POST['pd']) : "";
$in_personal_time = (isset($_POST['pt'])) ? preg_replace("/[^0-9:]/", "", $_POST['pt']) : "";
$in_personal_note = (isset($_POST['pn'])) ? $_POST['pn'] : "";

$in_vacation_date = (isset($_POST['vd'])) ? preg_replace("/[^-0-9, ]/", "", $_POST['vd']) : "";
$in_vacation_employee_note = (isset($_POST['ven'])) ? $_POST['ven'] : "";



// setup variables
$today = date('Y-m-d');
$start_date = "";
$end_date = "";
$DAY = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$showClockIn = true;
$showClockInNew = true;
$hideClockTime = 0;
$pageTitle = "";
$isCurrentWeek = 0;
$forgotClockIn = 0;
$forgotClockOut = 0;
$rowCount = 0;

$V_USER_HR = explode("|", $in_vacation_employee_list_hour);
$V_USER_MIN = explode("|", $in_vacation_employee_list_minute);
$V_USER_ID = explode("|", $in_vacation_employee_list_id);

$H_USER_HR = explode("|", $in_holiday_employee_list_hour);
$H_USER_MIN = explode("|", $in_holiday_employee_list_minute);
$H_USER_ID = explode("|", $in_holiday_employee_list_id);

$P_USER_HR = explode("|", $in_personal_employee_list_hour);
$P_USER_MIN = explode("|", $in_personal_employee_list_minute);
$P_USER_ID = explode("|", $in_personal_employee_list_id);

$S_USER_HR = explode("|", $in_sick_employee_list_hour);
$S_USER_MIN = explode("|", $in_sick_employee_list_minute);
$S_USER_ID = explode("|", $in_sick_employee_list_id);

// check whether or not to include the timecard functions
if (!isset($includeTimecardFunctions))
{
    // include functions
    // include("timecard_functions.php");
}

// get the start and end dates of the current week
list($start_date, $end_date) = x_week_range($today);
$isCurrentWeek = 1;

// IF the in_date is not empty, get the start and end dates of the in_date week
if ($in_date != "")
{
    $hideClockTime = 1;
    list($start_date, $end_date) = x_week_range($in_date);

    // check if this is the current week
    if ( (strtotime($start_date) <= strtotime($today)) && (strtotime($end_date) >= strtotime($today)) )
    {
        $hideClockTime = 0;
        $isCurrentWeek = 1;
    }
}

// IF the in_uid is not empty, reset the userID,uname and get the start and end dates of this week
if ($in_uid != "")
{
    $hideClockTime = 0;
    $userID = ($in_uid == 0) ? $userID : $in_uid;
    if ($in_date == "") {list($start_date, $end_date) = x_week_range($today);}

    // check if this is the current week
    if ( (strtotime($start_date) <= strtotime($today)) && (strtotime($end_date) >= strtotime($today)) )
    {
        $isCurrentWeek = 1;
    }

    // get the user's info
    $sql = "SELECT E.*, CONCAT(first_name, ' ', last_name) AS name 
            FROM Employee E 
            WHERE E.id = $userID";

    $query = mysql_query($sql);
    $user = mysql_fetch_object($query);
    $uname = $user->name;
    $start_time = $user->start_time;
    $_SESSION["uid"] = $userID;
    $_SESSION["uname"] = $uname;


    // print "userID: ".$userID."<br>";
    // print "uid: ".$uid."<br>";
    // print "in_date: ".$in_date."<br>";
    // print "in_uid: ".$in_uid."<br>";
    // print "start_date: ".$start_date."<br>";
    // print "end_date: ".$end_date."<br>";
    // exit;

}

// IF the in_vacation times are not empty, save the vacation time to the database
if ( ($in_vacation_hour != "") && ($in_vacation_minute != "") )
{
    // correct the times if they are < 10
    $in_vacation_hour = ($in_vacation_hour < 10) ? "0".$in_vacation_hour : $in_vacation_hour;
    $in_vacation_minute = ($in_vacation_minute < 10) ? "0".$in_vacation_minute : $in_vacation_minute;

    // set the vacation time
    $vacation_time = $in_vacation_hour.":".$in_vacation_minute.":00";

    // IF the in_vacation_edit >= 1, then remove the old vacation times for this user on this date
    if ($in_vacation_edit >= 1)
    {
        // delete the record from the Vacation_Time
        $q = $db_pdo->prepare("DELETE FROM `timecard`.`Vacation_Time` WHERE `Vacation_Time`.`eid` = :userID AND `Vacation_Time`.`date` = :in_date");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error deleting Vacation_Time: " . $e->getMessage() . "</br>";
        }
    }

    // IF the in_uid != 0 AND in_edit != 2 (delete), then save the vacation time for this employee
    if ( ($in_uid != 0) && ($in_vacation_edit != 2) )
    {
        // only insert a new Vacation_Time record if the hour and minute are not == 0
        // if (!( (intval($in_vacation_hour == 0)) && (intval($in_vacation_minute == 0)) ))
        // {


        // INSERT the new Vacation_Time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Vacation_Time` (`id`, `eid`, `time`, `date`, `note`) VALUES (NULL, :userID, :time, :in_date, :in_note)");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':time' => $vacation_time, ':in_date' => $in_date, ':in_note' => $in_vacation_note));
            $labID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Vacation_Time: " . $e->getMessage() . "</br>";
        }


        // }
    }
    else
    {
        // IF the in_uid == 0, then save the vacation time for All Employees in the company(s) in this company group
        $query = mysql_query("SELECT E.id
                              FROM Employee E
                              WHERE E.companyID IN 
                              (
                                SELECT CA.cid 
                                FROM Company_Administrator CA
                                WHERE CA.aid = $adminID
                              )
                              ORDER BY E.id ASC");
        $EMPLOYEE_ID = array();
        while ($employee = mysql_fetch_object($query))
        {
            $EMPLOYEE_ID[] = $employee->id;
        }

        // setup the counter
        $i = 0;

        // loop through each employee id from the $V_USER_ID array
        foreach ($V_USER_ID as $eid)
        {
            // make sure the times are not empty values
            $V_USER_HR[$i] = (empty($V_USER_HR[$i])) ? 0 : intval($V_USER_HR[$i]);
            $V_USER_MIN[$i] = (empty($V_USER_MIN[$i])) ? 0 : intval($V_USER_MIN[$i]);

            // correct the times if they are < 10
            $V_USER_HR[$i] = ($V_USER_HR[$i] < 10) ? "0".$V_USER_HR[$i] : $V_USER_HR[$i];
            $V_USER_MIN[$i] = ($V_USER_MIN[$i] < 10) ? "0".$V_USER_MIN[$i] : $V_USER_MIN[$i];

            // set the vacation time
            $vacation_time = $V_USER_HR[$i].":".$V_USER_MIN[$i].":00";

            // only insert a new Vacation_Time record if the hour and minute are not == 0
            if (!( (intval($V_USER_HR[$i]) == 0) && (intval($V_USER_MIN[$i]) == 0) ))
            {
                // INSERT the new Vacation_Time for the current employee id
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Vacation_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :userID, :time, :in_date)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':userID' => $eid, ':time' => $vacation_time, ':in_date' => $in_date));
                    $labID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new ALL Vacation_Time: " . $e->getMessage() . "</br>";
                }
            }

            // update the counter
            $i++;
        }
    }
}

// IF the in_holiday times are not empty, save the holiday time to the database
if ( ($in_holiday_hour != "") && ($in_holiday_minute != "") )
{
    // correct the times if they are < 10
    $in_holiday_hour = ($in_holiday_hour < 10) ? "0".$in_holiday_hour : $in_holiday_hour;
    $in_holiday_minute = ($in_holiday_minute < 10) ? "0".$in_holiday_minute : $in_holiday_minute;

    // set the holiday time
    $holiday_time = $in_holiday_hour.":".$in_holiday_minute.":00";

    // IF the in_holiday_edit >= 1, then remove the old holiday times for this user on this date
    if ($in_holiday_edit >= 1)
    {
        // delete the record from the Holiday_Time
        $q = $db_pdo->prepare("DELETE FROM `timecard`.`Holiday_Time` WHERE `Holiday_Time`.`eid` = :userID AND `Holiday_Time`.`date` = :in_date");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error deleting Holiday_Time: " . $e->getMessage() . "</br>";
        }
    }

    // IF the in_uid != 0 AND in_edit != 2 (delete), then save the holiday time for this employee
    if ( ($in_uid != 0) && ($in_holiday_edit != 2) )
    {
        // only insert a new Holiday_Time record if the hour and minute are not == 0
        if (!( (intval($in_holiday_hour == 0)) && (intval($in_holiday_minute == 0)) ))
        {
            // INSERT the new Holiday_Time
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Holiday_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :userID, :time, :in_date)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':userID' => $userID, ':time' => $holiday_time, ':in_date' => $in_date));
                $labID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Holiday_Time: " . $e->getMessage() . "</br>";
            }
        }
    }
    else
    {
        // IF the in_uid == 0, then save the holiday time for All Employees in the company(s) in this company group
        $query = mysql_query("SELECT E.id
                              FROM Employee E
                              WHERE E.companyID IN 
                              (
                                SELECT CA.cid 
                                FROM Company_Administrator CA
                                WHERE CA.aid = $adminID
                              )
                              ORDER BY E.id ASC");
        $EMPLOYEE_ID = array();
        while ($employee = mysql_fetch_object($query))
        {
            $EMPLOYEE_ID[] = $employee->id;
        }

        // setup the counter
        $i = 0;

        // loop through each employee id from the $H_USER_ID array
        foreach ($H_USER_ID as $eid)
        {
            // make sure the times are not empty values
            $H_USER_HR[$i] = (empty($H_USER_HR[$i])) ? 0 : intval($H_USER_HR[$i]);
            $H_USER_MIN[$i] = (empty($H_USER_MIN[$i])) ? 0 : intval($H_USER_MIN[$i]);

            // correct the times if they are < 10
            $H_USER_HR[$i] = ($H_USER_HR[$i] < 10) ? "0".$H_USER_HR[$i] : $H_USER_HR[$i];
            $H_USER_MIN[$i] = ($H_USER_MIN[$i] < 10) ? "0".$H_USER_MIN[$i] : $H_USER_MIN[$i];

            // set the holiday time
            $holiday_time = $H_USER_HR[$i].":".$H_USER_MIN[$i].":00";

            // only insert a new Holiday_Time record if the hour and minute are not == 0
            if (!( (intval($H_USER_HR[$i]) == 0) && (intval($H_USER_MIN[$i]) == 0) ))
            {
                // INSERT the new Holiday_Time for the current employee id
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Holiday_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :userID, :time, :in_date)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':userID' => $eid, ':time' => $holiday_time, ':in_date' => $in_date));
                    $labID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new ALL Holiday_Time: " . $e->getMessage() . "</br>";
                }
            }

            // update the counter
            $i++;
        }
    }
}

// IF the in_personal times are not empty, save the personal time to the database
if ( ($in_personal_hour != "") && ($in_personal_minute != "") )
{
    // correct the times if they are < 10
    $in_personal_hour = ($in_personal_hour < 10) ? "0".$in_personal_hour : $in_personal_hour;
    $in_personal_minute = ($in_personal_minute < 10) ? "0".$in_personal_minute : $in_personal_minute;

    // set the personal time
    $personal_time = $in_personal_hour.":".$in_personal_minute.":00";

    // IF the in_personal_edit >= 1, then remove the old personal times for this user on this date
    if ($in_personal_edit >= 1)
    {
        // delete the record from the Personal_Time
        $q = $db_pdo->prepare("DELETE FROM `timecard`.`Personal_Time` WHERE `Personal_Time`.`eid` = :userID AND `Personal_Time`.`date` = :in_date");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error deleting Personal_Time: " . $e->getMessage() . "</br>";
        }
    }

    // IF the in_uid != 0 AND in_edit != 2 (delete), then save the personal time for this employee
    if ( ($in_uid != 0) && ($in_personal_edit != 2) )
    {
        // only insert a new Personal_Time record if the hour and minute are not == 0
        if (!( (intval($in_personal_hour == 0)) && (intval($in_personal_minute == 0)) ))
        {
            // INSERT the new Personal_Time
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Personal_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :userID, :time, :in_date)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':userID' => $userID, ':time' => $personal_time, ':in_date' => $in_date));
                $labID = $db_pdo->lastInsertId();
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Personal_Time: " . $e->getMessage() . "</br>";
            }
        }
    }
    else
    {
        // IF the in_uid == 0, then save the personal time for All Employees in the company(s) in this company group
        $query = mysql_query("SELECT E.id
                              FROM Employee E
                              WHERE E.companyID IN 
                              (
                                SELECT CA.cid 
                                FROM Company_Administrator CA
                                WHERE CA.aid = $adminID
                              )
                              ORDER BY E.id ASC");
        $EMPLOYEE_ID = array();
        while ($employee = mysql_fetch_object($query))
        {
            $EMPLOYEE_ID[] = $employee->id;
        }

        // setup the counter
        $i = 0;

        // loop through each employee id from the $P_USER_ID array
        foreach ($P_USER_ID as $eid)
        {
            // make sure the times are not empty values
            $P_USER_HR[$i] = (empty($P_USER_HR[$i])) ? 0 : intval($P_USER_HR[$i]);
            $P_USER_MIN[$i] = (empty($P_USER_MIN[$i])) ? 0 : intval($P_USER_MIN[$i]);

            // correct the times if they are < 10
            $P_USER_HR[$i] = ($P_USER_HR[$i] < 10) ? "0".$P_USER_HR[$i] : $P_USER_HR[$i];
            $P_USER_MIN[$i] = ($P_USER_MIN[$i] < 10) ? "0".$P_USER_MIN[$i] : $P_USER_MIN[$i];

            // set the personal time
            $personal_time = $P_USER_HR[$i].":".$P_USER_MIN[$i].":00";

            // only insert a new Personal_Time record if the hour and minute are not == 0
            if (!( (intval($P_USER_HR[$i]) == 0) && (intval($P_USER_MIN[$i]) == 0) ))
            {
                // INSERT the new Personal_Time for the current employee id
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Personal_Time` (`id`, `eid`, `time`, `date`) VALUES (NULL, :userID, :time, :in_date)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':userID' => $eid, ':time' => $personal_time, ':in_date' => $in_date));
                    $labID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new ALL Personal_Time: " . $e->getMessage() . "</br>";
                }
            }

            // update the counter
            $i++;
        }
    }
}



// IF the in_sick times are not empty, save the sick time to the database
if ( ($in_sick_hour != "") && ($in_sick_minute != "") )
{
    // correct the times if they are < 10
    $in_sick_hour = ($in_sick_hour < 10) ? "0".$in_sick_hour : $in_sick_hour;
    $in_sick_minute = ($in_sick_minute < 10) ? "0".$in_sick_minute : $in_sick_minute;

    // set the sick time
    $sick_time = $in_sick_hour.":".$in_sick_minute.":00";


    // IF the in_sick_edit >= 1, then remove the old sick times for this user on this date
    if ($in_sick_edit >= 1)
    {
        // delete the record from the Sick_Time
        $q = $db_pdo->prepare("DELETE FROM `timecard`.`Sick_Time` WHERE `Sick_Time`.`eid` = :userID AND `Sick_Time`.`date` = :in_date");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_date' => $in_date));
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error deleting Sick_Time: " . $e->getMessage() . "</br>";
        }
    }
    

    // IF the in_uid != 0 AND in_edit != 2 (delete), then save the sick time for this employee
    if ( ($in_uid != 0) && ($in_sick_edit != 2) )
    {
        // INSERT the new Sick_Time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Sick_Time` (`id`, `eid`, `time`, `date`, `unexcused`) VALUES (NULL, :userID, :time, :in_date, :absence)");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':time' => $sick_time, ':in_date' => $in_date, ':absence' => $in_sick_absence));
            $labID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Sick_Time: " . $e->getMessage() . "</br>";
        }
    }
    else
    {
        // IF the in_uid == 0, then save the sick time for All Employees in the company(s) in this company group
        $query = mysql_query("SELECT E.id
                              FROM Employee E
                              WHERE E.companyID IN 
                              (
                                SELECT CA.cid 
                                FROM Company_Administrator CA
                                WHERE CA.aid = $adminID
                              )
                              ORDER BY E.id ASC");
        $EMPLOYEE_ID = array();
        while ($employee = mysql_fetch_object($query))
        {
            $EMPLOYEE_ID[] = $employee->id;
        }

        // setup the counter
        $i = 0;

        // loop through each employee id from the $S_USER_ID array
        foreach ($S_USER_ID as $eid)
        {
            // make sure the times are not empty values
            $S_USER_HR[$i] = (empty($S_USER_HR[$i])) ? 0 : intval($S_USER_HR[$i]);
            $S_USER_MIN[$i] = (empty($S_USER_MIN[$i])) ? 0 : intval($S_USER_MIN[$i]);

            // correct the times if they are < 10
            $S_USER_HR[$i] = ($S_USER_HR[$i] < 10) ? "0".$S_USER_HR[$i] : $S_USER_HR[$i];
            $S_USER_MIN[$i] = ($S_USER_MIN[$i] < 10) ? "0".$S_USER_MIN[$i] : $S_USER_MIN[$i];

            // set the sick time
            $sick_time = $S_USER_HR[$i].":".$S_USER_MIN[$i].":00";

            // only insert a new Sick_Time record if the hour and minute are not == 0
            if (!( (intval($S_USER_HR[$i]) == 0) && (intval($S_USER_MIN[$i]) == 0) ))
            {
                // INSERT the new Sick_Time for the current employee id
                $q = $db_pdo->prepare("INSERT INTO `timecard`.`Sick_Time` (`id`, `eid`, `time`, `date`, `unexcused`) VALUES (NULL, :userID, :time, :in_date, :absence)");
                try {
                    $db_pdo->beginTransaction();
                    $q->execute(array(':userID' => $eid, ':time' => $sick_time, ':in_date' => $in_date, ':absence' => $in_sick_absence));
                    $labID = $db_pdo->lastInsertId();
                    $db_pdo->commit();
                } catch(PDOExecption $e) {
                    $db_pdo->rollback();
                    print "Error inserting new ALL Sick_Time: " . $e->getMessage() . "</br>";
                }
            }

            // update the counter
            $i++;
        }
    }
}



// IF the in_personal_date is not empty, save the requested personal time to the database
if ( ($in_personal_date != "") && ($in_personal_date != "0000-00-00") && ($in_personal_date != 0) )
{
    // get the anniversary date for the user
    $sql = "SELECT *
            FROM Employee E
            WHERE E.id = $userID";

    $query = mysql_query($sql);
    $r = mysql_fetch_object($query);
    $selected_year_anniversary_date = date("Y", strtotime($in_personal_date)).date("-m-d",strtotime($r->start_date))." 00:00:00";
    $personal_date = $in_personal_date." 00:00:00";


    // get the number of personal days remaining for this user for the selected period
    if ($personal_date < $selected_year_anniversary_date)
    {
        $sql = "SELECT E.personal_days-
                       (
                           SELECT COUNT(*) AS count
                           FROM Request_Personal_Time RP 
                           WHERE RP.eid = $userID
                           AND RP.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'
                       )-
                       COUNT(*) AS count 
                FROM Personal_Time P, Employee E 
                WHERE P.eid = $userID
                AND P.eid = E.id
                AND P.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
    }
    else
    {
        $sql = "SELECT E.personal_days-
                       (
                           SELECT COUNT(*) AS count
                           FROM Request_Personal_Time RP 
                           WHERE RP.eid = $userID
                           AND RP.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR 
                       )-
                       COUNT(*) AS count 
                FROM Personal_Time P, Employee E 
                WHERE P.eid = $userID
                AND P.eid = E.id
                AND P.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
    }


    $query = mysql_query($sql);
    $personalTime = mysql_fetch_object($query);
    $personalTimeRemaining = $personalTime->count;


    // error_log("personalTimeRemaining: ".$personalTimeRemaining);
    // error_log("personal_days: ".$r->personal_days);


    // IF the personalTimeRemaining <= 0, print 0 and exit
    if ($personalTimeRemaining <= 0)
    {
        print "0";
        error_log("personal time error");
        exit;
    }

    // IF the in_uid != 0 and personalTimeRemaining > 0, then save the requested personal time for this employee
    if ( ($uid != 0) && ($personalTimeRemaining > 0) )
    {
        // INSERT the new Personal_Time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Request_Personal_Time` (`eid`, `time`, `date`, `employee_note`, `created_at`) VALUES (:userID, :in_time, :in_date, :in_note, NOW())");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_time' => $in_personal_time, ':in_date' => $in_personal_date, ':in_note' => $in_personal_note));
            $labID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Request_Personal_Time: " . $e->getMessage() . "</br>";
        }
    }
}


// IF the in_vacation_date is not empty, save the requested vacation time to the database
if ( ($in_vacation_date != "") && ($in_vacation_date != "0000-00-00") )
{
    $VDATES = explode(", ", $in_vacation_date);
    $before_anniversary_count = 0;
    $after_anniversary_count = 0;


    foreach ($VDATES as $vdate)
    {
        // get the anniversary date for the user
        $sql = "SELECT *
                FROM Employee E
                WHERE E.id = $userID";

        $query = mysql_query($sql);
        $r = mysql_fetch_object($query);
        $selected_year_anniversary_date = date("Y", strtotime($vdate)).date("-m-d",strtotime($r->start_date))." 00:00:00";
        $vacation_date = $vdate." 00:00:00";


        // get the number of vacation days remaining for this user for the selected period
        if ($vacation_date < $selected_year_anniversary_date)
        {
            $sql = "SELECT E.vacation_days-
                           (
                                SELECT COUNT(*) AS count
                                FROM Request_Vacation_Time RV, Request_Vacation_Date RD
                                WHERE RV.eid = $userID
                                AND RV.id = RD.rvtid
                                AND RV.status = 0
                                AND RD.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'
                           )-
                           COUNT(*) AS count 
                    FROM Vacation_Time V, Employee E 
                    WHERE V.eid = $userID
                    AND V.eid = E.id
                    AND V.date BETWEEN '$selected_year_anniversary_date' - INTERVAL 1 YEAR AND '$selected_year_anniversary_date'";
            
            $before_anniversary_count++;
        }
        else
        {
            $sql = "SELECT E.vacation_days-
                           (
                                SELECT COUNT(*) AS count
                                FROM Request_Vacation_Time RV, Request_Vacation_Date RD
                                WHERE RV.eid = $userID
                                AND RV.id = RD.rvtid
                                AND RV.status = 0
                                AND RD.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR
                           )-
                           COUNT(*) AS count
                    FROM Vacation_Time V, Employee E
                    WHERE V.eid = $userID
                    AND V.eid = E.id
                    AND V.date BETWEEN '$selected_year_anniversary_date' AND '$selected_year_anniversary_date' + INTERVAL 1 YEAR";
            
            $after_anniversary_count++;
        }


        $query = mysql_query($sql);
        $vacationTime = mysql_fetch_object($query);
        $vacationTimeRemaining = $vacationTime->count;


        // IF the vacationTimeRemaining <= 0, print 0 and exit
        if ( ($vacationTimeRemaining <= 0) || ($vacationTimeRemaining-$before_anniversary_count < 0) || ($vacationTimeRemaining-$after_anniversary_count < 0) )
        {
            print "0";
            error_log("vacation time error");
            exit;
        }
    }
    
    
    // IF the in_uid != 0 and vacationTimeRemaining > 0, then save the requested vacation time for this employee
    if ( ($uid != 0) && ($vacationTimeRemaining > 0) )
    {
        // INSERT the new Request_Vacation_Time
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Request_Vacation_Time` (`id`, `eid`, `status`, `employee_note`, `admin_note`, `created_at`, `updated_at`) VALUES (NULL, :userID, 0, :in_employee_note, '', NOW(), NOW())");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID, ':in_employee_note' => $in_vacation_employee_note));
            $requestVacationTimeID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Request_Vacation_Time: " . $e->getMessage() . "</br>";
        }


        foreach ($VDATES as $vdate)
        {
            // INSERT the new Request_Vacation_Date
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Request_Vacation_Date` (`id`, `rvtid`, `date`) VALUES (NULL, :rvtid, :rvtDate)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':rvtid' => $requestVacationTimeID, ':rvtDate' => $vdate));
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Request_Vacation_Date: " . $e->getMessage() . "</br>";
            }
        }
    }



}

$pageTitle = "AMS Timecard | ".$uname." | ".$start_date." to ".$end_date;

?>
<table id="user-table" class="table table-striped table-bordered table-hover text-center">
    <thead>
        <?php
        
        // check IF the employee is an administrator
        $sql = "SELECT COUNT(*) AS count 
                FROM Administrator A 
                WHERE A.eid = $userID";

        $query = mysql_query($sql);
        $user = mysql_fetch_object($query);
        $adminCount = $user->count;


        // get the user's info
        $sql = "SELECT E.*, CONCAT(first_name, ' ', last_name) AS name 
                FROM Employee E 
                WHERE E.id = $userID";

        $query = mysql_query($sql);
        $user = mysql_fetch_object($query);
        $start_time = $user->start_time;


        // get the average start time for this user
        $sql = "SELECT 
                SEC_TO_TIME(AVG(TIME_TO_SEC(t.starting_time))) AS average_start_time
                FROM
                (
                    SELECT starting_time
                    FROM
                    (
                        SELECT DISTINCT EH.date,
                        (
                            SELECT EH2.clock_in
                            FROM Employee_Hours EH2
                            WHERE EH2.eid = $userID
                            AND EH2.date = EH.date
                            ORDER BY EH2.clock_in ASC
                            LIMIT 1
                        ) AS starting_time
                        FROM Employee_Hours EH
                        WHERE EH.eid = $userID
                        AND EH.date >= '2016-06-01'
                    ) AS t2
                ) AS t";

        $query = mysql_query($sql);
        $employee_hours = mysql_fetch_object($query);
        $average_start_time = $employee_hours->average_start_time;

        $start_time_compare = date("Y-m-d H:i:s", strtotime("2000-01-01 ".$start_time));
        $average_start_time_compare = date("Y-m-d H:i:s", strtotime("2000-01-01 ".$average_start_time));

        $average_start_time_class = ($average_start_time_compare > $start_time_compare) ? 'bg-error' : 'bg-black';
        $averageStartTimeDisplay = '<div class="col-xs-12 col-sm-2"><h5><strong>Your Start Time = '.str_replace(" ", "&nbsp;", date("g:i a", strtotime("2000-01-01 ".$start_time))).'</strong></h5></div>
                                    <div class="col-xs-12 col-sm-2 '.$average_start_time_class.'"><h5><strong>Your Avg. Start Time = '.str_replace(" ", "&nbsp;", date("g:i a", strtotime("2000-01-01 ".$average_start_time))).'</strong></h5></div>
                                    <div class="col-xs-12 col-sm-6"><p id="avg-start-time-message" class="alert alert-sm alert-info">Your Avg. Start Time should be 1-2 minutes prior to your actual start time.</p></div>';


        // print the User's Title and Name
        if ($adminCount > 0)
        {
            $changeWeekLink = '<a href="#" class="header-link" onclick="setupModal(\'view-history-week\')" data-toggle="modal" data-target="#view-history-modal">Change Week</a>';
            print '<tr><td colspan="100"><div class="container-fliud"><div class="row"><div class="col-xs-12 col-sm-2 text-center"><h4><strong><u>Administrator:</u> '.str_replace(" ", "&nbsp;", $uname).'</strong></h4></div>'.$averageStartTimeDisplay.'</div></div></td></tr>';
            // print '<tr><td colspan="100"><div class="container"><div class="row"><div class="col-xs-4"></div><div class="col-xs-4"><u>Administrator:</u> '.str_replace(" ", "&nbsp;", $uname).'</div><div class="col-xs-4 text-left">'.$changeWeekLink.'</div></div></div></td></tr>';
        }
        else
        {
            $changeWeekLink = '<a href="#" class="header-link" onclick="setupModal(\'view-history-week\')" data-toggle="modal" data-target="#view-history-modal">Change Week</a>';
            print '<tr><td colspan="100"><div class="container-fliud"><div class="row"><div class="col-xs-12 col-sm-2 text-center"><h4><strong><u>Employee:</u> '.str_replace(" ", "&nbsp;", $uname).'</strong></h4></div>'.$averageStartTimeDisplay.'</div></div></td></tr>';
            // print '<tr><td colspan="100"><div class="container-fliud"><div class="row"><div class="col-xs-5 text-left"><h3><strong><u>Employee:</u> '.$uname.'</strong></h3></div><div class="col-xs-5"></div><div class="col-xs-2 text-right">'.$changeWeekLink.'</div></div></div></td></tr>';
        }
        

        $sql = "SELECT
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) )
                    FROM Employee_Hours H
                    WHERE H.date >= '$start_date'
                    AND H.date <= '$end_date' 
                    AND H.eid = $userID
                    AND H.clock_out IS NOT NULL
                ) AS totalRegularTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( V.time ) ) ) AS totalVacationTime
                    FROM Vacation_Time V
                    WHERE V.date >= '$start_date'
                    AND V.date <= '$end_date' 
                    AND V.eid = $userID
                ) AS totalVacationTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( HT.time ) ) ) AS totalHolidayTime
                    FROM Holiday_Time HT
                    WHERE HT.date >= '$start_date'
                    AND HT.date <= '$end_date' 
                    AND HT.eid = $userID
                ) AS totalHolidayTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( PT.time ) ) ) AS totalPersonalTime
                    FROM Personal_Time PT
                    WHERE PT.date >= '$start_date'
                    AND PT.date <= '$end_date' 
                    AND PT.eid = $userID
                ) AS totalPersonalTime,
                (
                    SELECT 
                    SEC_TO_TIME( SUM( TIME_TO_SEC( ST.time ) ) ) AS totalSickTime
                    FROM Sick_Time ST
                    WHERE ST.date >= '$start_date'
                    AND ST.date <= '$end_date' 
                    AND ST.eid = $userID
                ) AS totalSickTime";

        $query = mysql_query($sql);
        $total = mysql_fetch_object($query);
        $totalRegularTime = (is_null($total->totalRegularTime)) ? "00:00:00" : $total->totalRegularTime;
        $totalVacationTime = (is_null($total->totalVacationTime)) ? "00:00:00" : $total->totalVacationTime;
        $totalHolidayTime = (is_null($total->totalHolidayTime)) ? "00:00:00" : $total->totalHolidayTime;
        $totalPersonalTime = (is_null($total->totalPersonalTime)) ? "00:00:00" : $total->totalPersonalTime;
        $totalSickTime = (is_null($total->totalSickTime)) ? "00:00:00" : $total->totalSickTime;

        $totalRegularTimeWeek = (is_null($total->totalRegularTime)) ? "" : totalTime($total->totalRegularTime);
        $totalVacationTimeWeek = (is_null($total->totalVacationTime)) ? "" : totalTime($total->totalVacationTime);
        $totalHolidayTimeWeek = (is_null($total->totalHolidayTime)) ? "" : totalTime($total->totalHolidayTime);
        $totalPersonalTimeWeek = (is_null($total->totalPersonalTime)) ? "" : totalTime($total->totalPersonalTime);
        $totalSickTimeWeek = (is_null($total->totalSickTime)) ? "" : totalTime($total->totalSickTime);

        $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( '$totalRegularTime' ) + TIME_TO_SEC( '$totalVacationTime' ) + TIME_TO_SEC( '$totalHolidayTime' ) + TIME_TO_SEC( '$totalPersonalTime' ) + TIME_TO_SEC( '$totalSickTime' ) ) ) AS totalTime";

        $query = mysql_query($sql);
        $total = mysql_fetch_object($query);
        $totalTimeWeek = ($total->totalTime == "") ? "" : totalTime($total->totalTime);

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
        
        
        // setup default table variables
        $headerTD = '';
        $headerPeriods = '';
        $headerInOut = '';
        $headerColSpan = 1;
        $totalColSpan = 4;
        $MAX_VISIBLE_BREAKS_PER_DAY = 2;


        // IF there is vacation time from this week, add the 'Vacation Time' in the table header
        if ($totalVacationTimeWeek != "")
        {
            $headerTD .= '<td>Vacation Time</td>';
            $headerColSpan++;
            $totalColSpan++;
        }

        // IF there is holiday time from this week, add the 'Holiday Time' in the table header
        if ($totalHolidayTimeWeek != "")
        {
            $headerTD .= '<td>Holiday Time</td>';
            $headerColSpan++;
            $totalColSpan++;
        }

        // IF there is personal time from this week, add the 'Personal Time' in the table header
        if ($totalPersonalTimeWeek != "")
        {
            $headerTD .= '<td>Personal Time</td>';
            $headerColSpan++;
            $totalColSpan++;
        }

        // IF there is sick time from this week, add the 'Sick Time' in the table header
        if ($totalSickTimeWeek != "")
        {
            $headerTD .= '<td>Sick Time</td>';
            $headerColSpan++;
            $totalColSpan++;
        }
        

        // count the number of Employee_Hours records for this user today
        $sql = "SELECT COUNT(*) AS count
                FROM Employee_Hours EH
                WHERE EH.eid = $userID
                AND EH.date = '$today'";

        $query = mysql_query($sql);
        $records = mysql_fetch_object($query);
        $recordsCount = $records->count;


        // get the user's company information
        $sql = "SELECT C.*
                FROM Company C, Employee E
                WHERE C.id = E.companyID";

        $query = mysql_query($sql);
        $company = mysql_fetch_object($query);
        $breaks_per_day = $company->breaks_per_day;


        for ($i = 1; $i <= $breaks_per_day; $i++)
        {
            if ($breaks_per_day > $MAX_VISIBLE_BREAKS_PER_DAY) {break;}

            $headerPeriods .= '<td colspan="2">Period '.($i+1).'</td>';
            $headerInOut .= '<td>In</td><td>Out</td>';
            $totalColSpan += 2;
        }


        // print the table header and total row
        $tableHeader = '<td>Day</td><td>Date</td><td colspan="2">Period 1</td>'.$headerPeriods.$headerTD.'<td>Total</td>';
        $tableHeader2 = '<td colspan="2"></td><td>In</td><td>Out</td>'.$headerInOut.'<td colspan="'.$headerColSpan.'"></td>';
        $totalRow = '<tr class="success table-total"><td class="text-right" colspan="'.$totalColSpan.'"><h3>Total Time:</h3></td><td class="text-center"><h3>'.$totalTimeWeek.'</h3>';
        
        ?>
        <tr class="info">
            <?php print $tableHeader; ?>
        </tr>
        <tr class="info">
            <?php print $tableHeader2; ?>
        </tr>
    </thead>
    <tbody>
        <?php

        $cellCount = 1;
        $vacationCellCount = 1;
        $holidayCellCount = 1;
        $personalCellCount = 1;
        for ($i=0; $i<7; $i++)
        {
            // setup variables
            $date = ($i == 0) ? $start_date : date('Y-m-d', strtotime($start_date." + $i day"));
            $CLOCK_IN = array("","","");
            $CLOCK_OUT = array("","","");
            // $modifiedDate = preg_replace('/-/', ' ', $date, 1);
            $modifiedDate = date('F j, Y', strtotime($date));

            // get the CLOCK_IN and CLOCK_OUT times
            $sql = "SELECT H.id, H.clock_in, H.clock_out
                    FROM Employee_Hours H
                    WHERE H.eid = $userID 
                    AND H.date = '$date'
                    ORDER BY H.clock_in ASC";

            // setup variables
            $count = 0;
            $count2 = 0;
            $CLOCK_IN = [];
            $CLOCK_OUT = [];
            $CLOCK_IN_HOUR = [];
            $CLOCK_IN_MINUTE = [];
            $CLOCK_IN_DAYTIME = [];
            $CLOCK_OUT_HOUR = [];
            $CLOCK_OUT_MINUTE = [];
            $CLOCK_OUT_DAYTIME = [];
            $IS_IN_BLANK = []; // set every IN cell to "BLANK"
            $IS_OUT_BLANK = []; // set every OUT cell to "BLANK"
            $IS_IN_FORGOT = [];
            $IS_OUT_FORGOT = [];
            $query = mysql_query($sql);
            while ($time = mysql_fetch_object($query))
            {
                // add the hours, minutes, daytime, and is_blank of the clock_in and clock_out times to an array
                $CLOCK_IN_HOUR[$count] = intval(date('g', strtotime($date." ".$time->clock_in)));
                $CLOCK_IN_MINUTE[$count] = intval(date('i', strtotime($date." ".$time->clock_in)));
                $CLOCK_IN_DAYTIME[$count] = (date('a', strtotime($date." ".$time->clock_in)) == "am") ? 0 : 1;
                $IS_IN_BLANK[$count] = 0;
                
                $CLOCK_OUT_HOUR[$count] = intval(date('g', strtotime($date." ".$time->clock_out)));
                $CLOCK_OUT_MINUTE[$count] = intval(date('i', strtotime($date." ".$time->clock_out)));
                $CLOCK_OUT_DAYTIME[$count] = (date('a', strtotime($date." ".$time->clock_out)) == "am") ? 0 : 1;
                $IS_OUT_BLANK[$count] = 0;

                // add the current clock_in and clock_out times to an array
                $CLOCK_IN[$count] = ($time->clock_in == NULL) ? "" : date("g:i a", strtotime($date." ".$time->clock_in));
                $CLOCK_OUT[$count] = ($time->clock_out == NULL) ? "" : date("g:i a", strtotime($date." ".$time->clock_out));

                // set the recordID
                $recordID = $time->id;

                // check if the Employee_Hours record is NULL
                $sql = "SELECT H.clock_in, H.clock_out 
                        FROM Employee_Hours H 
                        WHERE H.id = $recordID";

                $q3 = mysql_query($sql);
                $hour = mysql_fetch_object($q3);
                $forgotClockIn = $hour->clock_in;
                $forgotClockOut = $hour->clock_out;

                // add the forgot record value to the forgot arrays
                $IS_IN_FORGOT[$count] = ($forgotClockIn == NULL) ? "forgot" : "";
                $IS_OUT_FORGOT[$count] = ($forgotClockOut == NULL) ? "forgot" : "";

                // check if the Employee_Hours record is in the Forgot_Hour table
                $sql = "SELECT F.clock_in, F.clock_out
                        FROM Forgot_Hour F  
                        WHERE F.ehid = $recordID";

                $q2 = mysql_query($sql);
                $forgot = mysql_fetch_object($q2);
                $forgotClockIn = $forgot->clock_in;
                $forgotClockOut = $forgot->clock_out;

                // add the forgot record value to the forgot arrays
                $IS_IN_FORGOT[$count] = ($forgotClockIn == 1) ? "forgot" : $IS_IN_FORGOT[$count];
                $IS_OUT_FORGOT[$count] = ($forgotClockOut == 1) ? "forgot" : $IS_OUT_FORGOT[$count];

                // update the counter
                $count++;
            }

            // total hours worked for the current date
            // $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) ) AS totalTime
            //         FROM Employee_Hours H
            //         WHERE H.date = '$date' 
            //         AND H.eid = $userID
            //         AND H.clock_out IS NOT NULL";

            $sql = "SELECT
                     (
                        SELECT 
                        SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) )
                        FROM Employee_Hours H
                        WHERE H.date = '$date'
                        AND H.eid = $userID
                        AND H.clock_out IS NOT NULL
                     ) AS totalRegularTime,
                     (
                        SELECT 
                        SEC_TO_TIME( SUM( TIME_TO_SEC( V.time ) ) ) AS totalVacationTime
                        FROM Vacation_Time V
                        WHERE V.date = '$date'
                        AND V.eid = $userID
                     ) AS totalVacationTime,
                     (
                        SELECT 
                        SEC_TO_TIME( SUM( TIME_TO_SEC( HT.time ) ) ) AS totalHolidayTime
                        FROM Holiday_Time HT
                        WHERE HT.date = '$date'
                        AND HT.eid = $userID
                     ) AS totalHolidayTime,
                     (
                        SELECT 
                        SEC_TO_TIME( SUM( TIME_TO_SEC( PT.time ) ) ) AS totalPersonalTime
                        FROM Personal_Time PT
                        WHERE PT.date = '$date'
                        AND PT.eid = $userID
                     ) AS totalPersonalTime,
                     (
                        SELECT 
                        SEC_TO_TIME( SUM( TIME_TO_SEC( ST.time ) ) ) AS totalSickTime
                        FROM Sick_Time ST
                        WHERE ST.date = '$date'
                        AND ST.eid = $userID
                     ) AS totalSickTime";

            $query = mysql_query($sql);
            $total = mysql_fetch_object($query);
            $totalRegularTime = (is_null($total->totalRegularTime)) ? "00:00:00" : $total->totalRegularTime;
            $totalVacationTime = (is_null($total->totalVacationTime)) ? "00:00:00" : $total->totalVacationTime;
            $totalHolidayTime = (is_null($total->totalHolidayTime)) ? "00:00:00" : $total->totalHolidayTime;
            $totalPersonalTime = (is_null($total->totalPersonalTime)) ? "00:00:00" : $total->totalPersonalTime;
            $totalSickTime = (is_null($total->totalSickTime)) ? "00:00:00" : $total->totalSickTime;

            $totalRegularTimeDay = (is_null($total->totalRegularTime)) ? "" : totalTime($total->totalRegularTime);
            $totalVacationTimeDay = (is_null($total->totalVacationTime)) ? "" : totalTime($total->totalVacationTime);
            $totalHolidayTimeDay = (is_null($total->totalHolidayTime)) ? "" : totalTime($total->totalHolidayTime);
            $totalPersonalTimeDay = (is_null($total->totalPersonalTime)) ? "" : totalTime($total->totalPersonalTime);
            $totalSickTimeDay = (is_null($total->totalSickTime)) ? "" : '<span class="fa fa-bed fa-2x"></span><br>'.totalTime($total->totalSickTime);


            // IF there is sick time for this date, get the sick time absence type
            if ($totalSickTimeDay != "")
            {
                $sql = "SELECT ST.unexcused
                        FROM Sick_Time ST
                        WHERE ST.date = '$date'
                        AND ST.eid = $userID";

                $qsick = mysql_query($sql);
                $sickTime = mysql_fetch_object($qsick);
                $SICK_ABSENCE_TYPE = $sickTime->unexcused;
            }


            // split the total vacation time
            $vTime = explode(":", $totalVacationTime);

            // split the total holiday time
            $hTime = explode(":", $totalHolidayTime);

            // split the total personal time
            $pTime = explode(":", $totalPersonalTime);

            // split the total sick time
            $sTime = explode(":", $totalSickTime);

            // get the total vacation time hour and minute variables
            $VACATION_HOUR = intval($vTime[0]);
            $VACATION_MINUTE = intval($vTime[1]);
            $IS_VACATION_BLANK = ( ($VACATION_HOUR == 0) && ($VACATION_MINUTE == 0) ) ? 1 : 0;

            // get the total holiday time hour and minute variables
            $HOLIDAY_HOUR = intval($hTime[0]);
            $HOLIDAY_MINUTE = intval($hTime[1]);
            $IS_HOLIDAY_BLANK = ( ($HOLIDAY_HOUR == 0) && ($HOLIDAY_MINUTE == 0) ) ? 1 : 0;

            // get the total personal time hour and minute variables
            $PERSONAL_HOUR = intval($pTime[0]);
            $PERSONAL_MINUTE = intval($pTime[1]);
            $IS_PERSONAL_BLANK = ( ($PERSONAL_HOUR == 0) && ($PERSONAL_MINUTE == 0) ) ? 1 : 0;

            // get the total sick time hour and minute variables
            $SICK_HOUR = intval($sTime[0]);
            $SICK_MINUTE = intval($sTime[1]);
            $IS_SICK_BLANK = (is_null($total->totalSickTime)) ? 1 : 0;

            // get the total time worked for the day
            $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( '$totalRegularTime' ) + TIME_TO_SEC( '$totalVacationTime' ) + TIME_TO_SEC( '$totalHolidayTime' ) + TIME_TO_SEC( '$totalPersonalTime' ) + TIME_TO_SEC( '$totalSickTime' ) ) ) AS totalTime";
            $query = mysql_query($sql);
            $total = mysql_fetch_object($query);
            $totalTimeDay = ($total->totalTime == "") ? "" : totalTime($total->totalTime);

            // set the row counter
            $rowCount = 0;

            // set the cell count variables
            $cellCount1 = $cellCount+1;
            $cellCount2 = $cellCount+2;
            $cellCount3 = $cellCount+3;
            $cellCount4 = $cellCount+4;
            $cellCount5 = $cellCount+5;

            // IF the user is an admin, print editable table cells
            if ($adminID != "")
            {
                // print the table row
                $row = '<tr>';
                $row .= '<td>'.$DAY[$i].'</td>';
                $row .= '<td>'.$modifiedDate.'</td>';
                $row .= '<td id="cell'.$cellCount.'" class="'.$IS_IN_FORGOT[0].' edit-time" onclick="selectTime(\''.$date.'\',1,\'in\','.$cellCount.','.$CLOCK_IN_HOUR[0].','.$CLOCK_IN_MINUTE[0].','.$CLOCK_IN_DAYTIME[0].','.$IS_IN_BLANK[0].')">'.$CLOCK_IN[0].'</td>';
                $row .= '<td id="cell'.$cellCount1.'" class="'.$IS_OUT_FORGOT[0].' edit-time" onclick="selectTime(\''.$date.'\',1,\'out\','.$cellCount1.','.$CLOCK_OUT_HOUR[0].','.$CLOCK_OUT_MINUTE[0].','.$CLOCK_OUT_DAYTIME[0].','.$IS_OUT_BLANK[0].')">'.$CLOCK_OUT[0].'</td>';
                
                if ( ($breaks_per_day < 0) || ($breaks_per_day >= 1) )
                {
                    $row .= '<td id="cell'.$cellCount2.'" class="'.$IS_IN_FORGOT[1].' edit-time" onclick="selectTime(\''.$date.'\',2,\'in\','.$cellCount2.','.$CLOCK_IN_HOUR[1].','.$CLOCK_IN_MINUTE[1].','.$CLOCK_IN_DAYTIME[1].','.$IS_IN_BLANK[1].')">'.$CLOCK_IN[1].'</td>';
                    $row .= '<td id="cell'.$cellCount3.'" class="'.$IS_OUT_FORGOT[1].' edit-time" onclick="selectTime(\''.$date.'\',2,\'out\','.$cellCount3.','.$CLOCK_OUT_HOUR[1].','.$CLOCK_OUT_MINUTE[1].','.$CLOCK_OUT_DAYTIME[1].','.$IS_OUT_BLANK[1].')">'.$CLOCK_OUT[1].'</td>';
                }

                if ( ($breaks_per_day < 0) || ($breaks_per_day >= 2) )
                {
                    $row .= '<td id="cell'.$cellCount4.'" class="'.$IS_IN_FORGOT[2].' edit-time" onclick="selectTime(\''.$date.'\',3,\'in\','.$cellCount4.','.$CLOCK_IN_HOUR[2].','.$CLOCK_IN_MINUTE[2].','.$CLOCK_IN_DAYTIME[2].','.$IS_IN_BLANK[2].')">'.$CLOCK_IN[2].'</td>';
                    $row .= '<td id="cell'.$cellCount5.'" class="'.$IS_OUT_FORGOT[2].' edit-time" onclick="selectTime(\''.$date.'\',3,\'out\','.$cellCount5.','.$CLOCK_OUT_HOUR[2].','.$CLOCK_OUT_MINUTE[2].','.$CLOCK_OUT_DAYTIME[2].','.$IS_OUT_BLANK[2].')">'.$CLOCK_OUT[2].'</td>';
                }
                

                if ($totalVacationTimeWeek != "")
                {
                    $row .= '<td id="vacation'.$vacationCellCount.'" class="vacation" onclick="selectVacationTime(\''.$date.'\','.$vacationCellCount.','.$VACATION_HOUR.','.$VACATION_MINUTE.','.$IS_VACATION_BLANK.')">'.$totalVacationTimeDay.'</td>';
                }

                if ($totalHolidayTimeWeek != "")
                {
                    $row .= '<td id="holiday'.$holidayCellCount.'" class="holiday" onclick="selectHolidayTime(\''.$date.'\','.$holidayCellCount.','.$HOLIDAY_HOUR.','.$HOLIDAY_MINUTE.','.$IS_HOLIDAY_BLANK.')">'.$totalHolidayTimeDay.'</td>';
                }

                if ($totalPersonalTimeWeek != "")
                {
                    $row .= '<td id="personal'.$personalCellCount.'" class="personal" onclick="selectPersonalTime(\''.$date.'\','.$personalCellCount.','.$PERSONAL_HOUR.','.$PERSONAL_MINUTE.','.$IS_PERSONAL_BLANK.')">'.$totalPersonalTimeDay.'</td>';
                }

                if ($totalSickTimeWeek != "")
                {
                    $row .= '<td id="sick'.$sickCellCount.'" class="sick" onclick="selectSickTime(\''.$date.'\','.$sickCellCount.','.$SICK_HOUR.','.$SICK_MINUTE.','.$IS_SICK_BLANK.','.$SICK_ABSENCE_TYPE.')">'.$totalSickTimeDay.'</td>';
                }

                $row .= '<td>'.$totalTimeDay.'</td>';
                $row .= '</tr>';

                // error check the row
                // NOTE: the blank cells will default to 12:00 AM
                $row = str_replace(',12,0,0,0)"></td>', ',12,0,0,1)"></td>', $row);
                $row = str_replace(',,,,', ',12,0,0,1', $row);

                // update the row counter
                $rowCount++;
            }
            else // ELSE, the user is not an admin, so print normal table cells
            {
                $row = '<tr>';
                $row .= '<td>'.$DAY[$i].'</td>';
                $row .= '<td>'.$modifiedDate.'</td>';

                // IF the date <= today
                if (strtotime($date) <= strtotime($today))
                {
                    $row .= '<td id="cell'.$cellCount.'" class="'.$IS_IN_FORGOT[0].'" onclick="selectForgot(\''.$date.'\',1,\'in\','.$cellCount.','.$IS_IN_BLANK[0].')">'.$CLOCK_IN[0].'</td>';
                    $row .= '<td id="cell'.$cellCount1.'" class="'.$IS_OUT_FORGOT[0].'" onclick="selectForgot(\''.$date.'\',1,\'out\','.$cellCount1.','.$IS_OUT_BLANK[0].')">'.$CLOCK_OUT[0].'</td>';
                    
                    if ( ($breaks_per_day < 0) || ($breaks_per_day >= 1) )
                    {
                        $row .= '<td id="cell'.$cellCount2.'" class="'.$IS_IN_FORGOT[1].'" onclick="selectForgot(\''.$date.'\',2,\'in\','.$cellCount2.','.$IS_IN_BLANK[1].')">'.$CLOCK_IN[1].'</td>';
                        $row .= '<td id="cell'.$cellCount3.'" class="'.$IS_OUT_FORGOT[1].'" onclick="selectForgot(\''.$date.'\',2,\'out\','.$cellCount3.','.$IS_OUT_BLANK[1].')">'.$CLOCK_OUT[1].'</td>';
                    }

                    if ( ($breaks_per_day < 0) || ($breaks_per_day >= 2) )
                    {
                        $row .= '<td id="cell'.$cellCount4.'" class="'.$IS_IN_FORGOT[2].'" onclick="selectForgot(\''.$date.'\',3,\'in\','.$cellCount4.','.$IS_IN_BLANK[2].')">'.$CLOCK_IN[2].'</td>';
                        $row .= '<td id="cell'.$cellCount5.'" class="'.$IS_OUT_FORGOT[2].'" onclick="selectForgot(\''.$date.'\',3,\'out\','.$cellCount5.','.$IS_OUT_BLANK[2].')">'.$CLOCK_OUT[2].'</td>';
                    }
                }
                else // ELSE, date > today
                {
                    $row .= '<td id="cell'.$cellCount.'">'.$CLOCK_IN[0].'</td>';
                    $row .= '<td id="cell'.$cellCount1.'">'.$CLOCK_OUT[0].'</td>';
                    
                    if ( ($breaks_per_day < 0) || ($breaks_per_day >= 1) )
                    {
                        $row .= '<td id="cell'.$cellCount2.'">'.$CLOCK_IN[1].'</td>';
                        $row .= '<td id="cell'.$cellCount3.'">'.$CLOCK_OUT[1].'</td>';
                    }

                    if ( ($breaks_per_day < 0) || ($breaks_per_day >= 2) )
                    {
                        $row .= '<td id="cell'.$cellCount4.'">'.$CLOCK_IN[2].'</td>';
                        $row .= '<td id="cell'.$cellCount5.'">'.$CLOCK_OUT[2].'</td>';
                    }
                }

                if ($totalVacationTimeWeek != "")
                {
                    $row .= '<td>'.$totalVacationTimeDay.'</td>';
                }

                if ($totalHolidayTimeWeek != "")
                {
                    $row .= '<td>'.$totalHolidayTimeDay.'</td>';
                }

                if ($totalPersonalTimeWeek != "")
                {
                    $row .= '<td>'.$totalPersonalTimeDay.'</td>';
                }

                if ($totalSickTimeWeek != "")
                {
                    $row .= '<td>'.$totalSickTimeDay.'</td>';
                }

                $row .= '<td>'.$totalTimeDay.'</td>';
                $row .= '</tr>';

                // error check the row
                // NOTE: the blank cells will default to 12:00 AM
                $row = str_replace(',0)"></td>', ',1)"></td>', $row);
                $row = str_replace(',)"></td>', ',1)"></td>', $row);

                // update the row counter
                $rowCount++;
            }

            // update the cell counters
            $cellCount += 6;
            $vacationCellCount++;
            $holidayCellCount++;
            $personalCellCount++;
            $sickCellCount++;

            // print the row
            print $row;
        }

        // total hours worked this week
        // $sql = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( H.clock_out ) - TIME_TO_SEC( H.clock_in ) ) ) AS totalTime
        //         FROM Employee_Hours H
        //         WHERE H.date >= '$start_date'
        //         AND H.date <= '$end_date' 
        //         AND H.eid = $userID
        //         AND H.clock_out IS NOT NULL";

        // print the total row
        print $totalRow;

        if ($hideClockTime == 0)
        {
            // determine if clock-in or clock-out should be hidden
            $sql = "SELECT COUNT(*) AS count
                    FROM Employee_Hours H 
                    WHERE H.date = '$today' 
                    AND H.eid = $userID
                    AND H.clock_out IS NULL";

            $query = mysql_query($sql);
            $total = mysql_fetch_object($query);
            $showClockIn = ($total->count == 0) ? true : false;
            $showClockInNew = ($total->count == 0) ? "1" : "0";

            if ($total->count > 0)
            {
                // get the newest Employee_Hours record for this employee today
                $sql = "SELECT H.id
                        FROM Employee_Hours H 
                        WHERE H.date = '$today' 
                        AND H.eid = $userID
                        AND H.clock_out IS NULL
                        ORDER BY H.id DESC
                        LIMIT 1";
                $q1 = mysql_query($sql);
                $record = mysql_fetch_object($q1);
                $recordID = $record->id;

                // check if the newest Employee_Hours record is in the Forgot_Hour table
                $sql = "SELECT F.clock_in, F.clock_out
                        FROM Forgot_Hour F  
                        WHERE F.ehid = $recordID";
                $q2 = mysql_query($sql);
                $forgot = mysql_fetch_object($q2);
                $forgotClockIn = $forgot->clock_in;
                $forgotClockOut = $forgot->clock_out;

                $showClockIn = ($forgotClockOut == 1) ? true : false;
                $showClockInNew = ($forgotClockOut == 1) ? "1" : "0";
            }
        }

        // determine if the employee has any missing times
        $sql = "SELECT COUNT(*) AS count
                FROM Employee_Hours H 
                WHERE H.eid = $userID
                AND (
                        H.clock_out IS NULL
                        OR H.clock_in IS NULL
                    )
                AND H.date < '$today'";

        $query = mysql_query($sql);
        $total = mysql_fetch_object($query);
        $missingHours = ($total->count > 0) ? "1" : "0";

        // determine if the employee is on track to get overtime hours this week
        // checks IF the employee has over 32 hours OR they have 32 hours and some minutes
        $overtimeHours = ( (intval($totalTimeWeekHours) > 32) || ((intval($totalTimeWeekHours) == 32) && (intval($totalTimeWeekMinutes) > 0)) ) ? "1" : "0";


        // check IF the clockTime should be hidden or not
        if ( ($recordsCount >= ($breaks_per_day+1)) && ($breaks_per_day >= 0) && ($showClockIn == 1) )
        {
            $hideClockTime = 1;
        }


        // print the hidden inputs
        print '<input type="hidden" class="hide" id="isCurrentWeek" name="isCurrentWeek" value="'.$isCurrentWeek.'" />';
        print '<input type="hidden" class="hide" id="showClockInNew" name="showClockInNew" value="'.$showClockInNew.'" />';
        print '<input type="hidden" class="hide" id="hideClockTime" name="hideClockTime" value="'.$hideClockTime.'" />';
        print '<input type="hidden" class="hide" id="missingHours" name="missingHours" value="'.$missingHours.'" />';
        print '<input type="hidden" class="hide" id="overtimeHours" name="overtimeHours" value="'.$overtimeHours.'" />';
        print '<input type="hidden" class="hide" id="pageTitleNew" name="pageTitleNew" value="'.$pageTitle.'" />';
        print '</td></tr>';

        ?>
    </tbody>
</table>

<!-- Internal User-table Javascript -->
<script type="text/javascript">

    // $(".edit-time").dblclick(function() {
    //     $('#edit-time-modal').show();
    // });

</script>