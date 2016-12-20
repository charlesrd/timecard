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
$in_employee_type = (isset($_POST['et'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['et'])) : "";
$in_status = (isset($_POST['st'])) ? intval(preg_replace("/[^0-9]/", "", $_POST['st'])) : "";
$in_start_time = (isset($_POST['stm'])) ? preg_replace("/[^0-9:]/", "", $_POST['stm']) : "";
$in_start_date = (isset($_POST['sd'])) ? preg_replace("/[^-0-9]/", "", $_POST['sd']) : "";
$in_end_date = (isset($_POST['ed'])) ? preg_replace("/[^-0-9]/", "", $_POST['ed']) : "";


// setup variables
$today = date('Y-m-d');
$employeeReviewDateTime = date('Y-m-d 12:00:00', strtotime("+180 days"));

// error checking
if ( ($userID != $in_uid) || (is_null($in_uid)) || (is_null($in_first_name)) || (is_null($in_middle_name)) || (is_null($in_last_name)) || (is_null($in_email)) || (is_null($in_username)) || (is_null($in_password)) || (is_null($in_confirm_password)) || ($in_password != $in_confirm_password) || (is_null($in_payrate)) || (is_null($in_salary)) || (is_null($in_paytype)) || (is_null($in_full_time)) || (is_null($in_company)) || (is_null($in_employee_type)) || (is_null($in_status)) || (is_null($in_start_time)) || (is_null($in_start_date)) || (is_null($in_end_date)) ) {exit;}


// Insert the new Employee record
$sql = "INSERT INTO `timecard`.`Employee` 
        (
            `id`,
            `first_name`, 
            `middle_name`, 
            `last_name`, 
            `email`,
            `username`,
            `password`,
            `payrate`,
            `salary`,
            `paytype`,
            `full_time`,
            `companyID`,
            `active`,
            `start_time`,
            `start_date`,
            `end_date`
        )
        VALUES 
        (
            NULL,
            :first_name,
            :middle_name,
            :last_name,
            :email,
            :username,
            :password,
            :payrate,
            :salary,
            :paytype,
            :full_time,
            :company,
            :active,
            :start_time,
            :start_date,
            :end_date
        )";

$q = $db_pdo->prepare($sql);
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':first_name' => $in_first_name, ':middle_name' => $in_middle_name, ':last_name' => $in_last_name, ':email' => $in_email, ':username' => $in_username, ':password' => $in_password, ':payrate' => $in_payrate, ':salary' => $in_salary, ':paytype' => $in_paytype, ':full_time' => $in_full_time, ':company' => $in_company, ':active' => $in_status, ':start_time' => $in_start_time, ':start_date' => $in_start_date, ':end_date' => $in_end_date));
    $newUserID = $db_pdo->lastInsertId();
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting new Employee: " . $e->getMessage() . "</br>";
}


// get the new user's full name and update the session variable
// $query = mysql_query("SELECT CONCAT(E.first_name, ' ', E.last_name) AS name FROM Employee E WHERE E.id = $newUserID");
// $user = mysql_fetch_object($query);
// $uName = $user->name;
// $_SESSION["uname"] = $uName;

// // update the session user id
// $_SESSION["uid"] = $newUserID;




// INSERT the new Employee's Employee_Review starting date
$q = $db_pdo->prepare("INSERT INTO `timecard`.`Employee_Review` (`id`, `eid`, `date`, `created_at`) VALUES (NULL, :userID, :employeeReviewDateTime, NOW())");
try {
    $db_pdo->beginTransaction();
    $q->execute(array(':userID' => $newUserID, ':employeeReviewDateTime' => $employeeReviewDateTime));
    $db_pdo->commit();
} catch(PDOExecption $e) {
    $db_pdo->rollback();
    print "Error inserting new Employee_Review: " . $e->getMessage() . "</br>";
}




// IF in_employee_type == 2, set the employee as an Administrator
if ($in_employee_type == 2)
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

    // get the list of companies that this new administrator can access
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

// don't redeclare the timecard functions in the user-table
global $includeTimecardFunctions;
$includeTimecardFunctions = 0;

// print the user-table
include('user-table.php');

?>