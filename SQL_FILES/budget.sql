DROP TABLE IF EXISTS `budget`;
CREATE TABLE IF NOT EXISTS `budget` (
  `budget_id` varchar(12) NOT NULL,
  `sport_id` varchar(6) NOT NULL,
  `year` year NOT NULL,
  `allocated_amount` int NOT NULL,
  `spent_amount` int NOT NULL,
  `allocation_date` date NOT NULL,
  PRIMARY KEY (`budget_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`) VALUES
('ANUTKD01', 'TKDA', 2025, 400000, 178000, '2024-12-15'),
('ANUTKD02', 'TKDA', 2025, 100000, 9000, '2024-12-18');
COMMIT;