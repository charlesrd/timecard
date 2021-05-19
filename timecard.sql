SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timecard`
--
CREATE DATABASE IF NOT EXISTS `timecard` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `timecard`;

-- --------------------------------------------------------

--
-- Table structure for table `Administrator`
--

CREATE TABLE `Administrator` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Announcement`
--

CREATE TABLE `Announcement` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Code`
--

CREATE TABLE `Code` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company`
--

CREATE TABLE `Company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `breaks_per_day` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company_Administrator`
--

CREATE TABLE `Company_Administrator` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company_Group`
--

CREATE TABLE `Company_Group` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `gid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company_Holiday`
--

CREATE TABLE `Company_Holiday` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company_IP_Address`
--

CREATE TABLE `Company_IP_Address` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `ip_address` bigint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Company_Message`
--

CREATE TABLE `Company_Message` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee`
--

CREATE TABLE `Employee` (
  `id` int(11) NOT NULL,
  `first_name` char(50) NOT NULL,
  `middle_name` char(50) NOT NULL,
  `last_name` char(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `payrate` decimal(10,2) NOT NULL,
  `salary` int(11) NOT NULL,
  `paytype` int(1) NOT NULL,
  `department` int(1) NOT NULL,
  `full_time` int(1) NOT NULL DEFAULT '0',
  `companyID` int(11) NOT NULL DEFAULT '0',
  `peachID` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `dlp_active` int(1) NOT NULL DEFAULT '0',
  `dlp_admin` int(1) NOT NULL DEFAULT '0',
  `sales_calendar_active` tinyint(1) NOT NULL DEFAULT '0',
  `incident_report_signed` int(1) NOT NULL DEFAULT '0',
  `admin_milling` int(1) NOT NULL DEFAULT '0',
  `admin_quality_control` int(1) NOT NULL DEFAULT '0',
  `admin_billing` int(1) NOT NULL DEFAULT '0',
  `admin_shipping` int(1) NOT NULL DEFAULT '0',
  `admin_incident_reports` int(1) NOT NULL DEFAULT '0',
  `overtime_alerts` int(1) NOT NULL DEFAULT '1',
  `enforce_ip_address` int(1) NOT NULL DEFAULT '1',
  `marketing_dept` tinyint(1) NOT NULL DEFAULT '0',
  `has_key` int(1) NOT NULL DEFAULT '0',
  `alarm_code` varchar(50) NOT NULL,
  `start_time` time NOT NULL DEFAULT '09:00:00',
  `days_between_review` int(3) NOT NULL DEFAULT '90',
  `personal_days` int(3) NOT NULL DEFAULT '0',
  `vacation_days` int(3) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `birth_date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Announcement`
--

CREATE TABLE `Employee_Announcement` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_File`
--

CREATE TABLE `Employee_File` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Hours`
--

CREATE TABLE `Employee_Hours` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `is_clock_out_for_a_break` tinyint(1) DEFAULT NULL COMMENT 'null = n/a (no clock_out time); 1 = yes, going on a break; 0 = no, going home',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Message`
--

CREATE TABLE `Employee_Message` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Note`
--

CREATE TABLE `Employee_Note` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Review`
--

CREATE TABLE `Employee_Review` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL,
  `reviewed` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Employee_Review_Note`
--

CREATE TABLE `Employee_Review_Note` (
  `id` int(11) NOT NULL,
  `erid` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `File_Type`
--

CREATE TABLE `File_Type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Forgot_Hour`
--

CREATE TABLE `Forgot_Hour` (
  `id` int(11) NOT NULL,
  `ehid` int(11) NOT NULL,
  `clock_in` int(1) NOT NULL DEFAULT '0',
  `clock_out` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Group`
--

CREATE TABLE `Group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dlpID` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Group_Administrator`
--

CREATE TABLE `Group_Administrator` (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Group_Holiday`
--

CREATE TABLE `Group_Holiday` (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Group_IP_Address`
--

CREATE TABLE `Group_IP_Address` (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `ip_address` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Holiday_Time`
--

CREATE TABLE `Holiday_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Is_Done`
--

CREATE TABLE `Is_Done` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `end_time` time DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `On_Break`
--

CREATE TABLE `On_Break` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Opinion`
--

CREATE TABLE `Opinion` (
  `id` int(11) NOT NULL,
  `self_opinion` text NOT NULL,
  `company_opinion` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Opinion_Code`
--

CREATE TABLE `Opinion_Code` (
  `id` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `code` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Paytype`
--

CREATE TABLE `Paytype` (
  `id` int(11) NOT NULL,
  `name` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Personal_Time`
--

CREATE TABLE `Personal_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Request_Personal_Time`
--

CREATE TABLE `Request_Personal_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `time` time NOT NULL DEFAULT '00:00:00',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `status` int(1) NOT NULL DEFAULT '0',
  `employee_note` varchar(255) NOT NULL,
  `admin_note` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Request_Vacation_Date`
--

CREATE TABLE `Request_Vacation_Date` (
  `id` int(11) NOT NULL,
  `rvtid` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Request_Vacation_Time`
--

CREATE TABLE `Request_Vacation_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `employee_note` varchar(255) NOT NULL,
  `admin_note` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Sick_Time`
--

CREATE TABLE `Sick_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL,
  `unexcused` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Vacation_Time`
--

CREATE TABLE `Vacation_Time` (
  `id` int(11) NOT NULL,
  `eid` int(11) NOT NULL,
  `time` time NOT NULL,
  `date` date NOT NULL,
  `note` text NOT NULL,
  `cashed_in` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Administrator`
--
ALTER TABLE `Administrator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`);

--
-- Indexes for table `Announcement`
--
ALTER TABLE `Announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Code`
--
ALTER TABLE `Code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Company`
--
ALTER TABLE `Company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `Company_Administrator`
--
ALTER TABLE `Company_Administrator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `aid` (`aid`);

--
-- Indexes for table `Company_Group`
--
ALTER TABLE `Company_Group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `gid` (`gid`);

--
-- Indexes for table `Company_Holiday`
--
ALTER TABLE `Company_Holiday`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `title` (`title`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Company_IP_Address`
--
ALTER TABLE `Company_IP_Address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `Company_Message`
--
ALTER TABLE `Company_Message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`),
  ADD KEY `message` (`message`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `Employee`
--
ALTER TABLE `Employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `first_name` (`first_name`),
  ADD KEY `middle_name` (`middle_name`),
  ADD KEY `last_name` (`last_name`),
  ADD KEY `email` (`email`),
  ADD KEY `password` (`password`),
  ADD KEY `payrate` (`payrate`),
  ADD KEY `salary` (`salary`),
  ADD KEY `paytype` (`paytype`),
  ADD KEY `company` (`companyID`),
  ADD KEY `full_time` (`full_time`),
  ADD KEY `has_key` (`has_key`),
  ADD KEY `alarm_code` (`alarm_code`),
  ADD KEY `days_between_review` (`days_between_review`),
  ADD KEY `start_date` (`start_date`),
  ADD KEY `end_date` (`end_date`),
  ADD KEY `start_time` (`start_time`),
  ADD KEY `peachID` (`peachID`);

--
-- Indexes for table `Employee_Announcement`
--
ALTER TABLE `Employee_Announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Employee_File`
--
ALTER TABLE `Employee_File`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Employee_Hours`
--
ALTER TABLE `Employee_Hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `clock_in` (`clock_in`),
  ADD KEY `clock_out` (`clock_out`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Employee_Message`
--
ALTER TABLE `Employee_Message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Employee_Note`
--
ALTER TABLE `Employee_Note`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Employee_Review`
--
ALTER TABLE `Employee_Review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Employee_Review_Note`
--
ALTER TABLE `Employee_Review_Note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `erid` (`erid`),
  ADD KEY `aid` (`aid`),
  ADD KEY `note` (`note`);

--
-- Indexes for table `File_Type`
--
ALTER TABLE `File_Type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Forgot_Hour`
--
ALTER TABLE `Forgot_Hour`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ehid` (`ehid`),
  ADD KEY `clock_in` (`clock_in`),
  ADD KEY `clock_out` (`clock_out`);

--
-- Indexes for table `Group`
--
ALTER TABLE `Group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `dlpID` (`dlpID`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `Group_Administrator`
--
ALTER TABLE `Group_Administrator`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gid` (`gid`),
  ADD KEY `aid` (`aid`);

--
-- Indexes for table `Group_Holiday`
--
ALTER TABLE `Group_Holiday`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gid` (`gid`),
  ADD KEY `title` (`title`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Group_IP_Address`
--
ALTER TABLE `Group_IP_Address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gid` (`gid`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `Holiday_Time`
--
ALTER TABLE `Holiday_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `time` (`time`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Is_Done`
--
ALTER TABLE `Is_Done`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `date` (`date`),
  ADD KEY `end_time` (`end_time`);

--
-- Indexes for table `On_Break`
--
ALTER TABLE `On_Break`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `start_time` (`start_time`),
  ADD KEY `end_time` (`end_time`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Opinion`
--
ALTER TABLE `Opinion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Opinion_Code`
--
ALTER TABLE `Opinion_Code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Paytype`
--
ALTER TABLE `Paytype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `Personal_Time`
--
ALTER TABLE `Personal_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `time` (`time`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Request_Personal_Time`
--
ALTER TABLE `Request_Personal_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `date` (`date`),
  ADD KEY `approved` (`status`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `employee_note` (`employee_note`),
  ADD KEY `admin_note` (`admin_note`);

--
-- Indexes for table `Request_Vacation_Date`
--
ALTER TABLE `Request_Vacation_Date`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rvtid` (`rvtid`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Request_Vacation_Time`
--
ALTER TABLE `Request_Vacation_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `status` (`status`),
  ADD KEY `employee_note` (`employee_note`),
  ADD KEY `admin_note` (`admin_note`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `updated_at` (`updated_at`);

--
-- Indexes for table `Sick_Time`
--
ALTER TABLE `Sick_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `time` (`time`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `Vacation_Time`
--
ALTER TABLE `Vacation_Time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eid` (`eid`),
  ADD KEY `time` (`time`),
  ADD KEY `date` (`date`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Administrator`
--
ALTER TABLE `Administrator`
  ADD CONSTRAINT `Administrator_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Company_Administrator`
--
ALTER TABLE `Company_Administrator`
  ADD CONSTRAINT `Company_Administrator_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `Company` (`id`),
  ADD CONSTRAINT `Company_Administrator_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Administrator` (`id`);

--
-- Constraints for table `Company_Group`
--
ALTER TABLE `Company_Group`
  ADD CONSTRAINT `Company_Group_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `Company` (`id`),
  ADD CONSTRAINT `Company_Group_ibfk_2` FOREIGN KEY (`gid`) REFERENCES `Group` (`id`);

--
-- Constraints for table `Company_Holiday`
--
ALTER TABLE `Company_Holiday`
  ADD CONSTRAINT `Company_Holiday_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `Company` (`id`);

--
-- Constraints for table `Company_IP_Address`
--
ALTER TABLE `Company_IP_Address`
  ADD CONSTRAINT `Company_IP_Address_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `Company` (`id`);

--
-- Constraints for table `Company_Message`
--
ALTER TABLE `Company_Message`
  ADD CONSTRAINT `Company_Message_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `Company` (`id`);

--
-- Constraints for table `Employee_Hours`
--
ALTER TABLE `Employee_Hours`
  ADD CONSTRAINT `Employee_Hours_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Employee_Review`
--
ALTER TABLE `Employee_Review`
  ADD CONSTRAINT `Employee_Review_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Employee_Review_Note`
--
ALTER TABLE `Employee_Review_Note`
  ADD CONSTRAINT `Employee_Review_Note_ibfk_1` FOREIGN KEY (`erid`) REFERENCES `Employee_Review` (`id`),
  ADD CONSTRAINT `Employee_Review_Note_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Administrator` (`id`);

--
-- Constraints for table `Forgot_Hour`
--
ALTER TABLE `Forgot_Hour`
  ADD CONSTRAINT `Forgot_Hour_ibfk_1` FOREIGN KEY (`ehid`) REFERENCES `Employee_Hours` (`id`);

--
-- Constraints for table `Group_Administrator`
--
ALTER TABLE `Group_Administrator`
  ADD CONSTRAINT `Group_Administrator_ibfk_1` FOREIGN KEY (`gid`) REFERENCES `Group` (`id`),
  ADD CONSTRAINT `Group_Administrator_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Administrator` (`id`);

--
-- Constraints for table `Group_Holiday`
--
ALTER TABLE `Group_Holiday`
  ADD CONSTRAINT `Group_Holiday_ibfk_1` FOREIGN KEY (`gid`) REFERENCES `Group` (`id`);

--
-- Constraints for table `Group_IP_Address`
--
ALTER TABLE `Group_IP_Address`
  ADD CONSTRAINT `Group_IP_Address_ibfk_1` FOREIGN KEY (`gid`) REFERENCES `Group` (`id`);

--
-- Constraints for table `Holiday_Time`
--
ALTER TABLE `Holiday_Time`
  ADD CONSTRAINT `Holiday_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Is_Done`
--
ALTER TABLE `Is_Done`
  ADD CONSTRAINT `Is_Done_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `On_Break`
--
ALTER TABLE `On_Break`
  ADD CONSTRAINT `On_Break_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Personal_Time`
--
ALTER TABLE `Personal_Time`
  ADD CONSTRAINT `Personal_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Request_Personal_Time`
--
ALTER TABLE `Request_Personal_Time`
  ADD CONSTRAINT `Request_Personal_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Request_Vacation_Time`
--
ALTER TABLE `Request_Vacation_Time`
  ADD CONSTRAINT `Request_Vacation_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Sick_Time`
--
ALTER TABLE `Sick_Time`
  ADD CONSTRAINT `Sick_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

--
-- Constraints for table `Vacation_Time`
--
ALTER TABLE `Vacation_Time`
  ADD CONSTRAINT `Vacation_Time_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `Employee` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
