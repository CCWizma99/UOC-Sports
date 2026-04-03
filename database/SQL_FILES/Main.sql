-- phpMyAdmin SQL Dump
-- Merged schema
-- Source 1: Feb 21, 2026
-- Source 2: Apr 01, 2026 (primary/latest)
--
-- Host: 127.0.0.1:3306
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uoc-sports`
--

-- --------------------------------------------------------
-- Table structure for table `active_booking_attempts`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `active_booking_attempts`;
CREATE TABLE IF NOT EXISTS `active_booking_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `facility_id` int NOT NULL,
  `date` date NOT NULL,
  `last_active_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`facility_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `attendance`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` varchar(12) NOT NULL,
  `practice_id` int NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `status` varchar(12) NOT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `attendance` (`attendance_id`, `practice_id`, `user_id`, `status`) VALUES
('ATDFD77F382E', 8, 'L3NCL2J4', 'ABSENT'),
('ATD16507D601', 8, '5Q1XZO2Y', 'PRESENT');

-- --------------------------------------------------------
-- Table structure for table `budget`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `budget`;
CREATE TABLE IF NOT EXISTS `budget` (
  `budget_id` varchar(12) NOT NULL,
  `sport_id` varchar(6) NOT NULL,
  `year` year NOT NULL,
  `allocated_amount` int NOT NULL,
  `spent_amount` int NOT NULL,
  `allocation_date` date NOT NULL,
  `description` text,
  PRIMARY KEY (`budget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`, `description`) VALUES
('ANUTKD01', 'TKDA', 2025, 400000, 178000, '2024-12-15', NULL),
('ANUTKD02', 'TKDA', 2025, 100000, 9000, '2024-12-18', NULL),
('1', '1', 2025, 500000, 250000, '2025-01-15', NULL),
('2', '2', 2025, 400000, 150000, '2025-02-10', NULL),
('3', '3', 2025, 300000, 100000, '2025-03-12', NULL),
('4', '4', 2025, 200000, 50000, '2025-04-05', NULL),
('5', '5', 2025, 150000, 30000, '2025-05-01', NULL),
('ABC012', 'CRI', 2025, 200000, 42000, '2025-08-24', 'This is for testing'),
('BDG96F74E4F0', 'ROW', 2026, 100000, 12000, '2025-12-26', '-'),
('BDG975A8955F', 'BAD', 2026, 60000, 20000, '2026-02-25', '-');

-- --------------------------------------------------------
-- Table structure for table `captain_sport`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `captain_sport`;
CREATE TABLE IF NOT EXISTS `captain_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `coach_sport`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `coach_sport`;
CREATE TABLE IF NOT EXISTS `coach_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `comment`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` varchar(8) NOT NULL,
  `post_id` varchar(12) NOT NULL,
  `comment_from` varchar(12) NOT NULL,
  `reply_to` varchar(12) NOT NULL,
  `content` varchar(300) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `competition`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `competition`;
CREATE TABLE IF NOT EXISTS `competition` (
  `competition_id` int NOT NULL AUTO_INCREMENT,
  `competition_name` varchar(100) NOT NULL,
  `sport_id` varchar(20) NOT NULL,
  `participant_pdf` varchar(255) DEFAULT NULL,
  `participants` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date` date NOT NULL,
  PRIMARY KEY (`competition_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

INSERT INTO `competition` (`competition_id`, `competition_name`, `sport_id`, `participant_pdf`, `participants`, `created_at`, `date`) VALUES
(1, 'Inter University Basketball Competition', 'BAS', '', '', '2026-01-25 19:35:25', '0000-00-00'),
(3, 'National Cricket Championship', 'CRI', 'competition_1769374959_697684efe76e6.pdf', 'Dineth Amarasinghe', '2026-01-25 20:30:22', '2026-01-30');

-- --------------------------------------------------------
-- Table structure for table `equipment`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `equipment_name` varchar(32) NOT NULL,
  `max_allow` int NOT NULL,
  `image_name` varchar(48) NOT NULL,
  PRIMARY KEY (`equipment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `equipment` (`equipment_id`, `sport_id`, `equipment_name`, `max_allow`, `image_name`) VALUES
('EQ001', 'BAD', 'Badminton Racket', 2, ''),
('EQ002', 'BAD', 'Shuttlecock', 1, ''),
('EQ003', 'BAD', 'Badminton Net', 1, ''),
('EQ004', 'VOL', 'Volleyball', 1, ''),
('EQ005', 'VOL', 'Volleyball Net', 1, ''),
('EQ006', 'VOL', 'Knee Pads', 2, ''),
('EQ007', 'FOO', 'Football', 1, ''),
('EQ008', 'FOO', 'Goal Post', 2, ''),
('EQ009', 'FOO', 'Shin Guards', 0, ''),
('EQ010', 'FOO', 'Goalkeeper Gloves', 4, ''),
('EQ011', 'TEN', 'Tennis Racket', 2, ''),
('EQ012', 'TEN', 'Tennis Ball', 1, ''),
('EQ013', 'TEN', 'Tennis Net', 1, ''),
('EQ014', 'BAS', 'Basketball', 1, ''),
('EQ015', 'BAS', 'Basketball Hoop', 0, ''),
('EQ016', 'BAS', 'Shot Clock', 0, ''),
('EQ017', 'HOC', 'Hockey Stick', 1, ''),
('EQ018', 'HOC', 'Hockey Ball', 1, ''),
('EQ019', 'HOC', 'Goalkeeper Pads', 0, ''),
('EQ020', 'NET', 'Netball', 1, ''),
('EQ021', 'NET', 'Netball Post', 0, ''),
('EQ022', 'CRI', 'Cricket Bat', 2, ''),
('EQ023', 'CRI', 'Cricket Ball', 1, ''),
('EQ024', 'CRI', 'Batting Pads', 4, ''),
('EQ025', 'CRI', 'Helmet', 2, ''),
('EQ026', 'RUG', 'Rugby Ball', 0, ''),
('EQ027', 'RUG', 'Head Guard', 0, ''),
('EQ028', 'SWI', 'Swimming Goggles', 0, ''),
('EQ029', 'SWI', 'Swim Cap', 0, ''),
('EQ030', 'SWI', 'Kick Board', 0, ''),
('EQ031', 'TT', 'Table Tennis Bat', 0, ''),
('EQ032', 'TT', 'Table Tennis Ball', 0, ''),
('EQ033', 'TT', 'TT Table', 0, ''),
('EQ034', 'WL', 'Barbell', 0, ''),
('EQ035', 'WL', 'Dumbbell', 0, ''),
('EQ036', 'WL', 'Weight Plates', 0, ''),
('EQ037', 'ROW', 'Rowing Boat', 0, ''),
('EQ038', 'ROW', 'Oars', 0, ''),
('EQ039', 'WRE', 'Wrestling Mat', 0, ''),
('EQ040', 'CHE', 'Chess Board', 0, ''),
('EQ041', 'CHE', 'Chess Timer', 0, ''),
('EQ042', 'ATH', 'Starting Blocks', 0, ''),
('EQ043', 'ATH', 'Javelin', 0, ''),
('EQ044', 'ATH', 'Discus', 0, ''),
('EQ045', 'ATH', 'Shot Put', 0, ''),
('EQ046', 'BOX', 'Boxing Gloves', 0, ''),
('EQ047', 'BOX', 'Punching Bag', 0, ''),
('EQ048', 'TKD', 'Chest Guard', 0, ''),
('EQ049', 'TKD', 'Head Guard', 0, ''),
('EQ050', 'KRT', 'Karate Gi', 0, ''),
('EQ051', 'KRT', 'Hand Protectors', 0, ''),
('EQ052', 'RR', 'Stopwatch', 0, ''),
('EQ053', 'RR', 'Race Bib', 0, ''),
('EQ054', 'SCR', 'Scrabble Board', 0, ''),
('EQ055', 'ELL', 'Elle Game Set', 0, ''),
('EQ056', 'BB', 'Baseball Bat', 0, ''),
('EQ057', 'BB', 'Baseball', 0, ''),
('EQ058', 'BB', 'Baseball Glove', 0, ''),
('EQ059', 'KBD', 'Kabaddi Mat', 0, ''),
('EQ060', 'CRM', 'Carrom Board', 1, ''),
('EQ061', 'CRM', 'Carrom Coins', 1, ''),
('EQ062', 'CRM', 'Striker', 1, ''),
('EQ69354316b1', 'TKD', 'Taekwondo Tatami', 9, 'taekwondo_tatami_2938.jpg'),
('EQ6937e28ddf', 'BOX', 'Boxing Shoes', 1, 'boxing_shoes_3247.jpg'),
-- Only in Doc 1; included for completeness
('EQ699563ae09', 'NET', 'Netball BALL', 0, '');

-- --------------------------------------------------------
-- Table structure for table `equipment-requests`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `equipment-requests`;
CREATE TABLE IF NOT EXISTS `equipment-requests` (
  `request_id` varchar(12) NOT NULL,
  `student_id` varchar(12) DEFAULT NULL,
  `category_name` varchar(100) NOT NULL,
  `equipment_id` varchar(12) DEFAULT NULL,
  `request_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(64) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `notes` varchar(64) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `reserved_location` varchar(100) NOT NULL,
  `requester_name` varchar(100) NOT NULL,
  `equipment_items` text COMMENT 'JSON array of equipment items with quantities',
  PRIMARY KEY (`request_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `equipment-requests` (`request_id`, `student_id`, `category_name`, `equipment_id`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`, `sport_id`, `reserved_location`, `requester_name`, `equipment_items`) VALUES
('req_6937e152', 'FMX6Z8DF', 'Badminton Re', NULL, '2025-12-29', '08:00:00', '10:00:00', 'For the Taekwondo Provincial matches practices', 'COMPLETED', '-', 'BAD', 'Ground', 'K S Silva', NULL),
('req_6d607bf3', NULL, 'Tennis Racket (x2)', NULL, '2026-02-13', '10:30:00', '11:30:00', '', 'ACTIVE', '', 'TEN', 'Tennis Court', 'Student ', '[{"equipment_name":"Tennis Racket","quantity":2}]'),
('req_eec57c81', NULL, 'Cricket Bat', NULL, '2026-01-24', '06:21:00', '07:21:00', '', 'COMPLETED', '', 'CRI', 'Cricket Pitch', 'Student ', NULL),
('req_9fdd61ac', NULL, 'Netball Post', NULL, '2026-01-25', '09:50:00', '11:49:00', '', 'PENDING', 'Team Practice', 'NET', 'Ground', 'In person reservation', NULL),
('req_c9eaa56c', 'In person', 'Goalkeeper P', NULL, '2026-01-25', '12:01:00', '13:01:00', '', 'ACCEPTED', 'Freshers', 'HOC', 'Ground', 'Savi', '[{"equipment_name":"Goalkeeper Pads","quantity":1},{"equipment_name":"Hockey Ball","quantity":1}]'),
('req_693a734e', '23020342', 'Boxing Shoes', NULL, '2026-01-01', '13:00:00', '15:00:00', 'Foot work practice', 'ACTIVE', '-', 'BOX', 'Indoor court', 'S J', NULL),
('req_de7286f7', NULL, 'Relay baton (x4)', NULL, '2026-02-13', '06:30:00', '08:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S K', '[{"equipment_name":"Relay baton","quantity":4}]'),
('req_eefe02a8', NULL, 'Relay baton (x1)', NULL, '2026-02-13', '06:30:00', '07:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S Silv', '[{"equipment_name":"Relay baton","quantity":1}]');

-- --------------------------------------------------------
-- Table structure for table `equipment_categories`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `equipment_categories`;
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `category_id` varchar(8) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `equipment_categories` (`category_id`, `category_name`, `description`) VALUES
('CAT001', 'Balls', 'Various types of balls for different sports'),
('CAT002', 'Bats & Rackets', 'Batting and racket equipment'),
('CAT003', 'Protective Gear', 'Safety and protective equipment'),
('CAT004', 'Nets & Goals', 'Nets, goals, and target equipment'),
('CAT005', 'Training Equipment', 'Practice and training gear'),
('CAT006', 'Mats & Flooring', 'Mats, tatami, and floor equipment'),
('CAT007', 'Clothing & Shoes', 'Sports apparel and footwear'),
('CAT008', 'Other', 'Miscellaneous equipment');

-- --------------------------------------------------------
-- Table structure for table `equipment_inventory`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `equipment_inventory`;
CREATE TABLE IF NOT EXISTS `equipment_inventory` (
  `stock_id` varchar(8) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `quantity` int NOT NULL,
  `usable` int NOT NULL,
  `added_date` date NOT NULL,
  `remarks` varchar(256) NOT NULL,
  PRIMARY KEY (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `equipment_inventory` (`stock_id`, `equipment_id`, `sport_id`, `quantity`, `usable`, `added_date`, `remarks`) VALUES
('STK69354', 'EQ020', 'NET', 4, 4, '2025-12-07', '-'),
('STK00001', 'EQ001', 'BAD', 20, 15, '2025-12-08', '-'),
('STK00002', 'EQ002', 'BAD', 200, 200, '2025-12-08', '-'),
('STK00003', 'EQ003', 'BAD', 5, 5, '2025-12-08', '-'),
('STK00004', 'EQ004', 'VOL', 15, 15, '2025-12-08', '-'),
('STK00005', 'EQ005', 'VOL', 6, 6, '2025-12-08', '-'),
('STK00006', 'EQ006', 'VOL', 30, 30, '2025-12-08', '-'),
('STK00007', 'EQ007', 'FOO', 18, 18, '2025-12-08', '-'),
('STK00008', 'EQ008', 'FOO', 4, 4, '2025-12-08', '-'),
('STK00009', 'EQ009', 'FOO', 25, 25, '2025-12-08', '-'),
('STK00010', 'EQ010', 'FOO', 10, 10, '2025-12-08', '-'),
('STK00011', 'EQ011', 'TEN', 12, 8, '2025-12-08', '-'),
('STK00012', 'EQ012', 'TEN', 150, 150, '2025-12-08', '-'),
('STK00013', 'EQ013', 'TEN', 4, 4, '2025-12-08', '-'),
('STK00014', 'EQ014', 'BAS', 10, 10, '2025-12-08', '-'),
('STK00015', 'EQ015', 'BAS', 6, 6, '2025-12-08', '-'),
('STK00016', 'EQ016', 'BAS', 2, 2, '2025-12-08', '-'),
('STK00017', 'EQ017', 'HOC', 20, 6, '2025-12-08', '-'),
('STK00018', 'EQ018', 'HOC', 30, 30, '2025-12-08', '-'),
('STK00019', 'EQ019', 'HOC', 6, 6, '2025-12-08', '-'),
('STK00020', 'EQ020', 'NET', 10, 10, '2025-12-08', '-'),
('STK00021', 'EQ021', 'NET', 4, 4, '2025-12-08', '-'),
('STK00022', 'EQ022', 'CRI', 12, 10, '2025-12-08', '-'),
('STK00023', 'EQ023', 'CRI', 60, 60, '2025-12-08', '-'),
('STK00024', 'EQ024', 'CRI', 10, 10, '2025-12-08', '-'),
('STK00025', 'EQ025', 'CRI', 8, 8, '2025-12-08', '-'),
('STK00026', 'EQ026', 'RUG', 10, 10, '2025-12-08', '-'),
('STK00027', 'EQ027', 'RUG', 12, 12, '2025-12-08', '-'),
('STK00028', 'EQ028', 'SWI', 25, 25, '2025-12-08', '-'),
('STK00029', 'EQ029', 'SWI', 30, 30, '2025-12-08', '-'),
('STK00030', 'EQ030', 'SWI', 10, 10, '2025-12-08', '-'),
('STK00031', 'EQ031', 'TT', 20, 20, '2025-12-08', '-'),
('STK00032', 'EQ032', 'TT', 150, 150, '2025-12-08', '-'),
('STK00033', 'EQ033', 'TT', 4, 4, '2025-12-08', '-'),
('STK00034', 'EQ034', 'WL', 10, 10, '2025-12-08', '-'),
('STK00035', 'EQ035', 'WL', 20, 20, '2025-12-08', '-'),
('STK00036', 'EQ036', 'WL', 80, 80, '2025-12-08', '-'),
('STK00037', 'EQ037', 'ROW', 6, 6, '2025-12-08', '-'),
('STK00038', 'EQ038', 'ROW', 20, 20, '2025-12-08', '-'),
('STK00039', 'EQ039', 'WRE', 4, 4, '2025-12-08', '-'),
('STK00040', 'EQ040', 'CHE', 15, 3, '2025-12-08', '-'),
('STK00041', 'EQ041', 'CHE', 10, 10, '2025-12-08', '-'),
('STK00042', 'EQ042', 'ATH', 8, 8, '2025-12-08', '-'),
('STK00043', 'EQ043', 'ATH', 12, 12, '2025-12-08', '-'),
('STK00044', 'EQ044', 'ATH', 10, 10, '2025-12-08', '-'),
('STK00045', 'EQ045', 'ATH', 14, 14, '2025-12-08', '-'),
('STK00046', 'EQ046', 'BOX', 16, 16, '2025-12-08', '-'),
('STK00047', 'EQ047', 'BOX', 4, 4, '2025-12-08', '-'),
('STK00048', 'EQ048', 'TKD', 20, 20, '2025-12-08', '-'),
('STK00049', 'EQ049', 'TKD', 20, 20, '2025-12-08', '-'),
('STK00050', 'EQ050', 'KRT', 20, 20, '2025-12-08', '-'),
('STK00051', 'EQ051', 'KRT', 20, 20, '2025-12-08', '-'),
('STK00052', 'EQ052', 'RR', 30, 30, '2025-12-08', '-'),
('STK00053', 'EQ053', 'RR', 200, 200, '2025-12-08', '-'),
('STK00054', 'EQ054', 'SCR', 10, 10, '2025-12-08', '-'),
('STK00055', 'EQ055', 'ELL', 8, 8, '2025-12-08', '-'),
('STK00056', 'EQ056', 'BB', 12, 12, '2025-12-08', '-'),
('STK00057', 'EQ057', 'BB', 40, 40, '2025-12-08', '-'),
('STK00058', 'EQ058', 'BB', 20, 20, '2025-12-08', '-'),
('STK00059', 'EQ059', 'KBD', 4, 4, '2025-12-08', '-'),
('STK00060', 'EQ060', 'CRM', 10, 10, '2025-12-08', '-'),
('STK00061', 'EQ061', 'CRM', 40, 40, '2025-12-08', '-'),
('STK00062', 'EQ062', 'CRM', 15, 15, '2025-12-08', '-'),
('STK00063', 'EQ69354316b1', 'TKD', 200, 200, '2025-12-08', '-'),
('STK6937d', 'EQ046', 'BOX', 12, 12, '2025-12-09', 'Donated by Sri Lanka Boxing Federation'),
('STK6937e', 'EQ6937e28ddf', 'BOX', 20, 20, '2025-12-09', 'Donated by Sri Lanka Boxing Federation'),
('STK69956', 'EQ699563ae09', 'NET', 5, 5, '2026-12-18', '-');

-- --------------------------------------------------------
-- Table structure for table `event_result_permissions`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `event_result_permissions`;
CREATE TABLE IF NOT EXISTS `event_result_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` varchar(24) NOT NULL,
  `captain_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `granted_by` varchar(12) NOT NULL COMMENT 'Admin user_id who granted the permission',
  `granted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ACTIVE','REVOKED') NOT NULL DEFAULT 'ACTIVE',
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournament_captain` (`tournament_id`,`captain_id`),
  KEY `captain_id` (`captain_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `event_result_permissions` (`id`, `tournament_id`, `captain_id`, `sport_id`, `granted_by`, `granted_at`, `status`, `email_sent`) VALUES
(1, 'TOUR_69ccde3bc3ae2', '5Q1XZO2Y', 'VOL', '91', '2026-04-01 09:24:28', 'ACTIVE', 1);

-- --------------------------------------------------------
-- Table structure for table `facility`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `physical_facility`;
CREATE TABLE IF NOT EXISTS `physical_facility` (
  `facility_id` varchar(12) NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`facility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `physical_facility` (`facility_id`, `facility_name`, `location`) VALUES
('FAC_INDOOR', 'Main Indoor Stadium', 'Upper Campus'),
('FAC_GROUND', 'University Main Ground', 'Lower Campus'),
('FAC_TENNIS', 'Tennis Grounds', 'Upper Campus'),
('FAC_BASKETBALL', 'Basketball Ground', 'Upper Campus');

-- --------------------------------------------------------
-- Table structure for table `facility-booking`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `facility-booking`;
CREATE TABLE IF NOT EXISTS `facility-booking` (
  `booking_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `facility_id` varchar(8) NOT NULL,
  `date` date NOT NULL,
  `slot` varchar(12) NOT NULL,
  `purpose` varchar(300) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'BOOKED',
  `payment_status` varchar(12) NOT NULL DEFAULT 'INCOMPLETE',
  `payment_id` varchar(50) DEFAULT NULL,
  `rejection_reason` varchar(256) NOT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `slot`, `purpose`, `status`, `payment_status`, `payment_id`, `rejection_reason`) VALUES
('BK711559', 'H4J1OHSX', '9', '2025-12-11', 'FULL', 'To practice for Inter Provincial Matches held in January 2026', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK398317', 'H4J1OHSX', '3', '2025-12-10', 'AFTERNOON', 'Badminton Provincial Matches Practice', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK861578', 'L3NCL2J4', '11', '2025-12-10', 'MORNING', 'For Inter University Practices for SLIIT University', 'REJECTED', 'INCOMPLETE', NULL, 'No reason'),
('BK937846', 'L3NCL2J4', '15', '2025-12-18', 'FULL', 'For TOC Championship Match Practice', 'REJECTED', 'INCOMPLETE', NULL, 'A maintenance on the ground has been scheduled for that day. Sorry for the inconvenience.'),
('BK405911', 'H4J1OHSX', '5', '2025-12-11', 'FULL', 'Divisional Tennis Matches', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK662944', '5Q1XZO2Y', '15', '2025-12-12', 'FULL', 'Inter Uni Matches Practice', 'BOOKED', 'COMPLETE', 'RETURN-1768619626', ''),
('BK743077', 'L3NCL2J4', '18', '2025-12-27', 'FULL', 'Cricket practice', 'ACCEPTED', 'INCOMPLETE', NULL, ''),
('BK425118', 'H4J1OHSX', '13', '2025-12-29', 'FULL', '-', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK896561', 'H4J1OHSX', '13', '2026-01-01', 'FULL', '-', 'ACCEPTED', 'COMPLETE', 'RETURN-1767766634', ''),
('BK228271', 'H4J1OHSX', '4', '2026-01-09', 'MORNING', 'Inter uni practices', 'BOOKED', 'COMPLETE', 'RETURN-1767779957', ''),
('BK656929', 'H4J1OHSX', '2', '2026-01-16', 'MORNING', 'Testing 01', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK623825', 'H4J1OHSX', '2', '2026-01-16', 'AFTERNOON', 'Testing 02', 'BOOKED', 'COMPLETE', 'RETURN-1768311979', ''),
('BK572996', 'H4J1OHSX', '2', '2026-01-14', 'MORNING', 'Testing 3', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK341930', '5Q1XZO2Y', '2', '2026-01-14', 'AFTERNOON', 'testing 4', 'BOOKED', 'COMPLETE', 'RETURN-1768619654', ''),
('BK390858', 'H4J1OHSX', '13', '2026-01-21', 'FULL', '-', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK890801', 'NPM8O9RE', '14', '2026-02-20', 'MORNING', 'Practice', 'BOOKED', 'INCOMPLETE', NULL, ''),
('BK955511', 'H4J1OHSX', '2', '2026-02-18', 'AFTERNOON', '-', 'BOOKED', 'INCOMPLETE', NULL, '');

-- --------------------------------------------------------
-- Table structure for table `facility_rates`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `facility_rates`;
CREATE TABLE IF NOT EXISTS `facility_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` varchar(12) DEFAULT NULL COMMENT 'References facility.facility_id (Physical Location)',
  `facility_type` enum('INDOOR_GYM','GROUND') NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `capacity` int DEFAULT NULL,
  `practice_working_hours` decimal(10,2) DEFAULT NULL,
  `practice_other_hours` decimal(10,2) DEFAULT NULL,
  `tournament_full_day_working` decimal(10,2) DEFAULT NULL,
  `tournament_half_day_working` decimal(10,2) DEFAULT NULL,
  `tournament_full_day_other` decimal(10,2) DEFAULT NULL,
  `tournament_half_day_other` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_facility_type` (`facility_type`),
  KEY `idx_facility_name` (`facility_name`),
  KEY `idx_facility_id` (`facility_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

INSERT INTO `facility_rates` (`id`, `facility_id`, `facility_type`, `facility_name`, `capacity`, `practice_working_hours`, `practice_other_hours`, `tournament_full_day_working`, `tournament_half_day_working`, `tournament_full_day_other`, `tournament_half_day_other`, `created_at`, `updated_at`) VALUES
(1, 'FAC_INDOOR', 'INDOOR_GYM', 'Badminton one Court (08 Persons for practices)', 8, '800.00', '1100.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(2, 'FAC_INDOOR', 'INDOOR_GYM', 'Badminton two Courts (16 Persons for practices)', 16, '1600.00', '1900.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(3, 'FAC_INDOOR', 'INDOOR_GYM', 'Badminton Four Courts (30 Persons for practices)', 30, '3000.00', '3600.00', '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(4, 'FAC_INDOOR', 'INDOOR_GYM', 'Table Tennis Two tables (08 Persons for practices)', 8, '900.00', '1200.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(5, 'FAC_INDOOR', 'INDOOR_GYM', 'Table Tennis', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(6, 'FAC_INDOOR', 'INDOOR_GYM', 'Karate / Taekwondo with without Tatami', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(7, 'FAC_INDOOR', 'INDOOR_GYM', 'Wrestling without mattress', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(8, 'FAC_INDOOR', 'INDOOR_GYM', 'Volleyball (25 Persons for practices)', 25, '5000.00', '5600.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(9, 'FAC_INDOOR', 'INDOOR_GYM', 'Volleyball', NULL, NULL, NULL, '60000.00', '40000.00', '69000.00', '46000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(10, 'FAC_INDOOR', 'INDOOR_GYM', 'Student Sport Center and surrounding area (sports activities & functions)', NULL, NULL, NULL, '30000.00', '20000.00', '39000.00', '26000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(11, 'FAC_GROUND', 'GROUND', 'Baseball (30 Persons for practices)', 30, '30000.00', '17500.00', NULL, NULL, '65000.00', '35000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(12, 'FAC_BASKETBALL', 'GROUND', 'Basketball (25 Persons for practices) (without light)', 25, '20000.00', '12000.00', '6000.00', '40000.00', '25000.00', '10000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(13, 'FAC_BASKETBALL', 'GROUND', 'Basketball (25 Persons for practices) (with light)', 25, NULL, '17500.00', '8000.00', NULL, '25000.00', '12500.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(14, 'FAC_GROUND', 'GROUND', 'Cricket - Hard Ball with matting (only one team allowed for practices)', NULL, '30000.00', '17500.00', '10000.00', '35000.00', '20000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(15, 'FAC_GROUND', 'GROUND', 'Cricket - Hard Ball fielding practices (only one team allowed)', NULL, NULL, NULL, '6000.00', NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(16, 'FAC_GROUND', 'GROUND', 'Soft Ball Cricket & Other functions (maximum three pitches)', NULL, NULL, NULL, '4000.00', '115000.00', '65000.00', '10000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(17, 'FAC_GROUND', 'GROUND', 'Cricket - Side Wicket (one wicket) (18 Persons)', 18, NULL, NULL, '4000.00', NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(18, 'FAC_GROUND', 'GROUND', 'Cricket Turf', NULL, NULL, NULL, '7000.00', '45000.00', '25000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(19, 'FAC_GROUND', 'GROUND', 'Elle', NULL, NULL, NULL, NULL, '45000.00', '25000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(20, 'FAC_GROUND', 'GROUND', 'Football One Court without court marking (40 Persons for practices)', 40, '30000.00', '20000.00', '16000.00', NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(21, 'FAC_GROUND', 'GROUND', 'Football with court marking', NULL, '30000.00', '27500.00', '17500.00', '70000.00', '40000.00', '27500.00', '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(22, 'FAC_GROUND', 'GROUND', 'Hockey (30 Persons for practices)', 30, '30000.00', '20000.00', '10000.00', NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(23, 'FAC_GROUND', 'GROUND', 'Hockey with court marking', NULL, '30000.00', '27500.00', '17500.00', '70000.00', '40000.00', '27500.00', '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(24, 'FAC_GROUND', 'GROUND', 'Netball (25 Persons for practices) One Court', 25, '30000.00', '20000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(25, 'FAC_GROUND', 'GROUND', 'Netball (50 Persons for practices) Six Courts', 50, '45000.00', '30000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(26, 'FAC_GROUND', 'GROUND', 'Rugby (40 Persons for practices)', 40, '40000.00', '25000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(27, 'FAC_GROUND', 'GROUND', 'Rugby with court Marking', NULL, '47500.00', '32500.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(28, 'FAC_TENNIS', 'GROUND', 'Tennis One Court (04 Persons)', 4, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(29, 'FAC_TENNIS', 'GROUND', 'Tennis Two Courts (04 Persons each)', 8, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(30, 'FAC_GROUND', 'GROUND', 'Track & Field (without ground marking and without High Jump/ Mattress)', NULL, '45000.00', '25000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(31, 'FAC_GROUND', 'GROUND', 'Track & Field (with ground marking and with High Jump/ Mattress)', NULL, '65000.00', '45000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(32, 'FAC_GROUND', 'GROUND', 'Volleyball (Outdoor) (1 court) (25 Persons for practices)', 25, '30000.00', '20000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32');

-- --------------------------------------------------------
-- Table structure for table `faculty`
-- (Using full list from Doc 1; Doc 2 only had 2 rows)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` varchar(4) NOT NULL,
  `faculty_name` varchar(64) NOT NULL,
  `registrar_id` varchar(12) DEFAULT NULL COMMENT 'User ID of the faculty registrar',
  `registrar_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`faculty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `faculty` (`faculty_id`, `faculty_name`, `registrar_id`, `registrar_email`) VALUES
('1', 'University of Colombo School of Computing (UCSC)', 'REG003', 'kasun.silva@ucsc.uoc.lk'),
('2', 'Faculty of Science', NULL, NULL),
('3', 'Faculty of Arts', NULL, NULL),
('4', 'Faculty of Education', NULL, NULL),
('5', 'Faculty of Indigenous Medicine', NULL, NULL),
('6', 'Faculty of Law', NULL, NULL),
('7', 'Faculty of Management & Finance', NULL, NULL),
('8', 'Faculty of Medicine', NULL, NULL),
('9', 'Faculty of Nursing', NULL, NULL),
('10', 'Faculty of Technology', NULL, NULL);

-- --------------------------------------------------------
-- Table structure for table `good_condemn_notes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `good_condemn_notes`;
CREATE TABLE IF NOT EXISTS `good_condemn_notes` (
  `gcn_id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(4) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `stock_id` varchar(8) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gcn_id`),
  KEY `sport_id` (`sport_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

INSERT INTO `good_condemn_notes` (`gcn_id`, `sport_id`, `equipment_id`, `stock_id`, `quantity`, `created_at`) VALUES
(1, 'BAD', 'EQ001', 'STK00001', 3, '2025-09-01 10:00:00'),
(2, 'HOC', 'EQ017', 'STK00017', 8, '2025-10-15 14:00:00'),
(3, 'TEN', 'EQ011', 'STK00011', 2, '2025-11-20 09:30:00'),
(4, 'CRI', 'EQ022', 'STK00022', 1, '2025-12-10 11:00:00'),
(5, 'CHE', 'EQ040', 'STK00040', 12, '2026-01-05 08:45:00'),
(6, 'FOO', 'EQ009', 'STK00009', 3, '2026-01-22 10:15:00'),
(7, 'BAD', 'EQ002', 'STK00002', 15, '2026-02-10 13:00:00'),
(8, 'VOL', 'EQ006', 'STK00006', 5, '2026-02-19 09:00:00');

-- --------------------------------------------------------
-- Table structure for table `good_issue_notes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `good_issue_notes`;
CREATE TABLE IF NOT EXISTS `good_issue_notes` (
  `gin_id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(4) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `date` date NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(32) NOT NULL,
  `stock_id` varchar(8) NOT NULL,
  `sport_manager_id` varchar(12) DEFAULT NULL,
  `captain_id` varchar(12) DEFAULT NULL,
  `equipment_manager_id` varchar(12) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gin_id`),
  KEY `sport_id` (`sport_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

INSERT INTO `good_issue_notes` (`gin_id`, `sport_id`, `equipment_id`, `date`, `quantity`, `unit`, `stock_id`, `sport_manager_id`, `captain_id`, `equipment_manager_id`, `created_at`) VALUES
(1, 'BAD', 'EQ001', '2025-07-10', 5, 'Nos', 'STK00001', 'usr_694d89fa', '5Q1XZO2Y', 'usr_68f82fe0', '2025-07-10 08:00:00'),
(2, 'CRI', 'EQ022', '2025-08-05', 2, 'Nos', 'STK00022', 'usr_68f89be0', NULL, 'usr_68f89998', '2025-08-05 09:30:00'),
(3, 'FOO', 'EQ007', '2025-09-20', 4, 'Nos', 'STK00007', 'SPT004', NULL, 'usr_68f82fe0', '2025-09-20 10:00:00'),
(4, 'VOL', 'EQ004', '2025-10-15', 3, 'Nos', 'STK00004', 'usr_694d89fa', NULL, 'usr_68f89998', '2025-10-15 11:30:00'),
(5, 'TEN', 'EQ011', '2025-11-10', 4, 'Nos', 'STK00011', 'SPT004', '5Q1XZO2Y', 'usr_68f82fe0', '2025-11-10 14:00:00'),
(6, 'HOC', 'EQ017', '2025-12-01', 6, 'Nos', 'STK00017', 'usr_68f89be0', NULL, 'usr_68f89998', '2025-12-01 09:15:00'),
(7, 'BAS', 'EQ014', '2026-01-08', 3, 'Nos', 'STK00014', 'usr_694d89fa', '5Q1XZO2Y', 'usr_68f82fe0', '2026-01-08 10:45:00'),
(8, 'ATH', 'EQ042', '2026-01-15', 2, 'Nos', 'STK00042', 'SPT004', NULL, 'usr_68f89998', '2026-01-15 08:30:00'),
(9, 'CRI', 'EQ023', '2026-02-01', 10, 'Nos', 'STK00023', 'usr_68f89be0', '5Q1XZO2Y', 'usr_68f82fe0', '2026-02-01 11:00:00'),
(10, 'BAD', 'EQ002', '2026-02-18', 20, 'Tubes', 'STK00002', 'usr_694d89fa', NULL, 'usr_68f89998', '2026-02-18 09:00:00');

-- --------------------------------------------------------
-- Table structure for table `good_received_notes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `good_received_notes`;
CREATE TABLE IF NOT EXISTS `good_received_notes` (
  `grn_id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(4) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `date` date NOT NULL,
  `po_number` varchar(64) DEFAULT NULL,
  `supplier_id` int NOT NULL,
  `invoice_no` varchar(64) DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(32) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `reference_info` varchar(128) DEFAULT NULL,
  `stock_id` varchar(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`grn_id`),
  KEY `sport_id` (`sport_id`),
  KEY `equipment_id` (`equipment_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

INSERT INTO `good_received_notes` (`grn_id`, `sport_id`, `equipment_id`, `description`, `date`, `po_number`, `supplier_id`, `invoice_no`, `quantity`, `unit`, `unit_price`, `reference_info`, `stock_id`, `created_at`) VALUES
(1, 'BAD', 'EQ001', 'Badminton Rackets - Yonex Astrox 88D', '2025-06-15', 'PO-2025-001', 6, 'INV-YNX-0456', 20, 'Nos', '8500.00', 'Annual procurement', 'STK00001', '2025-06-15 10:00:00'),
(2, 'BAD', 'EQ002', 'Shuttlecocks - Yonex Mavis 350', '2025-06-15', 'PO-2025-001', 6, 'INV-YNX-0457', 200, 'Tubes', '1200.00', 'Annual procurement', 'STK00002', '2025-06-15 10:00:00'),
(3, 'CRI', 'EQ022', 'Cricket Bats - SG Profile Xtreme', '2025-07-20', 'PO-2025-005', 1, 'INV-LS-1123', 12, 'Nos', '12000.00', 'Inter-university tournament', 'STK00022', '2025-07-20 09:30:00'),
(4, 'CRI', 'EQ023', 'Cricket Balls - SG Test Red', '2025-07-20', 'PO-2025-005', 1, 'INV-LS-1124', 60, 'Nos', '950.00', 'Inter-university tournament', 'STK00023', '2025-07-20 09:30:00'),
(5, 'FOO', 'EQ007', 'Footballs - Nivia Ashtang', '2025-08-10', 'PO-2025-008', 7, 'INV-NIV-0089', 18, 'Nos', '3500.00', 'Seasonal stock', 'STK00007', '2025-08-10 14:00:00'),
(6, 'BAS', 'EQ014', 'Basketballs - Molten GG7X', '2025-09-01', 'PO-2025-012', 4, 'INV-CHP-0234', 10, 'Nos', '7500.00', 'Replacement stock', 'STK00014', '2025-09-01 11:00:00'),
(7, 'VOL', 'EQ004', 'Volleyballs - Mikasa MVA200', '2025-09-15', 'PO-2025-015', 3, 'INV-IST-0567', 15, 'Nos', '6800.00', 'Inter-faculty games', 'STK00004', '2025-09-15 13:30:00'),
(8, 'TEN', 'EQ011', 'Tennis Rackets - Wilson Blade', '2025-10-05', 'PO-2025-018', 2, 'INV-PRO-0345', 12, 'Nos', '15000.00', 'Varsity team', 'STK00011', '2025-10-05 10:15:00'),
(9, 'HOC', 'EQ017', 'Hockey Sticks - Malik Carbon', '2025-11-12', 'PO-2025-022', 1, 'INV-LS-1200', 20, 'Nos', '9500.00', 'National championship prep', 'STK00017', '2025-11-12 09:00:00'),
(10, 'ATH', 'EQ043', 'Javelins - Nordic Competition', '2025-12-01', 'PO-2025-025', 5, 'INV-SLSA-0078', 12, 'Nos', '22000.00', 'Track & Field equipment', 'STK00043', '2025-12-01 08:45:00'),
(11, 'BOX', 'EQ046', 'Boxing Gloves - Everlast Pro', '2025-12-09', 'PO-2025-028', 4, 'INV-CHP-0290', 16, 'Pairs', '4500.00', 'Boxing team', 'STK00046', '2025-12-09 11:20:00'),
(12, 'SWI', 'EQ028', 'Swimming Goggles - Speedo Fastskin', '2026-01-10', 'PO-2026-001', 3, 'INV-IST-0601', 25, 'Nos', '3200.00', 'New season stock', 'STK00028', '2026-01-10 09:00:00'),
(13, 'RUG', 'EQ026', 'Rugby Balls - Gilbert Match XV', '2026-01-20', 'PO-2026-003', 7, 'INV-NIV-0112', 10, 'Nos', '5500.00', 'Rugby season', 'STK00026', '2026-01-20 14:30:00'),
(14, 'TT', 'EQ031', 'Table Tennis Bats - Butterfly Timo Boll', '2026-02-05', 'PO-2026-005', 2, 'INV-PRO-0389', 20, 'Nos', '6200.00', 'TT club renewal', 'STK00031', '2026-02-05 10:00:00'),
(15, 'BAD', 'EQ003', 'Badminton Nets - Li-Ning Tournament', '2026-02-15', 'PO-2026-008', 6, 'INV-YNX-0512', 5, 'Nos', '4500.00', 'Net replacement', 'STK00003', '2026-02-15 09:15:00'),
(16, 'ATH', 'EQ044', 'Testing Description', '2026-01-01', '09-98-AD', 3, 'AD-78-65-43', 4, 'pcs', '12000.00', 'Athletics/Throwing', 'STK00044', '2026-03-30 06:12:35');

-- --------------------------------------------------------
-- Table structure for table `injury_report`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `injury_report`;
CREATE TABLE IF NOT EXISTS `injury_report` (
  `report_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `coach_id` varchar(12) NOT NULL,
  `practice_id` varchar(12) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(256) NOT NULL,
  `need_substitude` varchar(3) NOT NULL,
  `substitude_id` varchar(12) NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `injury_report` (`report_id`, `user_id`, `coach_id`, `practice_id`, `date`, `description`, `need_substitude`, `substitude_id`) VALUES
('IRP6971E85EA', 'P001', 'NPM8O9RE', '4', '2026-01-01', 'test (Minor)', 'YES', 'P002');

-- --------------------------------------------------------
-- Table structure for table `inquiry`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `inquiry`;
CREATE TABLE IF NOT EXISTS `inquiry` (
  `inquiry_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `email` varchar(64) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `message` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'NOT-RESOLVED',
  PRIMARY KEY (`inquiry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `inquiry` (`inquiry_id`, `user_id`, `email`, `subject`, `message`, `date`, `status`) VALUES
('INQA1A688463', 'H4J1OHSX', 'maximal@gmail.com', 'Testing contact', 'Something Something', '2025-12-15', 'RESOLVED'),
('INQE8F057499', 'H4J1OHSX', 'dakshinagn@gmail.com', 'about group project', 'on progress', '2025-12-18', 'RESOLVED');

-- --------------------------------------------------------
-- Table structure for table `invitational_players`
-- (Only in Doc 1)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `invitational_players`;
CREATE TABLE IF NOT EXISTS `invitational_players` (
  `inv_player_id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `university` varchar(100) NOT NULL,
  `student_id` varchar(30) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`inv_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `lost_found`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `lost_found`;
CREATE TABLE IF NOT EXISTS `lost_found` (
  `case_id` varchar(12) NOT NULL,
  `case_title` varchar(64) NOT NULL,
  `description` varchar(256) NOT NULL,
  `reported_time` timestamp NOT NULL,
  `reported_by` varchar(32) NOT NULL,
  `reporter_contact` varchar(15) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'NOT-RESOLVED',
  PRIMARY KEY (`case_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `lost_found_images`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `lost_found_images`;
CREATE TABLE IF NOT EXISTS `lost_found_images` (
  `case_id` varchar(12) NOT NULL,
  `image_name` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `lost_item`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `lost_item`;
CREATE TABLE IF NOT EXISTS `lost_item` (
  `lostItem_id` int NOT NULL AUTO_INCREMENT,
  `itemName` varchar(255) NOT NULL,
  `foundDate` date NOT NULL,
  `description` text,
  `foundLocation` varchar(255) NOT NULL,
  `foundBy` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `itemStatus` varchar(20) NOT NULL DEFAULT 'unclaimed',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`lostItem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `manager_sport`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `manager_sport`;
CREATE TABLE IF NOT EXISTS `manager_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_ball_court`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_ball_court`;
CREATE TABLE IF NOT EXISTS `match_ball_court` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `team_a_name` varchar(100) DEFAULT NULL,
  `team_b_name` varchar(100) DEFAULT NULL,
  `sport_subtype` enum('BASKETBALL','VOLLEYBALL','BASEBALL') NOT NULL,
  `period_scores` json DEFAULT NULL,
  `final_score_a` int DEFAULT '0',
  `final_score_b` int DEFAULT '0',
  `overtime_periods` int DEFAULT '0',
  `sets_won_a` int DEFAULT NULL,
  `sets_won_b` int DEFAULT NULL,
  `innings_played` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `match_ball_court` (`id`, `match_id`, `team_a_name`, `team_b_name`, `sport_subtype`, `period_scores`, `final_score_a`, `final_score_b`, `overtime_periods`, `sets_won_a`, `sets_won_b`, `innings_played`, `notes`) VALUES
(1, 'match_69cce68043d035.97638693', 'USJP', 'UOC', 'VOLLEYBALL', '[{"a": 25, "b": 23}, {"a": 22, "b": 25}, {"a": 19, "b": 25}]', 1, 2, 0, 1, 2, NULL, 'UOC won the Match');

-- --------------------------------------------------------
-- Table structure for table `match_board_game`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_board_game`;
CREATE TABLE IF NOT EXISTS `match_board_game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `player_a_name` varchar(100) DEFAULT NULL,
  `player_b_name` varchar(100) DEFAULT NULL,
  `game_type` enum('CHESS','SCRABBLE','CARROM') NOT NULL,
  `chess_result` enum('WHITE_WIN','BLACK_WIN','DRAW','STALEMATE') DEFAULT NULL,
  `chess_opening` varchar(100) DEFAULT NULL,
  `moves_count` int DEFAULT NULL,
  `time_control` varchar(50) DEFAULT NULL,
  `scrabble_score_a` int DEFAULT NULL,
  `scrabble_score_b` int DEFAULT NULL,
  `highest_word_score` int DEFAULT NULL,
  `highest_word` varchar(50) DEFAULT NULL,
  `carrom_score_a` int DEFAULT NULL,
  `carrom_score_b` int DEFAULT NULL,
  `boards_played` int DEFAULT '1',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_combat`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_combat`;
CREATE TABLE IF NOT EXISTS `match_combat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `fighter_a_name` varchar(100) DEFAULT NULL,
  `fighter_b_name` varchar(100) DEFAULT NULL,
  `weight_category` varchar(50) DEFAULT NULL,
  `round_scores` json DEFAULT NULL,
  `total_rounds` int DEFAULT '3',
  `rounds_completed` int DEFAULT '0',
  `final_score_a` int DEFAULT '0',
  `final_score_b` int DEFAULT '0',
  `result_type` enum('POINTS','KO','TKO','SUBMISSION','IPPON','WAZA_ARI','DISQUALIFICATION','WALKOVER','PIN') DEFAULT 'POINTS',
  `knockdowns_a` int DEFAULT '0',
  `knockdowns_b` int DEFAULT '0',
  `warnings_a` int DEFAULT '0',
  `warnings_b` int DEFAULT '0',
  `pins_a` int DEFAULT '0',
  `pins_b` int DEFAULT '0',
  `raid_points_a` int DEFAULT NULL,
  `raid_points_b` int DEFAULT NULL,
  `tackle_points_a` int DEFAULT NULL,
  `tackle_points_b` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `match_combat` (`id`, `match_id`, `fighter_a_name`, `fighter_b_name`, `weight_category`, `round_scores`, `total_rounds`, `rounds_completed`, `final_score_a`, `final_score_b`, `result_type`, `knockdowns_a`, `knockdowns_b`, `warnings_a`, `warnings_b`, `pins_a`, `pins_b`, `raid_points_a`, `raid_points_b`, `tackle_points_a`, `tackle_points_b`, `notes`) VALUES
(1, 'match_695d2d74e031d4.90256849', 'UCSC', 'Science', '72+', '[{"a": 15, "b": 8}, {"a": 18, "b": 0}]', 3, 2, 0, 0, '', 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------
-- Table structure for table `match_cricket`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_cricket`;
CREATE TABLE IF NOT EXISTS `match_cricket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `team_a_name` varchar(100) DEFAULT NULL,
  `team_b_name` varchar(100) DEFAULT NULL,
  `match_format` enum('TEST','ODI','T20','T10','OTHER') DEFAULT 'T20',
  `overs_per_innings` decimal(4,1) DEFAULT NULL,
  `innings_1_team` char(1) DEFAULT NULL,
  `innings_1_runs` int DEFAULT '0',
  `innings_1_wickets` int DEFAULT '0',
  `innings_1_overs` decimal(4,1) DEFAULT '0.0',
  `innings_1_extras` int DEFAULT '0',
  `innings_2_team` char(1) DEFAULT NULL,
  `innings_2_runs` int DEFAULT '0',
  `innings_2_wickets` int DEFAULT '0',
  `innings_2_overs` decimal(4,1) DEFAULT '0.0',
  `innings_2_extras` int DEFAULT '0',
  `result_type` enum('WIN_RUNS','WIN_WICKETS','TIE','DRAW','NO_RESULT','SUPER_OVER') DEFAULT NULL,
  `win_margin` int DEFAULT NULL,
  `winning_team` char(1) DEFAULT NULL,
  `super_over_team_a` int DEFAULT NULL,
  `super_over_team_b` int DEFAULT NULL,
  `potm_user_id` varchar(12) DEFAULT NULL,
  `toss_won_by` char(1) DEFAULT NULL,
  `toss_decision` enum('BAT','BOWL') DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_participant`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_participant`;
CREATE TABLE IF NOT EXISTS `match_participant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `team` enum('A','B') DEFAULT 'A',
  `score` int DEFAULT NULL,
  `performance_data` json DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_racket`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_racket`;
CREATE TABLE IF NOT EXISTS `match_racket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `player_a_name` varchar(100) DEFAULT NULL,
  `player_b_name` varchar(100) DEFAULT NULL,
  `match_format` enum('BEST_OF_3','BEST_OF_5','SINGLE_SET') DEFAULT 'BEST_OF_3',
  `match_type` enum('SINGLES','DOUBLES','MIXED_DOUBLES') DEFAULT 'SINGLES',
  `set_scores` json DEFAULT NULL,
  `sets_won_a` int DEFAULT '0',
  `sets_won_b` int DEFAULT '0',
  `total_points_a` int DEFAULT '0',
  `total_points_b` int DEFAULT '0',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_team_goal`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_team_goal`;
CREATE TABLE IF NOT EXISTS `match_team_goal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `team_a_name` varchar(100) DEFAULT NULL,
  `team_b_name` varchar(100) DEFAULT NULL,
  `team_a_goals` int DEFAULT '0',
  `team_b_goals` int DEFAULT '0',
  `team_a_yellow_cards` int DEFAULT '0',
  `team_b_yellow_cards` int DEFAULT '0',
  `team_a_red_cards` int DEFAULT '0',
  `team_b_red_cards` int DEFAULT '0',
  `team_a_tries` int DEFAULT NULL,
  `team_b_tries` int DEFAULT NULL,
  `team_a_conversions` int DEFAULT NULL,
  `team_b_conversions` int DEFAULT NULL,
  `team_a_penalties` int DEFAULT NULL,
  `team_b_penalties` int DEFAULT NULL,
  `extra_time` tinyint(1) DEFAULT '0',
  `penalty_shootout` tinyint(1) DEFAULT '0',
  `penalty_score_a` int DEFAULT NULL,
  `penalty_score_b` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_timed`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_timed`;
CREATE TABLE IF NOT EXISTS `match_timed` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `event_type` enum('SPRINT','MIDDLE_DISTANCE','LONG_DISTANCE','RELAY','SWIMMING','ROWING','FIELD_THROW','FIELD_JUMP','ROAD_RACE') DEFAULT 'SPRINT',
  `event_name` varchar(100) DEFAULT NULL,
  `results` json DEFAULT NULL,
  `winning_time` decimal(10,3) DEFAULT NULL,
  `winning_distance` decimal(10,3) DEFAULT NULL,
  `winner_user_id` varchar(12) DEFAULT NULL,
  `is_record` tinyint(1) DEFAULT '0',
  `record_type` enum('PERSONAL_BEST','UNIVERSITY_RECORD','NATIONAL_RECORD') DEFAULT NULL,
  `weather_conditions` varchar(100) DEFAULT NULL,
  `wind_speed` decimal(4,2) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `match_weight_lifting`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `match_weight_lifting`;
CREATE TABLE IF NOT EXISTS `match_weight_lifting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `athlete_name` varchar(100) DEFAULT NULL,
  `weight_category` varchar(50) DEFAULT NULL,
  `snatch_1` decimal(5,1) DEFAULT NULL,
  `snatch_1_valid` tinyint(1) DEFAULT NULL,
  `snatch_2` decimal(5,1) DEFAULT NULL,
  `snatch_2_valid` tinyint(1) DEFAULT NULL,
  `snatch_3` decimal(5,1) DEFAULT NULL,
  `snatch_3_valid` tinyint(1) DEFAULT NULL,
  `snatch_best` decimal(5,1) DEFAULT NULL,
  `cj_1` decimal(5,1) DEFAULT NULL,
  `cj_1_valid` tinyint(1) DEFAULT NULL,
  `cj_2` decimal(5,1) DEFAULT NULL,
  `cj_2_valid` tinyint(1) DEFAULT NULL,
  `cj_3` decimal(5,1) DEFAULT NULL,
  `cj_3_valid` tinyint(1) DEFAULT NULL,
  `cj_best` decimal(5,1) DEFAULT NULL,
  `total_kg` decimal(5,1) DEFAULT NULL,
  `competition_results` json DEFAULT NULL,
  `final_position` int DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `message`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `message_id` varchar(12) NOT NULL,
  `sender_id` varchar(12) NOT NULL COMMENT 'User ID of sender',
  `recipient_id` varchar(12) NOT NULL COMMENT 'User ID of recipient',
  `recipient_type` enum('COACH','MANAGER','CAPTAIN') NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `title` varchar(128) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `message` (`message_id`, `sender_id`, `recipient_id`, `recipient_type`, `sport_id`, `title`, `message`, `sent_at`, `is_read`) VALUES
('MSG699AD6597', '5Q1XZO2Y', 'H4J1OHSX', '', 'VOL', 'Requesting Javelins', 'Hello sir, can we have the Javelins I requested today?', '2026-02-22 10:11:37', 0);

-- --------------------------------------------------------
-- Table structure for table `newsfeed_post`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `newsfeed_post`;
CREATE TABLE IF NOT EXISTS `newsfeed_post` (
  `post_id` varchar(12) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `commenting` varchar(8) NOT NULL,
  `date_posted` date NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `newsfeed_post` (`post_id`, `title`, `description`, `commenting`, `date_posted`, `status`) VALUES
('P0001', 'Track & Field and Ground Marking Workshop', 'A workshop on Track & Field and Ground Marking was held on the 30th and 31st of March 2025 at the University of Colombo ground premises. This workshop was conducted by Mr Palitha Jayathilaka, Senior Technical Official at the Sri Lanka Athletic Association, to update our staff members on the new methods and changes in ground marking. Participants who completed this workshop successfully received a valuable certificate.', 'YES', '2025-12-09', 'ACTIVE'),
('P0002', '36th National Rowing Championship', 'University of Colombo rowers won 5 medals at the 36th National Rowing Championship which was held on the 12-13 March 2021 at Diyawannawa Rowing Center. In the Open Category (Women\'s), Ms Ranmalee Nanayakkara and Nadani Mendis won the Silver medal in the Open Double scull, Ms Nadani Mendis and Upuli Edirisingha won the bronze medal in the open Pair and Ms Ranmalee Nanayakkara won the bronze medal in the Open Scull category. In the Intermediate Category (Women\'s), Ms Himasha Panditharatne and Vibhanga Amarasinghe won the bronze medal in the pair event. In the Intermediate Category (Men\'s), Mr Avishka Jayaweera, Mr Shehan Shamalka, Mr Dushyantha Hettiarachchi, Mr Shehan Dinusha Liyanage, Mr Samitha Wijethilake won the Bronze medal in the coxed four events.', 'NO', '2025-12-09', 'ACTIVE'),
('P0003', 'ggghg', 'bnvvhjbjkkbjh', 'YES', '2025-12-18', 'ACTIVE'),
('P0004', 'Test News', 'This is a test news post for admin feature testing purposes.', 'YES', '2025-12-26', 'ACTIVE');

-- --------------------------------------------------------
-- Table structure for table `newsfeed_post_image`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `newsfeed_post_image`;
CREATE TABLE IF NOT EXISTS `newsfeed_post_image` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `post_id` varchar(12) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

INSERT INTO `newsfeed_post_image` (`image_id`, `post_id`, `image_path`) VALUES
(14, 'P0001', 'images/posts/img_6937eab81c8785.14752532.jpg'),
(15, 'P0002', 'images/posts/img_69380b0ce85be5.20787564.jpg'),
(16, 'P0003', 'images/posts/img_6943e8018adf40.57949946.jpg');

-- --------------------------------------------------------
-- Table structure for table `parallel_checker`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `parallel_checker`;
CREATE TABLE IF NOT EXISTS `parallel_checker` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` varchar(120) NOT NULL,
  `facility_id` int NOT NULL,
  `last_heartbeat` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `selected_date` date DEFAULT NULL,
  `selected_slot` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`,`facility_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

INSERT INTO `parallel_checker` (`id`, `session_id`, `facility_id`, `last_heartbeat`, `selected_date`, `selected_slot`) VALUES
(19, '2v06gb8fktdgpiulcj5b5397ac', 1, '2026-03-29 17:05:28', '2026-03-31', NULL);

-- --------------------------------------------------------
-- Table structure for table `payment`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `booking_id` varchar(12) NOT NULL,
  `amount` int NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(24) NOT NULL,
  `payment_status` varchar(12) NOT NULL DEFAULT 'DONE',
  PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `playing_teams`
-- (Only in Doc 1)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `playing_teams`;
CREATE TABLE IF NOT EXISTS `playing_teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) NOT NULL,
  `created_by` varchar(12) DEFAULT NULL COMMENT 'Captain user_id who first added',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`team_id`),
  UNIQUE KEY `team_name` (`team_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `practice_sessions`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `practice_sessions`;
CREATE TABLE IF NOT EXISTS `practice_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(8) NOT NULL,
  `added_by` varchar(8) NOT NULL,
  `facility` varchar(100) NOT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `notes` text,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `end_time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `need_equipment` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;

INSERT INTO `practice_sessions` (`id`, `sport_id`, `added_by`, `facility`, `session_date`, `start_time`, `notes`, `status`, `created_at`, `updated_at`, `end_time`, `location`, `need_equipment`) VALUES
(9, 'SCR', 'SPT', 'Select the Location', '2026-01-25', '14:30:00', '', 'ACTIVE', '2026-01-25 08:57:16', NULL, '00:00:00', '', ''),
(10, 'KRT', 'SPT', '', '2026-01-09', '17:27:00', '', 'ACTIVE', '2026-01-25 08:57:47', '2026-01-25 10:04:13', '00:00:00', 'Indoor Court', 'No'),
(11, 'CRI', 'SPT', '', '2026-01-25', '14:40:00', '', 'ACCEPTED', '2026-01-25 09:09:46', '2026-01-25 22:06:34', '16:40:00', 'Outdoor Field', 'Yes'),
(13, 'BAD', 'SPT', '', '2026-01-25', '16:50:00', '', 'CANCELED', '2026-01-25 10:21:02', '2026-01-28 23:54:41', '18:50:00', 'Indoor court', 'No'),
(14, 'BAD', 'SPT', '', '2026-02-08', '15:55:00', '', 'ACCEPTED', '2026-01-25 10:24:38', '2026-02-08 14:14:50', '17:54:00', 'Indoor court', 'No'),
(15, 'CRI', 'SPT', '', '2026-01-29', '12:25:00', '', 'PENDING', '2026-01-28 23:57:10', '2026-01-28 23:57:48', '15:25:00', 'Outdoor Field', 'No'),
(16, 'BAD', 'SPT', '', '2026-01-24', '22:45:00', '', 'ACCEPTED', '2026-02-08 14:15:32', '2026-02-08 14:44:37', '22:45:00', 'Indoor court', 'No'),
(17, 'KBD', 'SPT', '', '2026-02-08', '22:24:00', '', 'ACTIVE', '2026-02-08 16:54:31', NULL, '23:24:00', 'Indoor court', 'No'),
(19, 'BAD', 'SPT', '', '2026-02-14', '10:30:00', '', 'PENDING', '2026-02-13 01:54:34', NULL, '11:30:00', 'Outdoor Field', 'Yes');

-- --------------------------------------------------------
-- Table structure for table `remember_tokens`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 0, 'd125df99b6f85a0d3861dc2db2ca31c3a9e4da797d1503cd6dd738381a807173', 1762935327);

-- --------------------------------------------------------
-- Table structure for table `saved_emails`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `saved_emails`;
CREATE TABLE IF NOT EXISTS `saved_emails` (
  `email` varchar(64) NOT NULL,
  `recepient_name` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `saved_emails` (`email`, `recepient_name`) VALUES
('sports@usj.ac.lk', 'USJ');

-- --------------------------------------------------------
-- Table structure for table `sport`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sport`;
CREATE TABLE IF NOT EXISTS `sport` (
  `sport_id` varchar(4) NOT NULL,
  `sport_name` varchar(24) NOT NULL,
  `sport_category` enum('TEAM_GOAL','RACKET','CRICKET','COMBAT','TRACK_FIELD','BOARD_GAME','BALL_COURT','WEIGHT') NOT NULL,
  `coach_id` varchar(12) NOT NULL,
  `captain_id` varchar(12) NOT NULL,
  `manager_id` varchar(12) NOT NULL,
  PRIMARY KEY (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `sport` (`sport_id`, `sport_name`, `sport_category`, `coach_id`, `captain_id`, `manager_id`) VALUES
('BAD', 'Badminton', 'RACKET', '', '', ''),
('VOL', 'Volleyball', 'BALL_COURT', '', '5Q1XZO2Y', ''),
('FOO', 'Football', 'TEAM_GOAL', '', '', ''),
('TEN', 'Tennis', 'RACKET', '', '', ''),
('BAS', 'Basketball', 'BALL_COURT', '', '', 'SPT004'),
('HOC', 'Hockey', 'TEAM_GOAL', '', '', ''),
('NET', 'Netball', 'TEAM_GOAL', '', '', ''),
('CRI', 'Cricket', 'CRICKET', '', '', 'usr_68f89be0'),
('RUG', 'Rugby', 'TEAM_GOAL', '', '', ''),
('SWI', 'Swimming', 'TRACK_FIELD', '', '', ''),
('TT', 'Table Tennis', 'RACKET', '', '', ''),
('WL', 'Weight Lifting', 'WEIGHT', '', '', ''),
('ROW', 'Rowing', 'TRACK_FIELD', 'NPM8O9RE', '', ''),
('WRE', 'Wrestling', 'COMBAT', '', '', ''),
('CHE', 'Chess', 'BOARD_GAME', '', '', ''),
('ATH', 'Athletics', 'TRACK_FIELD', '', '', 'usr_68f89be0'),
('BOX', 'Boxing', 'COMBAT', '', '', ''),
('TKD', 'Taekwondo', 'COMBAT', '', '', ''),
('KRT', 'Karate', 'COMBAT', '', '', ''),
('RR', 'Road Race', 'TRACK_FIELD', '', '', ''),
('SCR', 'Scrabble', 'BOARD_GAME', '', '', ''),
('ELL', 'Elle', 'TEAM_GOAL', '', '', ''),
('BB', 'Baseball', 'BALL_COURT', '', '', ''),
('KBD', 'Kabaddi', 'COMBAT', '', '', ''),
('CRM', 'Carrom', 'BOARD_GAME', '', '', '');

--
-- Triggers `sport`
--
DROP TRIGGER IF EXISTS `trg_sport_captain_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_captain_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.captain_id != NEW.captain_id THEN
        IF OLD.captain_id != '' THEN
            UPDATE captain_sport
            SET date_relieved = CURDATE()
            WHERE sport_id = OLD.sport_id
              AND user_id = OLD.captain_id
              AND date_relieved IS NULL;
        END IF;
        IF NEW.captain_id != '' THEN
            INSERT INTO captain_sport (user_id, sport_id, date_started)
            VALUES (NEW.captain_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `trg_sport_coach_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_coach_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.coach_id != NEW.coach_id THEN
        IF OLD.coach_id != '' THEN
            UPDATE coach_sport
            SET date_relieved = CURDATE()
            WHERE sport_id = OLD.sport_id
              AND user_id = OLD.coach_id
              AND date_relieved IS NULL;
        END IF;
        IF NEW.coach_id != '' THEN
            INSERT INTO coach_sport (user_id, sport_id, date_started)
            VALUES (NEW.coach_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `trg_sport_manager_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_manager_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.manager_id != NEW.manager_id THEN
        IF OLD.manager_id != '' THEN
            UPDATE manager_sport
            SET date_relieved = CURDATE()
            WHERE sport_id = OLD.sport_id
              AND user_id = OLD.manager_id
              AND date_relieved IS NULL;
        END IF;
        IF NEW.manager_id != '' THEN
            INSERT INTO manager_sport (user_id, sport_id, date_started)
            VALUES (NEW.manager_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END $$
DELIMITER ;

-- --------------------------------------------------------
-- Table structure for table `sport_expenses`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sport_expenses`;
CREATE TABLE IF NOT EXISTS `sport_expenses` (
  `expense_id` int NOT NULL AUTO_INCREMENT,
  `sport` varchar(100) NOT NULL,
  `expense_title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt` varchar(255) DEFAULT NULL,
  `submitted_by` varchar(100) NOT NULL,
  `notes` text,
  `expense_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `sports-team`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sports-team`;
CREATE TABLE IF NOT EXISTS `sports-team` (
  `sport_id` varchar(12) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  `joined_date` date NOT NULL,
  `in_team` varchar(7) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`sport_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `sports-team` (`sport_id`, `student_id`, `joined_date`, `in_team`) VALUES
('TKD', 'L3NCL2J4', '2025-12-03', 'NO'),
('ATH', 'L3NCL2J4', '2025-12-09', 'NO'),
('VOL', '5Q1XZO2Y', '2025-10-25', 'NO'),
('ATH', '5Q1XZO2Y', '2025-12-11', 'NO'),
('ROW', 'L3NCL2J4', '2025-12-15', 'NO'),
('BAS', 'STU005', '2026-01-04', 'NO');

-- --------------------------------------------------------
-- Table structure for table `student_id_cards`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `student_id_cards`;
CREATE TABLE IF NOT EXISTS `student_id_cards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(12) NOT NULL COMMENT 'References user.student_id',
  `image_name` varchar(128) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `suppliers`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `supplier_id` int NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(128) NOT NULL,
  `address` varchar(256) NOT NULL,
  `telephone_1` varchar(20) NOT NULL,
  `telephone_2` varchar(20) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `address`, `telephone_1`, `telephone_2`, `email`, `created_at`) VALUES
(1, 'Lanka Sports Pvt Ltd', 'No. 45, Galle Road, Colombo 03', '0112345678', '0112345679', 'info@lankasports.lk', '2026-02-22 04:31:04'),
(2, 'Prozone Sports Equipment', 'No. 12, Kandy Road, Peradeniya', '0812234567', '', 'sales@prozone.lk', '2026-02-22 04:31:04'),
(3, 'Island Sports Traders', 'No. 78, High Level Road, Nugegoda', '0112889900', '0112889901', 'orders@islandsports.lk', '2026-02-22 04:31:04'),
(4, 'Champion Sporting Goods', 'No. 5, Stadium Road, Colombo 07', '0114567890', '', 'contact@champion.lk', '2026-02-22 04:31:04'),
(5, 'Sri Lanka Sports Authority Supplies', 'No. 100, Independence Square, Colombo 07', '0112678900', '0112678901', 'supplies@slsa.gov.lk', '2026-02-22 04:31:04'),
(6, 'Yonex Sri Lanka', 'No. 22, Duplication Road, Colombo 04', '0115678901', '', 'info@yonex.lk', '2026-02-22 04:31:04'),
(7, 'Nivia Sports Lanka', 'No. 88, Baseline Road, Colombo 09', '0116789012', '0116789013', 'orders@nivia.lk', '2026-02-22 04:31:04'),
(8, 'University Sports Store', 'University of Colombo, College House, Colombo 03', '0112158000', '', 'sports.store@uoc.lk', '2026-02-22 04:31:04');

-- --------------------------------------------------------
-- Table structure for table `tournament`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `tournament_id` varchar(24) NOT NULL,
  `tournament_name` varchar(64) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'INCOMPLETE',
  PRIMARY KEY (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `tournament` (`tournament_id`, `tournament_name`, `sport_id`, `start_date`, `end_date`, `status`) VALUES
('TOUR_693ea72aa6387', 'Vice Chancellors Invitational Badminton Championship', 'BAD', '2026-01-01', '2026-02-26', 'INCOMPLETE'),
('TOUR_694cd4c59abad', 'This is an sport event', 'KRT', '2026-02-01', '2026-12-01', 'INCOMPLETE'),
('TOUR_69ccde3bc3ae2', 'Inter University Volleyball Championship', 'VOL', '2026-03-30', '2026-04-04', 'INCOMPLETE');

-- --------------------------------------------------------
-- Table structure for table `tournament_match`
-- (Using Doc 1 schema which has more columns; data merged from both)
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tournament_match`;
CREATE TABLE IF NOT EXISTS `tournament_match` (
  `match_id` varchar(50) NOT NULL,
  `tournament_id` varchar(24) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `sport_category` enum('TEAM_GOAL','RACKET','CRICKET','COMBAT','TRACK_FIELD','BOARD_GAME','BALL_COURT','WEIGHT') NOT NULL,
  `match_name` varchar(100) NOT NULL,
  `match_date` date NOT NULL,
  `winner_id` varchar(12) DEFAULT NULL COMMENT 'References user.user_id for internal players',
  `winner_name` varchar(120) DEFAULT NULL COMMENT 'Display name: full name for players, team name for team sports',
  `winner_type` enum('INTERNAL','INVITATIONAL','TEAM','DRAW') DEFAULT NULL,
  `winner_invitational_id` int DEFAULT NULL COMMENT 'References invitational_players.inv_player_id',
  `result_status` enum('COMPLETED','CANCELLED','DRAW','PENDING','NO_RESULT') DEFAULT 'PENDING',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `submitted_by` varchar(24) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`match_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `sport_id` (`sport_id`),
  KEY `sport_category` (`sport_category`),
  KEY `winner_id` (`winner_id`),
  KEY `winner_invitational_id` (`winner_invitational_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tournament_match` (`match_id`, `tournament_id`, `sport_id`, `sport_category`, `match_name`, `match_date`, `winner_id`, `winner_name`, `winner_type`, `winner_invitational_id`, `result_status`, `is_published`, `submitted_by`, `created_at`) VALUES
('match_695d2d74e031d4.90256849', 'TOUR_694cd4c59abad', 'KRT', 'COMBAT', 'Quarter Final', '2026-01-01', 'L3NCL2J4', NULL, NULL, NULL, 'COMPLETED', 1, 'ADMIN', '2026-01-06 15:42:44'),
('match_69cce68043d035.97638693', 'TOUR_69ccde3bc3ae2', 'VOL', 'BALL_COURT', 'Quarter Final Match', '2026-03-30', NULL, NULL, NULL, NULL, 'COMPLETED', 0, NULL, '2026-04-01 09:33:52');

-- --------------------------------------------------------
-- Table structure for table `tournament_result`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tournament_result`;
CREATE TABLE IF NOT EXISTS `tournament_result` (
  `result_id` varchar(12) NOT NULL,
  `match_id` varchar(12) NOT NULL,
  `player_id` varchar(12) DEFAULT NULL,
  `team_id` varchar(12) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`result_id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `transaction`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE IF NOT EXISTS `transaction` (
  `transaction_id` varchar(12) NOT NULL,
  `budget_id` varchar(12) NOT NULL,
  `amount` int NOT NULL,
  `purpose` varchar(256) NOT NULL,
  `timestamp` timestamp NOT NULL,
  `proof_doc` varchar(32) NOT NULL,
  `remarks` varchar(256) NOT NULL,
  `change_reason` varchar(256) NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaction` (`transaction_id`, `budget_id`, `amount`, `purpose`, `timestamp`, `proof_doc`, `remarks`, `change_reason`) VALUES
('1', '1', 50000, 'Purchase of cricket bats', '2025-01-20 05:00:00', 'proof_cricket_ba', '', ''),
('2', '1', 30000, 'Ground maintenance', '2025-02-05 08:45:00', 'proof_ground_mai', '', ''),
('3', '2', 40000, 'Football gear purchase', '2025-02-15 04:15:00', 'proof_football_g', '', ''),
('4', '3', 20000, 'Basketball court repair', '2025-03-20 10:40:00', 'proof_basketball', '', ''),
('5', '4', 10000, 'Volleyball net purchase', '2025-04-12 06:20:00', 'proof_volleyball', '', ''),
('T0001', 'ABC012', 30000, '', '2025-10-22 17:35:33', 'tx_68f92f4ea50506.21004129.png', '', 'No No'),
('T0002', 'ABC012', 12000, 'Bats', '2025-10-23 08:16:19', 'tx_68f9e453457406.31160978.png', '', '');

-- --------------------------------------------------------
-- Table structure for table `user`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` varchar(12) NOT NULL,
  `fname` varchar(24) NOT NULL,
  `lname` varchar(24) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'PUBLIC',
  `email` varchar(64) NOT NULL,
  `password` varchar(256) NOT NULL,
  `must_change_pass` int NOT NULL,
  `joined_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contact_no` varchar(12) DEFAULT NULL,
  `profile_img` varchar(64) NOT NULL,
  `sport_id` varchar(5) NOT NULL,
  `student_id` varchar(12) DEFAULT NULL,
  `faculty_id` varchar(4) DEFAULT NULL,
  `status` varchar(6) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `Email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`user_id`, `fname`, `lname`, `type`, `email`, `password`, `must_change_pass`, `joined_date`, `contact_no`, `profile_img`, `sport_id`, `student_id`, `faculty_id`, `status`) VALUES
('1', 'Chamal', 'Chamuditha', 'PUBLIC', 'chamal@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('VSSMS4ZL', 'Ravindu', 'Rasa', 'PUBLIC', 'ravi@kgla.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('FK9C62HG', 'Pasindu', 'Anjana', 'PUBLIC', 'pasindu@anura.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('101', 'John', 'Smith', 'PUBLIC', 'john.smith@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0771234567', '', '', NULL, '', 'ACTIVE'),
('102', 'David', 'Perera', 'PUBLIC', 'david.perera@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0779876543', '', '', NULL, '', 'ACTIVE'),
('103', 'Alex', 'Fernando', 'PUBLIC', 'alex.fernando@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0713456789', '', '', NULL, '', 'ACTIVE'),
('104', 'Mark', 'Silva', 'PUBLIC', 'mark.silva@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0752345678', '', '', NULL, '', 'ACTIVE'),
('105', 'Kamal', 'Jayasinghe', 'PUBLIC', 'kamal.jayasinghe@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0761112233', '', '', NULL, '', 'ACTIVE'),
('201', 'Sameera', 'Dissanayake', 'PUBLIC', 'sameera.dissanayake@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0775566778', '', '', NULL, '', 'ACTIVE'),
('202', 'Nuwan', 'Karunaratne', 'EXECUTIVE', 'nuwan.karunaratne@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0711122334', '', '', NULL, '', 'ACTIVE'),
('203', 'Ruwan', 'Senanayake', 'PUBLIC', 'ruwan.senanayake@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0729988776', '', '', NULL, '', 'ACTIVE'),
('204', 'Suresh', 'Kumara', 'PUBLIC', 'suresh.kumara@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0765544332', '', '', NULL, '', 'ACTIVE'),
('205', 'Ashan', 'Wijesinghe', 'PUBLIC', 'ashan.wijesinghe@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0776677889', '', '', NULL, '', 'ACTIVE'),
('301', 'Pradeep', 'Gunawardena', 'PUBLIC', 'pradeep.gunawardena@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0718899001', '', '', NULL, '', 'ACTIVE'),
('302', 'Chathura', 'Ekanayake', 'PUBLIC', 'chathura.ekanayake@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0752233445', '', '', NULL, '', 'ACTIVE'),
('303', 'Isuru', 'Lakshan', 'PUBLIC', 'isuru.lakshan@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0723344556', '', '', NULL, '', 'ACTIVE'),
('304', 'Gayan', 'Rathnayake', 'PUBLIC', 'gayan.rathnayake@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0779988775', '', '', NULL, '', 'ACTIVE'),
('305', 'Roshan', 'Abeysinghe', 'PUBLIC', 'roshan.abeysinghe@example.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '0764455667', '', '', NULL, '', 'ACTIVE'),
('NPM8O9RE', 'Chamal', 'Chamuditha', 'COACH', 'chamal1@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 22:53:08', NULL, '', '', NULL, '', 'ACTIVE'),
('UBVXZ90U', 'ddkjn', 'fsrvn', 'PUBLIC', 'maximal@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:24:32', NULL, '', '', NULL, '', 'ACTIVE'),
('KI5RL42D', 'ddkjn', 'fsrvn', 'PUBLIC', 'hj@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:29:53', NULL, '', '', NULL, '', 'ACTIVE'),
('PA0XK3QZ', 'ddkjn', 'fsrvn', 'PUBLIC', 'hjggd@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:32:55', NULL, '', '', NULL, '', 'ACTIVE'),
('JIIJ51LA', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsjvn@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:39:19', NULL, '', '', NULL, '', 'ACTIVE'),
('VTLMC3YK', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsvn@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:47:59', NULL, '', '', NULL, '', 'ACTIVE'),
('R13QQJC2', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsgvn@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:50:07', NULL, '', '', NULL, '', 'ACTIVE'),
('JORD04QN', 'vvds', 'qeq', 'PUBLIC', 'esfef@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:52:30', NULL, '', '', NULL, '', 'ACTIVE'),
('KCLIH538', 'vvds', 'qeq', 'PUBLIC', 'esff@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:53:34', NULL, '', '', NULL, '', 'ACTIVE'),
('43N1VK76', 'vvdsdwef', 'qeq', 'PUBLIC', 'esrdrfff@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:57:10', NULL, '', '', NULL, '', 'ACTIVE'),
('CE02XIPB', 'Admin', 'UOC', 'PUBLIC', 'admin@uocs.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 00:01:26', NULL, '', '', NULL, '', 'ACTIVE'),
('H4J1OHSX', 'Chamal', 'Chamuditha', 'ADMIN', 'chamal.admin@uocs.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 02:04:39', NULL, 'H4J1OHSX.png', '', NULL, '', 'ACTIVE'),
('usr_694d89fa', 'Amal', 'Shantha', 'SPT', 'chamlaanil99@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-12-25 19:01:15', '0716379044', '', 'KBD', NULL, NULL, 'ACTIVE'),
('L3NCL2J4', 'Chamal', 'Hettiarachchi', 'STUDENT', 'chamal2@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-14 04:48:58', NULL, 'L3NCL2J4.jpg', '', '23000000', '', 'ACTIVE'),
('usr_68f82fe0', 'Shashini', 'Malsha', 'EQP', 'ccwrecker99@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 01:14:08', '076543213', '', '', NULL, '', 'ACTIVE'),
('usr_68f89998', 'Jaye', 'Jayaweera', 'EQP', 'jayashinisjayaweera@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 08:45:12', '0763452143', '', '', NULL, '', 'ACTIVE'),
('usr_68f89be0', 'J', 'Jaye', 'SPT', '2023is043@stu.ucsc.cmb.ac.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 08:54:56', '0763452145', '', 'CRI', NULL, '', 'ACTIVE'),
('FMX6Z8DF', 'Shashini', 'Malsha', 'STUDENT', 'shashini@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-23 07:10:43', NULL, '', '', '23020997', '', 'ACTIVE'),
('5Q1XZO2Y', 'Jansika', 'Balakrishnan', 'CAPTAIN', 'starshi2003@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-23 07:23:06', NULL, '5Q1XZO2Y.jpg', '', '23020342', '', 'ACTIVE'),
('STU001', 'Ashan', 'Fernando', 'STUDENT', 'ashan.fernando@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0771234567', '', '', '23001001', '1', 'ACTIVE'),
('STU002', 'Nimali', 'Perera', 'STUDENT', 'nimali.perera@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0772345678', '', '', '23001002', '1', 'ACTIVE'),
('REG003', 'Kasun', 'Silva', 'REG', 'kasun.silva@ucsc.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0773456789', '', '', '23001003', '1', 'ACTIVE'),
('SPT004', 'Dilini', 'Jayasinghe', 'SPT', 'dilini.jayasinghe@uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0774567890', '', '', '23001004', '1', 'ACTIVE'),
('STU005', 'Tharindu', 'Wickramasinghe', 'STUDENT', 'tharindu.wickramasinghe@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0775678901', '', '', '23001005', '1', 'ACTIVE'),
('STU006', 'Sanduni', 'Rathnayake', 'STUDENT', 'sanduni.rathnayake@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0776789012', '', '', '23001006', '1', 'ACTIVE'),
('STU007', 'Ravindu', 'Dissanayake', 'STUDENT', 'ravindu.dissanayake@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0777890123', '', '', '23001007', '1', 'ACTIVE'),
('STU008', 'Ishara', 'Gunasekara', 'STUDENT', 'ishara.gunasekara@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0778901234', '', '', '23001008', '1', 'ACTIVE'),
('STU009', 'Dineth', 'Amarasinghe', 'STUDENT', 'dineth.amarasinghe@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0779012345', '', '', '23001009', '1', 'ACTIVE'),
('STU010', 'Sachini', 'Wijewardena', 'STUDENT', 'sachini.wijewardena@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0770123456', '', '', '23001010', '1', 'ACTIVE');

-- --------------------------------------------------------
-- Table structure for table `user_points`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `user_points`;
CREATE TABLE IF NOT EXISTS `user_points` (
  `user_id` varchar(12) NOT NULL,
  `user_points` int DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `user_points` (`user_id`, `user_points`) VALUES
('STU010', 17),
('STU009', 0);

-- --------------------------------------------------------
-- Table structure for table `achievement`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `achievement`;
CREATE TABLE IF NOT EXISTS `achievement` (
  `achievement_id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(12) DEFAULT NULL,
  `sport_id` varchar(10) DEFAULT NULL,
  `competition_id` varchar(20) DEFAULT NULL,
  `achievement` varchar(50) DEFAULT NULL,
  `points` int DEFAULT '0',
  PRIMARY KEY (`achievement_id`),
  KEY `fk_achievement_user` (`user_id`),
  KEY `fk_achievement_competition` (`competition_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

INSERT INTO `achievement` (`achievement_id`, `user_id`, `sport_id`, `competition_id`, `achievement`, `points`) VALUES
(6, 'STU010', 'CRI', '101', '2nd place', 3),
(7, 'STU010', 'CRI', '103', 'Best performance', 7),
(8, 'STU009', 'CRI', '103', 'Participation', 0),
(9, 'STU010', 'CRI', '105', 'Best performance', 7);

--
-- Triggers `achievement`
--
DROP TRIGGER IF EXISTS `trg_assign_points`;
DELIMITER $$
CREATE TRIGGER `trg_assign_points` BEFORE INSERT ON `achievement` FOR EACH ROW BEGIN
    IF NEW.achievement = '1st place' THEN
        SET NEW.points = 5;
    ELSEIF NEW.achievement = '2nd place' THEN
        SET NEW.points = 3;
    ELSEIF NEW.achievement = '3rd place' THEN
        SET NEW.points = 2;
    ELSEIF NEW.achievement = '4th place' THEN
        SET NEW.points = 1;
    ELSEIF NEW.achievement = 'Best performance' THEN
        SET NEW.points = 7;
    ELSE
        SET NEW.points = 0;
    END IF;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS `trg_update_student_points`;
DELIMITER $$
CREATE TRIGGER `trg_update_student_points` AFTER INSERT ON `achievement` FOR EACH ROW BEGIN
    DECLARE total_points INT;
    SELECT SUM(points)
    INTO total_points
    FROM achievement
    WHERE user_id = NEW.user_id;
    INSERT INTO user_points (user_id, user_points)
    VALUES (NEW.user_id, total_points)
    ON DUPLICATE KEY UPDATE
        user_points = total_points;
END $$
DELIMITER ;

-- --------------------------------------------------------
-- Table structure for table `verification_requests`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `verification_requests`;
CREATE TABLE IF NOT EXISTS `verification_requests` (
  `request_id` varchar(16) NOT NULL,
  `requested_by` varchar(12) NOT NULL COMMENT 'Sport manager user_id',
  `sport_id` varchar(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('PENDING','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  `notes` text,
  PRIMARY KEY (`request_id`),
  KEY `requested_by` (`requested_by`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `verification_request_students`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `verification_request_students`;
CREATE TABLE IF NOT EXISTS `verification_request_students` (
  `request_id` varchar(16) NOT NULL,
  `student_id` varchar(12) NOT NULL COMMENT 'References user.student_id',
  `faculty_id` varchar(4) NOT NULL,
  `verified_by` varchar(12) DEFAULT NULL COMMENT 'Registrar user_id who verified',
  `verification_status` enum('PENDING','VERIFIED','REJECTED') DEFAULT 'PENDING',
  `verified_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`request_id`,`student_id`),
  KEY `faculty_id` (`faculty_id`),
  KEY `verification_status` (`verification_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

COMMIT;