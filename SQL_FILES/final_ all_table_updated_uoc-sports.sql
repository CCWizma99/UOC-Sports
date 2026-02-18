-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 13, 2026 at 02:14 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

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
-- Table structure for table `achievement`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `achievement`
--

INSERT INTO `achievement` (`achievement_id`, `user_id`, `sport_id`, `competition_id`, `achievement`, `points`) VALUES
(9, 'STU010', 'CRI', '105', 'Best performance', 7),
(8, 'STU009', 'CRI', '103', 'Participation', 0),
(7, 'STU010', 'CRI', '103', 'Best performance', 7),
(6, 'STU010', 'CRI', '101', '2nd place', 3);

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
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_update_student_points`;
DELIMITER $$
CREATE TRIGGER `trg_update_student_points` AFTER INSERT ON `achievement` FOR EACH ROW BEGIN
    DECLARE total_points INT;

    -- Calculate total points for that user
    SELECT SUM(points)
    INTO total_points
    FROM achievement
    WHERE user_id = NEW.user_id;

    -- Insert new record OR update existing one
    INSERT INTO user_points (user_id, user_points)
    VALUES (NEW.user_id, total_points)
    ON DUPLICATE KEY UPDATE
        user_points = total_points;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` varchar(12) NOT NULL,
  `practice_id` int NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `status` varchar(12) NOT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `practice_id`, `user_id`, `status`) VALUES
('ATDFD77F382E', 8, 'L3NCL2J4', 'ABSENT'),
('ATD16507D601', 8, '5Q1XZO2Y', 'PRESENT');

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
('ANUTKD01', 'TKDA', '2025', 400000, 178000, '2024-12-15', NULL),
('ANUTKD02', 'TKDA', '2025', 100000, 9000, '2024-12-18', NULL),
('1', '1', '2025', 500000, 250000, '2025-01-15', NULL),
('2', '2', '2025', 400000, 150000, '2025-02-10', NULL),
('3', '3', '2025', 300000, 100000, '2025-03-12', NULL),
('4', '4', '2025', 200000, 50000, '2025-04-05', NULL),
('5', '5', '2025', 150000, 30000, '2025-05-01', NULL),
('ABC012', 'CRI', '2025', 200000, 42000, '2025-08-24', 'This is for testing'),
('BDG96F74E4F0', 'ROW', '2026', 100000, 12000, '2025-12-26', '-');

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

-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `competition`
--

INSERT INTO `competition` (`competition_id`, `competition_name`, `sport_id`, `participant_pdf`, `participants`, `created_at`, `date`) VALUES
(1, 'Inter University Basketball Competition', 'BAS', '', '', '2026-01-25 19:35:25', '0000-00-00'),
(3, 'National Cricket Championship', 'CRI', 'competition_1769374959_697684efe76e6.pdf', 'Dineth Amarasinghe', '2026-01-25 20:30:22', '2026-01-30');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` int NOT NULL AUTO_INCREMENT,
  `equipment_name` varchar(100) NOT NULL,
  `sport_id` varchar(10) NOT NULL,
  `max_allow` int NOT NULL,
  PRIMARY KEY (`equipment_id`),
  KEY `fk_sport` (`sport_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `equipment_name`, `sport_id`, `max_allow`) VALUES
(1, 'BarBell', 'WL', 1),
(2, 'Lifting Belt', 'WL', 1),
(3, 'Weight Plates', 'WL', 1),
(4, 'Score Board', 'BAS', 1),
(5, 'Carrom Board', 'CAR', 1),
(6, 'Coin Box', 'CAR', 1),
(7, 'Powder', 'CAR', 1),
(8, 'Scrabble Board', 'SCR', 1),
(9, 'Score Sheet', 'SCR', 1),
(10, 'Timer', 'SCR', 1),
(11, 'Chess Board', 'CHE', 1),
(12, 'Chess Pieces Box', 'CHE', 1),
(13, 'Chess Clock', 'CHE', 1),
(14, 'Relay baton', 'ATH', 4),
(15, 'Hurdles', 'ATH', 8),
(16, 'Shot Put', 'ATH', 1),
(17, 'High Jump Bar and Stand', 'ATH', 1),
(18, 'Rugby Ball', 'RUG', 2),
(19, 'Scrum Machine', 'RUG', 1),
(20, 'Tennis Racket', 'TEN', 2),
(21, 'Tennis Ball', 'TEN', 6),
(22, 'Net', 'TEN', 1),
(23, 'Basketball', 'BAS', 2),
(24, 'Score Board', 'BAS', 1),
(25, 'Foot Ball', 'FOO', 2),
(26, 'Corner Flags', 'FOO', 4),
(27, 'Shin Guards', 'FOO', 4),
(28, 'Baseball Bat', 'BSB', 1),
(29, 'Glove', 'BSB', 4),
(30, 'Helmet', 'BSB', 4),
(31, 'Rowing Machine', 'ROW', 1),
(32, 'Net Ball', 'NET', 2),
(33, 'Head Guard', 'TKD', 10),
(34, 'Shin Guard', 'TKD', 10),
(35, 'Hockey Stick', 'HOC', 8),
(36, 'Goal Keeper Pads', 'HOC', 2),
(37, 'Elle Stick', 'ELL', 2),
(38, 'Cricket Bat', 'CRI', 2),
(39, 'Stumps', 'CRI', 3),
(40, 'Mat', 'KBD', 1);

--
-- Triggers `equipment`
--
DROP TRIGGER IF EXISTS `before_insert_equipment`;
DELIMITER $$
CREATE TRIGGER `before_insert_equipment` BEFORE INSERT ON `equipment` FOR EACH ROW BEGIN
    IF NEW.equipment_id NOT LIKE 'EQ%' THEN
        SET NEW.equipment_id = CONCAT('EQ', NEW.equipment_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `equipment-requests`
--

DROP TABLE IF EXISTS `equipment-requests`;
CREATE TABLE IF NOT EXISTS `equipment-requests` (
  `request_id` varchar(12) NOT NULL,
  `student_id` varchar(12) DEFAULT NULL,
  `category_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `request_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(64) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `notes` varchar(64) NOT NULL,
  `sport_id` varchar(10) NOT NULL,
  `reserved_location` varchar(100) NOT NULL,
  `requester_name` varchar(100) NOT NULL,
  `equipment_items` text COMMENT 'JSON array of equipment items with quantities',
  PRIMARY KEY (`request_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment-requests`
--

INSERT INTO `equipment-requests` (`request_id`, `student_id`, `category_name`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`, `sport_id`, `reserved_location`, `requester_name`, `equipment_items`) VALUES
('req_6937e152', 'FMX6Z8DF', 'Badminton Re', '2025-12-29', '08:00:00', '10:00:00', 'For the Taekwondo Provincial matches practices', 'COMPLETED', '-', 'BAD', 'Ground', 'K S Silva', NULL),
('req_6d607bf3', NULL, 'Tennis Racket (x2)', '2026-02-13', '10:30:00', '11:30:00', '', 'ACTIVE', '', 'TEN', 'Tennis Court', 'Student ', '[{\"equipment_name\":\"Tennis Racket\",\"quantity\":2}]'),
('req_eec57c81', NULL, 'Cricket Bat', '2026-01-24', '06:21:00', '07:21:00', '', 'COMPLETED', '', 'CRI', 'Cricket Pitch', 'Student ', NULL),
('req_9fdd61ac', NULL, 'Netball Post', '2026-01-25', '09:50:00', '11:49:00', '', 'PENDING', 'Team Practice', 'NET', 'Ground', 'In person reservation', NULL),
('req_c9eaa56c', 'In person', 'Goalkeeper P', '2026-01-25', '12:01:00', '13:01:00', '', 'ACCEPTED', 'Freshers', 'HOC', 'Ground', 'Savi', '[{\"equipment_name\":\"Goalkeeper Pads\",\"quantity\":1},{\"equipment_name\":\"Hockey Ball\",\"quantity\":1}]'),
('req_de7286f7', NULL, 'Relay baton (x4)', '2026-02-13', '06:30:00', '08:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S K', '[{\"equipment_name\":\"Relay baton\",\"quantity\":4}]'),
('req_eefe02a8', NULL, 'Relay baton (x1)', '2026-02-13', '06:30:00', '07:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S Silv', '[{\"equipment_name\":\"Relay baton\",\"quantity\":1}]');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_categories`
--

DROP TABLE IF EXISTS `equipment_categories`;
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `equipment_id` varchar(10) NOT NULL,
  `sport_id` varchar(10) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `fk_inventory_equipment` (`equipment_id`),
  KEY `fk_inventory_sport` (`sport_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_categories`
--

INSERT INTO `equipment_categories` (`category_id`, `category_name`, `equipment_id`, `sport_id`) VALUES
(1, 'Iron barbells', '1', 'WL'),
(2, 'Leather belts', '2', 'WL'),
(3, 'Metal plates', '3', 'WL'),
(4, 'Leather scoreboard', '4', 'BAS'),
(5, 'Match balls', '23', 'BAS'),
(6, 'Wooden board', '5', 'CRM'),
(7, 'Coin box', '6', 'CRM'),
(8, 'Carrom Powder', '7', 'CRM'),
(9, 'Board', '8', 'SCR'),
(10, 'Score sheets', '9', 'SCR'),
(11, 'Digital timer', '10', 'SCR'),
(12, 'Plastic Board', '11', 'CHE'),
(13, 'Piece boxes', '12', 'CHE'),
(14, 'Chess clock', '13', 'CHE'),
(15, 'Relay Metal Baton', '14', 'ATH'),
(16, 'Hurdles', '15', 'ATH'),
(17, 'Shot put', '16', 'ATH'),
(18, 'High jump', '17', 'ATH'),
(19, 'Match balls', '18', 'RUG'),
(20, 'Scrum machine', '19', 'RUG'),
(21, 'Rackets', '20', 'TEN'),
(22, 'Tennis balls', '21', 'TEN'),
(23, 'Court nets', '22', 'TEN'),
(24, 'Match balls', '25', 'FOO'),
(25, 'Corner flags', '26', 'FOO'),
(26, 'Shin guards', '27', 'FOO'),
(27, 'Cricket bats', '38', 'CRI'),
(28, 'Stumps', '39', 'CRI');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_inventory`
--

DROP TABLE IF EXISTS `equipment_inventory`;
CREATE TABLE IF NOT EXISTS `equipment_inventory` (
  `stock_id` int NOT NULL AUTO_INCREMENT,
  `equipment_id` varchar(10) NOT NULL,
  `sport_id` varchar(10) NOT NULL,
  `category_id` int NOT NULL,
  `quantity` int NOT NULL,
  `usable` int NOT NULL,
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_date` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`stock_id`),
  KEY `fk_inventory_equipment` (`equipment_id`),
  KEY `fk_inventory_sport` (`sport_id`),
  KEY `fk_inventory_category` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_inventory`
--

INSERT INTO `equipment_inventory` (`stock_id`, `equipment_id`, `sport_id`, `category_id`, `quantity`, `usable`, `created_date`, `updated_date`, `remarks`) VALUES
(1, '1', 'WL', 1, 10, 9, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Iron barbells'),
(2, '2', 'WL', 2, 15, 14, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Leather belts'),
(3, '3', 'WL', 3, 50, 45, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Metal plates'),
(4, '4', 'BAS', 4, 8, 7, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Leather scoreboard'),
(5, '23', 'BAS', 5, 12, 10, '2026-02-12 19:59:53', '2026-02-13 05:46:23', 'Match balls'),
(6, '5', 'CRM', 6, 5, 5, '2026-02-12 19:59:53', '2026-02-13 05:47:15', 'Wooden board'),
(7, '6', 'CRM', 7, 10, 9, '2026-02-12 19:59:53', '2026-02-13 05:49:03', 'Coin box'),
(8, '7', 'CRM', 8, 20, 18, '2026-02-12 19:59:53', '2026-02-13 05:49:36', 'Carrom powder'),
(9, '8', 'SCR', 9, 4, 4, '2026-02-12 19:59:53', '2026-02-13 05:49:50', 'Board'),
(10, '9', 'SCR', 10, 30, 30, '2026-02-12 19:59:53', '2026-02-13 05:50:15', 'Score sheets'),
(11, '10', 'SCR', 11, 5, 4, '2026-02-12 19:59:53', '2026-02-13 05:51:40', 'Digital timer'),
(12, '11', 'CHE', 12, 6, 6, '2026-02-12 19:59:53', '2026-02-13 05:51:53', 'Plastic Board'),
(13, '12', 'CHE', 13, 10, 9, '2026-02-12 19:59:53', '2026-02-13 05:52:19', 'Piece boxes'),
(14, '13', 'CHE', 14, 5, 5, '2026-02-12 19:59:53', '2026-02-13 05:56:30', 'Chess clock'),
(15, '14', 'ATH', 15, 6, 6, '2026-02-12 19:59:53', '2026-02-13 05:57:25', 'Relay baton'),
(16, '15', 'ATH', 16, 12, 10, '2026-02-12 19:59:53', '2026-02-13 05:58:09', 'Hurdles'),
(17, '16', 'ATH', 16, 5, 5, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Shot put'),
(18, '17', 'ATH', 18, 3, 3, '2026-02-12 19:59:53', '2026-02-13 05:58:51', 'High jump'),
(19, '18', 'RUG', 19, 10, 9, '2026-02-12 19:59:53', '2026-02-13 05:59:14', 'Match balls'),
(20, '19', 'RUG', 19, 2, 2, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Scrum machine'),
(21, '20', 'TEN', 21, 15, 14, '2026-02-12 19:59:53', '2026-02-13 05:59:39', 'Rackets'),
(22, '21', 'TEN', 22, 50, 45, '2026-02-12 19:59:53', '2026-02-13 06:12:57', 'Tennis balls'),
(23, '22', 'TEN', 23, 4, 4, '2026-02-12 19:59:53', '2026-02-13 06:13:16', 'Court nets'),
(24, '25', 'FOO', 24, 20, 18, '2026-02-12 19:59:53', '2026-02-13 06:14:55', 'Match balls'),
(25, '26', 'FOO', 25, 8, 8, '2026-02-12 19:59:53', '2026-02-13 06:14:18', 'Corner flags'),
(26, '27', 'FOO', 26, 15, 12, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Shin guards'),
(27, '38', 'CRI', 38, 12, 10, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Cricket bats'),
(28, '39', 'CRI', 39, 6, 6, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Stumps'),
(29, '40', 'KBD', 40, 3, 3, '2026-02-12 19:59:53', '2026-02-12 19:59:53', 'Training mat');

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
  `rejection_reason` varchar(256) NOT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility-booking`
--

INSERT INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `slot`, `purpose`, `status`, `payment_status`, `rejection_reason`) VALUES
('BK711559', 'H4J1OHSX', '9', '2025-12-11', 'FULL', 'To practice for Inter Provincial Matches held in January 2026', 'BOOKED', 'INCOMPLETE', ''),
('BK398317', 'H4J1OHSX', '3', '2025-12-10', 'AFTERNOON', 'Badminton Provincial Matches Practice', 'BOOKED', 'INCOMPLETE', ''),
('BK861578', 'L3NCL2J4', '11', '2025-12-10', 'MORNING', 'For Inter University Practices for SLIIT University', 'REJECTED', 'INCOMPLETE', 'No reason'),
('BK937846', 'L3NCL2J4', '15', '2025-12-18', 'FULL', 'For TOC Championship Match Practice', 'REJECTED', 'INCOMPLETE', 'A maintenance on the ground has been scheduled for that day. Sorry for the inconvenience.'),
('BK405911', 'H4J1OHSX', '5', '2025-12-11', 'FULL', 'Divisional Tennis Matches', 'BOOKED', 'INCOMPLETE', ''),
('BK662944', '5Q1XZO2Y', '15', '2025-12-12', 'FULL', 'Inter Uni Matches Practice', 'BOOKED', 'INCOMPLETE', ''),
('BK743077', 'L3NCL2J4', '18', '2025-12-27', 'FULL', 'Cricket practice', 'ACCEPTED', 'INCOMPLETE', ''),
('BK425118', 'H4J1OHSX', '13', '2025-12-29', 'FULL', '-', 'BOOKED', 'INCOMPLETE', ''),
('BK896561', 'H4J1OHSX', '13', '2026-01-01', 'FULL', '-', 'BOOKED', 'INCOMPLETE', '');

-- --------------------------------------------------------

--
-- Table structure for table `facility_rates`
--

DROP TABLE IF EXISTS `facility_rates`;
CREATE TABLE IF NOT EXISTS `facility_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_type` enum('INDOOR_GYM','GROUND') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `facility_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_rates`
--

INSERT INTO `facility_rates` (`id`, `facility_type`, `facility_name`, `capacity`, `practice_working_hours`, `practice_other_hours`, `tournament_full_day_working`, `tournament_half_day_working`, `tournament_full_day_other`, `tournament_half_day_other`, `created_at`, `updated_at`) VALUES
(1, 'INDOOR_GYM', 'Badminton one Court (08 Persons for practices)', 8, 800.00, 1100.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(2, 'INDOOR_GYM', 'Badminton two Courts (16 Persons for practices)', 16, 1600.00, 1900.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(3, 'INDOOR_GYM', 'Badminton Four Courts (30 Persons for practices)', 30, 3000.00, 3600.00, 50000.00, 35000.00, 59000.00, 41000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(4, 'INDOOR_GYM', 'Table Tennis Two tables (08 Persons for practices)', 8, 900.00, 1200.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(5, 'INDOOR_GYM', 'Table Tennis', NULL, NULL, NULL, 50000.00, 35000.00, 59000.00, 41000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(6, 'INDOOR_GYM', 'Karate / Taekwondo with without Tatami', NULL, NULL, NULL, 50000.00, 35000.00, 59000.00, 41000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(7, 'INDOOR_GYM', 'Wrestling without mattress', NULL, NULL, NULL, 50000.00, 35000.00, 59000.00, 41000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(8, 'INDOOR_GYM', 'Volleyball (25 Persons for practices)', 25, 5000.00, 5600.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(9, 'INDOOR_GYM', 'Volleyball', NULL, NULL, NULL, 60000.00, 40000.00, 69000.00, 46000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(10, 'INDOOR_GYM', 'Student Sport Center and surrounding area (sports activities & functions)', NULL, NULL, NULL, 30000.00, 20000.00, 39000.00, 26000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(11, 'GROUND', 'Baseball (30 Persons for practices)', 30, 30000.00, 17500.00, NULL, NULL, 65000.00, 35000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(12, 'GROUND', 'Basketball (25 Persons for practices) (without light)', 25, 20000.00, 12000.00, 6000.00, 40000.00, 25000.00, 10000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(13, 'GROUND', 'Basketball (25 Persons for practices) (with light)', 25, NULL, 17500.00, 8000.00, NULL, 25000.00, 12500.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(14, 'GROUND', 'Cricket - Hard Ball with matting (only one team allowed for practices)', NULL, 30000.00, 17500.00, 10000.00, 35000.00, 20000.00, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(15, 'GROUND', 'Cricket - Hard Ball fielding practices (only one team allowed)', NULL, NULL, NULL, 6000.00, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(16, 'GROUND', 'Soft Ball Cricket & Other functions (maximum three pitches)', NULL, NULL, NULL, 4000.00, 115000.00, 65000.00, 10000.00, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(17, 'GROUND', 'Cricket - Side Wicket (one wicket) (18 Persons)', 18, NULL, NULL, 4000.00, NULL, NULL, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(18, 'GROUND', 'Cricket Turf', NULL, NULL, NULL, 7000.00, 45000.00, 25000.00, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(19, 'GROUND', 'Elle', NULL, NULL, NULL, NULL, 45000.00, 25000.00, NULL, '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(20, 'GROUND', 'Football One Court without court marking (40 Persons for practices)', 40, 30000.00, 20000.00, 16000.00, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(21, 'GROUND', 'Football with court marking', NULL, 30000.00, 27500.00, 17500.00, 70000.00, 40000.00, 27500.00, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(22, 'GROUND', 'Hockey (30 Persons for practices)', 30, 30000.00, 20000.00, 10000.00, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(23, 'GROUND', 'Hockey with court marking', NULL, 30000.00, 27500.00, 17500.00, 70000.00, 40000.00, 27500.00, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(24, 'GROUND', 'Netball (25 Persons for practices) One Court', 25, 30000.00, 20000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(25, 'GROUND', 'Netball (50 Persons for practices) Six Courts', 50, 45000.00, 30000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(26, 'GROUND', 'Rugby (40 Persons for practices)', 40, 40000.00, 25000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(27, 'GROUND', 'Rugby with court Marking', NULL, 47500.00, 32500.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(28, 'GROUND', 'Tennis One Court (04 Persons)', 4, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(29, 'GROUND', 'Tennis Two Courts (04 Persons each)', 8, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(30, 'GROUND', 'Track & Field (without ground marking and without High Jump/ Mattress)', NULL, 45000.00, 25000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(31, 'GROUND', 'Track & Field (with ground marking and with High Jump/ Mattress)', NULL, 65000.00, 45000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32'),
(32, 'GROUND', 'Volleyball (Outdoor) (1 court) (25 Persons for practices)', 25, 30000.00, 20000.00, NULL, NULL, NULL, NULL, '2025-08-15 23:13:32', '2025-08-15 23:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` varchar(4) NOT NULL,
  `faculty_name` varchar(64) NOT NULL,
  `registrar_id` varchar(12) DEFAULT NULL COMMENT 'User ID of the faculty registrar',
  `registrar_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`faculty_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`, `registrar_id`, `registrar_email`) VALUES
('1', 'UCSC', 'REG003', 'kasun.silva@ucsc.uoc.lk'),
('2', 'Science', NULL, NULL);

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

--
-- Dumping data for table `inquiry`
--

INSERT INTO `inquiry` (`inquiry_id`, `user_id`, `email`, `subject`, `message`, `date`, `status`) VALUES
('INQA1A688463', 'H4J1OHSX', 'maximal@gmail.com', 'Testing contact', 'Something Something', '2025-12-15', 'RESOLVED'),
('INQE8F057499', 'H4J1OHSX', 'dakshinagn@gmail.com', 'about group project', 'on progress', '2025-12-18', 'NOT-RESOLVED');

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
-- Table structure for table `lost_item`
--

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lost_item`
--

INSERT INTO `lost_item` (`lostItem_id`, `itemName`, `foundDate`, `description`, `foundLocation`, `foundBy`, `contactNumber`, `itemStatus`, `image`) VALUES
(1, 'Black Bottle', '2026-01-17', 'test', 'test', 'test person', '0717171711', 'unclaimed', '1768858273_Screenshot 2025-12-28 140316.png');

-- --------------------------------------------------------

--
-- Table structure for table `manager_sport`
--

DROP TABLE IF EXISTS `manager_sport`;
CREATE TABLE IF NOT EXISTS `manager_sport` (
  `user_id` varchar(50) NOT NULL,
  `sport_id` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`,`sport_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manager_sport`
--

INSERT INTO `manager_sport` (`user_id`, `sport_id`) VALUES
('usr_68f89be0', 'BAD'),
('usr_68f89be0', 'CRI'),
('usr_68f89be0', 'CRM'),
('usr_68f89be0', 'FOO'),
('usr_68f89be0', 'KBD'),
('usr_68f89be0', 'KRT'),
('usr_68f89be0', 'SWI'),
('usr_68f89be0', 'VOL');

-- --------------------------------------------------------

--
-- Table structure for table `match_ball_court`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_board_game`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_combat`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_cricket`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_participant`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_racket`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_team_goal`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_timed`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_weight_lifting`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
('P0001', 'Track & Field and Ground Marking Workshop', 'A workshop on Track & Field and Ground Marking was held on the 30th and 31st of March 2025 at the University of Colombo ground premises. This workshop was conducted by Mr Palitha Jayathilaka, Senior Technical Official at the Sri Lanka Athletic Association, to update our staff members on the new methods and changes in ground marking. Participants who completed this workshop successfully received a valuable certificate.', 'YES', '2025-12-09', 'ACTIVE'),
('P0002', '36th National Rowing Championship', 'University of Colombo rowers won 5 medals at the 36th National Rowing Championship which was held on the 12-13 March 2021 at Diyawannawa Rowing Center. In the Open Category (Women’s), Ms Ranmalee Nanayakkara and Nadani Mendis won the Silver medal in the Open Double scull, Ms Nadani Mendis and Upuli Edirisingha won the bronze medal in the open Pair and Ms Ranmalee Nanayakkara won the bronze medal in the Open Scull category. In the Intermediate Category (Women’s), Ms Himasha Panditharatne and Vibhanga Amarasinghe won the bronze medal in the pair event. In the Intermediate Category (Men’s), Mr Avishka Jayaweera, Mr Shehan Shamalka, Mr Dushyantha Hettiarachchi, Mr Shehan Dinusha Liyanage, Mr Samitha Wijethilake won the Bronze medal in the coxed four events.', 'NO', '2025-12-09', 'ACTIVE'),
('P0003', 'ggghg', 'bnvvhjbjkkbjh', 'YES', '2025-12-18', 'ACTIVE'),
('P0004', 'Test News', 'This is a test news post for admin feature testing purposes.', 'YES', '2025-12-26', 'ACTIVE');

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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsfeed_post_image`
--

INSERT INTO `newsfeed_post_image` (`image_id`, `post_id`, `image_path`) VALUES
(16, 'P0003', 'images/posts/img_6943e8018adf40.57949946.jpg'),
(15, 'P0002', 'images/posts/img_69380b0ce85be5.20787564.jpg'),
(14, 'P0001', 'images/posts/img_6937eab81c8785.14752532.jpg');

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
-- Table structure for table `practice_sessions`
--

DROP TABLE IF EXISTS `practice_sessions`;
CREATE TABLE IF NOT EXISTS `practice_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sport_id` varchar(8) NOT NULL,
  `added_by` varchar(8) NOT NULL,
  `facility` varchar(100) NOT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `status` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `end_time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `need_equipment` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `practice_sessions`
--

INSERT INTO `practice_sessions` (`id`, `sport_id`, `added_by`, `facility`, `session_date`, `start_time`, `notes`, `status`, `created_at`, `updated_at`, `end_time`, `location`, `need_equipment`) VALUES
(17, 'KBD', 'SPT', '', '2026-02-08', '22:24:00', '', 'ACTIVE', '2026-02-08 16:54:31', NULL, '23:24:00', 'Indoor court', 'No'),
(14, 'BAD', 'SPT', '', '2026-02-08', '15:55:00', '', 'ACCEPTED', '2026-01-25 10:24:38', '2026-02-08 14:14:50', '17:54:00', 'Indoor court', 'No'),
(15, 'CRI', 'SPT', '', '2026-01-29', '12:25:00', '', 'PENDING', '2026-01-28 23:57:10', '2026-01-28 23:57:48', '15:25:00', 'Outdoor Field', 'No'),
(16, 'BAD', 'SPT', '', '2026-01-24', '22:45:00', '', 'ACCEPTED', '2026-02-08 14:15:32', '2026-02-08 14:44:37', '22:45:00', 'Indoor court', 'No'),
(9, 'SCR', 'SPT', 'Select the Location', '2026-01-25', '14:30:00', '', 'ACTIVE', '2026-01-25 08:57:16', NULL, '00:00:00', '', ''),
(10, 'KRT', 'SPT', '', '2026-01-09', '17:27:00', '', 'ACTIVE', '2026-01-25 08:57:47', '2026-01-25 10:04:13', '00:00:00', 'Indoor Court', 'No'),
(11, 'CRI', 'SPT', '', '2026-01-25', '14:40:00', '', 'ACCEPTED', '2026-01-25 09:09:46', '2026-01-25 22:06:34', '16:40:00', 'Outdoor Field', 'Yes'),
(13, 'BAD', 'SPT', '', '2026-01-25', '16:50:00', '', 'CANCELED', '2026-01-25 10:21:02', '2026-01-28 23:54:41', '18:50:00', 'Indoor court', 'No'),
(19, 'BAD', 'SPT', '', '2026-02-14', '10:30:00', '', 'PENDING', '2026-02-13 01:54:34', NULL, '11:30:00', 'Outdoor Field', 'Yes');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 0, 'd125df99b6f85a0d3861dc2db2ca31c3a9e4da797d1503cd6dd738381a807173', 1762935327),
(2, 0, '9c004ae75989bbbff72c928244ce76d429b1e46ffc0fa470dac2fd3a3d219228', 1771546465),
(3, 0, '62282fb9e5e5adf4e70978f096f010e611bb2c49336e5d5d4f7a15dde932cbe4', 1771573857),
(4, 0, 'd3ad223373bb77bc07208ecda7efbc4181d8810d89de906d0e7d4098dbb989fe', 1771574720);

-- --------------------------------------------------------

--
-- Table structure for table `saved_emails`
--

DROP TABLE IF EXISTS `saved_emails`;
CREATE TABLE IF NOT EXISTS `saved_emails` (
  `email` varchar(64) NOT NULL,
  `recepient_name` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `saved_emails`
--

INSERT INTO `saved_emails` (`email`, `recepient_name`) VALUES
('sports@usj.ac.lk', 'USJ');

-- --------------------------------------------------------

--
-- Table structure for table `sport`
--

DROP TABLE IF EXISTS `sport`;
CREATE TABLE IF NOT EXISTS `sport` (
  `sport_id` varchar(4) NOT NULL,
  `sport_name` varchar(24) NOT NULL,
  `sport_category` enum('TEAM_GOAL','RACKET','CRICKET','COMBAT','TRACK_FIELD','BOARD_GAME','BALL_COURT','WEIGHT') NOT NULL,
  `coach_id` varchar(12) NOT NULL,
  `captain_id` varchar(12) NOT NULL,
  `manager_id` varchar(12) NOT NULL,
  PRIMARY KEY (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport`
--

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
('ROW', 'Rowing', 'TRACK_FIELD', '', '', ''),
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

-- --------------------------------------------------------

--
-- Table structure for table `sports-team`
--

DROP TABLE IF EXISTS `sports-team`;
CREATE TABLE IF NOT EXISTS `sports-team` (
  `sport_id` varchar(12) NOT NULL,
  `student_id` varchar(12) NOT NULL,
  `joined_date` date NOT NULL,
  `in_team` varchar(7) NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`sport_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sports-team`
--

INSERT INTO `sports-team` (`sport_id`, `student_id`, `joined_date`, `in_team`) VALUES
('TKD', 'L3NCL2J4', '2025-12-03', 'NO'),
('ATH', 'L3NCL2J4', '2025-12-09', 'NO'),
('VOL', '5Q1XZO2Y', '2025-10-25', 'NO'),
('ATH', '5Q1XZO2Y', '2025-12-11', 'NO'),
('VOL', 'L3NCL2J4', '2025-12-15', 'NO'),
('BAS', 'STU005', '2026-01-04', 'NO');

-- --------------------------------------------------------

--
-- Table structure for table `sport_expenses`
--

DROP TABLE IF EXISTS `sport_expenses`;
CREATE TABLE IF NOT EXISTS `sport_expenses` (
  `expense_id` int NOT NULL AUTO_INCREMENT,
  `sport` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `expense_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sport_expenses`
--

INSERT INTO `sport_expenses` (`expense_id`, `sport`, `expense_title`, `amount`, `receipt`, `submitted_by`, `notes`, `expense_date`, `created_at`, `updated_at`) VALUES
(6, 'Cricket', 'test 1', 1200.00, '1769552497_Test 2.pdf', 'J', '', '2026-01-27 03:51:37', '2026-01-27 22:21:37', '2026-01-27 22:23:23'),
(7, 'Cricket', 'Test 2', 1300.00, '1769552523_Test 2.pdf', 'J', '', '2026-01-26 03:52:03', '2026-01-27 22:22:03', '2026-01-27 22:23:51');

-- --------------------------------------------------------

--
-- Table structure for table `sport_manager`
--

DROP TABLE IF EXISTS `sport_manager`;
CREATE TABLE IF NOT EXISTS `sport_manager` (
  `manager_id` varchar(20) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`manager_id`),
  KEY `fname` (`fname`),
  KEY `lname` (`lname`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport_manager`
--

INSERT INTO `sport_manager` (`manager_id`, `fname`, `lname`, `email`) VALUES
('usr_68f89be0', 'J', 'Jaye', '2023is043@stu.ucsc.cmb.ac.lk');

-- --------------------------------------------------------

--
-- Table structure for table `student_id_cards`
--

DROP TABLE IF EXISTS `student_id_cards`;
CREATE TABLE IF NOT EXISTS `student_id_cards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(12) NOT NULL COMMENT 'References user.student_id',
  `image_name` varchar(128) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `tournament_id` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tournament_name` varchar(64) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'INCOMPLETE',
  PRIMARY KEY (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`tournament_id`, `tournament_name`, `sport_id`, `start_date`, `end_date`, `status`) VALUES
('TOUR_693ea72aa6387', 'Vice Chancellors Invitational Badminton Championship', 'BAD', '2026-01-01', '2026-02-26', 'INCOMPLETE'),
('TOUR_694cd4c59abad', 'This is an sport event', 'KRT', '2026-02-01', '2026-12-01', 'INCOMPLETE');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_match`
--

DROP TABLE IF EXISTS `tournament_match`;
CREATE TABLE IF NOT EXISTS `tournament_match` (
  `match_id` varchar(50) NOT NULL,
  `tournament_id` varchar(24) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `sport_category` enum('TEAM_GOAL','RACKET','CRICKET','COMBAT','TRACK_FIELD','BOARD_GAME','BALL_COURT','WEIGHT') NOT NULL,
  `match_name` varchar(100) NOT NULL,
  `match_date` date NOT NULL,
  `winner_id` varchar(12) DEFAULT NULL,
  `result_status` enum('COMPLETED','CANCELLED','DRAW','PENDING','NO_RESULT') DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`match_id`),
  KEY `tournament_id` (`tournament_id`),
  KEY `sport_id` (`sport_id`),
  KEY `sport_category` (`sport_category`),
  KEY `winner_id` (`winner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `faculty_id` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
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
('NPM8O9RE', 'Chamal', 'Chamuditha', 'COACH', 'chamal1@gmail.com', '$2y$10$8trbPHuAHueKspCIvyWQyudyy97asXBJzzKTvWW7bXgSeoLdq2aku', 0, '2025-09-01 22:53:08', NULL, '', '', NULL, '', 'ACTIVE'),
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
('H4J1OHSX', 'Chamal', 'Chamuditha', 'ADMIN', 'chamal.admin@uocs.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 02:04:39', NULL, 'H4J1OHSX.png', '', NULL, '', 'ACTIVE'),
('usr_694d89fa', 'Amal', 'Shantha', 'SPT', 'chamlaanil99@gmail.com', '$2y$10$5GAnmRD7UblTn9Q3TQOXCeWwslM.A8uoZdXe1EDU9g32gc7tBem3S', 1, '2025-12-25 19:01:15', '0716379044', '', 'KBD', NULL, NULL, 'ACTIVE'),
('L3NCL2J4', 'Chamal', 'Hettiarachchi', 'STUDENT', 'chamal2@gmail.com', '$2y$10$zlUHk9p5y7uAz7u2jQQ0X.PNaxkDwan5JIDlR/jySjsAgtcutfqpm', 0, '2025-10-14 04:48:58', NULL, 'L3NCL2J4.jpg', 'BAD', '23000000', '', 'ACTIVE'),
('usr_68f82fe0', 'Shashini', 'Malsha', 'EQP', 'ccwrecker99@gmail.com', '$2y$10$0Tn8wECDAB8QNE6PwnexKeewRZA2GHwtm9Ljpx5USTm2LKEjsvL6W', 1, '2025-10-22 01:14:08', '076543213', '', '', NULL, '', 'ACTIVE'),
('usr_68f89998', 'Jaye', 'Jayaweera', 'EQP', 'jayashinisjayaweera@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 08:45:12', '0763452143', '', '', NULL, '', 'ACTIVE'),
('usr_68f89be0', 'J', 'Jaye', 'SPT', '2023is043@stu.ucsc.cmb.ac.lk', '$2y$10$0Z1ZoYUII3O2MDC3ltxdku2r3ROkBM.swVOJs88JYaG4fsZCyFy2W', 1, '2025-10-22 08:54:56', '0763452145', '', 'CRI', NULL, '', 'ACTIVE'),
('FMX6Z8DF', 'Shashini', 'Malsha', 'STUDENT', 'shashini@gmail.com', '$2y$10$PUWxFaoItXKbGY/52bG/vebAWPEQyHc39o5nwtTb2iPoZ6zpAd0rq', 0, '2025-10-23 07:10:43', NULL, '', 'CRI', '23020997', '', 'ACTIVE'),
('5Q1XZO2Y', 'Jansika', 'Balakrishnan', 'CAPTAIN', 'jansi@gmail.com', '$2y$10$50U4SKStJpeogM4DSK5r2OnQO041WacupfYjfsX3w1B18UtX6RvCy', 0, '2025-10-23 07:23:06', NULL, '5Q1XZO2Y.jpg', '', '23020342', '', 'ACTIVE'),
('STU001', 'Ashan', 'Fernando', 'STUDENT', 'ashan.fernando@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0771234567', '', '', '23001001', '1', 'ACTIVE'),
('STU002', 'Nimali', 'Perera', 'STUDENT', 'nimali.perera@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0772345678', '', '', '23001002', '1', 'ACTIVE'),
('REG003', 'Kasun', 'Silva', 'REG', 'kasun.silva@ucsc.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '0773456789', '', '', '23001003', '1', 'ACTIVE'),
('SPT004', 'Dilini', 'Jayasinghe', 'SPT', 'dilini.jayasinghe@uoc.lk', '$2y$10$zlUHk9p5y7uAz7u2jQQ0X.PNaxkDwan5JIDlR/jySjsAgtcutfqpm', 0, '2025-12-10 17:47:24', '0774567890', '', '', '23001004', '1', 'ACTIVE'),
('STU005', 'Tharindu', 'Wickramasinghe', 'STUDENT', 'tharindu.wickramasinghe@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0775678901', '', '', '23001005', '1', 'ACTIVE'),
('STU006', 'Sanduni', 'Rathnayake', 'STUDENT', 'sanduni.rathnayake@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0776789012', '', '', '23001006', '1', 'ACTIVE'),
('STU007', 'Ravindu', 'Dissanayake', 'STUDENT', 'ravindu.dissanayake@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0777890123', '', '', '23001007', '1', 'ACTIVE'),
('STU008', 'Ishara', 'Gunasekara', 'STUDENT', 'ishara.gunasekara@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0778901234', '', '', '23001008', '1', 'ACTIVE'),
('STU009', 'Dineth', 'Amarasinghe', 'STUDENT', 'dineth.amarasinghe@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0779012345', '', 'CRI', '23001009', '1', 'ACTIVE'),
('STU010', 'Sachini', 'Wijewardena', 'STUDENT', 'sachini.wijewardena@student.uoc.lk', '$2y$10$xCPw7W7/c0MvcP6jqG/fxee6tOWfcnP9eNa.ht9aymTKeKei4prui', 0, '2025-12-10 17:47:24', '0770123456', '', 'CRI', '23001010', '1', 'ACTIVE');

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `add_sport_manager`;
DELIMITER $$
CREATE TRIGGER `add_sport_manager` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.type = 'SPT' THEN
        INSERT INTO sport_manager (manager_id, fname, lname, email)
        VALUES (NEW.user_id, NEW.fname, NEW.lname, NEW.email);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

DROP TABLE IF EXISTS `user_points`;
CREATE TABLE IF NOT EXISTS `user_points` (
  `user_id` varchar(12) NOT NULL,
  `user_points` int DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`user_id`, `user_points`) VALUES
('STU010', 17),
('STU009', 0);

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_request_students`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
