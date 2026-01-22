-- ============================================
-- SAMPLE DATA FOR EQUIPMENT ANALYTICS
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================

-- First, let's add some damage to inventory (reduce usable) for condition alerts
UPDATE `equipment_inventory` SET `usable` = 15 WHERE `equipment_id` = 'EQ001'; -- Badminton Racket: 20 total, 15 usable
UPDATE `equipment_inventory` SET `usable` = 8 WHERE `equipment_id` = 'EQ011';  -- Tennis Racket: 12 total, 8 usable
UPDATE `equipment_inventory` SET `usable` = 10 WHERE `equipment_id` = 'EQ022'; -- Cricket Bat: 12 total, 10 usable
UPDATE `equipment_inventory` SET `usable` = 3 WHERE `equipment_id` = 'EQ040';  -- Chess Board: 15 total, 3 usable (high damage)
UPDATE `equipment_inventory` SET `usable` = 6 WHERE `equipment_id` = 'EQ017';  -- Hockey Stick: 20 total, 6 usable

-- ============================================
-- EQUIPMENT REQUESTS - Generating realistic booking data
-- Mix of dates, times, students, and equipment
-- ============================================

INSERT INTO `equipment-requests` (`request_id`, `student_id`, `equipment_id`, `request_date`, `start_time`, `end_time`, `purpose`, `status`, `notes`) VALUES
-- HIGH DEMAND: Badminton Racket (EQ001) - Many requests
('REQ00001', '23000001', 'EQ001', '2026-01-10', '08:00:00', '10:00:00', 'Morning practice', 'ACTIVE', '-'),
('REQ00002', '23000002', 'EQ001', '2026-01-10', '14:00:00', '16:00:00', 'Club practice', 'COMPLETED', '-'),
('REQ00003', '23000003', 'EQ001', '2026-01-11', '09:00:00', '11:00:00', 'Training session', 'ACTIVE', '-'),
('REQ00004', '23000001', 'EQ001', '2026-01-12', '15:00:00', '17:00:00', 'Match preparation', 'ACTIVE', '-'),
('REQ00005', '23000004', 'EQ001', '2026-01-13', '10:00:00', '12:00:00', 'Friendly match', 'COMPLETED', '-'),
('REQ00006', '23000005', 'EQ001', '2026-01-14', '14:00:00', '16:00:00', 'Practice session', 'ACTIVE', '-'),
('REQ00007', '23000002', 'EQ001', '2026-01-15', '16:00:00', '18:00:00', 'Evening practice', 'ACTIVE', '-'),
('REQ00008', '23000006', 'EQ001', '2026-01-16', '08:00:00', '10:00:00', 'Morning warmup', 'COMPLETED', '-'),
('REQ00009', '23000003', 'EQ001', '2026-01-17', '13:00:00', '15:00:00', 'Tournament prep', 'ACTIVE', '-'),

-- HIGH DEMAND: Basketball (EQ014) - Popular
('REQ00010', '23000007', 'EQ014', '2026-01-10', '09:00:00', '11:00:00', 'Team practice', 'ACTIVE', '-'),
('REQ00011', '23000008', 'EQ014', '2026-01-11', '15:00:00', '17:00:00', 'Shooting drills', 'COMPLETED', '-'),
('REQ00012', '23000007', 'EQ014', '2026-01-12', '14:00:00', '16:00:00', 'Match practice', 'ACTIVE', '-'),
('REQ00013', '23000009', 'EQ014', '2026-01-13', '16:00:00', '18:00:00', 'Evening game', 'ACTIVE', '-'),
('REQ00014', '23000010', 'EQ014', '2026-01-14', '10:00:00', '12:00:00', 'Training', 'COMPLETED', '-'),
('REQ00015', '23000008', 'EQ014', '2026-01-15', '09:00:00', '11:00:00', 'Practice match', 'ACTIVE', '-'),
('REQ00016', '23000011', 'EQ014', '2026-01-16', '15:00:00', '17:00:00', 'Team session', 'ACTIVE', '-'),

-- MODERATE: Cricket Bat (EQ022)
('REQ00017', '23000012', 'EQ022', '2026-01-10', '08:00:00', '11:00:00', 'Net practice', 'COMPLETED', '-'),
('REQ00018', '23000013', 'EQ022', '2026-01-12', '09:00:00', '12:00:00', 'Batting practice', 'ACTIVE', '-'),
('REQ00019', '23000012', 'EQ022', '2026-01-14', '15:00:00', '18:00:00', 'Match simulation', 'COMPLETED', '-'),
('REQ00020', '23000014', 'EQ022', '2026-01-16', '10:00:00', '13:00:00', 'Team practice', 'ACTIVE', '-'),
('REQ00021', '23000015', 'EQ022', '2026-01-17', '14:00:00', '17:00:00', 'Tournament prep', 'ACTIVE', '-'),

-- MODERATE: Football (EQ007)
('REQ00022', '23000016', 'EQ007', '2026-01-10', '16:00:00', '18:00:00', 'Evening practice', 'COMPLETED', '-'),
('REQ00023', '23000017', 'EQ007', '2026-01-11', '15:00:00', '17:00:00', 'Passing drills', 'ACTIVE', '-'),
('REQ00024', '23000016', 'EQ007', '2026-01-13', '09:00:00', '11:00:00', 'Morning session', 'COMPLETED', '-'),
('REQ00025', '23000018', 'EQ007', '2026-01-15', '14:00:00', '16:00:00', 'Free kicks', 'ACTIVE', '-'),
('REQ00026', '23000019', 'EQ007', '2026-01-17', '16:00:00', '18:00:00', 'Match day prep', 'ACTIVE', '-'),

-- MODERATE: Tennis Racket (EQ011)
('REQ00027', '23000020', 'EQ011', '2026-01-10', '07:00:00', '09:00:00', 'Early practice', 'COMPLETED', '-'),
('REQ00028', '23000021', 'EQ011', '2026-01-12', '10:00:00', '12:00:00', 'Singles practice', 'ACTIVE', '-'),
('REQ00029', '23000020', 'EQ011', '2026-01-14', '15:00:00', '17:00:00', 'Doubles game', 'COMPLETED', '-'),
('REQ00030', '23000022', 'EQ011', '2026-01-16', '09:00:00', '11:00:00', 'Serve practice', 'ACTIVE', '-'),

-- LOW USAGE: Volleyball (EQ004)
('REQ00031', '23000023', 'EQ004', '2026-01-11', '14:00:00', '16:00:00', 'Team volleyball', 'COMPLETED', '-'),
('REQ00032', '23000024', 'EQ004', '2026-01-17', '10:00:00', '12:00:00', 'Spiking practice', 'ACTIVE', '-'),

-- LOW USAGE: Hockey Stick (EQ017)
('REQ00033', '23000025', 'EQ017', '2026-01-12', '08:00:00', '10:00:00', 'Dribbling drills', 'COMPLETED', '-'),

-- Table Tennis (EQ031) - Some usage
('REQ00034', '23000001', 'EQ031', '2026-01-10', '12:00:00', '13:00:00', 'Lunch break game', 'COMPLETED', '-'),
('REQ00035', '23000002', 'EQ031', '2026-01-13', '13:00:00', '14:00:00', 'Quick match', 'ACTIVE', '-'),
('REQ00036', '23000003', 'EQ031', '2026-01-15', '12:00:00', '13:00:00', 'Recreation', 'COMPLETED', '-'),

-- Chess (EQ040) - Some usage
('REQ00037', '23000004', 'EQ040', '2026-01-11', '14:00:00', '16:00:00', 'Chess club', 'COMPLETED', '-'),
('REQ00038', '23000005', 'EQ040', '2026-01-14', '15:00:00', '17:00:00', 'Tournament prep', 'ACTIVE', '-'),

-- Carrom (EQ060) - Casual usage
('REQ00039', '23000006', 'EQ060', '2026-01-12', '13:00:00', '14:00:00', 'Recreation', 'COMPLETED', '-'),
('REQ00040', '23000007', 'EQ060', '2026-01-16', '12:00:00', '13:00:00', 'Casual game', 'ACTIVE', '-'),

-- HISTORICAL DATA - Last month (December) for trends
('REQ00041', '23000001', 'EQ001', '2025-12-05', '10:00:00', '12:00:00', 'Practice', 'COMPLETED', '-'),
('REQ00042', '23000002', 'EQ001', '2025-12-10', '14:00:00', '16:00:00', 'Training', 'COMPLETED', '-'),
('REQ00043', '23000003', 'EQ014', '2025-12-12', '09:00:00', '11:00:00', 'Team game', 'COMPLETED', '-'),
('REQ00044', '23000004', 'EQ022', '2025-12-15', '15:00:00', '18:00:00', 'Net session', 'COMPLETED', '-'),
('REQ00045', '23000005', 'EQ007', '2025-12-18', '16:00:00', '18:00:00', 'Football match', 'COMPLETED', '-'),
('REQ00046', '23000001', 'EQ001', '2025-12-20', '08:00:00', '10:00:00', 'Morning game', 'COMPLETED', '-'),
('REQ00047', '23000006', 'EQ011', '2025-12-22', '10:00:00', '12:00:00', 'Tennis match', 'COMPLETED', '-'),
('REQ00048', '23000002', 'EQ014', '2025-12-28', '14:00:00', '16:00:00', 'Holiday game', 'COMPLETED', '-'),

-- November data for monthly trends
('REQ00049', '23000007', 'EQ001', '2025-11-10', '09:00:00', '11:00:00', 'Practice', 'COMPLETED', '-'),
('REQ00050', '23000008', 'EQ014', '2025-11-15', '15:00:00', '17:00:00', 'Team game', 'COMPLETED', '-'),
('REQ00051', '23000009', 'EQ022', '2025-11-20', '10:00:00', '13:00:00', 'Cricket nets', 'COMPLETED', '-'),
('REQ00052', '23000010', 'EQ007', '2025-11-25', '16:00:00', '18:00:00', 'Football', 'COMPLETED', '-'),

-- October data
('REQ00053', '23000011', 'EQ001', '2025-10-05', '14:00:00', '16:00:00', 'Badminton', 'COMPLETED', '-'),
('REQ00054', '23000012', 'EQ014', '2025-10-12', '10:00:00', '12:00:00', 'Basketball', 'COMPLETED', '-'),
('REQ00055', '23000013', 'EQ011', '2025-10-18', '09:00:00', '11:00:00', 'Tennis', 'COMPLETED', '-');

-- ============================================
-- SUMMARY OF WHAT THIS DATA PROVIDES:
-- ============================================
-- Utilization: Badminton Racket & Basketball have high active bookings
-- High Demand: EQ001 (Badminton), EQ014 (Basketball) show pressure
-- Underutilized: Many equipment types with 0 bookings
-- Peak Hours: Most bookings at 14:00-16:00 and 09:00-11:00
-- Peak Days: Data spread across weekdays
-- Condition Alerts: Chess Board (80% damaged), Hockey Stick (70% damaged)
-- Sport Demand: Badminton, Basketball, Cricket lead
-- Booking Duration: Cricket has longest (3 hours), Table Tennis shortest (1 hour)
-- Active Students: 23000001, 23000002, 23000012 appear frequently
