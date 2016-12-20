<?php


require_once("config.php");


// get the incoming variables
$in_uid = (isset($_POST['uid'])) ? str_replace(' ', '', $_POST['uid']) : "";
$in_first_name = (isset($_POST['fn'])) ? str_replace(' ', '', $_POST['fn']) : "";
$in_middle_name = (isset($_POST['mn'])) ? str_replace(' ', '', $_POST['mn']) : "";
$in_last_name = (isset($_POST['ln'])) ? str_replace(' ', '', $_POST['ln']) : "";
$in_email = (isset($_POST['em'])) ? str_replace(' ', '', $_POST['em']) : "";
$in_username = (isset($_POST['un'])) ? strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $_POST['un'])) : "";
$in_password = (isset($_POST['pw'])) ? str_replace(' ', '', $_POST['pw']) : "";
$in_confirm_password = (isset($_POST['cpw'])) ? str_replace(' ', '', $_POST['cpw']) : "";
$in_payrate = (isset($_POST['pr'])) ? decimal_numbers($_POST['pr']) : 0.00;
$in_salary = (isset($_POST['sl'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['sl'])) : "";
$in_paytype = (isset($_POST['pt'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['pt'])) : "";
$in_full_time = (isset($_POST['ft'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['ft'])) : "";
$in_company = (isset($_POST['cm'])) ? $_POST['cm'] : "";
$in_peachID = (isset($_POST['peachID'])) ? $_POST['peachID'] : "";
$in_employee_type = (isset($_POST['et'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['et'])) : "";
$in_status = (isset($_POST['st'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['st'])) : "";
$in_start_time = (isset($_POST['stm'])) ? preg_replace("/[^0-9:]/", "", $_POST['stm']) : "";
$in_days_to_review = (isset($_POST['dbr'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['dbr'])) : "";
$in_personal_days = (isset($_POST['pd'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['pd'])) : "";
$in_vacation_days = (isset($_POST['vd'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['vd'])) : "";
$in_start_date = (isset($_POST['sd'])) ? preg_replace("/[^-0-9]/", "", $_POST['sd']) : "";
$in_end_date = (isset($_POST['ed'])) ? preg_replace("/[^-0-9]/", "", $_POST['ed']) : "";
$in_birth_date = (isset($_POST['bd'])) ? preg_replace("/[^-0-9]/", "", $_POST['bd']) : "";

$in_admin_billing = (isset($_POST['adminBilling'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['adminBilling'])) : "";
$in_admin_incident_reports = (isset($_POST['adminIncidentReports'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['adminIncidentReports'])) : "";
$in_admin_milling = (isset($_POST['adminMilling'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['adminMilling'])) : "";
$in_admin_quality_control = (isset($_POST['adminQualityControl'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['adminQualityControl'])) : "";
$in_admin_shipping = (isset($_POST['adminShipping'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['adminShipping'])) : "";

$in_signs_incident_reports = (isset($_POST['signsIncidentReports'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['signsIncidentReports'])) : "";
$in_sales_calendar_active = (isset($_POST['salesCalendarActive'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['salesCalendarActive'])) : "";
$in_marketing_dept = (isset($_POST['marketingDept'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['marketingDept'])) : "";
$in_dlp_active = (isset($_POST['dlpActive'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['dlpActive'])) : "";
$in_dlp_admin = (isset($_POST['dlpAdmin'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['dlpAdmin'])) : "";
$in_overtime_alerts = (isset($_POST['overtimeAlerts'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['overtimeAlerts'])) : "";
$in_enforce_ip_address = (isset($_POST['enforceIPAddress'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['enforceIPAddress'])) : "";


// setup variables
$today = date('Y-m-d');


// error checking
if ( ($userID != $in_uid) || (is_null($in_uid)) || (is_null($in_password)) || (is_null($in_confirm_password)) || ($in_password != $in_confirm_password) ) {exit;}


// update the Employee record
$sql = "UPDATE `timecard`.`Employee` 
        SET `Employee`.`first_name` = :first_name,
        `Employee`.`middle_name` = :middle_name,
        `Employee`.`last_name` = :last_name,
        `Employee`.`email` = :email,
        `Employee`.`username` = :username,
        `Employee`.`password` = :password,
        `Employee`.`payrate` = :payrate,
        `Employee`.`salary` = :salary,
        `Employee`.`paytype` = :paytype,
        `Employee`.`full_time` = :full_time,
        `Employee`.`companyID` = :company,
        `Employee`.`peachID` = :peachID,
        `Employee`.`active` = :active,
        `Employee`.`dlp_active` = :dlp_active,
        `Employee`.`dlp_admin` = :dlp_admin,
        `Employee`.`start_time` = :start_time,
        `Employee`.`days_between_review` = :days_to_review,
        `Employee`.`personal_days` = :personal_days,
        `Employee`.`vacation_days` = :vacation_days,
        `Employee`.`start_date` = :start_date,
        `Employee`.`end_date` = :end_date,
        `Employee`.`birth_date` = :birth_date,
        `Employee`.`admin_billing` = :admin_billing,
        `Employee`.`admin_incident_reports` = :admin_incident_reports,
        `Employee`.`admin_milling` = :admin_milling,
        `Employee`.`admin_quality_control` = :admin_quality_control,
        `Employee`.`admin_shipping` = :admin_shipping,
        `Employee`.`incident_report_signed` = :signs_incident_reports,
        `Employee`.`sales_calendar_active` = :sales_calendar_active,
        `Employee`.`marketing_dept` = :marketing_dept,
        `Employee`.`overtime_alerts` = :overtime_alerts,
        `Employee`.`enforce_ip_address` = :enforce_ip_address
        WHERE `Employee`.`id` = :id";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':first_name' => $in_first_name, ':middle_name' => $in_middle_name, ':last_name' => $in_last_name, ':email' => $in_email, ':username' => $in_username, ':password' => $in_password, ':payrate' => $in_payrate, ':salary' => $in_salary, ':paytype' => $in_paytype, ':full_time' => $in_full_time, ':company' => $in_company, ':peachID' => $in_peachID, ':active' => $in_status, ':dlp_active' => $in_dlp_active, ':dlp_admin' => $in_dlp_admin, ':start_time' => $in_start_time, ':days_to_review' => $in_days_to_review, ':personal_days' => $in_personal_days, ':vacation_days' => $in_vacation_days, ':start_date' => $in_start_date, ':end_date' => $in_end_date, ':birth_date' => $in_birth_date, ':admin_billing' => $in_admin_billing, ':admin_incident_reports' => $in_admin_incident_reports, ':admin_milling' => $in_admin_milling, ':admin_quality_control' => $in_admin_quality_control, ':admin_shipping' => $in_admin_shipping, ':signs_incident_reports' => $in_signs_incident_reports, ':sales_calendar_active' => $in_sales_calendar_active, ':marketing_dept' => $in_marketing_dept, ':overtime_alerts' => $in_overtime_alerts, ':enforce_ip_address' => $in_enforce_ip_address, ':id' => $userID));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error updating Employee: " . $e->getMessage() . "</br>";
}


// get the user's full name and update the session variable
$query = mysql_query("SELECT CONCAT(E.first_name, ' ', E.last_name) AS name FROM Employee E WHERE E.id = $userID");
$user = mysql_fetch_object($query);
$uName = $user->name;
$_SESSION["uname"] = $uName;


// get the employee's administrator id (if one exists)
$query = mysql_query("SELECT A.id FROM Administrator A WHERE A.eid = $userID");
$user = mysql_fetch_object($query);
$oldAdminID = (is_null($user->id)) ? 0 : $user->id;


// IF in_employee_type == 1, set the employee as an Employee
if ($in_employee_type == 1)
{
    // delete the old Company_Administrator associations
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Company_Administrator` WHERE `Company_Administrator`.`aid` = :id");
    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':id' => $oldAdminID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Company_Administrator: " . $e->getMessage() . "</br>";
    }

    // delete the old Group_Administrator associations
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Group_Administrator` WHERE `Group_Administrator`.`aid` = :id");
    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':id' => $oldAdminID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Group_Administrator: " . $e->getMessage() . "</br>";
    }

    // delete the old Administrator association
    $q = $db_pdo->prepare("DELETE FROM `timecard`.`Administrator` WHERE `Administrator`.`id` = :id");
    try {
        $db_pdo->beginTransaction();
        $q->execute(array(':id' => $oldAdminID));
        $db_pdo->commit();
    } catch(PDOExecption $e) {
        $db_pdo->rollback();
        print "Error deleting Administrator: " . $e->getMessage() . "</br>";
    }
}
// IF in_employee_type == 2, set the employee as an Administrator
elseif ($in_employee_type == 2)
{
    // IF the employee is already an administrator, don't make them an administrator again
    if ($oldAdminID == 0)
    {
        // INSERT the new Administrator
        $q = $db_pdo->prepare("INSERT INTO `timecard`.`Administrator` (`id`, `eid`) VALUES (NULL, :userID)");
        try {
            $db_pdo->beginTransaction();
            $q->execute(array(':userID' => $userID));
            $newAdminID = $db_pdo->lastInsertId();
            $db_pdo->commit();
        } catch(PDOExecption $e) {
            $db_pdo->rollback();
            print "Error inserting new Administrator: " . $e->getMessage() . "</br>";
        }

        // get the list of companies that this administrator can access
        $sql = "SELECT 
                C.id 
                FROM Company C, Company_Administrator CA 
                WHERE CA.aid = $adminID
                AND C.id = CA.cid
                ORDER BY C.name ASC";
        $query = mysql_query($sql);
        while ($record = mysql_fetch_object($query))
        {
            $companyID = $record->id;

            // INSERT the new Company_Administrator association
            $q = $db_pdo->prepare("INSERT INTO `timecard`.`Company_Administrator` (`id`, `cid`, `aid`) VALUES (NULL, :companyID, :adminID)");
            try {
                $db_pdo->beginTransaction();
                $q->execute(array(':companyID' => $companyID, ':adminID' => $newAdminID));
                $db_pdo->commit();
            } catch(PDOExecption $e) {
                $db_pdo->rollback();
                print "Error inserting new Company_Administrator: " . $e->getMessage() . "</br>";
            }
        }
    }
}
else {exit;}


// print "Almost done updating...";


// don't redeclare the timecard functions in the user-table
global $includeTimecardFunctions;
$includeTimecardFunctions = 0;

// print the user-table
include('/usr/www/www.amstimecard.com/tpl/user-table.php');

?>