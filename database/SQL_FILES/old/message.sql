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
);
