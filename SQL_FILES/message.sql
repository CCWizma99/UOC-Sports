-- ========================================
-- Message Table for UOC Sports
-- ========================================
-- This table stores messages between Captains, Coaches, and Sports Managers.
-- 
-- HOW TO USE:
-- 1. Copy this entire SQL file contents
-- 2. Go to phpMyAdmin (http://localhost/phpmyadmin)
-- 3. Select the 'uoc-sports' database
-- 4. Click on the 'SQL' tab
-- 5. Paste the SQL and click 'Go'
-- ========================================

-- Create the generic message table
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
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
