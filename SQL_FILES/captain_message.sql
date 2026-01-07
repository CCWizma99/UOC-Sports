-- ========================================
-- Captain Message Table for UOC Sports
-- ========================================
-- This table stores messages sent by captains to their sport's coach and manager.
-- 
-- HOW TO USE:
-- 1. Copy this entire SQL file contents
-- 2. Go to phpMyAdmin (http://localhost/phpmyadmin)
-- 3. Select the 'uoc-sports' database
-- 4. Click on the 'SQL' tab
-- 5. Paste the SQL and click 'Go'
-- ========================================

DROP TABLE IF EXISTS `captain_message`;
CREATE TABLE IF NOT EXISTS `captain_message` (
  `message_id` varchar(12) NOT NULL,
  `sender_id` varchar(12) NOT NULL COMMENT 'Captain user_id',
  `recipient_id` varchar(12) NOT NULL COMMENT 'Coach or Manager user_id',
  `recipient_type` enum('COACH','MANAGER') NOT NULL,
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
