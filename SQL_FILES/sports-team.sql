DROP TABLE IF EXISTS `sports-team`;
CREATE TABLE IF NOT EXISTS `sports-team` (
  `sport_id` int NOT NULL,
  `student_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;