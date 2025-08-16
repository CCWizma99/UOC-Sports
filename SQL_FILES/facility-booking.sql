DROP TABLE IF EXISTS `facility-booking`;
CREATE TABLE IF NOT EXISTS `facility-booking` (
  `booking_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `facility_id` varchar(8) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `ent_time` time NOT NULL,
  `purpose` varchar(300) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'BOOKED',
  `payment_status` varchar(12) NOT NULL DEFAULT 'INCOMPLETE',
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility-booking`
--

INSERT INTO `facility-booking` (`booking_id`, `user_id`, `facility_id`, `date`, `start_time`, `ent_time`, `purpose`, `status`, `payment_status`) VALUES
('GNDA25081201', 'VSSMS4ZL', 'GNDA', '2025-08-12', '08:30:00', '17:00:00', 'Annual \"Aware Aurudu Event\" By UCSC', 'BOOKED', 'INCOMPLETE');
COMMIT;