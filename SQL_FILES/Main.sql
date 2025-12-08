-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 08, 2025 at 06:52 PM
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

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` varchar(12) NOT NULL,
  `practice_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `status` varchar(12) NOT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`, `description`) VALUES
('ANUTKD01', 'TKDA', 2025, 400000, 178000, '2024-12-15', NULL),
('ANUTKD02', 'TKDA', 2025, 100000, 9000, '2024-12-18', NULL),
('1', '1', 2025, 500000, 250000, '2025-01-15', NULL),
('2', '2', 2025, 400000, 150000, '2025-02-10', NULL),
('3', '3', 2025, 300000, 100000, '2025-03-12', NULL),
('4', '4', 2025, 200000, 50000, '2025-04-05', NULL),
('5', '5', 2025, 150000, 30000, '2025-05-01', NULL),
('ABC012', 'CRI', 2025, 200000, 42000, '2025-08-24', 'This is for testing');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `comment_id` varchar(8) NOT NULL,
  `post_id` varchar(12) NOT NULL,
  `comment_from` varchar(12) NOT NULL,
  `reply_to` varchar(12) NOT NULL,
  `content` varchar(300) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `post_id`, `comment_from`, `reply_to`, `content`) VALUES
('cmt_68ec', '', 'H4J1OHSX', 'P0003', 'hi'),
('cmt_68ec', '', 'H4J1OHSX', 'P0003', 'Hi'),
('cmt_68ec', '', 'H4J1OHSX', 'P0003', 'Hi'),
('cmt_68ec', '', 'H4J1OHSX', 'P0003', 'Hi'),
('cmt_68ec', 'P0003', 'H4J1OHSX', 'P0003', 'Hi'),
('cmt_68ec', 'P0003', 'H4J1OHSX', '', 'Hi'),
('cmt_68ec', 'P0003', 'H4J1OHSX', '', 'Hi'),
('cmt_68ec', 'P0003', 'H4J1OHSX', '', 'Hi'),
('cmt_68ec', 'P0003', 'H4J1OHSX', '', 'Hello'),
('cmt_68ed', 'P0005', 'H4J1OHSX', '', 'Hi'),
('cmt_68ef', 'P0001', 'L3NCL2J4', '', 'Hello'),
('cmt_68f6', 'P0008', 'H4J1OHSX', '', 'Hi'),
('cmt_68f8', 'P0001', 'usr_68f82fe0', '', 'Hello'),
('cmt_68f9', 'P0008', 'FMX6Z8DF', '', 'Hello');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `equipment_name` varchar(32) NOT NULL,
  `max_allow` int NOT NULL,
  `image_name` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`equipment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

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
('EQ69354316b1', 'TKD', 'Taekwondo Tatami', 0, 'taekwondo_tatami_2938.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `equipment-requests`
--

DROP TABLE IF EXISTS `equipment-requests`;
CREATE TABLE IF NOT EXISTS `equipment-requests` (
  `request_id` varchar(12) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `request_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(64) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `notes` varchar(64) NOT NULL,
  PRIMARY KEY (`request_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment-requests`
--

INSERT INTO `equipment-requests` (`request_id`, `student_id`, `equipment_id`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`) VALUES
('req_6936c2e1', '23000000', 'EQ69354316b1', '2025-12-17', '08:00:00', '10:00:00', '-', 'ACTIVE', '-');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_inventory`
--

DROP TABLE IF EXISTS `equipment_inventory`;
CREATE TABLE IF NOT EXISTS `equipment_inventory` (
  `stock_id` varchar(8) NOT NULL,
  `equipment_id` varchar(8) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `quantity` int NOT NULL,
  `added_date` date NOT NULL,
  `status` varchar(12) NOT NULL,
  `remarks` varchar(256) NOT NULL,
  PRIMARY KEY (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_inventory`
--

INSERT INTO `equipment_inventory` (`stock_id`, `equipment_id`, `sport_id`, `quantity`, `added_date`, `status`, `remarks`) VALUES
('STK69354', 'EQ020', 'NET', 4, '2025-12-07', 'USABLE', '-'),
('STK00001', 'EQ001', 'BAD', 20, '2025-12-08', 'USABLE', '-'),
('STK00002', 'EQ002', 'BAD', 200, '2025-12-08', 'USABLE', '-'),
('STK00003', 'EQ003', 'BAD', 5, '2025-12-08', 'USABLE', '-'),
('STK00004', 'EQ004', 'VOL', 15, '2025-12-08', 'USABLE', '-'),
('STK00005', 'EQ005', 'VOL', 6, '2025-12-08', 'USABLE', '-'),
('STK00006', 'EQ006', 'VOL', 30, '2025-12-08', 'USABLE', '-'),
('STK00007', 'EQ007', 'FOO', 18, '2025-12-08', 'USABLE', '-'),
('STK00008', 'EQ008', 'FOO', 4, '2025-12-08', 'USABLE', '-'),
('STK00009', 'EQ009', 'FOO', 25, '2025-12-08', 'USABLE', '-'),
('STK00010', 'EQ010', 'FOO', 10, '2025-12-08', 'USABLE', '-'),
('STK00011', 'EQ011', 'TEN', 12, '2025-12-08', 'USABLE', '-'),
('STK00012', 'EQ012', 'TEN', 150, '2025-12-08', 'USABLE', '-'),
('STK00013', 'EQ013', 'TEN', 4, '2025-12-08', 'USABLE', '-'),
('STK00014', 'EQ014', 'BAS', 10, '2025-12-08', 'USABLE', '-'),
('STK00015', 'EQ015', 'BAS', 6, '2025-12-08', 'USABLE', '-'),
('STK00016', 'EQ016', 'BAS', 2, '2025-12-08', 'USABLE', '-'),
('STK00017', 'EQ017', 'HOC', 20, '2025-12-08', 'USABLE', '-'),
('STK00018', 'EQ018', 'HOC', 30, '2025-12-08', 'USABLE', '-'),
('STK00019', 'EQ019', 'HOC', 6, '2025-12-08', 'USABLE', '-'),
('STK00020', 'EQ020', 'NET', 10, '2025-12-08', 'USABLE', '-'),
('STK00021', 'EQ021', 'NET', 4, '2025-12-08', 'USABLE', '-'),
('STK00022', 'EQ022', 'CRI', 12, '2025-12-08', 'USABLE', '-'),
('STK00023', 'EQ023', 'CRI', 60, '2025-12-08', 'USABLE', '-'),
('STK00024', 'EQ024', 'CRI', 10, '2025-12-08', 'USABLE', '-'),
('STK00025', 'EQ025', 'CRI', 8, '2025-12-08', 'USABLE', '-'),
('STK00026', 'EQ026', 'RUG', 10, '2025-12-08', 'USABLE', '-'),
('STK00027', 'EQ027', 'RUG', 12, '2025-12-08', 'USABLE', '-'),
('STK00028', 'EQ028', 'SWI', 25, '2025-12-08', 'USABLE', '-'),
('STK00029', 'EQ029', 'SWI', 30, '2025-12-08', 'USABLE', '-'),
('STK00030', 'EQ030', 'SWI', 10, '2025-12-08', 'USABLE', '-'),
('STK00031', 'EQ031', 'TT', 20, '2025-12-08', 'USABLE', '-'),
('STK00032', 'EQ032', 'TT', 150, '2025-12-08', 'USABLE', '-'),
('STK00033', 'EQ033', 'TT', 4, '2025-12-08', 'USABLE', '-'),
('STK00034', 'EQ034', 'WL', 10, '2025-12-08', 'USABLE', '-'),
('STK00035', 'EQ035', 'WL', 20, '2025-12-08', 'USABLE', '-'),
('STK00036', 'EQ036', 'WL', 80, '2025-12-08', 'USABLE', '-'),
('STK00037', 'EQ037', 'ROW', 6, '2025-12-08', 'USABLE', '-'),
('STK00038', 'EQ038', 'ROW', 20, '2025-12-08', 'USABLE', '-'),
('STK00039', 'EQ039', 'WRE', 4, '2025-12-08', 'USABLE', '-'),
('STK00040', 'EQ040', 'CHE', 15, '2025-12-08', 'USABLE', '-'),
('STK00041', 'EQ041', 'CHE', 10, '2025-12-08', 'USABLE', '-'),
('STK00042', 'EQ042', 'ATH', 8, '2025-12-08', 'USABLE', '-'),
('STK00043', 'EQ043', 'ATH', 12, '2025-12-08', 'USABLE', '-'),
('STK00044', 'EQ044', 'ATH', 10, '2025-12-08', 'USABLE', '-'),
('STK00045', 'EQ045', 'ATH', 14, '2025-12-08', 'USABLE', '-'),
('STK00046', 'EQ046', 'BOX', 16, '2025-12-08', 'USABLE', '-'),
('STK00047', 'EQ047', 'BOX', 4, '2025-12-08', 'USABLE', '-'),
('STK00048', 'EQ048', 'TKD', 20, '2025-12-08', 'USABLE', '-'),
('STK00049', 'EQ049', 'TKD', 20, '2025-12-08', 'USABLE', '-'),
('STK00050', 'EQ050', 'KRT', 20, '2025-12-08', 'USABLE', '-'),
('STK00051', 'EQ051', 'KRT', 20, '2025-12-08', 'USABLE', '-'),
('STK00052', 'EQ052', 'RR', 30, '2025-12-08', 'USABLE', '-'),
('STK00053', 'EQ053', 'RR', 200, '2025-12-08', 'USABLE', '-'),
('STK00054', 'EQ054', 'SCR', 10, '2025-12-08', 'USABLE', '-'),
('STK00055', 'EQ055', 'ELL', 8, '2025-12-08', 'USABLE', '-'),
('STK00056', 'EQ056', 'BB', 12, '2025-12-08', 'USABLE', '-'),
('STK00057', 'EQ057', 'BB', 40, '2025-12-08', 'USABLE', '-'),
('STK00058', 'EQ058', 'BB', 20, '2025-12-08', 'USABLE', '-'),
('STK00059', 'EQ059', 'KBD', 4, '2025-12-08', 'USABLE', '-'),
('STK00060', 'EQ060', 'CRM', 10, '2025-12-08', 'USABLE', '-'),
('STK00061', 'EQ061', 'CRM', 40, '2025-12-08', 'USABLE', '-'),
('STK00062', 'EQ062', 'CRM', 15, '2025-12-08', 'USABLE', '-'),
('STK00063', 'EQ693543', 'TKD', 4, '2025-12-08', 'USABLE', '-');

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
CREATE TABLE IF NOT EXISTS `facility` (
  `facility_id` varchar(8) NOT NULL,
  `facility_name` int NOT NULL,
  `slots` int NOT NULL,
  PRIMARY KEY (`facility_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility-booking`
--

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
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility-booking`
--

INSERT INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `slot`, `purpose`, `status`, `payment_status`) VALUES
('BK711559', 'H4J1OHSX', '9', '2025-12-11', 'FULL', 'To practice for Inter Provincial Matches held in January 2026', 'BOOKED', 'INCOMPLETE'),
('BK398317', 'H4J1OHSX', '3', '2025-12-10', 'AFTERNOON', 'Badminton Provincial Matches Practice', 'BOOKED', 'INCOMPLETE'),
('BK861578', 'L3NCL2J4', '11', '2025-12-10', 'MORNING', 'For Inter University Practices for SLIIT University', 'BOOKED', 'INCOMPLETE'),
('BK937846', 'L3NCL2J4', '15', '2025-12-18', 'FULL', 'For TOC Championship Match Practice', 'BOOKED', 'INCOMPLETE');

-- --------------------------------------------------------

--
-- Table structure for table `facility_rates`
--

DROP TABLE IF EXISTS `facility_rates`;
CREATE TABLE IF NOT EXISTS `facility_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_type` enum('INDOOR_GYM','GROUND') COLLATE utf8mb4_general_ci NOT NULL,
  `facility_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
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
  KEY `idx_capacity` (`capacity`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_rates`
--

INSERT INTO `facility_rates` (`id`, `facility_type`, `facility_name`, `capacity`, `practice_working_hours`, `practice_other_hours`, `tournament_full_day_working`, `tournament_half_day_working`, `tournament_full_day_other`, `tournament_half_day_other`, `created_at`, `updated_at`) VALUES
(1, 'INDOOR_GYM', 'Badminton one Court (08 Persons for practices)', 8, '800.00', '1100.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(2, 'INDOOR_GYM', 'Badminton two Courts (16 Persons for practices)', 16, '1600.00', '1900.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(3, 'INDOOR_GYM', 'Badminton Four Courts (30 Persons for practices)', 30, '3000.00', '3600.00', '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(4, 'INDOOR_GYM', 'Table Tennis Two tables (08 Persons for practices)', 8, '900.00', '1200.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(5, 'INDOOR_GYM', 'Table Tennis', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(6, 'INDOOR_GYM', 'Karate / Taekwondo with without Tatami', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(7, 'INDOOR_GYM', 'Wrestling without mattress', NULL, NULL, NULL, '50000.00', '35000.00', '59000.00', '41000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(8, 'INDOOR_GYM', 'Volleyball (25 Persons for practices)', 25, '5000.00', '5600.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(9, 'INDOOR_GYM', 'Volleyball', NULL, NULL, NULL, '60000.00', '40000.00', '69000.00', '46000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(10, 'INDOOR_GYM', 'Student Sport Center and surrounding area (sports activities & functions)', NULL, NULL, NULL, '30000.00', '20000.00', '39000.00', '26000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(11, 'GROUND', 'Baseball (30 Persons for practices)', 30, '30000.00', '17500.00', NULL, NULL, '65000.00', '35000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(12, 'GROUND', 'Basketball (25 Persons for practices) (without light)', 25, '20000.00', '12000.00', '6000.00', '40000.00', '25000.00', '10000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(13, 'GROUND', 'Basketball (25 Persons for practices) (with light)', 25, NULL, '17500.00', '8000.00', NULL, '25000.00', '12500.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(14, 'GROUND', 'Cricket - Hard Ball with matting (only one team allowed for practices)', NULL, '30000.00', '17500.00', '10000.00', '35000.00', '20000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(15, 'GROUND', 'Cricket - Hard Ball fielding practices (only one team allowed)', NULL, NULL, NULL, '6000.00', NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(16, 'GROUND', 'Soft Ball Cricket & Other functions (maximum three pitches)', NULL, NULL, NULL, '4000.00', '115000.00', '65000.00', '10000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(17, 'GROUND', 'Cricket - Side Wicket (one wicket) (18 Persons)', 18, NULL, NULL, '4000.00', NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(18, 'GROUND', 'Cricket Turf', NULL, NULL, NULL, '7000.00', '45000.00', '25000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(19, 'GROUND', 'Elle', NULL, NULL, NULL, NULL, '45000.00', '25000.00', NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(20, 'GROUND', 'Football One Court without court marking (40 Persons for practices)', 40, '30000.00', '20000.00', '16000.00', NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(21, 'GROUND', 'Football with court marking', NULL, '30000.00', '27500.00', '17500.00', '70000.00', '40000.00', '27500.00', '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(22, 'GROUND', 'Hockey (30 Persons for practices)', 30, '30000.00', '20000.00', '10000.00', NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(23, 'GROUND', 'Hockey with court marking', NULL, '30000.00', '27500.00', '17500.00', '70000.00', '40000.00', '27500.00', '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(24, 'GROUND', 'Netball (25 Persons for practices) One Court', 25, '30000.00', '20000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(25, 'GROUND', 'Netball (50 Persons for practices) Six Courts', 50, '45000.00', '30000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(26, 'GROUND', 'Rugby (40 Persons for practices)', 40, '40000.00', '25000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(27, 'GROUND', 'Rugby with court Marking', NULL, '47500.00', '32500.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(28, 'GROUND', 'Tennis One Court (04 Persons)', 4, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(29, 'GROUND', 'Tennis Two Courts (04 Persons each)', 8, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(30, 'GROUND', 'Track & Field (without ground marking and without High Jump/ Mattress)', NULL, '45000.00', '25000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(31, 'GROUND', 'Track & Field (with ground marking and with High Jump/ Mattress)', NULL, '65000.00', '45000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(32, 'GROUND', 'Volleyball (Outdoor) (1 court) (25 Persons for practices)', 25, '30000.00', '20000.00', NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` varchar(4) NOT NULL,
  `faculty_name` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`) VALUES
('1', 'UCSC'),
('2', 'Science');

-- --------------------------------------------------------

--
-- Table structure for table `injury_report`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found_images`
--

DROP TABLE IF EXISTS `lost_found_images`;
CREATE TABLE IF NOT EXISTS `lost_found_images` (
  `case_id` varchar(12) NOT NULL,
  `image_name` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsfeed_post`
--

DROP TABLE IF EXISTS `newsfeed_post`;
CREATE TABLE IF NOT EXISTS `newsfeed_post` (
  `post_id` varchar(12) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `commenting` varchar(8) NOT NULL,
  `date_posted` date NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsfeed_post`
--

INSERT INTO `newsfeed_post` (`post_id`, `title`, `description`, `commenting`, `date_posted`, `status`) VALUES
('P0001', 'UOC', 'The University of Colombo (informally Colombo University or UoC) is a public research university located primarily in Colombo, Sri Lanka. It is the oldest institution of modern higher education in Sri Lanka. It specializes in the fields of Natural, Social, Applied, Formal, and Computer Sciences as well as Fine Arts, Law and Technology. It is ranked among the top 10 universities in South Asia.[2]\r\nThe University of Colombo was founded in 1921 as University College Colombo, affiliated with the University of London. Degrees were issued to its students from 1923 onwards. The university traces its roots to 1870 when the Ceylon Medical School was established.[3] UoC has produced notable alumni in the fields of science, law, economics, business, literature, and politics.', 'YES', '2025-10-11', 'ACTIVE'),
('P0002', 'UOC', 'The University of Colombo (informally Colombo University or UoC) is a public research university located primarily in Colombo, Sri Lanka. It is the oldest institution of modern higher education in Sri Lanka. It specializes in the fields of Natural, Social, Applied, Formal, and Computer Sciences as well as Fine Arts, Law and Technology. It is ranked among the top 10 universities in South Asia.[2]\r\n\r\nThe University of Colombo was founded in 1921 as University College Colombo, affiliated with the University of London. Degrees were issued to its students from 1923 onwards. The university traces its roots to 1870 when the Ceylon Medical School was established.[3] UoC has produced notable alumni in the fields of science, law, economics, business, literature, and politics.', 'YES', '2025-10-11', 'ACTIVE'),
('P0003', 'UOC UOC', 'The University of Colombo (informally Colombo University or UoC) is a public research university located primarily in Colombo, Sri Lanka. It is the oldest institution of modern higher education in Sri Lanka. It specializes in the fields of Natural, Social, Applied, Formal, and Computer Sciences as well as Fine Arts, Law and Technology. It is ranked among the top 10 universities in South Asia.[2]\r\n\r\nThe University of Colombo was founded in 1921 as University College Colombo, affiliated with the University of London. Degrees were issued to its students from 1923 onwards. The university traces its roots to 1870 when the Ceylon Medical School was established.[3] UoC has produced notable alumni in the fields of science, law, economics, business, literature, and politics.', 'YES', '2025-10-11', 'ACTIVE'),
('P0004', 'Chess', 'some dat', 'YES', '2025-10-23', 'ACTIVE'),
('P0005', 'Weight Lifting Winners', 'The description goes here, but I don\'t want it', 'YES', '2025-11-18', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `newsfeed_post_image`
--

DROP TABLE IF EXISTS `newsfeed_post_image`;
CREATE TABLE IF NOT EXISTS `newsfeed_post_image` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `post_id` varchar(12) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsfeed_post_image`
--

INSERT INTO `newsfeed_post_image` (`image_id`, `post_id`, `image_path`) VALUES
(1, 'P0004', 'images/posts/img_68ea09b0a08e47.10740325.png'),
(2, 'P0004', 'images/posts/img_68ea09b0a13271.00437227.png'),
(3, 'P0004', 'images/posts/img_68ea09b0a1a209.61825111.png'),
(4, 'P0005', 'images/posts/img_68ea0a67303374.67241459.png'),
(5, 'P0005', 'images/posts/img_68ea0a6730ff65.18004106.png'),
(6, 'P0005', 'images/posts/img_68ea0a67318c59.20239513.png'),
(7, 'P0006', 'images/posts/img_68ea0b8b1f70b5.40059936.png'),
(8, 'P0007', 'images/posts/img_68ea0ba0eaec78.25493508.png'),
(9, 'P0008', 'images/posts/img_68f6748fea7445.42547219.png'),
(10, 'P0009', 'images/posts/img_68f678cf993129.41130705.png'),
(11, 'P0010', 'images/posts/img_68f6794d0c5296.42644225.png'),
(12, 'P0004', 'images/posts/img_68f9f38717d384.76990012.jpg'),
(13, 'P0005', 'images/posts/img_691c45d9977f18.03558971.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `practice-session`
--

DROP TABLE IF EXISTS `practice-session`;
CREATE TABLE IF NOT EXISTS `practice-session` (
  `practice_id` varchar(12) NOT NULL,
  `sport_id` varchar(12) NOT NULL,
  `facility_id` varchar(12) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(64) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `attendance_taken` varchar(3) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `practice_sessions`
--

DROP TABLE IF EXISTS `practice_sessions`;
CREATE TABLE IF NOT EXISTS `practice_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility` varchar(100) NOT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `practice_sessions`
--

INSERT INTO `practice_sessions` (`id`, `facility`, `session_date`, `session_time`, `description`, `created_at`, `updated_at`) VALUES
(4, 'Badminton one Court (08 Persons for practices)', '2025-12-03', '12:45:00', '-', '2025-11-28 07:11:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 0, 'd125df99b6f85a0d3861dc2db2ca31c3a9e4da797d1503cd6dd738381a807173', 1762935327);

-- --------------------------------------------------------

--
-- Table structure for table `sport`
--

DROP TABLE IF EXISTS `sport`;
CREATE TABLE IF NOT EXISTS `sport` (
  `sport_id` varchar(4) NOT NULL,
  `sport_name` varchar(24) NOT NULL,
  `coach_id` varchar(12) NOT NULL,
  `captain_id` varchar(12) NOT NULL,
  `manager_id` varchar(12) NOT NULL,
  PRIMARY KEY (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport`
--

INSERT INTO `sport` (`sport_id`, `sport_name`, `coach_id`, `captain_id`, `manager_id`) VALUES
('BAD', 'Badminton', '', '', ''),
('VOL', 'Volleyball', '', '', ''),
('FOO', 'Football', '', '', ''),
('TEN', 'Tennis', '', '', ''),
('BAS', 'Basketball', '', '', ''),
('HOC', 'Hockey', '', '', ''),
('NET', 'Netball', '', '', ''),
('CRI', 'Cricket', '', '', 'usr_68f89be0'),
('RUG', 'Rugby', '', '', ''),
('SWI', 'Swimming', '', '', ''),
('TT', 'Table Tennis', '', '', ''),
('WL', 'Weight Lifting', '', '', ''),
('ROW', 'Rowing', '', '', ''),
('WRE', 'Wrestling', '', '', ''),
('CHE', 'Chess', '', '', ''),
('ATH', 'Athletics', '', '', 'usr_68f89be0'),
('BOX', 'Boxing', '', '', ''),
('TKD', 'Taekwondo', '', '', ''),
('KRT', 'Karate', '', '', ''),
('RR', 'Road Race', '', '', ''),
('SCR', 'Scrabble', '', '', ''),
('ELL', 'Elle', '', '', ''),
('BB', 'Baseball', '', '', ''),
('KBD', 'Kabaddi', '', '', ''),
('CRM', 'Carrom', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sports-team`
--

DROP TABLE IF EXISTS `sports-team`;
CREATE TABLE IF NOT EXISTS `sports-team` (
  `sport_id` varchar(12) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  `joined_date` date NOT NULL,
  PRIMARY KEY (`sport_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sports-team`
--

INSERT INTO `sports-team` (`sport_id`, `student_id`, `joined_date`) VALUES
('TKD', 'L3NCL2J4', '2025-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `sport_result_field`
--

DROP TABLE IF EXISTS `sport_result_field`;
CREATE TABLE IF NOT EXISTS `sport_result_field` (
  `field_id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(4) NOT NULL,
  `field_name` varchar(32) NOT NULL,
  `field_label` varchar(64) NOT NULL,
  `data_type` varchar(16) NOT NULL DEFAULT 'INT',
  `unit` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`field_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport_result_field`
--

INSERT INTO `sport_result_field` (`field_id`, `sport_id`, `field_name`, `field_label`, `data_type`, `unit`) VALUES
(1, 'BAD', 'sets_won', 'Sets Won', 'INT', NULL),
(2, 'BAD', 'points_scored', 'Points Scored', 'INT', NULL),
(5, 'VOL', 'sets_won', 'Sets Won', 'INT', NULL),
(6, 'VOL', 'points_scored', 'Points Scored', 'INT', NULL),
(9, 'FOO', 'goals', 'Goals', 'INT', NULL),
(10, 'FOO', 'assists', 'Assists', 'INT', NULL),
(11, 'FOO', 'fouls', 'Fouls', 'INT', NULL),
(15, 'TEN', 'sets_won', 'Sets Won', 'INT', NULL),
(16, 'TEN', 'games_won', 'Games Won', 'INT', NULL),
(19, 'BAS', 'points_scored', 'Points Scored', 'INT', NULL),
(20, 'BAS', 'rebounds', 'Rebounds', 'INT', NULL),
(21, 'BAS', 'assists', 'Assists', 'INT', NULL),
(25, 'HOC', 'goals', 'Goals', 'INT', NULL),
(26, 'HOC', 'assists', 'Assists', 'INT', NULL),
(29, 'NET', 'goals', 'Goals', 'INT', NULL),
(31, 'CRI', 'runs', 'Runs Scored', 'INT', NULL),
(32, 'CRI', 'wickets', 'Wickets Taken', 'INT', NULL),
(33, 'CRI', 'overs', 'Overs Bowled', 'FLOAT', 'overs'),
(39, 'RUG', 'tries', 'Tries', 'INT', NULL),
(40, 'RUG', 'points', 'Points', 'INT', NULL),
(41, 'SWI', 'time', 'Time', 'FLOAT', 'seconds'),
(45, 'TT', 'sets_won', 'Sets Won', 'INT', NULL),
(46, 'TT', 'points_scored', 'Points Scored', 'INT', NULL),
(47, 'WL', 'snatch', 'Snatch Weight', 'FLOAT', 'kg'),
(48, 'WL', 'clean_jerk', 'Clean & Jerk', 'FLOAT', 'kg'),
(49, 'WL', 'total', 'Total', 'FLOAT', 'kg'),
(53, 'ROW', 'time', 'Time', 'FLOAT', 'seconds'),
(55, 'WRE', 'points', 'Points', 'INT', NULL),
(56, 'WRE', 'fall', 'Fall', 'INT', NULL),
(59, 'CHE', 'result', 'Result', 'VARCHAR', NULL),
(60, 'ATH', 'time', 'Time', 'FLOAT', 'seconds'),
(61, 'ATH', 'distance', 'Distance', 'FLOAT', 'meters'),
(62, 'BOX', 'points', 'Points', 'INT', NULL),
(63, 'BOX', 'knockdowns', 'Knockdowns', 'INT', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sport_result_value`
--

DROP TABLE IF EXISTS `sport_result_value`;
CREATE TABLE IF NOT EXISTS `sport_result_value` (
  `value_id` varchar(12) NOT NULL,
  `result_id` varchar(12) NOT NULL,
  `field_id` int NOT NULL,
  `field_value` varchar(64) NOT NULL,
  PRIMARY KEY (`value_id`),
  KEY `result_id` (`result_id`),
  KEY `field_id` (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `tournament_id` varchar(12) NOT NULL,
  `tournament_name` varchar(64) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'INCOMPLETE',
  PRIMARY KEY (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament_match`
--

DROP TABLE IF EXISTS `tournament_match`;
CREATE TABLE IF NOT EXISTS `tournament_match` (
  `match_id` varchar(12) NOT NULL,
  `tournament_id` varchar(12) NOT NULL,
  `match_name` varchar(64) DEFAULT NULL,
  `match_date` datetime DEFAULT NULL,
  `sport_id` varchar(4) NOT NULL,
  `winner_id` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`match_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament_result`
--

DROP TABLE IF EXISTS `tournament_result`;
CREATE TABLE IF NOT EXISTS `tournament_result` (
  `result_id` varchar(12) NOT NULL,
  `match_id` varchar(12) NOT NULL,
  `player_id` varchar(12) DEFAULT NULL,
  `team_id` varchar(12) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`result_id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE IF NOT EXISTS `transaction` (
  `transaction_id` varchar(12) NOT NULL,
  `budget_id` varchar(12) NOT NULL,
  `amount` int NOT NULL,
  `purpose` varchar(256) NOT NULL,
  `timestamp` timestamp NOT NULL,
  `proof_doc` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `remarks` varchar(256) NOT NULL,
  `change_reason` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `budget_id`, `amount`, `purpose`, `timestamp`, `proof_doc`, `remarks`, `change_reason`) VALUES
('1', '1', 50000, 'Purchase of cricket bats', '2025-01-20 05:00:00', 'proof_cricket_ba', '', ''),
('2', '1', 30000, 'Ground maintenance', '2025-02-05 08:45:00', 'proof_ground_mai', '', ''),
('3', '2', 40000, 'Football gear purchase', '2025-02-15 04:15:00', 'proof_football_g', '', ''),
('4', '3', 20000, 'Basketball court repair', '2025-03-20 10:40:00', 'proof_basketball', '', ''),
('5', '4', 10000, 'Volleyball net purchase', '2025-04-12 06:20:00', 'proof_volleyball', '', ''),
('T0001', 'ABC012', 30000, '', '2025-10-22 17:35:33', 'tx_68f92f4ea50506.21004129.png', '', 'No No'),
('T0002', 'ABC012', 12000, 'Bats', '2025-10-23 08:16:19', 'tx_68f9e453457406.31160978.png', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

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
  `contact_no` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `profile_img` varchar(64) NOT NULL,
  `sport_id` varchar(5) NOT NULL,
  `student_id` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `faculty_id` varchar(4) NOT NULL,
  `status` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `Email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `fname`, `lname`, `type`, `email`, `password`, `must_change_pass`, `joined_date`, `contact_no`, `profile_img`, `sport_id`, `student_id`, `faculty_id`, `status`) VALUES
('1', 'Chamal', 'Chamuditha', 'PUBLIC', 'chamal@gmail.com', '$2y$10$9uuUPpPn/UKC88kYSkNX.OjlW2QGabCWK8ufpDR2wob8/LijSlcu6', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('VSSMS4ZL', 'Ravindu', 'Rasa', 'PUBLIC', 'ravi@kgla.lk', '$2y$10$fnZzlj0G1IcqfC2ZYui/pObN8nSZQc.R2S1hW/h73nNONgSa1LknK', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('FK9C62HG', 'Pasindu', 'Anjana', 'PUBLIC', 'pasindu@anura.com', '$2y$10$CRDK.UCXZll9A4HCJgK2Cu/cEdcAv8kmhzXXLD8oXxiZx6mPZKHZi', 0, '2025-08-09 15:55:14', NULL, '', '', NULL, '', 'ACTIVE'),
('101', 'John', 'Smith', 'PUBLIC', 'john.smith@example.com', '', 0, '2025-08-24 06:07:00', '0771234567', '', '', NULL, '', 'ACTIVE'),
('102', 'David', 'Perera', 'PUBLIC', 'david.perera@example.com', '', 0, '2025-08-24 06:07:00', '0779876543', '', '', NULL, '', 'ACTIVE'),
('103', 'Alex', 'Fernando', 'PUBLIC', 'alex.fernando@example.com', '', 0, '2025-08-24 06:07:00', '0713456789', '', '', NULL, '', 'ACTIVE'),
('104', 'Mark', 'Silva', 'PUBLIC', 'mark.silva@example.com', '', 0, '2025-08-24 06:07:00', '0752345678', '', '', NULL, '', 'ACTIVE'),
('105', 'Kamal', 'Jayasinghe', 'PUBLIC', 'kamal.jayasinghe@example.com', '', 0, '2025-08-24 06:07:00', '0761112233', '', '', NULL, '', 'ACTIVE'),
('201', 'Sameera', 'Dissanayake', 'PUBLIC', 'sameera.dissanayake@example.com', '', 0, '2025-08-24 06:07:00', '0775566778', '', '', NULL, '', 'ACTIVE'),
('202', 'Nuwan', 'Karunaratne', 'PUBLIC', 'nuwan.karunaratne@example.com', '', 0, '2025-08-24 06:07:00', '0711122334', '', '', NULL, '', 'ACTIVE'),
('203', 'Ruwan', 'Senanayake', 'PUBLIC', 'ruwan.senanayake@example.com', '', 0, '2025-08-24 06:07:00', '0729988776', '', '', NULL, '', 'ACTIVE'),
('204', 'Suresh', 'Kumara', 'PUBLIC', 'suresh.kumara@example.com', '', 0, '2025-08-24 06:07:00', '0765544332', '', '', NULL, '', 'ACTIVE'),
('205', 'Ashan', 'Wijesinghe', 'PUBLIC', 'ashan.wijesinghe@example.com', '', 0, '2025-08-24 06:07:00', '0776677889', '', '', NULL, '', 'ACTIVE'),
('301', 'Pradeep', 'Gunawardena', 'PUBLIC', 'pradeep.gunawardena@example.com', '', 0, '2025-08-24 06:07:00', '0718899001', '', '', NULL, '', 'ACTIVE'),
('302', 'Chathura', 'Ekanayake', 'PUBLIC', 'chathura.ekanayake@example.com', '', 0, '2025-08-24 06:07:00', '0752233445', '', '', NULL, '', 'ACTIVE'),
('303', 'Isuru', 'Lakshan', 'PUBLIC', 'isuru.lakshan@example.com', '', 0, '2025-08-24 06:07:00', '0723344556', '', '', NULL, '', 'ACTIVE'),
('304', 'Gayan', 'Rathnayake', 'PUBLIC', 'gayan.rathnayake@example.com', '', 0, '2025-08-24 06:07:00', '0779988775', '', '', NULL, '', 'ACTIVE'),
('305', 'Roshan', 'Abeysinghe', 'PUBLIC', 'roshan.abeysinghe@example.com', '', 0, '2025-08-24 06:07:00', '0764455667', '', '', NULL, '', 'ACTIVE'),
('NPM8O9RE', 'Chamal', 'Chamuditha', 'PUBLIC', 'chamal1@gmail.com', '$2y$10$8trbPHuAHueKspCIvyWQyudyy97asXBJzzKTvWW7bXgSeoLdq2aku', 0, '2025-09-01 22:53:08', NULL, '', '', NULL, '', 'ACTIVE'),
('UBVXZ90U', 'ddkjn', 'fsrvn', 'PUBLIC', 'maximal@gmail.com', '$2y$10$LYuhIcrTAZJqsfDTzQlZLecow/GgbcLCngpkp7ltpuyKJZ6rlR6zi', 0, '2025-09-01 23:24:32', NULL, '', '', NULL, '', 'ACTIVE'),
('KI5RL42D', 'ddkjn', 'fsrvn', 'PUBLIC', 'hj@gmail.com', '$2y$10$TPyJAf4EyP825BBF0mACouJOFnqcAWbXO/bxm7IK5xNtTVI1PlP7S', 0, '2025-09-01 23:29:53', NULL, '', '', NULL, '', 'ACTIVE'),
('PA0XK3QZ', 'ddkjn', 'fsrvn', 'PUBLIC', 'hjggd@gmail.com', '$2y$10$cODg7SN2ZxBh2.gsibO4yOvm9zJYuPdMCDDDPCWHduYywH7RTLUSa', 0, '2025-09-01 23:32:55', NULL, '', '', NULL, '', 'ACTIVE'),
('JIIJ51LA', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsjvn@gmail.com', '$2y$10$Z9qcxldtUu9qzIwY6v4yiOWj/2bdj99OXDSaPUddntSmaCDzpWncK', 0, '2025-09-01 23:39:19', NULL, '', '', NULL, '', 'ACTIVE'),
('VTLMC3YK', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsvn@gmail.com', '$2y$10$o3cSmLAs4V6Nogqe6b/cA.x/EIIl.SGu.XihIBcFC75SamO4GCNDm', 0, '2025-09-01 23:47:59', NULL, '', '', NULL, '', 'ACTIVE'),
('R13QQJC2', 'kfkhef', 'ekjnv', 'PUBLIC', 'kdsgvn@gmail.com', '$2y$10$JVwP24LoaJqCuudXv8CZp.QbBUzfULWlQtCQwunuUuul6io9sDy3K', 0, '2025-09-01 23:50:07', NULL, '', '', NULL, '', 'ACTIVE'),
('JORD04QN', 'vvds', 'qeq', 'PUBLIC', 'esfef@gmail.com', '$2y$10$re.NOOjMteFeayYPjQ.n5uUK8bFG0rRuCRF.lz71kT1ZihYCDZ.GG', 0, '2025-09-01 23:52:30', NULL, '', '', NULL, '', 'ACTIVE'),
('KCLIH538', 'vvds', 'qeq', 'PUBLIC', 'esff@gmail.com', '$2y$10$sNchOEew9G1m6qKtSJbguuFPaYJ3qWHRsqTlDqoJ3q2tfnFojGenK', 0, '2025-09-01 23:53:34', NULL, '', '', NULL, '', 'ACTIVE'),
('43N1VK76', 'vvdsdwef', 'qeq', 'PUBLIC', 'esrdrfff@gmail.com', '$2y$10$.nBD9d6ZqFXVsMZ4tzknn.25DfshuRPdNEJdehgcOa8JX6Q5uIiaK', 0, '2025-09-01 23:57:10', NULL, '', '', NULL, '', 'ACTIVE'),
('CE02XIPB', 'Admin', 'UOC', 'PUBLIC', 'admin@uocs.com', '$2y$10$ZGlvkCP/uyqffeCxj8Pm7.HA8JUcle3TlPEw8NEsuHDHUvr.jSt0C', 0, '2025-09-02 00:01:26', NULL, '', '', NULL, '', 'ACTIVE'),
('H4J1OHSX', 'Chamal', 'Chamuditha', 'ADMIN', 'chamal.admin@uocs.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 02:04:39', NULL, '', '', NULL, '', 'ACTIVE'),
('usr_68ec8172', 'Sarath', 'Kumara', 'REG', 'chamlaanil99@gmail.com', '$2y$10$gOcGzGue3L4TOszEEwWAF.PlKwQwnFgUyMDXqwfX5urpgzd7Q18Ly', 1, '2025-10-13 04:34:59', '0710000000', '', '', NULL, '', 'ACTIVE'),
('L3NCL2J4', 'Chamal', 'Hettiarachchi', 'STUDENT', 'chamal2@gmail.com', '$2y$10$zlUHk9p5y7uAz7u2jQQ0X.PNaxkDwan5JIDlR/jySjsAgtcutfqpm', 0, '2025-10-14 04:48:58', NULL, 'L3NCL2J4.png', '', '23000000', '', 'ACTIVE'),
('usr_68f7c669', 'Jayashini', 'Jayaweera', 'SPT', 'chamalchamuditha1231@gmail.com', '$2y$10$mHrXH0uDC7UQkgi1qYZa5.QplnCgsCnu2RGBA1YWDoTn6EbGps5U.', 1, '2025-10-21 17:44:09', '0710000000', '', 'ATH', NULL, '', 'ACTIVE'),
('usr_68f82fe0', 'Shashini', 'Malsha', 'EQP', 'ccwrecker99@gmail.com', '$2y$10$0Tn8wECDAB8QNE6PwnexKeewRZA2GHwtm9Ljpx5USTm2LKEjsvL6W', 1, '2025-10-22 01:14:08', '076543213', '', '', NULL, '', 'ACTIVE'),
('usr_68f89998', 'Jaye', 'Jayaweera', 'EQP', 'jayashinisjayaweera@gmail.com', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 1, '2025-10-22 08:45:12', '0763452143', '', '', NULL, '', 'ACTIVE'),
('usr_68f89be0', 'J', 'Jaye', 'SPT', '2023is043@stu.ucsc.cmb.ac.lk', '$2y$10$0Z1ZoYUII3O2MDC3ltxdku2r3ROkBM.swVOJs88JYaG4fsZCyFy2W', 1, '2025-10-22 08:54:56', '0763452145', '', 'CRI', NULL, '', 'ACTIVE'),
('FMX6Z8DF', 'Shashini', 'Malsha', 'STUDENT', 'shashini@gmail.com', '$2y$10$PUWxFaoItXKbGY/52bG/vebAWPEQyHc39o5nwtTb2iPoZ6zpAd0rq', 0, '2025-10-23 07:10:43', NULL, '', '', '23020997', '', 'ACTIVE'),
('5Q1XZO2Y', 'Jansika', 'Balakrishnan', 'CAPTAIN', 'jansi@gmail.com', '$2y$10$50U4SKStJpeogM4DSK5r2OnQO041WacupfYjfsX3w1B18UtX6RvCy', 0, '2025-10-23 07:23:06', NULL, '', '', '23020342', '', 'ACTIVE');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
