-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 20, 2026 at 04:46 PM
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
-- Table structure for table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
CREATE TABLE IF NOT EXISTS `achievement` (
  `achievement_id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(12) DEFAULT NULL,
  `sport_id` varchar(10) DEFAULT NULL,
  `tournament_id` varchar(20) DEFAULT NULL,
  `achievement` varchar(50) DEFAULT NULL,
  `points` int DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`achievement_id`),
  KEY `fk_achievement_user` (`user_id`),
  KEY `fk_achievement_tournament` (`tournament_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `achievement`
--

INSERT INTO `achievement` (`achievement_id`, `user_id`, `sport_id`, `tournament_id`, `achievement`, `points`, `status`) VALUES
(1, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(2, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(3, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(4, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(5, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(6, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(7, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(8, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(9, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(10, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(11, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(12, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(13, 'STU_VW_002', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(14, 'STU_VW_002', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(15, 'L3NCL2J4', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(16, 'L3NCL2J4', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(17, 'STU_VW_005', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(18, 'STU_VW_005', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(19, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(20, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(21, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(22, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(23, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(24, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(25, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(26, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(27, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(28, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(29, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(30, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(31, 'STU_VW_002', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(32, 'STU_VW_002', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(33, 'L3NCL2J4', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(34, 'L3NCL2J4', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(35, 'STU_VW_005', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(36, 'STU_VW_005', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(37, 'STU_VW_007', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(38, 'STU_VW_007', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(39, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(40, 'STU_VW_009', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(41, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(42, '5Q1XZO2Y', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(43, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(44, 'STU_VW_011', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(45, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(46, 'STU_VW_012', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(47, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(48, 'STU_VW_017', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE'),
(49, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Participant', 1, 'ACTIVE'),
(50, 'STU_VW_018', 'VOL', 'TOUR_69d739fdd3a3b', 'Match Winner', 2, 'ACTIVE');

--
-- Triggers `achievement`
--
DROP TRIGGER IF EXISTS `trg_assign_points`;
DELIMITER $$
CREATE TRIGGER `trg_assign_points` BEFORE INSERT ON `achievement` FOR EACH ROW BEGIN
    -- Only override points if they haven't been explicitly set (i.e. are 0 or NULL)
    IF NEW.points IS NULL OR NEW.points = 0 THEN
        IF NEW.achievement = 'Participant' THEN
            SET NEW.points = 1;
        ELSEIF NEW.achievement = 'Match Winner' THEN
            SET NEW.points = 2;
        ELSEIF NEW.achievement LIKE 'Best %' OR NEW.achievement LIKE 'Golden %' OR NEW.achievement LIKE 'Man of %' OR NEW.achievement LIKE 'Most %' OR NEW.achievement = 'MVP' THEN
            -- Points depend on match level; default to 3 for university
            SET NEW.points = 3;
        ELSEIF NEW.achievement = '1st place' THEN
            SET NEW.points = 5;
        ELSEIF NEW.achievement = '2nd place' THEN
            SET NEW.points = 3;
        ELSEIF NEW.achievement = '3rd place' THEN
            SET NEW.points = 2;
        ELSEIF NEW.achievement = '4th place' THEN
            SET NEW.points = 1;
        ELSE
            SET NEW.points = 1;
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_update_student_points_insert`;
DELIMITER $$
CREATE TRIGGER `trg_update_student_points_insert` AFTER INSERT ON `achievement` FOR EACH ROW BEGIN
    DECLARE total_points INT;
    SELECT SUM(points)
    INTO total_points
    FROM achievement
    WHERE user_id = NEW.user_id AND status = 'ACTIVE';
    INSERT INTO user_points (user_id, user_points)
    VALUES (NEW.user_id, total_points)
    ON DUPLICATE KEY UPDATE
        user_points = total_points;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_update_student_points_update`;
DELIMITER $$
CREATE TRIGGER `trg_update_student_points_update` AFTER UPDATE ON `achievement` FOR EACH ROW BEGIN
    DECLARE total_points INT;
    IF OLD.status != NEW.status OR OLD.points != NEW.points THEN
        SELECT SUM(points)
        INTO total_points
        FROM achievement
        WHERE user_id = NEW.user_id AND status = 'ACTIVE';
        INSERT INTO user_points (user_id, user_points)
        VALUES (NEW.user_id, COALESCE(total_points, 0))
        ON DUPLICATE KEY UPDATE
            user_points = COALESCE(total_points, 0);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `active_booking_attempts`
--

DROP TABLE IF EXISTS `active_booking_attempts`;
CREATE TABLE IF NOT EXISTS `active_booking_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `facility_id` int NOT NULL,
  `date` date NOT NULL,
  `last_active_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`facility_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `record_status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`attendance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `practice_id`, `user_id`, `status`, `record_status`) VALUES
('ATD01A7354DA', 11, 'STU_VW_004', 'PRESENT', 'ACTIVE'),
('ATD02980F47C', 23, 'STU_VW_009', 'ABSENT', 'SUPERSEDED'),
('ATD04CC0BF1E', 23, 'STU_VW_005', 'PRESENT', 'SUPERSEDED'),
('ATD05184C89F', 20, 'STU_VW_008', 'PRESENT', 'ACTIVE'),
('ATD062361BE1', 20, 'STU005', 'ABSENT', 'SUPERSEDED'),
('ATD0793C190C', 23, 'STU_VW_003', 'ABSENT', 'SUPERSEDED'),
('ATD07C0133A0', 20, 'STU_VW_009', 'ABSENT', 'SUPERSEDED'),
('ATD084AA4818', 21, 'STU_VW_006', 'ABSENT', 'ACTIVE'),
('ATD0AECED3AE', 21, 'STU_VW_009', 'ABSENT', 'ACTIVE'),
('ATD0DFFD6163', 23, 'STU_VW_016', 'PRESENT', 'SUPERSEDED'),
('ATD0E520D907', 11, 'STU_VW_010', 'ABSENT', 'ACTIVE'),
('ATD0E558C497', 23, 'STU_VW_013', 'PRESENT', 'SUPERSEDED'),
('ATD0E959A835', 23, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATD0F8A0943E', 23, 'STU_VW_001', 'PRESENT', 'SUPERSEDED'),
('ATD0FC63FD1A', 23, 'STU_VW_001', 'PRESENT', 'SUPERSEDED'),
('ATD12383F99F', 23, 'STU_VW_018', 'PRESENT', 'SUPERSEDED'),
('ATD1390DA281', 23, 'STU_VW_011', 'PRESENT', 'SUPERSEDED'),
('ATD163A86427', 11, 'STU_VW_016', 'ABSENT', 'ACTIVE'),
('ATD16507D601', 8, '5Q1XZO2Y', 'PRESENT', 'ACTIVE'),
('ATD166E27EF6', 23, 'STU_VW_007', 'ABSENT', 'SUPERSEDED'),
('ATD17D25C595', 22, 'STU_VW_010', 'PRESENT', 'SUPERSEDED'),
('ATD17F8F6885', 20, 'STU_VW_020', 'PRESENT', 'ACTIVE'),
('ATD18564AD00', 23, 'STU_VW_005', 'ABSENT', 'ACTIVE'),
('ATD19CB11DFE', 20, 'STU_VW_010', 'PRESENT', 'ACTIVE'),
('ATD1B023D337', 23, 'STU_VW_017', 'PRESENT', 'SUPERSEDED'),
('ATD1B75F80A4', 23, 'STU_VW_002', 'ABSENT', 'SUPERSEDED'),
('ATD1C763E3E4', 23, 'STU_VW_017', 'PRESENT', 'ACTIVE'),
('ATD1D0E78DE4', 21, 'STU005', 'PRESENT', 'ACTIVE'),
('ATD1F2039229', 22, 'STU_VW_012', 'PRESENT', 'ACTIVE'),
('ATD22BD69B36', 20, 'STU_VW_004', 'PRESENT', 'SUPERSEDED'),
('ATD22D2C833A', 23, 'STU_VW_017', 'PRESENT', 'SUPERSEDED'),
('ATD247A22387', 23, 'STU_VW_008', 'PRESENT', 'ACTIVE'),
('ATD2495B773D', 23, 'STU_VW_017', 'PRESENT', 'SUPERSEDED'),
('ATD24A310A3D', 22, 'STU005', 'PRESENT', 'ACTIVE'),
('ATD26405E7DC', 22, 'STU_VW_008', 'PRESENT', 'SUPERSEDED'),
('ATD26C99FAD9', 23, 'STU_VW_019', 'PRESENT', 'SUPERSEDED'),
('ATD29272CE3F', 21, 'STU_VW_002', 'PRESENT', 'ACTIVE'),
('ATD2A4AD239A', 11, 'STU_VW_013', 'ABSENT', 'ACTIVE'),
('ATD2AD3B6B11', 20, 'STU_VW_014', 'PRESENT', 'ACTIVE'),
('ATD2AF813443', 22, 'STU_VW_001', 'PRESENT', 'ACTIVE'),
('ATD2BE09FEE5', 22, 'STU_VW_020', 'PRESENT', 'ACTIVE'),
('ATD2BFB20430', 20, 'STU_VW_001', 'PRESENT', 'ACTIVE'),
('ATD2CB56C357', 21, 'STU_VW_001', 'PRESENT', 'ACTIVE'),
('ATD2E310FC38', 22, 'STU_VW_006', 'PRESENT', 'SUPERSEDED'),
('ATD2E6A84FA0', 22, 'STU_VW_017', 'PRESENT', 'ACTIVE'),
('ATD3049ADBD6', 22, 'STU_VW_011', 'ABSENT', 'ACTIVE'),
('ATD310DDA53F', 20, 'STU_VW_016', 'PRESENT', 'SUPERSEDED'),
('ATD31352BAB8', 23, 'STU_VW_016', 'PRESENT', 'ACTIVE'),
('ATD316E69D15', 11, 'STU_VW_006', 'PRESENT', 'ACTIVE'),
('ATD31E5790FF', 21, 'STU_VW_018', 'PRESENT', 'ACTIVE'),
('ATD32B49AE72', 11, 'STU_VW_003', 'ABSENT', 'ACTIVE'),
('ATD3361198A6', 20, 'STU_VW_007', 'ABSENT', 'ACTIVE'),
('ATD345E31E3B', 21, '5Q1XZO2Y', 'ABSENT', 'ACTIVE'),
('ATD3534C6FAC', 23, 'STU_VW_003', 'ABSENT', 'ACTIVE'),
('ATD35373CF9F', 11, 'STU_VW_014', 'ABSENT', 'ACTIVE'),
('ATD36A6F30B7', 20, 'STU_VW_004', 'PRESENT', 'ACTIVE'),
('ATD36E19BE72', 23, 'STU_VW_014', 'PRESENT', 'ACTIVE'),
('ATD397460F95', 22, 'STU_VW_004', 'PRESENT', 'ACTIVE'),
('ATD3984BB84A', 23, 'STU005', 'PRESENT', 'ACTIVE'),
('ATD3ABC14430', 23, 'STU_VW_020', 'PRESENT', 'SUPERSEDED'),
('ATD3B0A4BE96', 23, 'STU_VW_007', 'ABSENT', 'ACTIVE'),
('ATD3B3BD1819', 22, 'STU_VW_017', 'PRESENT', 'SUPERSEDED'),
('ATD3CF5ED4CE', 23, 'STU_VW_006', 'PRESENT', 'SUPERSEDED'),
('ATD3EAA87977', 20, 'STU_VW_003', 'ABSENT', 'SUPERSEDED'),
('ATD40FCD6F57', 21, 'STU_VW_016', 'ABSENT', 'ACTIVE'),
('ATD415C6178A', 20, 'STU_VW_020', 'PRESENT', 'SUPERSEDED'),
('ATD416C2624A', 23, 'STU_VW_015', 'PRESENT', 'ACTIVE'),
('ATD416D3851E', 23, 'STU_VW_012', 'ABSENT', 'SUPERSEDED'),
('ATD41C051CEA', 11, 'STU_VW_012', 'PRESENT', 'ACTIVE'),
('ATD41DF13FB8', 20, 'STU_VW_003', 'ABSENT', 'ACTIVE'),
('ATD43F02CE1D', 23, 'STU_VW_008', 'PRESENT', 'SUPERSEDED'),
('ATD480B262C9', 22, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATD498FDE6CC', 20, 'STU_VW_013', 'PRESENT', 'SUPERSEDED'),
('ATD49B1274AD', 22, 'STU_VW_002', 'PRESENT', 'ACTIVE'),
('ATD4BB97142A', 11, 'STU_VW_020', 'PRESENT', 'ACTIVE'),
('ATD4BF16EC17', 23, 'STU_VW_018', 'PRESENT', 'SUPERSEDED'),
('ATD4CEE71D8A', 20, 'STU_VW_002', 'ABSENT', 'SUPERSEDED'),
('ATD4EEF2638F', 20, 'STU_VW_019', 'PRESENT', 'ACTIVE'),
('ATD503DF01F1', 23, 'STU_VW_004', 'PRESENT', 'ACTIVE'),
('ATD507248FFA', 23, 'STU_VW_013', 'PRESENT', 'ACTIVE'),
('ATD52082D369', 23, 'STU_VW_015', 'PRESENT', 'SUPERSEDED'),
('ATD530B04D54', 20, 'STU_VW_011', 'PRESENT', 'SUPERSEDED'),
('ATD53492DFE7', 23, 'STU_VW_009', 'ABSENT', 'ACTIVE'),
('ATD541BC01CC', 11, 'STU_VW_002', 'PRESENT', 'ACTIVE'),
('ATD571C09FA6', 11, 'STU_VW_011', 'PRESENT', 'ACTIVE'),
('ATD57C65DFB3', 22, 'STU_VW_011', 'PRESENT', 'SUPERSEDED'),
('ATD58D98A5A6', 22, '5Q1XZO2Y', 'ABSENT', 'ACTIVE'),
('ATD590449816', 22, 'STU_VW_013', 'PRESENT', 'ACTIVE'),
('ATD59A1932AF', 23, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATD5A0FB055E', 23, 'STU_VW_010', 'PRESENT', 'SUPERSEDED'),
('ATD5AE4D241D', 22, 'STU_VW_008', 'PRESENT', 'ACTIVE'),
('ATD5C0EA1CC5', 22, 'STU_VW_016', 'PRESENT', 'SUPERSEDED'),
('ATD5CD71D2CC', 20, 'STU_VW_015', 'PRESENT', 'SUPERSEDED'),
('ATD5D444F620', 23, 'STU_VW_006', 'PRESENT', 'ACTIVE'),
('ATD5D7BBCAF9', 23, 'STU_VW_004', 'PRESENT', 'SUPERSEDED'),
('ATD5E6A12C74', 11, 'STU_VW_007', 'ABSENT', 'ACTIVE'),
('ATD5EC50C78E', 21, 'STU_VW_011', 'PRESENT', 'ACTIVE'),
('ATD6082B39AE', 23, 'STU_VW_004', 'PRESENT', 'SUPERSEDED'),
('ATD60DDE7030', 22, 'STU_VW_019', 'PRESENT', 'SUPERSEDED'),
('ATD63F98EEBF', 21, 'STU_VW_019', 'ABSENT', 'ACTIVE'),
('ATD6532F7E8A', 11, 'STU_VW_009', 'PRESENT', 'ACTIVE'),
('ATD663830467', 11, 'STU_VW_001', 'PRESENT', 'ACTIVE'),
('ATD6675BA724', 20, 'STU_VW_009', 'ABSENT', 'ACTIVE'),
('ATD66A746562', 22, 'STU_VW_007', 'PRESENT', 'ACTIVE'),
('ATD682D891EB', 23, 'STU_VW_007', 'ABSENT', 'SUPERSEDED'),
('ATD69BF75935', 23, 'STU005', 'ABSENT', 'SUPERSEDED'),
('ATD6DAFCFD3B', 20, 'STU_VW_005', 'PRESENT', 'SUPERSEDED'),
('ATD6F4DFD335', 23, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATD71B102AF1', 23, '5Q1XZO2Y', 'PRESENT', 'ACTIVE'),
('ATD71DDD0CE3', 22, 'STU_VW_014', 'PRESENT', 'SUPERSEDED'),
('ATD7287F1A33', 23, 'STU_VW_012', 'ABSENT', 'SUPERSEDED'),
('ATD72F8ABC76', 22, 'STU_VW_003', 'PRESENT', 'SUPERSEDED'),
('ATD7351E8A29', 23, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATD73D86DF61', 22, 'STU_VW_003', 'PRESENT', 'ACTIVE'),
('ATD75204E3EE', 21, 'STU_VW_003', 'ABSENT', 'ACTIVE'),
('ATD752B9E301', 22, 'STU_VW_005', 'PRESENT', 'ACTIVE'),
('ATD75806BBBE', 23, 'STU_VW_010', 'PRESENT', 'SUPERSEDED'),
('ATD776984D52', 23, 'STU_VW_019', 'PRESENT', 'SUPERSEDED'),
('ATD788D3CAEE', 23, 'STU_VW_014', 'PRESENT', 'SUPERSEDED'),
('ATD7C4AB867E', 22, 'STU_VW_004', 'PRESENT', 'SUPERSEDED'),
('ATD7D3FC9E5D', 22, 'STU_VW_009', 'PRESENT', 'SUPERSEDED'),
('ATD7E6C6595C', 23, 'STU_VW_008', 'PRESENT', 'SUPERSEDED'),
('ATD7F8D826DE', 20, 'STU_VW_005', 'PRESENT', 'ACTIVE'),
('ATD7F9530C70', 23, 'STU_VW_014', 'PRESENT', 'SUPERSEDED'),
('ATD805D603F7', 11, 'STU_VW_019', 'PRESENT', 'ACTIVE'),
('ATD833F56EB2', 23, 'STU_VW_001', 'PRESENT', 'SUPERSEDED'),
('ATD835CAC21E', 23, 'STU_VW_019', 'PRESENT', 'ACTIVE'),
('ATD838D65388', 21, 'STU_VW_013', 'PRESENT', 'ACTIVE'),
('ATD844AC646B', 22, 'STU_VW_014', 'PRESENT', 'ACTIVE'),
('ATD84CC456BD', 23, 'STU_VW_018', 'PRESENT', 'SUPERSEDED'),
('ATD858AC6148', 23, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATD85B759C2A', 22, 'STU_VW_016', 'PRESENT', 'ACTIVE'),
('ATD88807A88C', 23, 'STU_VW_010', 'PRESENT', 'ACTIVE'),
('ATD8926EA135', 23, 'STU_VW_005', 'PRESENT', 'SUPERSEDED'),
('ATD8A42AD585', 23, 'STU_VW_014', 'PRESENT', 'SUPERSEDED'),
('ATD8AE9AC6A1', 20, 'STU_VW_013', 'PRESENT', 'ACTIVE'),
('ATD8B3C90F6F', 20, 'STU_VW_006', 'PRESENT', 'ACTIVE'),
('ATD8C9C72AF2', 11, 'STU005', 'PRESENT', 'ACTIVE'),
('ATD8CBBE680D', 22, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATD8D7E34CA9', 23, 'STU_VW_003', 'ABSENT', 'SUPERSEDED'),
('ATD8ECA857F8', 21, 'L3NCL2J4', 'PRESENT', 'ACTIVE'),
('ATD8F27FA054', 23, 'STU_VW_011', 'ABSENT', 'SUPERSEDED'),
('ATD8F4242ED0', 23, 'STU_VW_020', 'PRESENT', 'SUPERSEDED'),
('ATD94D25D7B5', 20, '5Q1XZO2Y', 'PRESENT', 'ACTIVE'),
('ATD959AC3D4E', 23, 'STU_VW_008', 'PRESENT', 'SUPERSEDED'),
('ATD95A05ABAF', 20, 'STU_VW_017', 'PRESENT', 'ACTIVE'),
('ATD9616CBDB3', 20, 'STU_VW_011', 'PRESENT', 'ACTIVE'),
('ATD968CB297C', 23, 'STU_VW_012', 'ABSENT', 'SUPERSEDED'),
('ATD987154B17', 23, 'L3NCL2J4', 'PRESENT', 'ACTIVE'),
('ATD999C46461', 22, 'STU_VW_015', 'PRESENT', 'SUPERSEDED'),
('ATD9A3D87E83', 20, 'STU_VW_017', 'PRESENT', 'SUPERSEDED'),
('ATD9CC6B81FB', 20, 'STU_VW_008', 'PRESENT', 'SUPERSEDED'),
('ATD9D4B6EC4B', 11, 'STU_VW_015', 'PRESENT', 'ACTIVE'),
('ATDA1FF184CC', 23, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATDA26D398A7', 21, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATDA5637C6F3', 20, '5Q1XZO2Y', 'PRESENT', 'SUPERSEDED'),
('ATDA625BB50A', 22, 'STU_VW_001', 'PRESENT', 'SUPERSEDED'),
('ATDA65F152DE', 21, 'STU_VW_012', 'PRESENT', 'ACTIVE'),
('ATDAADCBB728', 23, 'STU_VW_015', 'PRESENT', 'SUPERSEDED'),
('ATDAB90F1050', 20, 'STU_VW_012', 'PRESENT', 'ACTIVE'),
('ATDAE2386777', 23, 'STU_VW_002', 'ABSENT', 'SUPERSEDED'),
('ATDAE3EBE161', 20, 'STU_VW_018', 'PRESENT', 'SUPERSEDED'),
('ATDAF74E5A0E', 22, 'STU_VW_018', 'PRESENT', 'SUPERSEDED'),
('ATDB01577248', 23, 'STU_VW_004', 'PRESENT', 'SUPERSEDED'),
('ATDB0B1A76D0', 20, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATDB0FDC32D0', 23, 'STU_VW_018', 'PRESENT', 'ACTIVE'),
('ATDB47273E84', 20, 'STU_VW_002', 'ABSENT', 'ACTIVE'),
('ATDB5D5A3E06', 23, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATDB633FE9E8', 21, 'STU_VW_008', 'PRESENT', 'ACTIVE'),
('ATDB68D5F4CD', 23, 'STU_VW_013', 'PRESENT', 'SUPERSEDED'),
('ATDB6F24494A', 23, 'STU_VW_009', 'ABSENT', 'SUPERSEDED'),
('ATDB8B6E415C', 21, 'STU_VW_005', 'ABSENT', 'ACTIVE'),
('ATDB90CBD02B', 21, 'STU_VW_020', 'PRESENT', 'ACTIVE'),
('ATDB9B8D519F', 22, 'STU005', 'PRESENT', 'SUPERSEDED'),
('ATDBB177A73D', 20, 'STU_VW_014', 'PRESENT', 'SUPERSEDED'),
('ATDBCBDFA197', 11, 'STU_VW_008', 'PRESENT', 'ACTIVE'),
('ATDBD7B5A9EC', 23, 'STU_VW_016', 'PRESENT', 'SUPERSEDED'),
('ATDBDDEDF109', 22, 'L3NCL2J4', 'PRESENT', 'ACTIVE'),
('ATDBFE87D7B8', 23, 'STU_VW_001', 'PRESENT', 'ACTIVE'),
('ATDC0FECE199', 20, 'STU_VW_006', 'PRESENT', 'SUPERSEDED'),
('ATDC1C3230BC', 20, 'STU005', 'ABSENT', 'ACTIVE'),
('ATDC2288A24C', 22, 'STU_VW_006', 'PRESENT', 'ACTIVE'),
('ATDC264F8DB0', 20, 'STU_VW_001', 'PRESENT', 'SUPERSEDED'),
('ATDC31DAE159', 23, 'STU005', 'PRESENT', 'SUPERSEDED'),
('ATDC589F8EEC', 23, 'STU_VW_013', 'PRESENT', 'SUPERSEDED'),
('ATDC66E82A92', 23, 'STU_VW_006', 'PRESENT', 'SUPERSEDED'),
('ATDC67653C8A', 22, 'STU_VW_002', 'PRESENT', 'SUPERSEDED'),
('ATDC69214205', 22, 'STU_VW_019', 'PRESENT', 'ACTIVE'),
('ATDC7EF76F6A', 22, 'STU_VW_010', 'PRESENT', 'ACTIVE'),
('ATDCA45A0CB7', 23, 'L3NCL2J4', 'PRESENT', 'SUPERSEDED'),
('ATDCD4C50736', 11, 'STU_VW_017', 'PRESENT', 'ACTIVE'),
('ATDCE7B802B1', 23, 'STU_VW_005', 'PRESENT', 'SUPERSEDED'),
('ATDCF95FDEBE', 20, 'L3NCL2J4', 'PRESENT', 'ACTIVE'),
('ATDD0F73834C', 21, 'STU_VW_015', 'PRESENT', 'ACTIVE'),
('ATDD1BF1F156', 22, 'STU_VW_007', 'PRESENT', 'SUPERSEDED'),
('ATDD714522D5', 11, 'STU_VW_005', 'PRESENT', 'ACTIVE'),
('ATDD72B0F480', 23, 'STU005', 'ABSENT', 'SUPERSEDED'),
('ATDD7361851C', 23, 'STU_VW_020', 'PRESENT', 'SUPERSEDED'),
('ATDD9CD427EF', 20, 'STU_VW_012', 'ABSENT', 'SUPERSEDED'),
('ATDDA9840879', 11, 'STU_VW_018', 'ABSENT', 'ACTIVE'),
('ATDDB3452AE4', 22, 'STU_VW_015', 'PRESENT', 'ACTIVE'),
('ATDDC76CDC67', 11, 'L3NCL2J4', 'ABSENT', 'ACTIVE'),
('ATDDC9E995D7', 23, 'STU_VW_002', 'ABSENT', 'ACTIVE'),
('ATDDDBED98C9', 23, 'STU_VW_019', 'PRESENT', 'SUPERSEDED'),
('ATDDEB0AFE97', 20, 'STU_VW_015', 'PRESENT', 'ACTIVE'),
('ATDDECFB6B0F', 20, 'STU_VW_018', 'PRESENT', 'ACTIVE'),
('ATDDF4EFC882', 21, 'STU_VW_004', 'PRESENT', 'ACTIVE'),
('ATDE0F3316E4', 23, 'STU005', 'ABSENT', 'SUPERSEDED'),
('ATDE2A94E9F8', 21, 'STU_VW_007', 'PRESENT', 'ACTIVE'),
('ATDE3D529DCD', 21, 'STU_VW_014', 'PRESENT', 'ACTIVE'),
('ATDE3FEA8C93', 20, 'STU_VW_019', 'PRESENT', 'SUPERSEDED'),
('ATDE83961A38', 23, 'STU_VW_015', 'PRESENT', 'SUPERSEDED'),
('ATDEAF4E6155', 21, 'STU_VW_017', 'PRESENT', 'ACTIVE'),
('ATDEC283DB44', 22, 'STU_VW_018', 'ABSENT', 'ACTIVE'),
('ATDED699FDB2', 23, 'STU_VW_016', 'PRESENT', 'SUPERSEDED'),
('ATDEDE28D1C5', 22, 'STU_VW_013', 'PRESENT', 'SUPERSEDED'),
('ATDEF37D16A7', 20, 'STU_VW_016', 'PRESENT', 'ACTIVE'),
('ATDEF8C7835A', 23, 'STU_VW_003', 'ABSENT', 'SUPERSEDED'),
('ATDF0B4CF7F9', 22, 'STU_VW_020', 'PRESENT', 'SUPERSEDED'),
('ATDF1644D27C', 23, 'STU_VW_007', 'ABSENT', 'SUPERSEDED'),
('ATDF28C45018', 21, 'STU_VW_010', 'ABSENT', 'ACTIVE'),
('ATDF30919394', 23, 'STU_VW_009', 'ABSENT', 'SUPERSEDED'),
('ATDF419C4F5E', 22, 'STU_VW_012', 'PRESENT', 'SUPERSEDED'),
('ATDF57C10143', 22, 'STU_VW_009', 'PRESENT', 'ACTIVE'),
('ATDF5918D1AA', 23, 'STU_VW_002', 'ABSENT', 'SUPERSEDED'),
('ATDF70D72553', 23, 'STU_VW_012', 'ABSENT', 'ACTIVE'),
('ATDF75858F27', 23, 'STU_VW_020', 'PRESENT', 'ACTIVE'),
('ATDF78F2920D', 11, '5Q1XZO2Y', 'PRESENT', 'ACTIVE'),
('ATDF8547A818', 22, 'STU_VW_005', 'PRESENT', 'SUPERSEDED'),
('ATDF8A725AE5', 23, 'STU_VW_010', 'PRESENT', 'SUPERSEDED'),
('ATDF9A4AD253', 20, 'STU_VW_010', 'PRESENT', 'SUPERSEDED'),
('ATDFA7093D74', 23, 'STU_VW_011', 'ABSENT', 'SUPERSEDED'),
('ATDFBC1B8042', 23, 'STU_VW_011', 'ABSENT', 'ACTIVE'),
('ATDFC50F7BE4', 23, 'STU_VW_006', 'PRESENT', 'SUPERSEDED'),
('ATDFCF692506', 20, 'STU_VW_007', 'ABSENT', 'SUPERSEDED'),
('ATDFD77F382E', 8, 'L3NCL2J4', 'ABSENT', 'ACTIVE');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`, `description`) VALUES
('BDG36FD069B7', 'VOL', 2026, 100000, 12000, '2026-04-20', '-');

--
-- Triggers `budget`
--
DROP TRIGGER IF EXISTS `trg_budget_delete`;
DELIMITER $$
CREATE TRIGGER `trg_budget_delete` AFTER DELETE ON `budget` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('budget', OLD.budget_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_budget_insert`;
DELIMITER $$
CREATE TRIGGER `trg_budget_insert` AFTER INSERT ON `budget` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('budget', NEW.budget_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'budget_id', NULL, NEW.budget_id),
    (v_audit_id, 'sport_id', NULL, NEW.sport_id),
    (v_audit_id, 'year', NULL, NEW.year),
    (v_audit_id, 'allocated_amount', NULL, NEW.allocated_amount),
    (v_audit_id, 'spent_amount', NULL, NEW.spent_amount),
    (v_audit_id, 'allocation_date', NULL, NEW.allocation_date),
    (v_audit_id, 'description', NULL, NEW.description);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_budget_update`;
DELIMITER $$
CREATE TRIGGER `trg_budget_update` AFTER UPDATE ON `budget` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.budget_id <=> OLD.budget_id AND NEW.sport_id <=> OLD.sport_id AND NEW.year <=> OLD.year AND NEW.allocated_amount <=> OLD.allocated_amount AND NEW.spent_amount <=> OLD.spent_amount AND NEW.allocation_date <=> OLD.allocation_date AND NEW.description <=> OLD.description) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('budget', NEW.budget_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        IF NOT (NEW.budget_id <=> OLD.budget_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'budget_id', OLD.budget_id, NEW.budget_id); END IF;
        IF NOT (NEW.sport_id <=> OLD.sport_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'sport_id', OLD.sport_id, NEW.sport_id); END IF;
        IF NOT (NEW.year <=> OLD.year) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'year', OLD.year, NEW.year); END IF;
        IF NOT (NEW.allocated_amount <=> OLD.allocated_amount) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'allocated_amount', OLD.allocated_amount, NEW.allocated_amount); END IF;
        IF NOT (NEW.spent_amount <=> OLD.spent_amount) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'spent_amount', OLD.spent_amount, NEW.spent_amount); END IF;
        IF NOT (NEW.allocation_date <=> OLD.allocation_date) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'allocation_date', OLD.allocation_date, NEW.allocation_date); END IF;
        IF NOT (NEW.description <=> OLD.description) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'description', OLD.description, NEW.description); END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `captain_sport`
--

DROP TABLE IF EXISTS `captain_sport`;
CREATE TABLE IF NOT EXISTS `captain_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `captain_sport`
--

INSERT INTO `captain_sport` (`user_id`, `sport_id`, `date_started`, `date_relieved`) VALUES
('L3NCL2J4', 'KRT', '2026-04-09', NULL),
('STU_KRT_05', 'RR', '2026-04-20', NULL),
('STU001', 'BAD', '2026-04-09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coach_sport`
--

DROP TABLE IF EXISTS `coach_sport`;
CREATE TABLE IF NOT EXISTS `coach_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coach_sport`
--

INSERT INTO `coach_sport` (`user_id`, `sport_id`, `date_started`, `date_relieved`) VALUES
('NPM8O9RE', 'VOL', '2026-04-10', NULL);

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
  `content` varchar(300) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`comment_id`, `post_id`, `comment_from`, `reply_to`, `content`, `status`) VALUES
('cmt_69e2', 'P0005', 'H4J1OHSX', '', 'Hello', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `equipment` (
  `equipment_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `category_id` varchar(8) DEFAULT NULL,
  `equipment_name` varchar(32) NOT NULL,
  `max_allow` int NOT NULL,
  `image_name` varchar(48) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `sport_id`, `category_id`, `equipment_name`, `max_allow`, `image_name`, `status`) VALUES
('EQ001', 'BAD', 'CAT002', 'Badminton Racket', 2, '', 'ACTIVE'),
('EQ002', 'BAD', 'CAT001', 'Shuttlecock', 1, '', 'ACTIVE'),
('EQ003', 'BAD', 'CAT004', 'Badminton Net', 1, '', 'ACTIVE'),
('EQ004', 'VOL', 'CAT001', 'Volleyball', 1, '', 'ACTIVE'),
('EQ005', 'VOL', 'CAT001', 'Volleyball Net', 1, '', 'ACTIVE'),
('EQ006', 'VOL', 'CAT003', 'Knee Pads', 2, '', 'ACTIVE'),
('EQ007', 'FOO', 'CAT001', 'Football', 1, '', 'ACTIVE'),
('EQ008', 'FOO', 'CAT004', 'Goal Post', 2, '', 'ACTIVE'),
('EQ009', 'FOO', 'CAT003', 'Shin Guards', 0, '', 'ACTIVE'),
('EQ010', 'FOO', 'CAT004', 'Goalkeeper Gloves', 4, '', 'ACTIVE'),
('EQ011', 'TEN', 'CAT002', 'Tennis Racket', 2, '', 'ACTIVE'),
('EQ012', 'TEN', 'CAT001', 'Tennis Ball', 1, '', 'ACTIVE'),
('EQ013', 'TEN', 'CAT004', 'Tennis Net', 1, '', 'ACTIVE'),
('EQ014', 'BAS', 'CAT001', 'Basketball', 1, '', 'ACTIVE'),
('EQ015', 'BAS', 'CAT001', 'Basketball Hoop', 0, '', 'ACTIVE'),
('EQ016', 'BAS', NULL, 'Shot Clock', 0, '', 'ACTIVE'),
('EQ017', 'HOC', 'CAT002', 'Hockey Stick', 1, '', 'ACTIVE'),
('EQ018', 'HOC', 'CAT001', 'Hockey Ball', 1, '', 'ACTIVE'),
('EQ019', 'HOC', 'CAT004', 'Goalkeeper Pads', 0, '', 'ACTIVE'),
('EQ020', 'NET', 'CAT001', 'Netball', 1, '', 'ACTIVE'),
('EQ021', 'NET', 'CAT001', 'Netball Post', 0, '', 'ACTIVE'),
('EQ022', 'CRI', 'CAT002', 'Cricket Bat', 2, '', 'ACTIVE'),
('EQ023', 'CRI', 'CAT001', 'Cricket Ball', 1, '', 'ACTIVE'),
('EQ024', 'CRI', 'CAT002', 'Batting Pads', 4, '', 'ACTIVE'),
('EQ025', 'CRI', 'CAT003', 'Helmet', 2, '', 'ACTIVE'),
('EQ026', 'RUG', 'CAT001', 'Rugby Ball', 0, '', 'ACTIVE'),
('EQ027', 'RUG', 'CAT003', 'Head Guard', 0, '', 'ACTIVE'),
('EQ028', 'SWI', NULL, 'Swimming Goggles', 0, '', 'ACTIVE'),
('EQ029', 'SWI', 'CAT003', 'Swim Cap', 0, '', 'ACTIVE'),
('EQ030', 'SWI', NULL, 'Kick Board', 0, '', 'ACTIVE'),
('EQ031', 'TT', 'CAT002', 'Table Tennis Bat', 0, '', 'ACTIVE'),
('EQ032', 'TT', 'CAT001', 'Table Tennis Ball', 0, '', 'ACTIVE'),
('EQ033', 'TT', NULL, 'TT Table', 0, '', 'ACTIVE'),
('EQ034', 'WL', NULL, 'Barbell', 0, '', 'ACTIVE'),
('EQ035', 'WL', NULL, 'Dumbbell', 0, '', 'ACTIVE'),
('EQ036', 'WL', NULL, 'Weight Plates', 0, '', 'ACTIVE'),
('EQ037', 'ROW', NULL, 'Rowing Boat', 0, '', 'ACTIVE'),
('EQ038', 'ROW', NULL, 'Oars', 0, '', 'ACTIVE'),
('EQ039', 'WRE', 'CAT006', 'Wrestling Mat', 0, '', 'ACTIVE'),
('EQ040', 'CHE', NULL, 'Chess Board', 0, '', 'ACTIVE'),
('EQ041', 'CHE', NULL, 'Chess Timer', 0, '', 'ACTIVE'),
('EQ042', 'ATH', NULL, 'Starting Blocks', 0, '', 'ACTIVE'),
('EQ043', 'ATH', NULL, 'Javelin', 0, '', 'ACTIVE'),
('EQ044', 'ATH', NULL, 'Discus', 0, '', 'ACTIVE'),
('EQ045', 'ATH', NULL, 'Shot Put', 0, '', 'ACTIVE'),
('EQ046', 'BOX', 'CAT003', 'Boxing Gloves', 0, '', 'ACTIVE'),
('EQ047', 'BOX', NULL, 'Punching Bag', 0, '', 'ACTIVE'),
('EQ048', 'TKD', 'CAT003', 'Chest Guard', 0, '', 'ACTIVE'),
('EQ049', 'TKD', 'CAT003', 'Head Guard', 0, '', 'ACTIVE'),
('EQ050', 'KRT', 'CAT003', 'Karate Gi', 0, '', 'ACTIVE'),
('EQ051', 'KRT', NULL, 'Hand Protectors', 0, '', 'ACTIVE'),
('EQ052', 'RR', NULL, 'Stopwatch', 0, '', 'ACTIVE'),
('EQ053', 'RR', NULL, 'Race Bib', 0, '', 'ACTIVE'),
('EQ054', 'SCR', NULL, 'Scrabble Board', 0, '', 'ACTIVE'),
('EQ055', 'ELL', NULL, 'Elle Game Set', 0, '', 'ACTIVE'),
('EQ056', 'BB', 'CAT002', 'Baseball Bat', 0, '', 'ACTIVE'),
('EQ057', 'BB', 'CAT001', 'Baseball', 0, '', 'ACTIVE'),
('EQ058', 'BB', 'CAT001', 'Baseball Glove', 0, '', 'ACTIVE'),
('EQ059', 'KBD', 'CAT006', 'Kabaddi Mat', 0, '', 'ACTIVE'),
('EQ060', 'CRM', NULL, 'Carrom Board', 1, '', 'ACTIVE'),
('EQ061', 'CRM', NULL, 'Carrom Coins', 1, '', 'ACTIVE'),
('EQ062', 'CRM', NULL, 'Striker', 1, '', 'ACTIVE'),
('EQ69354316b1', 'TKD', 'CAT006', 'Taekwondo Tatami', 9, 'taekwondo_tatami_2938.jpg', 'ACTIVE'),
('EQ6937e28ddf', 'BOX', 'CAT007', 'Boxing Shoes', 1, 'boxing_shoes_3247.jpg', 'ACTIVE'),
('EQ699563ae09', 'NET', 'CAT001', 'Netball BALL', 0, '', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `equipment-requests`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment-requests`
--

INSERT INTO `equipment-requests` (`request_id`, `student_id`, `category_name`, `equipment_id`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`, `sport_id`, `reserved_location`, `requester_name`, `equipment_items`) VALUES
('req_3df246e8', 'L3NCL2J4', 'Badminton Net (x5), Badminton Racket (x1)', NULL, '2026-03-03', '13:00:00', '14:00:00', '', 'PENDING', '', 'BAD', 'Badminton Court', 'Chamal Hettiarachchi', '[{\"equipment_name\":\"Badminton Net\",\"quantity\":5},{\"equipment_name\":\"Badminton Racket\",\"quantity\":1}]'),
('req_6937e152', 'FMX6Z8DF', 'Badminton Re', NULL, '2025-12-29', '08:00:00', '10:00:00', 'For the Taekwondo Provincial matches practices', 'COMPLETED', '-', 'BAD', 'Ground', 'K S Silva', NULL),
('req_693a734e', '23020342', 'Boxing Shoes', NULL, '2026-01-01', '13:00:00', '15:00:00', 'Foot work practice', 'ACTIVE', '-', 'BOX', 'Indoor court', 'S J', NULL),
('req_69da2817', '23000000', 'Badminton Racket', 'EQ001', '2026-04-12', '15:00:00', '17:00:00', 'Play for Fun', 'ACTIVE', '', 'BAD', '', '', NULL),
('req_6d607bf3', NULL, 'Tennis Racket (x2)', NULL, '2026-02-13', '10:30:00', '11:30:00', '', 'ACTIVE', '', 'TEN', 'Tennis Court', 'Student ', '[{\"equipment_name\":\"Tennis Racket\",\"quantity\":2}]'),
('req_9fdd61ac', NULL, 'Netball Post', NULL, '2026-01-25', '09:50:00', '11:49:00', '', 'PENDING', 'Team Practice', 'NET', 'Ground', 'In person reservation', NULL),
('req_c9eaa56c', 'In person', 'Goalkeeper P', NULL, '2026-01-25', '12:01:00', '13:01:00', '', 'ACCEPTED', 'Freshers', 'HOC', 'Ground', 'Savi', '[{\"equipment_name\":\"Goalkeeper Pads\",\"quantity\":1},{\"equipment_name\":\"Hockey Ball\",\"quantity\":1}]'),
('req_de7286f7', NULL, 'Relay baton (x4)', NULL, '2026-02-13', '06:30:00', '08:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S K', '[{\"equipment_name\":\"Relay baton\",\"quantity\":4}]'),
('req_eec57c81', NULL, 'Cricket Bat', NULL, '2026-01-24', '06:21:00', '07:21:00', '', 'COMPLETED', '', 'CRI', 'Cricket Pitch', 'Student ', NULL),
('req_eefe02a8', NULL, 'Relay baton (x1)', NULL, '2026-02-13', '06:30:00', '07:00:00', '', 'ACTIVE', '', 'ATH', 'Ground', 'S Silv', '[{\"equipment_name\":\"Relay baton\",\"quantity\":1}]');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_categories`
--

DROP TABLE IF EXISTS `equipment_categories`;
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `category_id` varchar(8) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_categories`
--

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

--
-- Table structure for table `equipment_inventory`
--

DROP TABLE IF EXISTS `equipment_inventory`;
CREATE TABLE IF NOT EXISTS `equipment_inventory` (
  `stock_id` varchar(8) NOT NULL,
  `equipment_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `quantity` int NOT NULL,
  `usable` int NOT NULL,
  `added_date` date NOT NULL,
  `remarks` varchar(256) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_inventory`
--

INSERT INTO `equipment_inventory` (`stock_id`, `equipment_id`, `sport_id`, `quantity`, `usable`, `added_date`, `remarks`, `status`) VALUES
('STK00001', 'EQ001', 'BAD', 40, 35, '2025-12-08', '-', 'ACTIVE'),
('STK00002', 'EQ002', 'BAD', 200, 200, '2025-12-08', '-', 'ACTIVE'),
('STK00003', 'EQ003', 'BAD', 5, 5, '2025-12-08', '-', 'ACTIVE'),
('STK00004', 'EQ004', 'VOL', 15, 15, '2025-12-08', '-', 'ACTIVE'),
('STK00005', 'EQ005', 'VOL', 6, 6, '2025-12-08', '-', 'ACTIVE'),
('STK00006', 'EQ006', 'VOL', 30, 30, '2025-12-08', '-', 'ACTIVE'),
('STK00007', 'EQ007', 'FOO', 18, 18, '2025-12-08', '-', 'ACTIVE'),
('STK00008', 'EQ008', 'FOO', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00009', 'EQ009', 'FOO', 25, 25, '2025-12-08', '-', 'ACTIVE'),
('STK00010', 'EQ010', 'FOO', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00011', 'EQ011', 'TEN', 12, 8, '2025-12-08', '-', 'ACTIVE'),
('STK00012', 'EQ012', 'TEN', 150, 150, '2025-12-08', '-', 'ACTIVE'),
('STK00013', 'EQ013', 'TEN', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00014', 'EQ014', 'BAS', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00015', 'EQ015', 'BAS', 6, 6, '2025-12-08', '-', 'ACTIVE'),
('STK00016', 'EQ016', 'BAS', 2, 2, '2025-12-08', '-', 'ACTIVE'),
('STK00017', 'EQ017', 'HOC', 20, 6, '2025-12-08', '-', 'ACTIVE'),
('STK00018', 'EQ018', 'HOC', 30, 30, '2025-12-08', '-', 'ACTIVE'),
('STK00019', 'EQ019', 'HOC', 6, 6, '2025-12-08', '-', 'ACTIVE'),
('STK00020', 'EQ020', 'NET', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00021', 'EQ021', 'NET', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00022', 'EQ022', 'CRI', 12, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00023', 'EQ023', 'CRI', 60, 60, '2025-12-08', '-', 'ACTIVE'),
('STK00024', 'EQ024', 'CRI', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00025', 'EQ025', 'CRI', 8, 8, '2025-12-08', '-', 'ACTIVE'),
('STK00026', 'EQ026', 'RUG', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00027', 'EQ027', 'RUG', 12, 12, '2025-12-08', '-', 'ACTIVE'),
('STK00028', 'EQ028', 'SWI', 25, 25, '2025-12-08', '-', 'ACTIVE'),
('STK00029', 'EQ029', 'SWI', 30, 30, '2025-12-08', '-', 'ACTIVE'),
('STK00030', 'EQ030', 'SWI', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00031', 'EQ031', 'TT', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00032', 'EQ032', 'TT', 150, 150, '2025-12-08', '-', 'ACTIVE'),
('STK00033', 'EQ033', 'TT', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00034', 'EQ034', 'WL', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00035', 'EQ035', 'WL', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00036', 'EQ036', 'WL', 80, 80, '2025-12-08', '-', 'ACTIVE'),
('STK00037', 'EQ037', 'ROW', 6, 6, '2025-12-08', '-', 'ACTIVE'),
('STK00038', 'EQ038', 'ROW', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00039', 'EQ039', 'WRE', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00040', 'EQ040', 'CHE', 15, 3, '2025-12-08', '-', 'ACTIVE'),
('STK00041', 'EQ041', 'CHE', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00042', 'EQ042', 'ATH', 8, 8, '2025-12-08', '-', 'ACTIVE'),
('STK00043', 'EQ043', 'ATH', 12, 12, '2025-12-08', '-', 'ACTIVE'),
('STK00044', 'EQ044', 'ATH', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00045', 'EQ045', 'ATH', 14, 14, '2025-12-08', '-', 'ACTIVE'),
('STK00046', 'EQ046', 'BOX', 16, 16, '2025-12-08', '-', 'ACTIVE'),
('STK00047', 'EQ047', 'BOX', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00048', 'EQ048', 'TKD', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00049', 'EQ049', 'TKD', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00050', 'EQ050', 'KRT', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00051', 'EQ051', 'KRT', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00052', 'EQ052', 'RR', 30, 30, '2025-12-08', '-', 'ACTIVE'),
('STK00053', 'EQ053', 'RR', 200, 200, '2025-12-08', '-', 'ACTIVE'),
('STK00054', 'EQ054', 'SCR', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00055', 'EQ055', 'ELL', 8, 8, '2025-12-08', '-', 'ACTIVE'),
('STK00056', 'EQ056', 'BB', 12, 12, '2025-12-08', '-', 'ACTIVE'),
('STK00057', 'EQ057', 'BB', 40, 40, '2025-12-08', '-', 'ACTIVE'),
('STK00058', 'EQ058', 'BB', 20, 20, '2025-12-08', '-', 'ACTIVE'),
('STK00059', 'EQ059', 'KBD', 4, 4, '2025-12-08', '-', 'ACTIVE'),
('STK00060', 'EQ060', 'CRM', 10, 10, '2025-12-08', '-', 'ACTIVE'),
('STK00061', 'EQ061', 'CRM', 40, 40, '2025-12-08', '-', 'ACTIVE'),
('STK00062', 'EQ062', 'CRM', 15, 15, '2025-12-08', '-', 'ACTIVE'),
('STK00063', 'EQ69354316b1', 'TKD', 200, 200, '2025-12-08', '-', 'ACTIVE'),
('STK69354', 'EQ020', 'NET', 4, 4, '2025-12-07', '-', 'ACTIVE'),
('STK6937d', 'EQ046', 'BOX', 12, 12, '2025-12-09', 'Donated by Sri Lanka Boxing Federation', 'ACTIVE'),
('STK6937e', 'EQ6937e28ddf', 'BOX', 20, 20, '2025-12-09', 'Donated by Sri Lanka Boxing Federation', 'ACTIVE'),
('STK69956', 'EQ699563ae09', 'NET', 5, 5, '2026-12-18', '-', 'ACTIVE');

--
-- Triggers `equipment_inventory`
--
DROP TRIGGER IF EXISTS `trg_equipment_inventory_delete`;
DELIMITER $$
CREATE TRIGGER `trg_equipment_inventory_delete` AFTER DELETE ON `equipment_inventory` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('equipment_inventory', OLD.stock_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_equipment_inventory_insert`;
DELIMITER $$
CREATE TRIGGER `trg_equipment_inventory_insert` AFTER INSERT ON `equipment_inventory` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('equipment_inventory', NEW.stock_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'stock_id', NULL, NEW.stock_id),
    (v_audit_id, 'equipment_id', NULL, NEW.equipment_id),
    (v_audit_id, 'sport_id', NULL, NEW.sport_id),
    (v_audit_id, 'quantity', NULL, NEW.quantity),
    (v_audit_id, 'usable', NULL, NEW.usable),
    (v_audit_id, 'added_date', NULL, NEW.added_date),
    (v_audit_id, 'remarks', NULL, NEW.remarks),
    (v_audit_id, 'status', NULL, NEW.status);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_equipment_inventory_update`;
DELIMITER $$
CREATE TRIGGER `trg_equipment_inventory_update` AFTER UPDATE ON `equipment_inventory` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.stock_id <=> OLD.stock_id AND NEW.equipment_id <=> OLD.equipment_id AND NEW.sport_id <=> OLD.sport_id AND NEW.quantity <=> OLD.quantity AND NEW.usable <=> OLD.usable AND NEW.added_date <=> OLD.added_date AND NEW.remarks <=> OLD.remarks AND NEW.status <=> OLD.status) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('equipment_inventory', NEW.stock_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        IF NOT (NEW.stock_id <=> OLD.stock_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'stock_id', OLD.stock_id, NEW.stock_id); END IF;
        IF NOT (NEW.equipment_id <=> OLD.equipment_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'equipment_id', OLD.equipment_id, NEW.equipment_id); END IF;
        IF NOT (NEW.sport_id <=> OLD.sport_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'sport_id', OLD.sport_id, NEW.sport_id); END IF;
        IF NOT (NEW.quantity <=> OLD.quantity) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'quantity', OLD.quantity, NEW.quantity); END IF;
        IF NOT (NEW.usable <=> OLD.usable) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'usable', OLD.usable, NEW.usable); END IF;
        IF NOT (NEW.added_date <=> OLD.added_date) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'added_date', OLD.added_date, NEW.added_date); END IF;
        IF NOT (NEW.remarks <=> OLD.remarks) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'remarks', OLD.remarks, NEW.remarks); END IF;
        IF NOT (NEW.status <=> OLD.status) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'status', OLD.status, NEW.status); END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `event_result_permissions`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_result_permissions`
--

INSERT INTO `event_result_permissions` (`id`, `tournament_id`, `captain_id`, `sport_id`, `granted_by`, `granted_at`, `status`, `email_sent`) VALUES
(1, 'TOUR_69ccde3bc3ae2', '5Q1XZO2Y', 'VOL', 'H4J1OHSX', '2026-04-09 05:30:43', 'ACTIVE', 1),
(2, 'TOUR_69d739fdd3a3b', '5Q1XZO2Y', 'VOL', 'H4J1OHSX', '2026-04-19 02:17:35', 'ACTIVE', 1);

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
  `status` enum('BOOKED','CANCELLED','REJECTED','PENDING_CANCEL') NOT NULL DEFAULT 'BOOKED',
  `payment_status` varchar(12) NOT NULL DEFAULT 'INCOMPLETE',
  `payment_id` varchar(50) DEFAULT NULL,
  `payment_slip` varchar(255) DEFAULT NULL,
  `rejection_reason` varchar(256) DEFAULT NULL,
  `cancellation_reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `flag_status` varchar(12) DEFAULT 'NONE',
  `flag_date` timestamp NULL DEFAULT NULL,
  `flag_reason` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility-booking`
--

INSERT INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `slot`, `purpose`, `status`, `payment_status`, `payment_id`, `payment_slip`, `rejection_reason`, `cancellation_reason`, `created_at`, `flag_status`, `flag_date`, `flag_reason`) VALUES
('BK294458', 'L3NCL2J4', '8', '2026-04-23', 'AFTERNOON', '-', 'CANCELLED', 'INCOMPLETE', NULL, NULL, NULL, NULL, '2026-04-20 10:03:20', 'NONE', NULL, NULL),
('BK431104', 'L3NCL2J4', '3', '2026-04-20', 'MORNING', '-', 'BOOKED', 'COMPLETE', 'RETURN-1776679178', NULL, NULL, NULL, '2026-04-20 09:58:55', 'NONE', NULL, NULL),
('BK451205', 'YSZLE02E', '1', '2026-04-20', 'MORNING', '-', 'CANCELLED', 'COMPLETE', 'RETURN-1776668795', NULL, NULL, 'Hey, I don\'t want this anymore', '2026-04-20 07:01:56', 'NONE', NULL, NULL),
('BK464191', '5Q1XZO2Y', '3', '2026-04-18', 'AFTERNOON', 'Please', 'CANCELLED', 'INCOMPLETE', NULL, NULL, '', NULL, '2026-04-18 15:05:33', 'NONE', NULL, NULL),
('BK468865', 'H4J1OHSX', '23', '2026-04-21', 'FULL', 'hdhgdhdjg', 'BOOKED', 'PENDING', NULL, 'SLIP_BK468865_1776428796.pdf', '', NULL, '2026-04-17 12:26:12', 'NONE', NULL, NULL),
('BK587815', 'H4J1OHSX', '1', '2026-04-17', 'MORNING', '-', 'BOOKED', 'COMPLETE', 'RETURN-1776420415', NULL, '', NULL, '2026-04-17 10:06:15', 'NONE', NULL, NULL),
('BK820671', 'L3NCL2J4', '9', '2026-04-22', 'FULL', 'qqq', 'CANCELLED', 'INCOMPLETE', NULL, NULL, NULL, NULL, '2026-04-20 09:57:46', 'NONE', NULL, NULL),
('BK835860', 'L3NCL2J4', '8', '2026-04-23', 'MORNING', '-', 'CANCELLED', 'INCOMPLETE', NULL, NULL, NULL, NULL, '2026-04-20 10:02:43', 'NONE', NULL, NULL),
('BK864479', 'YSZLE02E', '3', '2026-04-24', 'MORNING', 'Inter-School Tournament Practices', 'CANCELLED', 'INCOMPLETE', NULL, NULL, NULL, NULL, '2026-04-20 07:31:40', 'NONE', NULL, NULL),
('BK923491', 'P1N116IE', '1', '2026-04-21', 'MORNING', '-', 'BOOKED', 'COMPLETE', 'RETURN-1776667384', NULL, NULL, NULL, '2026-04-20 06:42:20', 'NONE', NULL, NULL);

--
-- Triggers `facility-booking`
--
DROP TRIGGER IF EXISTS `trg_facility_booking_delete`;
DELIMITER $$
CREATE TRIGGER `trg_facility_booking_delete` AFTER DELETE ON `facility-booking` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('facility-booking', OLD.booking_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_facility_booking_insert`;
DELIMITER $$
CREATE TRIGGER `trg_facility_booking_insert` AFTER INSERT ON `facility-booking` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('facility-booking', NEW.booking_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'user_id', NULL, NEW.user_id),
    (v_audit_id, 'facility_id', NULL, NEW.facility_id),
    (v_audit_id, 'date', NULL, NEW.date),
    (v_audit_id, 'slot', NULL, NEW.slot),
    (v_audit_id, 'purpose', NULL, NEW.purpose),
    (v_audit_id, 'status', NULL, NEW.status),
    (v_audit_id, 'payment_status', NULL, NEW.payment_status);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_facility_booking_update`;
DELIMITER $$
CREATE TRIGGER `trg_facility_booking_update` AFTER UPDATE ON `facility-booking` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.user_id <=> OLD.user_id AND NEW.facility_id <=> OLD.facility_id AND NEW.date <=> OLD.date AND NEW.slot <=> OLD.slot AND NEW.purpose <=> OLD.purpose AND NEW.status <=> OLD.status AND NEW.payment_status <=> OLD.payment_status AND NEW.rejection_reason <=> OLD.rejection_reason AND NEW.flag_status <=> OLD.flag_status) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('facility-booking', NEW.booking_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        IF NOT (NEW.status <=> OLD.status) THEN
            INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'status', OLD.status, NEW.status);
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `facility_rates`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility_rates`
--

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
(12, 'FAC_BASKETBA', 'GROUND', 'Basketball (25 Persons for practices) (without light)', 25, '20000.00', '12000.00', '6000.00', '40000.00', '25000.00', '10000.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
(13, 'FAC_BASKETBA', 'GROUND', 'Basketball (25 Persons for practices) (with light)', 25, NULL, '17500.00', '8000.00', NULL, '25000.00', '12500.00', '2025-08-15 23:13:31', '2025-08-15 23:13:31'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`, `registrar_id`, `registrar_email`) VALUES
('1', 'University of Colombo School of Computing (UCSC)', 'REG003', 'kasun.silva@ucsc.uoc.lk'),
('10', 'Faculty of Technology', NULL, NULL),
('2', 'Faculty of Science', NULL, NULL),
('3', 'Faculty of Arts', NULL, NULL),
('4', 'Faculty of Education', NULL, NULL),
('5', 'Faculty of Indigenous Medicine', NULL, NULL),
('6', 'Faculty of Law', NULL, NULL),
('7', 'Faculty of Management & Finance', NULL, NULL),
('8', 'Faculty of Medicine', NULL, NULL),
('9', 'Faculty of Nursing', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `good_condemn_notes`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `good_condemn_notes`
--

INSERT INTO `good_condemn_notes` (`gcn_id`, `sport_id`, `equipment_id`, `stock_id`, `quantity`, `created_at`) VALUES
(1, 'BAD', 'EQ001', 'STK00001', 3, '2025-09-01 10:00:00'),
(2, 'HOC', 'EQ017', 'STK00017', 8, '2025-10-15 14:00:00'),
(3, 'TEN', 'EQ011', 'STK00011', 2, '2025-11-20 09:30:00'),
(4, 'CRI', 'EQ022', 'STK00022', 1, '2025-12-10 11:00:00'),
(5, 'CHE', 'EQ040', 'STK00040', 12, '2026-01-05 08:45:00'),
(6, 'FOO', 'EQ009', 'STK00009', 3, '2026-01-22 10:15:00'),
(7, 'BAD', 'EQ002', 'STK00002', 15, '2026-02-10 13:00:00'),
(8, 'VOL', 'EQ006', 'STK00006', 5, '2026-02-19 09:00:00');

--
-- Triggers `good_condemn_notes`
--
DROP TRIGGER IF EXISTS `trg_gcn_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_gcn_after_insert` AFTER INSERT ON `good_condemn_notes` FOR EACH ROW BEGIN
    UPDATE `equipment_inventory`
    SET `quantity` = GREATEST(0, CAST(`quantity` AS SIGNED) - CAST(NEW.quantity AS SIGNED)),
        `usable`   = GREATEST(0, CAST(`usable` AS SIGNED) - CAST(NEW.quantity AS SIGNED))
    WHERE `stock_id` = NEW.stock_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `good_issue_notes`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `good_issue_notes`
--

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
(10, 'BAD', 'EQ002', '2026-02-18', 20, 'Tubes', 'STK00002', 'usr_694d89fa', NULL, 'usr_68f89998', '2026-02-18 09:00:00'),
(11, 'ELL', 'EQ055', '2026-04-14', 12, 'pcs', 'STK00055', 'usr_694d89fa', '5Q1XZO2Y', 'usr_68f89998', '2026-04-17 12:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `good_received_notes`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `good_received_notes`
--

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
(16, 'ATH', 'EQ044', 'Testing Description', '2026-01-01', '09-98-AD', 3, 'AD-78-65-43', 4, 'pcs', '12000.00', 'Athletics/Throwing', 'STK00044', '2026-03-30 06:12:35'),
(17, 'BAD', 'EQ001', 'Desc', '2026-04-20', 'PO-09u', 1, 'INV-0987O', 20, 'pcs', '2000.00', 'File B9', 'STK00001', '2026-04-20 09:36:25');

--
-- Triggers `good_received_notes`
--
DROP TRIGGER IF EXISTS `trg_grn_after_insert`;
DELIMITER $$
CREATE TRIGGER `trg_grn_after_insert` AFTER INSERT ON `good_received_notes` FOR EACH ROW BEGIN
    UPDATE `equipment_inventory`
    SET `quantity` = `quantity` + NEW.quantity,
        `usable`   = `usable` + NEW.quantity
    WHERE `stock_id` = NEW.stock_id;
END
$$
DELIMITER ;

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
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `injury_report`
--

INSERT INTO `injury_report` (`report_id`, `user_id`, `coach_id`, `practice_id`, `date`, `description`, `need_substitude`, `substitude_id`, `status`) VALUES
('IRP6971E85EA', 'P001', 'NPM8O9RE', '4', '2026-01-01', 'test (Minor)', 'YES', 'P002', 'ACTIVE');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inquiry`
--

INSERT INTO `inquiry` (`inquiry_id`, `user_id`, `email`, `subject`, `message`, `date`, `status`) VALUES
('INQA1A688463', 'H4J1OHSX', 'maximal@gmail.com', 'Testing contact', 'Something Something', '2025-12-15', 'RESOLVED'),
('INQE8F057499', 'H4J1OHSX', 'dakshinagn@gmail.com', 'about group project', 'on progress', '2025-12-18', 'RESOLVED');

-- --------------------------------------------------------

--
-- Table structure for table `invitational_players`
--

DROP TABLE IF EXISTS `invitational_players`;
CREATE TABLE IF NOT EXISTS `invitational_players` (
  `inv_player_id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `university` varchar(100) NOT NULL,
  `student_id` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`inv_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found_images`
--

DROP TABLE IF EXISTS `lost_found_images`;
CREATE TABLE IF NOT EXISTS `lost_found_images` (
  `case_id` varchar(12) NOT NULL,
  `image_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lost_item`
--

INSERT INTO `lost_item` (`lostItem_id`, `itemName`, `foundDate`, `description`, `foundLocation`, `foundBy`, `contactNumber`, `itemStatus`, `image`) VALUES
(1, 'A bag with Umbrella and Shoes', '2026-04-09', '- A red umbrella\r\n- A pair of running shoes (Green color)', 'In the Indoor Stadium area', 'Chamal Chamuditha', '0710897643', 'unclaimed', '1775904427_WhatsApp Image 2026-03-14 at 19.57.31.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `manager_sport`
--

DROP TABLE IF EXISTS `manager_sport`;
CREATE TABLE IF NOT EXISTS `manager_sport` (
  `user_id` varchar(12) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `date_started` date NOT NULL,
  `date_relieved` date DEFAULT NULL,
  PRIMARY KEY (`user_id`,`sport_id`,`date_started`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manager_sport`
--

INSERT INTO `manager_sport` (`user_id`, `sport_id`, `date_started`, `date_relieved`) VALUES
('SPT004', 'VOL', '2026-04-10', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `match_ball_court`
--

INSERT INTO `match_ball_court` (`id`, `match_id`, `team_a_name`, `team_b_name`, `sport_subtype`, `period_scores`, `final_score_a`, `final_score_b`, `overtime_periods`, `sets_won_a`, `sets_won_b`, `innings_played`, `notes`) VALUES
(1, 'match_69cce68043d035.97638693', 'USJP', 'UOC', 'VOLLEYBALL', '[{\"a\": 25, \"b\": 23}, {\"a\": 22, \"b\": 25}, {\"a\": 19, \"b\": 25}]', 1, 2, 0, 1, 2, NULL, 'UOC won the Match'),
(2, 'MATCH_VOL_1776534555', 'University of Colombo', 'University of Sri Jayewardenepura (USJP)', 'VOLLEYBALL', '[{\"a\": 25, \"b\": 22}, {\"a\": 21, \"b\": 25}, {\"a\": 25, \"b\": 20}, {\"a\": 18, \"b\": 25}, {\"a\": 15, \"b\": 12}]', 3, 2, 0, 3, 2, NULL, NULL),
(3, 'match_69e43fd06239e9.14842488', 'University of Colombo', 'University of Sri Jayawardhanapura', 'VOLLEYBALL', '[{\"a\": 25, \"b\": 22}, {\"a\": 21, \"b\": 25}, {\"a\": 23, \"b\": 24}]', 1, 1, 0, 1, 2, NULL, 'USJP won the '),
(4, 'match_69e44489860104.22699606', 'University of Colombo', 'University of Sri Jayawardhanapura', 'VOLLEYBALL', '[{\"a\": 22, \"b\": 25}, {\"a\": 25, \"b\": 21}, {\"a\": 21, \"b\": 25}]', 1, 2, 0, 1, 2, NULL, 'USJP won the finale!'),
(5, 'match_69e5bd612c3917.15854926', 'University of Colombo', 'University of Kelaniya', 'VOLLEYBALL', '[{\"a\": 25, \"b\": 23}, {\"a\": 25, \"b\": 20}]', 2, 0, 0, 2, 0, NULL, ''),
(6, 'match_69e5f30468e1a6.49793767', 'University of Colombo', 'UOK', 'VOLLEYBALL', '[{\"a\": 25, \"b\": 23}, {\"a\": 25, \"b\": 24}]', 2, 0, 0, 2, 0, NULL, '');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `match_combat`
--

INSERT INTO `match_combat` (`id`, `match_id`, `fighter_a_name`, `fighter_b_name`, `weight_category`, `round_scores`, `total_rounds`, `rounds_completed`, `final_score_a`, `final_score_b`, `result_type`, `knockdowns_a`, `knockdowns_b`, `warnings_a`, `warnings_b`, `pins_a`, `pins_b`, `raid_points_a`, `raid_points_b`, `tackle_points_a`, `tackle_points_b`, `notes`) VALUES
(1, 'match_695d2d74e031d4.90256849', 'UCSC', 'Science', '72+', '[{\"a\": 15, \"b\": 8}, {\"a\": 18, \"b\": 0}]', 3, 2, 0, 0, '', 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, ''),
(2, 'MAT_KRT_001_662140bb888', 'Kusal Mendis (UOC)', 'S. Perera (USJP)', '-60kg', '[{\"a\": 3, \"b\": 0}, {\"a\": 2, \"b\": 1}, {\"a\": 4, \"b\": 2}]', 3, 3, 9, 3, 'POINTS', 1, 0, 1, 2, 0, 0, NULL, NULL, NULL, NULL, 'Dominant performance by UOC athlete.');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `match_players`
--

DROP TABLE IF EXISTS `match_players`;
CREATE TABLE IF NOT EXISTS `match_players` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `user_id` varchar(12) DEFAULT NULL,
  `player_name` varchar(120) NOT NULL,
  `external_id` varchar(50) DEFAULT NULL,
  `team_side` enum('A','B') NOT NULL,
  `is_uoc_student` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `match_players`
--

INSERT INTO `match_players` (`id`, `match_id`, `user_id`, `player_name`, `external_id`, `team_side`, `is_uoc_student`, `created_at`) VALUES
(1, 'match_69e43fd06239e9.14842488', 'STU_VW_009', 'Isuru Ekanayake', NULL, 'A', 1, '2026-04-19 02:37:04'),
(2, 'match_69e43fd06239e9.14842488', '5Q1XZO2Y', 'Jansika Balakrishnan', NULL, 'A', 1, '2026-04-19 02:37:04'),
(3, 'match_69e43fd06239e9.14842488', 'STU_VW_011', 'Kamal Amarasinghe', NULL, 'A', 1, '2026-04-19 02:37:04'),
(4, 'match_69e43fd06239e9.14842488', 'STU_VW_012', 'Lakshan Gamage', NULL, 'A', 1, '2026-04-19 02:37:04'),
(5, 'match_69e43fd06239e9.14842488', 'STU_VW_017', 'Quinton De Silva', NULL, 'A', 1, '2026-04-19 02:37:04'),
(6, 'match_69e43fd06239e9.14842488', 'STU_VW_018', 'Ruwan Abeyrathne', NULL, 'A', 1, '2026-04-19 02:37:04'),
(7, 'match_69e43fd06239e9.14842488', NULL, 'Dilon Rathnayake', 'USJP/ICT/0723', 'B', 0, '2026-04-19 02:37:04'),
(8, 'match_69e44489860104.22699606', 'STU_VW_009', 'Isuru Ekanayake', NULL, 'A', 1, '2026-04-19 02:57:13'),
(9, 'match_69e44489860104.22699606', '5Q1XZO2Y', 'Jansika Balakrishnan', NULL, 'A', 1, '2026-04-19 02:57:13'),
(10, 'match_69e44489860104.22699606', 'STU_VW_011', 'Kamal Amarasinghe', NULL, 'A', 1, '2026-04-19 02:57:13'),
(11, 'match_69e44489860104.22699606', 'STU_VW_012', 'Lakshan Gamage', NULL, 'A', 1, '2026-04-19 02:57:13'),
(12, 'match_69e44489860104.22699606', 'STU_VW_017', 'Quinton De Silva', NULL, 'A', 1, '2026-04-19 02:57:13'),
(13, 'match_69e44489860104.22699606', 'STU_VW_018', 'Ruwan Abeyrathne', NULL, 'A', 1, '2026-04-19 02:57:13'),
(14, 'match_69e44489860104.22699606', NULL, 'Jagath Dulsath', 'USJP/ICT/0765', 'B', 0, '2026-04-19 02:57:13'),
(15, 'match_69e5bd612c3917.15854926', 'STU_VW_002', 'Bandara Fernando', NULL, 'A', 1, '2026-04-20 05:45:05'),
(16, 'match_69e5bd612c3917.15854926', 'L3NCL2J4', 'Chamal Hettiarachchi', NULL, 'A', 1, '2026-04-20 05:45:05'),
(17, 'match_69e5bd612c3917.15854926', 'STU_VW_005', 'Eshan Dissanayake', NULL, 'A', 1, '2026-04-20 05:45:05'),
(18, 'match_69e5bd612c3917.15854926', 'STU_VW_009', 'Isuru Ekanayake', NULL, 'A', 1, '2026-04-20 05:45:05'),
(19, 'match_69e5bd612c3917.15854926', '5Q1XZO2Y', 'Jansika Balakrishnan', NULL, 'A', 1, '2026-04-20 05:45:05'),
(20, 'match_69e5bd612c3917.15854926', 'STU_VW_011', 'Kamal Amarasinghe', NULL, 'A', 1, '2026-04-20 05:45:05'),
(21, 'match_69e5bd612c3917.15854926', 'STU_VW_012', 'Lakshan Gamage', NULL, 'A', 1, '2026-04-20 05:45:05'),
(22, 'match_69e5bd612c3917.15854926', 'STU_VW_017', 'Quinton De Silva', NULL, 'A', 1, '2026-04-20 05:45:05'),
(23, 'match_69e5bd612c3917.15854926', 'STU_VW_018', 'Ruwan Abeyrathne', NULL, 'A', 1, '2026-04-20 05:45:05'),
(24, 'match_69e5f30468e1a6.49793767', 'STU_VW_002', 'Bandara Fernando', NULL, 'A', 1, '2026-04-20 09:33:56'),
(25, 'match_69e5f30468e1a6.49793767', 'L3NCL2J4', 'Chamal Hettiarachchi', NULL, 'A', 1, '2026-04-20 09:33:56'),
(26, 'match_69e5f30468e1a6.49793767', 'STU_VW_005', 'Eshan Dissanayake', NULL, 'A', 1, '2026-04-20 09:33:56'),
(27, 'match_69e5f30468e1a6.49793767', 'STU_VW_007', 'Gayan Gunawardena', NULL, 'A', 1, '2026-04-20 09:33:56'),
(28, 'match_69e5f30468e1a6.49793767', 'STU_VW_009', 'Isuru Ekanayake', NULL, 'A', 1, '2026-04-20 09:33:56'),
(29, 'match_69e5f30468e1a6.49793767', '5Q1XZO2Y', 'Jansika Balakrishnan', NULL, 'A', 1, '2026-04-20 09:33:56'),
(30, 'match_69e5f30468e1a6.49793767', 'STU_VW_011', 'Kamal Amarasinghe', NULL, 'A', 1, '2026-04-20 09:33:56'),
(31, 'match_69e5f30468e1a6.49793767', 'STU_VW_012', 'Lakshan Gamage', NULL, 'A', 1, '2026-04-20 09:33:56'),
(32, 'match_69e5f30468e1a6.49793767', 'STU_VW_017', 'Quinton De Silva', NULL, 'A', 1, '2026-04-20 09:33:56'),
(33, 'match_69e5f30468e1a6.49793767', 'STU_VW_018', 'Ruwan Abeyrathne', NULL, 'A', 1, '2026-04-20 09:33:56');

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
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `message_id` varchar(12) NOT NULL,
  `sender_id` varchar(12) NOT NULL COMMENT 'User ID of sender',
  `recipient_id` varchar(12) NOT NULL COMMENT 'User ID of recipient',
  `recipient_type` enum('COACH','MANAGER','CAPTAIN','ADMIN','SPT','EQP','REG','EXECUTIVE','STUDENT') NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `title` varchar(128) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_id`, `recipient_id`, `recipient_type`, `sport_id`, `title`, `message`, `sent_at`, `is_read`, `status`) VALUES
('MSG699AD6597', '5Q1XZO2Y', 'H4J1OHSX', '', 'VOL', 'Requesting Javelins', 'Hello sir, can we have the Javelins I requested today?', '2026-02-22 10:11:37', 0, 'ACTIVE'),
('MSG69D921C84', '5Q1XZO2Y', 'H4J1OHSX', '', 'VOL', 'Testing 01', 'Hello', '2026-04-10 16:14:00', 0, 'ACTIVE'),
('MSG69E3C6B32', '5Q1XZO2Y', 'SPT004', 'MANAGER', '', 'Budget of External practice session', 'Dear sir, can we meet on next Monday to discuss about budget of the Next month\'s external practice at Galle?', '2026-04-18 18:00:19', 1, 'ACTIVE'),
('MSG69E3CCEA5', 'SPT004', '5Q1XZO2Y', 'CAPTAIN', 'BAS', 'Re: Budget of External practice session', 'Okay, can you meet me at 3.00 pm on Monday', '2026-04-18 18:26:50', 0, 'ACTIVE'),
('MSG69E3D040D', 'SPT004', '5Q1XZO2Y', 'CAPTAIN', 'BAS', 'Test Email Integration', 'Hello Coach, this is a test message to verify that email delivery is working alongside the portal messaging system.', '2026-04-18 18:41:04', 0, 'ACTIVE'),
('MSG69E3D0639', 'SPT004', 'NPM8O9RE', 'COACH', 'BAS', 'Test Email Integration - Coach', 'Hello Coach, this is a test message to verify the email integration.', '2026-04-18 18:41:39', 0, 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `newsfeed_post`
--

DROP TABLE IF EXISTS `newsfeed_post`;
CREATE TABLE IF NOT EXISTS `newsfeed_post` (
  `post_id` varchar(12) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `commenting` varchar(8) NOT NULL,
  `date_posted` date NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsfeed_post`
--

INSERT INTO `newsfeed_post` (`post_id`, `title`, `description`, `commenting`, `date_posted`, `status`) VALUES
('P0001', 'Track & Field and Ground Marking Workshop', 'A workshop on Track & Field and Ground Marking was held on the 30th and 31st of March 2025 at the University of Colombo ground premises. This workshop was conducted by Mr Palitha Jayathilaka, Senior Technical Official at the Sri Lanka Athletic Association, to update our staff members on the new methods and changes in ground marking. Participants who completed this workshop successfully received a valuable certificate.', 'YES', '2025-12-09', 'ACTIVE'),
('P0002', '36th National Rowing Championship', 'University of Colombo rowers won 5 medals at the 36th National Rowing Championship which was held on the 12-13 March 2021 at Diyawannawa Rowing Center. In the Open Category (Women\'s), Ms Ranmalee Nanayakkara and Nadani Mendis won the Silver medal in the Open Double scull, Ms Nadani Mendis and Upuli Edirisingha won the bronze medal in the open Pair and Ms Ranmalee Nanayakkara won the bronze medal in the Open Scull category. In the Intermediate Category (Women\'s), Ms Himasha Panditharatne and Vibhanga Amarasinghe won the bronze medal in the pair event. In the Intermediate Category (Men\'s), Mr Avishka Jayaweera, Mr Shehan Shamalka, Mr Dushyantha Hettiarachchi, Mr Shehan Dinusha Liyanage, Mr Samitha Wijethilake won the Bronze medal in the coxed four events.', 'NO', '2025-12-09', 'ACTIVE'),
('P0003', 'Annual Inter-Faculty Cricket Tournament 2026', 'The most anticipated Inter-Faculty Cricket Tournament is set to begin next week. Teams from all 10 faculties will compete for the championship trophy. Join us at the University Grounds to cheer for your faculty stars!', 'YES', '2025-12-18', 'ACTIVE'),
('P0004', 'Inter-University Championship 2025: Squash Success', 'Our University Squash team displayed an outstanding performance at the Inter-University Championship held at the Sri Lanka Air Force Squash Courts. Congratulations to the team for securing the overall Runner-up position!', 'YES', '2025-12-26', 'ACTIVE'),
('P0005', 'Invitation for New Entrants: Martial Arts Trials', 'The Department of Physical Education invites all freshers to participate in the upcoming Martial Arts orientation and selection trials. Whether you are a beginner or experienced, this is your chance to represent UOC in Karate, Taekwondo, and Judo.', 'YES', '2026-04-17', 'ACTIVE');

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsfeed_post_image`
--

INSERT INTO `newsfeed_post_image` (`image_id`, `post_id`, `image_path`) VALUES
(14, 'P0001', 'images/posts/img_6937eab81c8785.14752532.jpg'),
(15, 'P0002', 'images/posts/img_69380b0ce85be5.20787564.jpg'),
(16, 'P0003', 'images/posts/img_6943e8018adf40.57949946.jpg'),
(17, 'P0005', 'images/posts/img_69e214fe734113.47643339.png');

-- --------------------------------------------------------

--
-- Table structure for table `parallel_checker`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=959 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `parallel_checker`
--

INSERT INTO `parallel_checker` (`id`, `session_id`, `facility_id`, `last_heartbeat`, `selected_date`, `selected_slot`) VALUES
(806, 'f62qj1vb13h5hjvjkh725nsqs4', 8, '2026-04-20 10:03:22', '2026-04-23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `user_id` varchar(12) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`token`),
  KEY `fk_pwd_reset_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`user_id`, `token`, `expires_at`, `created_at`) VALUES
('H4J1OHSX', '14171b15568b35d20fd26d0cc590db74638a5e02b5c791286d14fae5f465a647', '2026-04-17 10:29:57', '2026-04-17 09:29:57');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `physical_facility`
--

DROP TABLE IF EXISTS `physical_facility`;
CREATE TABLE IF NOT EXISTS `physical_facility` (
  `facility_id` varchar(12) NOT NULL,
  `facility_name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`facility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `physical_facility`
--

INSERT INTO `physical_facility` (`facility_id`, `facility_name`, `location`) VALUES
('FAC_BASKETBA', 'Basketball Ground', 'Upper Campus'),
('FAC_CARROM', 'Carrom Room', 'Upper Campus'),
('FAC_GROUND', 'University Main Ground', 'Lower Campus'),
('FAC_INDOOR', 'Main Indoor Stadium', 'Upper Campus'),
('FAC_TENNIS', 'Tennis Grounds', 'Upper Campus'),
('FAC_WEIGHT', 'Weight lifting Room', 'Upper Campus');

-- --------------------------------------------------------

--
-- Table structure for table `playing_teams`
--

DROP TABLE IF EXISTS `playing_teams`;
CREATE TABLE IF NOT EXISTS `playing_teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) NOT NULL,
  `created_by` varchar(12) DEFAULT NULL COMMENT 'Captain user_id who first added',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`team_id`),
  UNIQUE KEY `team_name` (`team_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `playing_teams`
--

INSERT INTO `playing_teams` (`team_id`, `team_name`, `created_by`, `created_at`, `status`) VALUES
(1, 'University of Colombo', '5Q1XZO2Y', '2026-04-19 02:37:04', 'ACTIVE'),
(2, 'University of Sri Jayawardhanapura', '5Q1XZO2Y', '2026-04-19 02:37:04', 'ACTIVE'),
(3, 'University of Kelaniya', '5Q1XZO2Y', '2026-04-20 05:45:05', 'ACTIVE'),
(4, 'UOK', '5Q1XZO2Y', '2026-04-20 09:33:56', 'ACTIVE');

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
  `physical_facility_id` varchar(12) DEFAULT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `notes` text,
  `status` varchar(12) NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `end_time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `need_equipment` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_practice_physical_facility` (`physical_facility_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `practice_sessions`
--

INSERT INTO `practice_sessions` (`id`, `sport_id`, `added_by`, `facility`, `physical_facility_id`, `session_date`, `start_time`, `notes`, `status`, `created_at`, `updated_at`, `end_time`, `location`, `need_equipment`) VALUES
(9, 'SCR', 'SPT', 'Select the Location', NULL, '2026-01-25', '14:30:00', '', 'ACTIVE', '2026-01-25 08:57:16', NULL, '00:00:00', '', ''),
(10, 'KRT', 'SPT', '', 'FAC_INDOOR', '2026-01-09', '17:27:00', '', 'ACTIVE', '2026-01-25 08:57:47', '2026-01-25 10:04:13', '00:00:00', 'Indoor Court', 'No'),
(11, 'VOL', 'SPT', '', 'FAC_GROUND', '2026-01-25', '14:40:00', '', 'MARKED', '2026-01-25 09:09:46', '2026-04-20 04:59:43', '16:40:00', 'Outdoor Field', 'Yes'),
(13, 'BAD', 'SPT', '', 'FAC_INDOOR', '2026-01-25', '16:50:00', '', 'CANCELED', '2026-01-25 10:21:02', '2026-01-28 23:54:41', '18:50:00', 'Indoor court', 'No'),
(14, 'BAD', 'SPT', '', 'FAC_INDOOR', '2026-02-08', '15:55:00', '', 'ACCEPTED', '2026-01-25 10:24:38', '2026-02-08 14:14:50', '17:54:00', 'Indoor court', 'No'),
(15, 'CRI', 'SPT', '', 'FAC_GROUND', '2026-01-29', '12:25:00', '', 'PENDING', '2026-01-28 23:57:10', '2026-01-28 23:57:48', '15:25:00', 'Outdoor Field', 'No'),
(16, 'BAD', 'SPT', '', 'FAC_INDOOR', '2026-01-24', '22:45:00', '', 'ACCEPTED', '2026-02-08 14:15:32', '2026-02-08 14:44:37', '22:45:00', 'Indoor court', 'No'),
(17, 'KBD', 'SPT', '', 'FAC_INDOOR', '2026-02-08', '22:24:00', '', 'ACTIVE', '2026-02-08 16:54:31', NULL, '23:24:00', 'Indoor court', 'No'),
(19, 'BAD', 'SPT', '', 'FAC_GROUND', '2026-02-14', '10:30:00', '', 'PENDING', '2026-02-13 01:54:34', NULL, '11:30:00', 'Outdoor Field', 'Yes'),
(20, 'VOL', '', 'Volleyball', 'FAC_GROUND', '2026-02-02', '13:00:00', '', 'MARKED', '2026-04-10 15:57:12', '2026-04-20 04:46:17', '17:00:00', 'Outdoor Field', 'No'),
(21, 'VOL', '', 'Volleyball', 'FAC_GROUND', '2026-04-04', '13:00:00', '-', 'MARKED', '2026-04-10 15:58:20', '2026-04-10 16:00:20', '16:00:00', 'Outdoor Field', 'No'),
(22, 'VOL', 'SPT', '', 'FAC_BASKETBA', '2026-01-01', '13:00:00', 'No', 'MARKED', '2026-04-11 08:28:11', '2026-04-20 04:52:47', '17:00:00', 'Outdoor Court', 'No'),
(23, 'VOL', 'SPT', '', 'FAC_GROUND', '2026-04-11', '14:00:00', '-', 'MARKED', '2026-04-11 08:29:58', '2026-04-18 13:48:26', '17:00:00', 'Outdoor Field', 'No'),
(24, 'VOL', '', 'Volleyball', NULL, '2026-04-30', '14:15:00', '-', '', '2026-04-18 04:18:43', NULL, '16:00:00', 'Indoor court', 'No'),
(25, 'RR', '', 'Road Race', NULL, '2026-04-25', '08:00:00', 'Please bring the running shoes', '', '2026-04-20 04:29:08', NULL, '15:00:00', 'University Main Ground', 'Yes'),
(26, 'VOL', '', 'Volleyball', NULL, '2026-04-21', '12:00:00', 'kaja', '', '2026-04-20 05:27:12', NULL, '15:00:00', 'Main Indoor Stadium', 'No'),
(27, 'VOL', '', 'Volleyball', NULL, '2026-04-22', '02:00:00', 'Regular', '', '2026-04-20 09:01:12', NULL, '15:30:00', 'Main Indoor Stadium', 'No'),
(28, 'KRT', 'SPT', '', 'FAC_INDOOR', '2026-04-20', '14:30:00', NULL, 'PENDING', '2026-04-20 09:17:21', NULL, '16:30:00', 'Main Indoor Stadium', 'No');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 0, 'd125df99b6f85a0d3861dc2db2ca31c3a9e4da797d1503cd6dd738381a807173', 1762935327);

-- --------------------------------------------------------

--
-- Table structure for table `saved_emails`
--

DROP TABLE IF EXISTS `saved_emails`;
CREATE TABLE IF NOT EXISTS `saved_emails` (
  `email` varchar(64) NOT NULL,
  `recepient_name` varchar(64) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `saved_emails`
--

INSERT INTO `saved_emails` (`email`, `recepient_name`, `status`) VALUES
('sports@usj.ac.lk', 'USJ', 'ACTIVE');

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
  `faculty_id` varchar(4) DEFAULT NULL COMMENT 'Faculty that manages this sport',
  PRIMARY KEY (`sport_id`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport`
--

INSERT INTO `sport` (`sport_id`, `sport_name`, `sport_category`, `coach_id`, `captain_id`, `manager_id`, `faculty_id`) VALUES
('ATH', 'Athletics', 'TRACK_FIELD', '', '', 'usr_68f89be0', NULL),
('BAD', 'Badminton', 'RACKET', '', 'STU001', '', NULL),
('BAS', 'Basketball', 'BALL_COURT', '', '', 'SPT004', NULL),
('BB', 'Baseball', 'BALL_COURT', '', '', '', NULL),
('BOX', 'Boxing', 'COMBAT', '', '', '', NULL),
('CHE', 'Chess', 'BOARD_GAME', '', '', '', NULL),
('CRI', 'Cricket', 'CRICKET', '', '', 'usr_68f89be0', NULL),
('CRM', 'Carrom', 'BOARD_GAME', '', '', '', NULL),
('ELL', 'Elle', 'TEAM_GOAL', '', '', '', NULL),
('FOO', 'Football', 'TEAM_GOAL', '', '', '', NULL),
('HOC', 'Hockey', 'TEAM_GOAL', '', '', '', NULL),
('KBD', 'Kabaddi', 'COMBAT', '', '', '', NULL),
('KRT', 'Karate', 'COMBAT', '', 'L3NCL2J4', '', NULL),
('NET', 'Netball', 'TEAM_GOAL', '', '', '', NULL),
('ROW', 'Rowing', 'TRACK_FIELD', '', '', '', NULL),
('RR', 'Road Race', 'TRACK_FIELD', '', 'STU_KRT_05', '', NULL),
('RUG', 'Rugby', 'TEAM_GOAL', '', '', '', NULL),
('SCR', 'Scrabble', 'BOARD_GAME', '', '', '', NULL),
('SWI', 'Swimming', 'TRACK_FIELD', '', '', '', NULL),
('TEN', 'Tennis', 'RACKET', '', '', '', NULL),
('TKD', 'Taekwondo', 'COMBAT', '', '', '', NULL),
('TT', 'Table Tennis', 'RACKET', '', '', '', NULL),
('VOL', 'Volleyball', 'BALL_COURT', 'NPM8O9RE', '5Q1XZO2Y', 'SPT004', NULL),
('WL', 'Weight Lifting', 'WEIGHT', '', '', '', NULL),
('WRE', 'Wrestling', 'COMBAT', '', '', '', NULL);

--
-- Triggers `sport`
--
DROP TRIGGER IF EXISTS `trg_sport_captain_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_captain_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.captain_id != NEW.captain_id THEN
        -- Close old tenure
        IF OLD.captain_id != '' THEN
            UPDATE captain_sport 
            SET date_relieved = CURDATE() 
            WHERE sport_id = OLD.sport_id 
              AND user_id = OLD.captain_id 
              AND date_relieved IS NULL;
        END IF;
        -- Start new tenure
        IF NEW.captain_id != '' THEN
            INSERT INTO captain_sport (user_id, sport_id, date_started)
            VALUES (NEW.captain_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_sport_coach_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_coach_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.coach_id != NEW.coach_id THEN
        -- Close old tenure
        IF OLD.coach_id != '' THEN
            UPDATE coach_sport 
            SET date_relieved = CURDATE() 
            WHERE sport_id = OLD.sport_id 
              AND user_id = OLD.coach_id 
              AND date_relieved IS NULL;
        END IF;
        -- Start new tenure
        IF NEW.coach_id != '' THEN
            INSERT INTO coach_sport (user_id, sport_id, date_started)
            VALUES (NEW.coach_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_sport_manager_history`;
DELIMITER $$
CREATE TRIGGER `trg_sport_manager_history` AFTER UPDATE ON `sport` FOR EACH ROW BEGIN
    IF OLD.manager_id != NEW.manager_id THEN
        -- Close old tenure
        IF OLD.manager_id != '' THEN
            UPDATE manager_sport 
            SET date_relieved = CURDATE() 
            WHERE sport_id = OLD.sport_id 
              AND user_id = OLD.manager_id 
              AND date_relieved IS NULL;
        END IF;
        -- Start new tenure
        IF NEW.manager_id != '' THEN
            INSERT INTO manager_sport (user_id, sport_id, date_started)
            VALUES (NEW.manager_id, NEW.sport_id, CURDATE());
        END IF;
    END IF;
END
$$
DELIMITER ;

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
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`sport_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sports-team`
--

INSERT INTO `sports-team` (`sport_id`, `student_id`, `joined_date`, `in_team`, `status`) VALUES
('ATH', '23000000', '2026-04-20', 'NO', 'ACTIVE'),
('ATH', '5Q1XZO2Y', '2025-12-11', 'NO', 'ACTIVE'),
('BAS', '23020342', '2026-04-18', 'NO', 'ACTIVE'),
('BOX', '23020342', '2026-04-18', 'NO', 'ACTIVE'),
('KRT', 'STU_KRT_01', '2026-04-18', 'NO', 'ACTIVE'),
('KRT', 'STU_KRT_02', '2026-04-18', 'NO', 'ACTIVE'),
('KRT', 'STU_KRT_03', '2026-04-18', 'NO', 'ACTIVE'),
('KRT', 'STU_KRT_04', '2026-04-18', 'NO', 'ACTIVE'),
('KRT', 'STU_KRT_05', '2026-04-18', 'NO', 'ACTIVE'),
('ROW', 'L3NCL2J4', '2025-12-15', 'NO', 'ACTIVE'),
('RR', '2023/cs/087', '2026-04-20', 'NO', 'ACTIVE'),
('TKD', 'L3NCL2J4', '2025-12-03', 'NO', 'ACTIVE'),
('VOL', '2023/s/1234', '2026-04-20', 'NO', 'ACTIVE'),
('VOL', '23001010', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', '5Q1XZO2Y', '2025-10-25', 'NO', 'ACTIVE'),
('VOL', 'L3NCL2J4', '2025-12-09', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_001', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_002', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_003', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_004', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_005', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_006', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_007', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_008', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_009', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_010', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_011', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_012', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_013', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_014', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_015', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_016', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_017', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_018', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_019', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VW_020', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_001', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_002', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_003', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_004', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_005', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_006', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_007', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_008', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_009', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_010', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_011', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_012', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_013', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_014', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_015', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_016', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_017', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_018', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_019', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VX_020', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_001', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_002', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_003', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_004', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_005', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_006', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_007', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_008', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_009', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_010', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_011', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_012', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_013', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_014', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_015', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_016', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_017', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_018', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_019', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU_VZ_020', '2026-04-18', 'NO', 'ACTIVE'),
('VOL', 'STU005', '2026-01-04', 'NO', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `sport_expenses`
--

DROP TABLE IF EXISTS `sport_expenses`;
CREATE TABLE IF NOT EXISTS `sport_expenses` (
  `expense_id` int NOT NULL AUTO_INCREMENT,
  `sport` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sport_event` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `receipt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expense_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sport_expenses`
--

INSERT INTO `sport_expenses` (`expense_id`, `sport`, `expense_title`, `sport_event`, `amount`, `receipt`, `submitted_by`, `notes`, `expense_date`, `created_at`, `updated_at`, `status`) VALUES
(7, 'Volleyball', 'Medical & First Aid', 'Inter-Faculty Tournament', '8000.00', '1776695719_Activity_Snapshot_AllTime (1).pdf', 'Dilini', NULL, '2026-04-20 20:05:00', '2026-04-20 14:35:19', '2026-04-20 14:35:19', 'ACTIVE');

--
-- Triggers `sport_expenses`
--
DROP TRIGGER IF EXISTS `trg_sport_expenses_delete`;
DELIMITER $$
CREATE TRIGGER `trg_sport_expenses_delete` AFTER DELETE ON `sport_expenses` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('sport_expenses', OLD.expense_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_sport_expenses_insert`;
DELIMITER $$
CREATE TRIGGER `trg_sport_expenses_insert` AFTER INSERT ON `sport_expenses` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('sport_expenses', NEW.expense_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'expense_id', NULL, NEW.expense_id),
    (v_audit_id, 'sport', NULL, NEW.sport),
    (v_audit_id, 'expense_title', NULL, NEW.expense_title),
    (v_audit_id, 'sport_event', NULL, NEW.sport_event),
    (v_audit_id, 'amount', NULL, NEW.amount),
    (v_audit_id, 'receipt', NULL, NEW.receipt),
    (v_audit_id, 'submitted_by', NULL, NEW.submitted_by),
    (v_audit_id, 'notes', NULL, NEW.notes),
    (v_audit_id, 'expense_date', NULL, NEW.expense_date),
    (v_audit_id, 'created_at', NULL, NEW.created_at),
    (v_audit_id, 'updated_at', NULL, NEW.updated_at),
    (v_audit_id, 'status', NULL, NEW.status);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_sport_expenses_update`;
DELIMITER $$
CREATE TRIGGER `trg_sport_expenses_update` AFTER UPDATE ON `sport_expenses` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.expense_id <=> OLD.expense_id AND NEW.sport <=> OLD.sport AND NEW.expense_title <=> OLD.expense_title AND NEW.sport_event <=> OLD.sport_event AND NEW.amount <=> OLD.amount AND NEW.receipt <=> OLD.receipt AND NEW.submitted_by <=> OLD.submitted_by AND NEW.notes <=> OLD.notes AND NEW.expense_date <=> OLD.expense_date AND NEW.created_at <=> OLD.created_at AND NEW.updated_at <=> OLD.updated_at AND NEW.status <=> OLD.status) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('sport_expenses', NEW.expense_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        IF NOT (NEW.expense_id <=> OLD.expense_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'expense_id', OLD.expense_id, NEW.expense_id); END IF;
        IF NOT (NEW.sport <=> OLD.sport) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'sport', OLD.sport, NEW.sport); END IF;
        IF NOT (NEW.expense_title <=> OLD.expense_title) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'expense_title', OLD.expense_title, NEW.expense_title); END IF;
        IF NOT (NEW.sport_event <=> OLD.sport_event) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'sport_event', OLD.sport_event, NEW.sport_event); END IF;
        IF NOT (NEW.amount <=> OLD.amount) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'amount', OLD.amount, NEW.amount); END IF;
        IF NOT (NEW.receipt <=> OLD.receipt) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'receipt', OLD.receipt, NEW.receipt); END IF;
        IF NOT (NEW.submitted_by <=> OLD.submitted_by) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'submitted_by', OLD.submitted_by, NEW.submitted_by); END IF;
        IF NOT (NEW.notes <=> OLD.notes) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'notes', OLD.notes, NEW.notes); END IF;
        IF NOT (NEW.expense_date <=> OLD.expense_date) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'expense_date', OLD.expense_date, NEW.expense_date); END IF;
        IF NOT (NEW.created_at <=> OLD.created_at) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'created_at', OLD.created_at, NEW.created_at); END IF;
        IF NOT (NEW.updated_at <=> OLD.updated_at) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'updated_at', OLD.updated_at, NEW.updated_at); END IF;
        IF NOT (NEW.status <=> OLD.status) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'status', OLD.status, NEW.status); END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student_email_verifications`
--

DROP TABLE IF EXISTS `student_email_verifications`;
CREATE TABLE IF NOT EXISTS `student_email_verifications` (
  `email` varchar(100) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_email_verifications`
--

INSERT INTO `student_email_verifications` (`email`, `otp_code`, `expires_at`, `is_verified`) VALUES
('2023is032@stu.ucsc.cmb.ac.lk', '741937', '2026-04-20 11:08:37', 0),
('2023is034@stu.ucsc.cmb.ac.lk', '979324', '2026-04-20 11:16:07', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

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

--
-- Table structure for table `system_audit`
--

DROP TABLE IF EXISTS `system_audit`;
CREATE TABLE IF NOT EXISTS `system_audit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `record_id` varchar(100) NOT NULL,
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `changed_by` varchar(50) DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_audit`
--

INSERT INTO `system_audit` (`id`, `table_name`, `record_id`, `action`, `changed_by`, `changed_at`) VALUES
(1, 'budget', 'BDG36FD069B7', 'INSERT', 'H4J1OHSX', '2026-04-20 14:23:57'),
(2, 'user', 'SPT004', 'UPDATE', 'SYSTEM', '2026-04-20 14:32:56'),
(3, 'sport_expenses', '7', 'INSERT', 'SPT004', '2026-04-20 14:35:19'),
(4, 'user', 'usr_68f89998', 'UPDATE', 'SYSTEM', '2026-04-20 16:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `system_audit_detail`
--

DROP TABLE IF EXISTS `system_audit_detail`;
CREATE TABLE IF NOT EXISTS `system_audit_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `audit_id` int NOT NULL,
  `column_name` varchar(100) NOT NULL,
  `old_value` text,
  `new_value` text,
  PRIMARY KEY (`id`),
  KEY `fk_audit_id` (`audit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_audit_detail`
--

INSERT INTO `system_audit_detail` (`id`, `audit_id`, `column_name`, `old_value`, `new_value`) VALUES
(1, 1, 'budget_id', NULL, 'BDG36FD069B7'),
(2, 1, 'sport_id', NULL, 'VOL'),
(3, 1, 'year', NULL, '2026'),
(4, 1, 'allocated_amount', NULL, '100000'),
(5, 1, 'spent_amount', NULL, '12000'),
(6, 1, 'allocation_date', NULL, '2026-04-20'),
(7, 1, 'description', NULL, '-'),
(8, 2, 'last_login_at', '2026-04-20 19:35:14', '2026-04-20 20:02:56'),
(9, 3, 'expense_id', NULL, '7'),
(10, 3, 'sport', NULL, 'Volleyball'),
(11, 3, 'expense_title', NULL, 'Medical & First Aid'),
(12, 3, 'sport_event', NULL, 'Inter-Faculty Tournament'),
(13, 3, 'amount', NULL, '8000.00'),
(14, 3, 'receipt', NULL, '1776695719_Activity_Snapshot_AllTime (1).pdf'),
(15, 3, 'submitted_by', NULL, 'Dilini'),
(16, 3, 'notes', NULL, NULL),
(17, 3, 'expense_date', NULL, '2026-04-20 20:05:00'),
(18, 3, 'created_at', NULL, '2026-04-20 20:05:19'),
(19, 3, 'updated_at', NULL, '2026-04-20 20:05:19'),
(20, 3, 'status', NULL, 'ACTIVE'),
(21, 4, 'last_login_at', '2026-04-20 15:10:06', '2026-04-20 21:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
CREATE TABLE IF NOT EXISTS `tournament` (
  `tournament_id` varchar(24) NOT NULL,
  `tournament_name` varchar(64) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'INCOMPLETE',
  `match_level` enum('UNIVERSITY','NATIONAL','INTERNATIONAL') NOT NULL DEFAULT 'UNIVERSITY',
  PRIMARY KEY (`tournament_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`tournament_id`, `tournament_name`, `sport_id`, `start_date`, `end_date`, `status`, `match_level`) VALUES
('T_VOL_1776533153', 'Inter-University Volleyball Championship 2026', 'VOL', '2026-04-10', '2026-04-12', 'COMPLETE', 'UNIVERSITY'),
('TOUR_693ea72aa6387', 'Vice Chancellors Invitational Badminton Championship', 'BAD', '2026-01-01', '2026-02-26', 'INCOMPLETE', 'UNIVERSITY'),
('TOUR_694cd4c59abad', 'Grand Inter-University Karate Open 2026', 'KRT', '2026-02-01', '2026-03-01', 'COMPLETE', 'UNIVERSITY'),
('TOUR_69ccde3bc3ae2', 'UOC Annual Athletics & Field Series', 'VOL', '2026-03-30', '2026-04-04', 'COMPLETE', 'UNIVERSITY'),
('TOUR_69d739fdd3a3b', 'Western Province Invitational Volleyball League', 'VOL', '2026-04-13', '2026-04-14', 'COMPLETE', 'NATIONAL'),
('TOUR_69e5f20679cba', 'National University Volleyball Finals', 'VOL', '2026-05-05', '2026-05-08', 'INCOMPLETE', 'NATIONAL'),
('TRN_KRT_2026', 'Inter-Faculty Karate Championship 2026', 'KRT', '2026-05-15', '2026-05-17', 'ACTIVE', 'UNIVERSITY');

--
-- Triggers `tournament`
--
DROP TRIGGER IF EXISTS `trg_tournament_delete`;
DELIMITER $$
CREATE TRIGGER `trg_tournament_delete` AFTER DELETE ON `tournament` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('tournament', OLD.tournament_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_tournament_insert`;
DELIMITER $$
CREATE TRIGGER `trg_tournament_insert` AFTER INSERT ON `tournament` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('tournament', NEW.tournament_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'tournament_id', NULL, NEW.tournament_id),
    (v_audit_id, 'tournament_name', NULL, NEW.tournament_name),
    (v_audit_id, 'sport_id', NULL, NEW.sport_id),
    (v_audit_id, 'start_date', NULL, NEW.start_date),
    (v_audit_id, 'end_date', NULL, NEW.end_date),
    (v_audit_id, 'status', NULL, NEW.status);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_tournament_update`;
DELIMITER $$
CREATE TRIGGER `trg_tournament_update` AFTER UPDATE ON `tournament` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.tournament_id <=> OLD.tournament_id AND NEW.tournament_name <=> OLD.tournament_name AND NEW.sport_id <=> OLD.sport_id AND NEW.start_date <=> OLD.start_date AND NEW.end_date <=> OLD.end_date AND NEW.status <=> OLD.status) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('tournament', NEW.tournament_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        IF NOT (NEW.tournament_id <=> OLD.tournament_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'tournament_id', OLD.tournament_id, NEW.tournament_id); END IF;
        IF NOT (NEW.tournament_name <=> OLD.tournament_name) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'tournament_name', OLD.tournament_name, NEW.tournament_name); END IF;
        IF NOT (NEW.sport_id <=> OLD.sport_id) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'sport_id', OLD.sport_id, NEW.sport_id); END IF;
        IF NOT (NEW.start_date <=> OLD.start_date) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'start_date', OLD.start_date, NEW.start_date); END IF;
        IF NOT (NEW.end_date <=> OLD.end_date) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'end_date', OLD.end_date, NEW.end_date); END IF;
        IF NOT (NEW.status <=> OLD.status) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'status', OLD.status, NEW.status); END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tournament_awards`
--

DROP TABLE IF EXISTS `tournament_awards`;
CREATE TABLE IF NOT EXISTS `tournament_awards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` varchar(24) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `award_title` varchar(100) NOT NULL,
  `points` int NOT NULL DEFAULT '3',
  `awarded_by` varchar(12) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_award` (`tournament_id`,`user_id`,`award_title`),
  KEY `idx_tournament` (`tournament_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_sport` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament_match`
--

INSERT INTO `tournament_match` (`match_id`, `tournament_id`, `sport_id`, `sport_category`, `match_name`, `match_date`, `winner_id`, `winner_name`, `winner_type`, `winner_invitational_id`, `result_status`, `is_published`, `submitted_by`, `created_at`) VALUES
('MAT_KRT_001_662140bb888', 'TRN_KRT_2026', 'KRT', 'COMBAT', 'Men\'s Kumite -60kg Final', '2026-05-17', 'STU_KRT_01', 'Kusal Mendis', 'INTERNAL', NULL, 'COMPLETED', 1, NULL, '2026-04-18 18:11:08'),
('match_695d2d74e031d4.90256849', 'TOUR_694cd4c59abad', 'KRT', 'COMBAT', 'National Open Quarter Final', '2026-01-01', 'L3NCL2J4', 'Naveen Rathnayake', 'INTERNAL', NULL, 'COMPLETED', 1, 'ADMIN', '2026-01-06 15:42:44'),
('match_69cce68043d035.97638693', 'TOUR_69ccde3bc3ae2', 'VOL', 'BALL_COURT', 'Inter-University Quarter Final', '2026-03-30', NULL, 'University of Kelaniya', 'TEAM', NULL, 'COMPLETED', 1, NULL, '2026-04-01 09:33:52'),
('match_69e44489860104.22699606', 'TOUR_69d739fdd3a3b', 'VOL', 'BALL_COURT', 'Invitational Final', '2026-04-14', NULL, 'University of Colombo', 'TEAM', NULL, 'COMPLETED', 1, 'CAPTAIN', '2026-04-19 02:57:13'),
('match_69e5f30468e1a6.49793767', 'TOUR_69d739fdd3a3b', 'VOL', 'BALL_COURT', 'Semi-Final Match', '2026-04-29', NULL, 'University of Moratuwa', 'TEAM', NULL, 'COMPLETED', 1, 'CAPTAIN', '2026-04-20 09:33:56'),
('MATCH_VOL_1776533155', 'T_VOL_1776533153', 'VOL', 'BALL_COURT', 'UOC vs USJP Final', '2026-04-12', NULL, 'University of Colombo', 'TEAM', NULL, 'COMPLETED', 1, NULL, '2026-04-18 17:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_participants`
--

DROP TABLE IF EXISTS `tournament_participants`;
CREATE TABLE IF NOT EXISTS `tournament_participants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` varchar(20) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `added_by` varchar(12) NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tournament_user` (`tournament_id`,`user_id`),
  KEY `fk_tp_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tournament_participants`
--

INSERT INTO `tournament_participants` (`id`, `tournament_id`, `user_id`, `added_by`, `added_at`, `status`) VALUES
(1, 'TOUR_69ccde3bc3ae2', '5Q1XZO2Y', '5Q1XZO2Y', '2026-04-18 07:18:06', 'ACTIVE'),
(2, 'TOUR_69ccde3bc3ae2', 'L3NCL2J4', '5Q1XZO2Y', '2026-04-18 07:18:08', 'INACTIVE'),
(3, 'TOUR_69ccde3bc3ae2', 'STU005', '5Q1XZO2Y', '2026-04-18 07:18:09', 'ACTIVE'),
(5, 'T_VOL_1776533153', 'STU_VW_001', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(6, 'T_VOL_1776533153', 'STU_VW_002', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(7, 'T_VOL_1776533153', 'STU_VW_003', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(8, 'T_VOL_1776533153', 'STU_VW_004', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(9, 'T_VOL_1776533153', 'STU_VW_005', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(10, 'T_VOL_1776533153', 'STU_VW_006', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(11, 'T_VOL_1776533153', 'STU_VW_007', 'ADMIN', '2026-04-18 17:25:54', 'ACTIVE'),
(12, 'T_VOL_1776533153', 'STU_VW_008', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(13, 'T_VOL_1776533153', 'STU_VW_009', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(14, 'T_VOL_1776533153', 'STU_VW_010', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(15, 'T_VOL_1776533153', 'STU_VW_011', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(16, 'T_VOL_1776533153', 'STU_VW_012', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(17, 'T_VOL_1776533153', 'STU_VW_013', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(18, 'T_VOL_1776533153', 'STU_VW_014', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(19, 'T_VOL_1776533153', 'STU_VW_015', 'ADMIN', '2026-04-18 17:25:55', 'ACTIVE'),
(35, 'TRN_KRT_2026', 'STU_KRT_01', 'H4J1OHSX', '2026-04-18 18:11:08', 'ACTIVE'),
(36, 'TRN_KRT_2026', 'STU_KRT_02', 'H4J1OHSX', '2026-04-18 18:11:08', 'ACTIVE'),
(37, 'TRN_KRT_2026', 'STU_KRT_03', 'H4J1OHSX', '2026-04-18 18:11:08', 'ACTIVE'),
(38, 'TRN_KRT_2026', 'STU_KRT_04', 'H4J1OHSX', '2026-04-18 18:11:08', 'ACTIVE'),
(39, 'TRN_KRT_2026', 'STU_KRT_05', 'H4J1OHSX', '2026-04-18 18:11:08', 'ACTIVE'),
(52, 'TOUR_69d739fdd3a3b', 'STU_VW_018', '5Q1XZO2Y', '2026-04-19 01:29:35', 'ACTIVE'),
(53, 'TOUR_69d739fdd3a3b', 'STU_VW_017', '5Q1XZO2Y', '2026-04-19 01:29:41', 'ACTIVE'),
(54, 'TOUR_69d739fdd3a3b', 'STU_VW_011', '5Q1XZO2Y', '2026-04-19 02:11:49', 'ACTIVE'),
(55, 'TOUR_69d739fdd3a3b', 'STU_VW_009', '5Q1XZO2Y', '2026-04-19 02:11:50', 'INACTIVE'),
(56, 'TOUR_69d739fdd3a3b', 'STU_VW_012', '5Q1XZO2Y', '2026-04-19 02:12:00', 'ACTIVE'),
(57, 'TOUR_69d739fdd3a3b', 'STU_VW_005', '5Q1XZO2Y', '2026-04-20 05:32:12', 'INACTIVE'),
(58, 'TOUR_69d739fdd3a3b', 'L3NCL2J4', '5Q1XZO2Y', '2026-04-20 05:32:16', 'ACTIVE'),
(59, 'TOUR_69d739fdd3a3b', 'STU_VW_002', '5Q1XZO2Y', '2026-04-20 05:33:42', 'ACTIVE'),
(60, 'TOUR_69d739fdd3a3b', 'STU_VW_007', '5Q1XZO2Y', '2026-04-20 09:31:28', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_result`
--

DROP TABLE IF EXISTS `tournament_result`;
CREATE TABLE IF NOT EXISTS `tournament_result` (
  `result_id` varchar(12) NOT NULL,
  `match_id` varchar(50) NOT NULL,
  `player_id` varchar(12) DEFAULT NULL,
  `team_id` varchar(12) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`result_id`),
  KEY `match_id` (`match_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `proof_doc` varchar(32) NOT NULL,
  `remarks` varchar(256) NOT NULL,
  `change_reason` varchar(256) NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `last_login_at` timestamp NULL DEFAULT NULL,
  `contact_no` varchar(12) DEFAULT NULL,
  `profile_img` varchar(64) NOT NULL,
  `sport_id` varchar(5) NOT NULL,
  `student_id` varchar(12) DEFAULT NULL,
  `faculty_id` varchar(4) DEFAULT NULL,
  `status` varchar(6) NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `Email` (`email`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `fname`, `lname`, `type`, `email`, `password`, `must_change_pass`, `joined_date`, `last_login_at`, `contact_no`, `profile_img`, `sport_id`, `student_id`, `faculty_id`, `status`) VALUES
('1', 'Chamal', 'Chamuditha', 'PUBLIC', 'chamal.c@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('101', 'Aruna', 'Perera', 'PUBLIC', 'aruna.p@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0771234567', '', '', NULL, '', 'ACTIVE'),
('102', 'David', 'Fernando', 'PUBLIC', 'david.f@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0779876543', '', '', NULL, '', 'ACTIVE'),
('103', 'Alex', 'Wijesinghe', 'PUBLIC', 'alex.w@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0713456789', '', '', NULL, '', 'ACTIVE'),
('104', 'Mark', 'Silva', 'PUBLIC', 'mark.s@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0752345678', '', '', NULL, '', 'ACTIVE'),
('105', 'Kamal', 'Jayasinghe', 'PUBLIC', 'kamal.j@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0761112233', '', '', NULL, '', 'ACTIVE'),
('201', 'Sameera', 'Dissanayake', 'PUBLIC', 'sameera.d@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0775566778', '', '', NULL, '', 'ACTIVE'),
('202', 'Nuwan', 'Karunaratne', 'EXECUTIVE', 'nuwan.k@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', '2026-04-18 14:36:07', '0711122334', '', '', NULL, '', 'ACTIVE'),
('203', 'Ruwan', 'Senanayake', 'PUBLIC', 'ruwan.s@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0729988776', '', '', NULL, '', 'ACTIVE'),
('204', 'Suresh', 'Kumara', 'PUBLIC', 'suresh.k@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0765544332', '', '', NULL, '', 'ACTIVE'),
('205', 'Ashan', 'Wijesinghe', 'PUBLIC', 'ashan.w@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0776677889', '', '', NULL, '', 'ACTIVE'),
('2240Q7DT', 'Pasan', 'Kumara', 'STUDENT', 'pasan.k@gmail.com', '$2y$10$tkD9qLNDVKwugT4xKjFW.euc0vBseNEkPZfeeqNnFWqLp20R92cJm', 0, '2026-04-18 17:08:59', '2026-04-18 17:09:25', NULL, '', '', '2022s12345', '1', 'ACTIVE'),
('301', 'Pradeep', 'Gunawardena', 'PUBLIC', 'pradeep.g@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0718899001', '', '', NULL, '', 'ACTIVE'),
('302', 'Chathura', 'Ekanayake', 'PUBLIC', 'chathura.e@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0752233445', '', '', NULL, '', 'ACTIVE'),
('303', 'Isuru', 'Lakshan', 'PUBLIC', 'isuru.l@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0723344556', '', '', NULL, '', 'ACTIVE'),
('304', 'Gayan', 'Rathnayake', 'PUBLIC', 'gayan.r@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0779988775', '', '', NULL, '', 'ACTIVE'),
('305', 'Roshan', 'Abeysinghe', 'PUBLIC', 'roshan.a@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-24 06:07:00', NULL, '0764455667', '', '', NULL, '', 'ACTIVE'),
('43N1VK76', 'Naveen', 'Rathnayake', 'PUBLIC', 'naveen.r@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:57:10', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('5Q1XZO2Y', 'Jansika', 'Balakrishnan', 'CAPTAIN', 'jansi.b@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-23 07:23:06', '2026-04-20 13:54:41', NULL, '5Q1XZO2Y.jpg', '', '23020342', '5', 'ACTIVE'),
('CE02XIPB', 'Admin', 'UOC', 'PUBLIC', 'admin@uocs.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 00:01:26', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('FK9C62HG', 'Pasindu', 'Anjana', 'PUBLIC', 'pasindu.a@anura.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('FMX6Z8DF', 'Shashini', 'Malsha', 'STUDENT', 'shashini.m@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-23 07:10:43', NULL, NULL, '', '', '23020997', '3', 'ACTIVE'),
('H4J1OHSX', 'Chamal', 'Chamuditha', 'ADMIN', 'chamal.admin@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-02 02:04:39', '2026-04-20 11:18:01', NULL, 'H4J1OHSX.png', '', NULL, '', 'ACTIVE'),
('JIIJ51LA', 'Kavindi', 'Gunawardena', 'PUBLIC', 'kavindi.g@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:39:19', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('JORD04QN', 'Sajith', 'Weerasinghe', 'PUBLIC', 'sajith.w@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:52:30', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('KCLIH538', 'Tharindu', 'Wickramasinghe', 'PUBLIC', 'tharindu.w@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:53:34', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('KI5RL42D', 'Menaka', 'Perera', 'PUBLIC', 'menaka.p@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:29:53', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('L3NCL2J4', 'Chamal', 'Hettiarachchi', 'STUDENT', 'chamal.h@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-14 04:48:58', '2026-04-20 09:52:26', NULL, 'L3NCL2J4.jpg', '', '23000000', '4', 'ACTIVE'),
('NPM8O9RE', 'Chamal', 'Chamuditha', 'COACH', 'chamal.coach@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 22:53:08', '2026-04-20 09:44:33', NULL, '', '', NULL, '', 'ACTIVE'),
('P1N116IE', 'Chamal', 'Perera', 'STUDENT', 'chamal.p@gmail.com', '$2y$10$MzKlqU/He4KLLamLkROvZe.4KCdmflN/yu5maZ1OjvrOHdJs7M/BC', 0, '2026-04-20 05:24:58', '2026-04-20 05:25:14', NULL, '', '', '2023/s/1234', '2', 'ACTIVE'),
('PA0XK3QZ', 'Hansani', 'Jayawardena', 'PUBLIC', 'hansani.j@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:32:55', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('R13QQJC2', 'Dimuthu', 'Karunaratne', 'PUBLIC', 'dimuthu.k@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:50:07', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('REG003', 'Kasun', 'Silva', 'REG', 'kasun.silva@ucsc.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '2026-04-18 14:36:07', '0773456789', '', '', '23001003', '1', 'ACTIVE'),
('SPT004', 'Dilini', 'Jayasinghe', 'SPT', 'dilini.jayasinghe@uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '2026-04-20 14:32:56', '0774567890', '', '', '23001004', '1', 'ACTIVE'),
('STU_KRT_01', 'Kusal', 'Mendis', 'STUDENT', 'kusal.mendis@uoc.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2026-04-18 18:08:38', NULL, NULL, '', 'KRT', NULL, '2', 'ACTIVE'),
('STU_KRT_02', 'Wanindu', 'Hasaranga', 'STUDENT', 'wanindu.h@uoc.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2026-04-18 18:08:38', NULL, NULL, '', 'KRT', NULL, '10', 'ACTIVE'),
('STU_KRT_03', 'Chamika', 'Karunaratne', 'STUDENT', 'chamika.k@uoc.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2026-04-18 18:08:38', NULL, NULL, '', 'KRT', NULL, '9', 'ACTIVE'),
('STU_KRT_04', 'Charith', 'Asalanka', 'STUDENT', 'charith.a@uoc.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2026-04-18 18:08:38', NULL, NULL, '', 'KRT', NULL, '8', 'ACTIVE'),
('STU_KRT_05', 'Dananjaya', 'de Silva', 'CAPTAIN', 'dds@uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 18:08:38', '2026-04-20 04:27:30', NULL, '', 'KRT', '2023/S/2381', '2', 'ACTIVE'),
('STU_VW_001', 'Aruna', 'Perera', 'STUDENT', 'aruna.p@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:34', NULL, NULL, '', '', '23000200', '1', 'ACTIVE'),
('STU_VW_002', 'Bandara', 'Fernando', 'STUDENT', 'bandara.f@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000201', '2', 'ACTIVE'),
('STU_VW_003', 'Chathura', 'Silva', 'STUDENT', 'chathura.s@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000202', '3', 'ACTIVE'),
('STU_VW_004', 'Dinesh', 'Jayasinghe', 'STUDENT', 'dinesh.j@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000203', '4', 'ACTIVE'),
('STU_VW_005', 'Eshan', 'Dissanayake', 'STUDENT', 'eshan.d@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000204', '5', 'ACTIVE'),
('STU_VW_006', 'Farook', 'Senanayake', 'STUDENT', 'farook.s@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000205', '6', 'ACTIVE'),
('STU_VW_007', 'Gayan', 'Gunawardena', 'STUDENT', 'gayan.g@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000206', '7', 'ACTIVE'),
('STU_VW_008', 'Harsha', 'Wickramasinghe', 'STUDENT', 'harsha.w@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000207', '8', 'ACTIVE'),
('STU_VW_009', 'Isuru', 'Ekanayake', 'STUDENT', 'isuru.e@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000208', '9', 'ACTIVE'),
('STU_VW_010', 'Janaka', 'Rathnayake', 'STUDENT', 'janaka.r@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000209', '10', 'ACTIVE'),
('STU_VW_011', 'Kamal', 'Amarasinghe', 'STUDENT', 'kamal.a@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000210', '1', 'ACTIVE'),
('STU_VW_012', 'Lakshan', 'Gamage', 'STUDENT', 'lakshan.g@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000211', '2', 'ACTIVE'),
('STU_VW_013', 'Mahesh', 'Hettiarachchi', 'STUDENT', 'mahesh.h@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:35', NULL, NULL, '', '', '23000212', '3', 'ACTIVE'),
('STU_VW_014', 'Nuwan', 'Karunaratne', 'STUDENT', 'nuwan.k@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000213', '4', 'ACTIVE'),
('STU_VW_015', 'Oshada', 'Liyanage', 'STUDENT', 'oshada.l@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000214', '5', 'ACTIVE'),
('STU_VW_016', 'Prabath', 'Wijesinghe', 'STUDENT', 'prabath.w@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000215', '6', 'ACTIVE'),
('STU_VW_017', 'Quinton', 'De Silva', 'STUDENT', 'quinton.d@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000216', '7', 'ACTIVE'),
('STU_VW_018', 'Ruwan', 'Abeyrathne', 'STUDENT', 'ruwan.a@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000217', '8', 'ACTIVE'),
('STU_VW_019', 'Sanjeewa', 'Jayawardena', 'STUDENT', 'sanjeewa.j@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000218', '9', 'ACTIVE'),
('STU_VW_020', 'Tharindu', 'Kumara', 'STUDENT', 'tharindu.k@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2026-04-18 17:24:36', NULL, NULL, '', '', '23000219', '10', 'ACTIVE'),
('STU001', 'Ashan', 'Fernando', 'STUDENT', 'ashan.f@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0771234567', '', '', '23001001', '4', 'ACTIVE'),
('STU002', 'Nimali', 'Perera', 'STUDENT', 'nimali.p@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0772345678', '', '', '23001002', '5', 'ACTIVE'),
('STU005', 'Tharindu', 'Wickramasinghe', 'STUDENT', 'tharindu.w2@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0775678901', '', '', '23001005', '6', 'ACTIVE'),
('STU006', 'Sanduni', 'Rathnayake', 'STUDENT', 'sanduni.r@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0776789012', '', '', '23001006', '7', 'ACTIVE'),
('STU007', 'Ravindu', 'Dissanayake', 'STUDENT', 'ravindu.d@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0777890123', '', '', '23001007', '8', 'ACTIVE'),
('STU008', 'Ishara', 'Gunasekara', 'STUDENT', 'ishara.g@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0778901234', '', '', '23001008', '9', 'ACTIVE'),
('STU009', 'Dineth', 'Amarasinghe', 'STUDENT', 'dineth.a@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', NULL, '0779012345', '', '', '23001009', '10', 'ACTIVE'),
('STU010', 'Sachini', 'Wijewardena', 'STUDENT', 'sachini.w@student.uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-12-10 17:47:24', '2026-04-18 19:30:26', '0770123456', '', '', '23001010', '1', 'ACTIVE'),
('UBVXZ90U', 'Dinuka', 'Perera', 'PUBLIC', 'dinuka.p@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:24:32', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('usr_68f82fe0', 'Shashini', 'Malsha', 'EQP', 'shashini.eqp@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-10-22 01:14:08', '2026-04-18 20:00:57', '076543213', '', '', NULL, '', 'ACTIVE'),
('usr_68f89998', 'Jaye', 'Jayaweera', 'EQP', 'jaye.jayaweera@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 08:45:12', '2026-04-20 16:22:20', '0763452143', '', '', NULL, '', 'ACTIVE'),
('usr_68f89be0', 'Janith', 'Jaye', 'SPT', 'janith.j@uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-10-22 08:54:56', '2026-04-20 11:59:41', '0763452145', '', 'CRI', NULL, '', 'ACTIVE'),
('usr_694d89fa', 'Amal', 'Shantha', 'SPT', 'amal.shantha@uoc.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 1, '2025-12-25 19:01:15', '2026-04-18 14:36:06', '0716379044', '', 'KBD', NULL, NULL, 'ACTIVE'),
('VSSMS4ZL', 'Ravindu', 'Rasa', 'PUBLIC', 'ravindu.r@kgla.lk', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-08-09 15:55:14', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('VTLMC3YK', 'Mahela', 'Jayawardene', 'PUBLIC', 'mahela.j@gmail.com', '$2y$10$6.jQeoNZuFwvekX/wmkBZeu/z2fTNOfsj2IHpop8ntxl7SJIO714q', 0, '2025-09-01 23:47:59', NULL, NULL, '', '', NULL, '', 'ACTIVE'),
('YSZLE02E', 'Anuruth', 'Kaushalya', 'STUDENT', 'anuruth.k@gmail.com', '$2y$10$zydwsgkDrqpLnEIgw0x7hO8ZV1DQeuHQv1BV7pfF8/fI7yGpJIT.W', 0, '2026-04-20 04:19:01', '2026-04-20 07:01:01', NULL, '', '', '2023/cs/087', '1', 'ACTIVE');

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `trg_user_delete`;
DELIMITER $$
CREATE TRIGGER `trg_user_delete` AFTER DELETE ON `user` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('user', OLD.user_id, 'DELETE', @current_user_id);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_user_insert`;
DELIMITER $$
CREATE TRIGGER `trg_user_insert` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    INSERT INTO system_audit (table_name, record_id, action, changed_by)
    VALUES ('user', NEW.user_id, 'INSERT', @current_user_id);
    SET v_audit_id = LAST_INSERT_ID();
    INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES 
    (v_audit_id, 'fname', NULL, NEW.fname),
    (v_audit_id, 'lname', NULL, NEW.lname),
    (v_audit_id, 'type', NULL, NEW.type),
    (v_audit_id, 'email', NULL, NEW.email);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_user_update`;
DELIMITER $$
CREATE TRIGGER `trg_user_update` AFTER UPDATE ON `user` FOR EACH ROW BEGIN
    DECLARE v_audit_id INT;
    IF NOT (NEW.fname <=> OLD.fname AND NEW.lname <=> OLD.lname AND NEW.type <=> OLD.type AND NEW.email <=> OLD.email AND NEW.last_login_at <=> OLD.last_login_at AND NEW.status <=> OLD.status) THEN
        INSERT INTO system_audit (table_name, record_id, action, changed_by)
        VALUES ('user', NEW.user_id, 'UPDATE', @current_user_id);
        SET v_audit_id = LAST_INSERT_ID();
        
        IF NOT (NEW.fname <=> OLD.fname) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'fname', OLD.fname, NEW.fname); END IF;
        IF NOT (NEW.lname <=> OLD.lname) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'lname', OLD.lname, NEW.lname); END IF;
        IF NOT (NEW.type <=> OLD.type) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'type', OLD.type, NEW.type); END IF;
        IF NOT (NEW.email <=> OLD.email) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'email', OLD.email, NEW.email); END IF;
        IF NOT (NEW.last_login_at <=> OLD.last_login_at) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'last_login_at', OLD.last_login_at, NEW.last_login_at); END IF;
        IF NOT (NEW.status <=> OLD.status) THEN INSERT INTO system_audit_detail (audit_id, column_name, old_value, new_value) VALUES (v_audit_id, 'status', OLD.status, NEW.status); END IF;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`user_id`, `user_points`) VALUES
('5Q1XZO2Y', 8),
('L3NCL2J4', 6),
('STU_VW_002', 6),
('STU_VW_005', 6),
('STU_VW_007', 3),
('STU_VW_009', 8),
('STU_VW_011', 8),
('STU_VW_012', 8),
('STU_VW_017', 8),
('STU_VW_018', 8),
('STU009', 0),
('STU010', 17);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sport`
--
ALTER TABLE `sport`
  ADD CONSTRAINT `sport_ibfk_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`);

--
-- Constraints for table `system_audit_detail`
--
ALTER TABLE `system_audit_detail`
  ADD CONSTRAINT `fk_audit_id` FOREIGN KEY (`audit_id`) REFERENCES `system_audit` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournament_participants`
--
ALTER TABLE `tournament_participants`
  ADD CONSTRAINT `fk_tp_tournament` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tp_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
