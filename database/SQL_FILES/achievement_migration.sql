-- =============================================
-- Achievement System Migration
-- Run this AFTER Main.sql has been applied
-- =============================================

-- 1. Add match_level to tournament table
ALTER TABLE `tournament`
  ADD COLUMN `match_level` ENUM('UNIVERSITY','NATIONAL','INTERNATIONAL') NOT NULL DEFAULT 'UNIVERSITY' AFTER `status`;

-- 2. Create match_players table
CREATE TABLE IF NOT EXISTS `match_players` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` varchar(50) NOT NULL,
  `user_id` varchar(12) DEFAULT NULL,
  `player_name` varchar(120) NOT NULL,
  `external_id` varchar(50) DEFAULT NULL,
  `team_side` enum('A','B') NOT NULL,
  `is_uoc_student` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_match` (`match_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. Create tournament_awards table
CREATE TABLE IF NOT EXISTS `tournament_awards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournament_id` varchar(24) NOT NULL,
  `sport_id` varchar(4) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `award_title` varchar(100) NOT NULL,
  `points` int NOT NULL DEFAULT 3,
  `awarded_by` varchar(12) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_unique_award` (`tournament_id`, `user_id`, `award_title`),
  KEY `idx_tournament` (`tournament_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_sport` (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 4. Create user_points table if it doesn't exist
CREATE TABLE IF NOT EXISTS `user_points` (
  `user_id` varchar(12) NOT NULL,
  `user_points` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 5. Update the achievement trigger to handle new point types
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

