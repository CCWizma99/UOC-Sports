CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` varchar(12) NOT NULL,
  `user_id` varchar(12) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
