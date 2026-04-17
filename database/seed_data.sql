-- ========================================
-- UOC Sports System - Seed Data
-- Comprehensive test data with all integrity constraints
-- Generated for Phase 1-3 Executive Dashboard testing
-- ========================================

-- First, disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS=0;

-- ========================================
-- 1. FACULTY DATA (Already exists, just ensuring coverage)
-- ========================================
DELETE FROM faculty WHERE faculty_id IN ('1', '2', '3', '4', '5', '6', '7', '8');
INSERT INTO `faculty` (`faculty_id`, `faculty_name`, `registrar_id`, `registrar_email`) VALUES
('1', 'Faculty of Science', 'REG003', 'registrar.science@uoc.lk'),
('2', 'Faculty of Arts', 'REG004', 'registrar.arts@uoc.lk'),
('3', 'Faculty of Education', 'REG005', 'registrar.education@uoc.lk'),
('4', 'Faculty of Medicine', 'REG006', 'registrar.medicine@uoc.lk'),
('5', 'Faculty of Law', NULL, 'registrar.law@uoc.lk'),
('6', 'Faculty of Management', NULL, 'registrar.management@uoc.lk'),
('7', 'Faculty of Engineering', NULL, 'registrar.engineering@uoc.lk'),
('8', 'Faculty of Indigenous Medicine', NULL, 'registrar.indigenous@uoc.lk');

-- ========================================
-- 2. USER DATA (Moved and consolidated in Main.sql)
-- ========================================

-- ========================================
-- 3. SPORT DATA with faculty assignments
-- ========================================
DELETE FROM sport WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `sport` (`sport_id`, `sport_name`, `sport_category`, `coach_id`, `captain_id`, `manager_id`, `faculty_id`) VALUES
('CRI', 'Cricket', 'CRICKET', 'COACH01', 'CAP001', 'MGR001', '1'),
('BAD', 'Badminton', 'RACKET', 'COACH02', 'CAP002', 'MGR002', '1'),
('FOO', 'Football', 'TEAM_GOAL', 'COACH03', 'CAP003', 'MGR003', '2'),
('VOL', 'Volleyball', 'BALL_COURT', 'COACH04', 'CAP004', 'MGR004', '1'),
('TEN', 'Tennis', 'RACKET', 'COACH05', 'CAP005', 'MGR005', '2');

-- ========================================
-- 4. BUDGET DATA (2025-2026)
-- ========================================
DELETE FROM budget WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `budget` (`budget_id`, `sport_id`, `year`, `allocated_amount`, `spent_amount`, `allocation_date`, `description`) VALUES
-- 2025 Budgets
('BDG_CRI_2025', 'CRI', 2025, 500000, 285000, '2025-01-15', 'Cricket team annual budget including equipment and travel'),
('BDG_BAD_2025', 'BAD', 2025, 250000, 145000, '2025-02-10', 'Badminton annual budget'),
('BDG_FOO_2025', 'FOO', 2025, 400000, 220000, '2025-01-20', 'Football team budget'),
('BDG_VOL_2025', 'VOL', 2025, 200000, 95000, '2025-03-05', 'Volleyball budget'),
('BDG_TEN_2025', 'TEN', 2025, 180000, 60000, '2025-02-15', 'Tennis budget'),
-- 2026 Budgets
('BDG_CRI_2026', 'CRI', 2026, 550000, 180000, '2026-01-10', 'Cricket team 2026 budget'),
('BDG_BAD_2026', 'BAD', 2026, 280000, 95000, '2026-02-05', 'Badminton 2026 budget'),
('BDG_FOO_2026', 'FOO', 2026, 420000, 130000, '2026-01-15', 'Football 2026 budget'),
('BDG_VOL_2026', 'VOL', 2026, 220000, 70000, '2026-03-01', 'Volleyball 2026 budget'),
('BDG_TEN_2026', 'TEN', 2026, 200000, 50000, '2026-02-10', 'Tennis 2026 budget');

-- ========================================
-- 5. FACILITY RATES DATA
-- ========================================
DELETE FROM facility_rates WHERE facility_name IN ('Main Sports Ground', 'Indoor Gym Hall A', 'Tennis Court 1', 'Badminton Hall', 'Swimming Pool');
INSERT INTO `facility_rates` (`facility_name`, `rate_per_hour`) VALUES
('Main Sports Ground', 5000),
('Indoor Gym Hall A', 3000),
('Tennis Court 1', 2500),
('Badminton Hall', 2000),
('Swimming Pool', 1500);

-- ========================================
-- 6. EQUIPMENT DATA
-- ========================================
DELETE FROM equipment WHERE equipment_name IN ('Cricket Bat', 'Cricket Ball', 'Badminton Racket', 'Shuttlecock', 'Football', 'Volleyball', 'Tennis Racket', 'Tennis Ball');
INSERT INTO `equipment` (`equipment_id`, `equipment_name`) VALUES
('EQP001', 'Cricket Bat'),
('EQP002', 'Cricket Ball'),
('EQP003', 'Badminton Racket'),
('EQP004', 'Shuttlecock'),
('EQP005', 'Football'),
('EQP006', 'Volleyball'),
('EQP007', 'Tennis Racket'),
('EQP008', 'Tennis Ball');

-- ========================================
-- 7. EQUIPMENT INVENTORY
-- ========================================
DELETE FROM equipment_inventory WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `equipment_inventory` (`equipment_id`, `sport_id`, `quantity`, `purchase_date`) VALUES
('EQP001', 'CRI', 25, '2025-06-01'),
('EQP002', 'CRI', 480, '2025-06-05'),
('EQP003', 'BAD', 20, '2025-05-10'),
('EQP004', 'BAD', 150, '2025-05-12'),
('EQP005', 'FOO', 15, '2025-04-20'),
('EQP006', 'VOL', 12, '2025-05-15'),
('EQP007', 'TEN', 18, '2025-06-10'),
('EQP008', 'TEN', 300, '2025-06-12');

-- ========================================
-- 8. GOOD RECEIVED NOTES (Equipment Procurement)
-- ========================================
DELETE FROM good_received_notes WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `good_received_notes` (`equipment_id`, `sport_id`, `date`, `quantity`, `unit_price`, `notes`) VALUES
('EQP001', 'CRI', '2025-06-01', 25, 8000, 'Premium cricket bats - imported'),
('EQP002', 'CRI', '2025-06-05', 480, 500, 'Bulk order of cricket balls'),
('EQP003', 'BAD', '2025-05-10', 20, 4000, 'Professional badminton rackets'),
('EQP004', 'BAD', '2025-05-12', 150, 300, 'Professional grade shuttlecocks'),
('EQP005', 'FOO', '2025-04-20', 15, 6000, 'Official match footballs'),
('EQP006', 'VOL', '2025-05-15', 12, 5000, 'Professional volleyballs'),
('EQP007', 'TEN', '2025-06-10', 18, 5500, 'Professional tennis rackets'),
('EQP008', 'TEN', '2025-06-12', 300, 400, 'Tennis balls (boxes)'),
('EQP001', 'CRI', '2026-01-15', 10, 8500, 'Replacement cricket bats - 2026'),
('EQP002', 'CRI', '2026-01-20', 240, 550, 'Cricket ball replenishment for 2026');

-- ========================================
-- 9. TOURNAMENT DATA
-- ========================================
DELETE FROM tournament WHERE tournament_id IN ('TOUR001', 'TOUR002', 'TOUR003', 'TOUR004', 'TOUR005');
INSERT INTO `tournament` (`tournament_id`, `tournament_name`, `sport_id`, `date`, `status`, `organizer`) VALUES
('TOUR001', 'Inter-Faculty Cricket Championship 2025', 'CRI', '2025-10-15', 'COMPLETED', 'Sports Directorate'),
('TOUR002', 'Badminton Doubles 2025', 'BAD', '2025-11-20', 'COMPLETED', 'Sports Directorate'),
('TOUR003', 'Football League 2025', 'FOO', '2025-09-01', 'COMPLETED', 'Sports Directorate'),
('TOUR004', 'Volleyball Championship 2026', 'VOL', '2026-03-15', 'UPCOMING', 'Sports Directorate'),
('TOUR005', 'Tennis Singles 2026', 'TEN', '2026-04-10', 'SCHEDULED', 'Sports Directorate');

-- ========================================
-- 10. TOURNAMENT MATCHES
-- ========================================
DELETE FROM `tournament-match` WHERE tournament_id IN ('TOUR001', 'TOUR002', 'TOUR003', 'TOUR004', 'TOUR005');
INSERT INTO `tournament-match` (`tournament_id`, `match_id`, `date`, `time`, `status`, `result`, `team1_score`, `team2_score`) VALUES
('TOUR001', 'MATCH001', '2025-10-15', '10:00', 'COMPLETED', 'TEAM1_WON', '175', '142'),
('TOUR001', 'MATCH002', '2025-10-16', '10:00', 'COMPLETED', 'TEAM2_WON', '168', '160'),
('TOUR001', 'MATCH003', '2025-10-17', '10:00', 'COMPLETED', 'TIE', '155', '155'),
('TOUR002', 'MATCH004', '2025-11-20', '14:00', 'COMPLETED', 'TEAM1_WON', '21', '15'),
('TOUR002', 'MATCH005', '2025-11-21', '14:00', 'COMPLETED', 'TEAM2_WON', '19', '17'),
('TOUR003', 'MATCH006', '2025-09-01', '16:00', 'COMPLETED', 'TEAM1_WON', '3', '2'),
('TOUR003', 'MATCH007', '2025-09-02', '16:00', 'COMPLETED', 'TEAM1_WON', '4', '1'),
('TOUR004', 'MATCH008', '2026-03-15', '18:00', 'SCHEDULED', 'PENDING', NULL, NULL),
('TOUR004', 'MATCH009', '2026-03-16', '18:00', 'SCHEDULED', 'PENDING', NULL, NULL),
('TOUR005', 'MATCH010', '2026-04-10', '15:00', 'SCHEDULED', 'PENDING', NULL, NULL);

-- ========================================
-- 11. SPORT EXPENSES
-- ========================================
DELETE FROM sport_expenses WHERE budget_id IN ('BDG_CRI_2025', 'BDG_BAD_2025', 'BDG_FOO_2025', 'BDG_VOL_2025', 'BDG_TEN_2025', 'BDG_CRI_2026', 'BDG_BAD_2026', 'BDG_FOO_2026', 'BDG_VOL_2026', 'BDG_TEN_2026');
INSERT INTO `sport_expenses` (`expense_id`, `budget_id`, `expense_category`, `expense_amount`, `expense_date`, `description`, `receipt_status`) VALUES
('EXP001', 'BDG_CRI_2025', 'Equipment Purchase', 75000, '2025-06-01', 'Cricket equipment purchase', 'VERIFIED'),
('EXP002', 'BDG_CRI_2025', 'Travel', 45000, '2025-08-15', 'Team travel for championship', 'VERIFIED'),
('EXP003', 'BDG_CRI_2025', 'Training', 35000, '2025-07-20', 'Coach honorarium and training', 'VERIFIED'),
('EXP004', 'BDG_CRI_2025', 'Tournament Fee', 50000, '2025-09-01', 'Tournament registration fee', 'VERIFIED'),
('EXP005', 'BDG_CRI_2025', 'Maintenance', 30000, '2025-10-10', 'Ground maintenance', 'VERIFIED'),
('EXP006', 'BDG_CRI_2025', 'Accommodation', 50000, '2025-10-14', 'Team accommodation for championship', 'VERIFIED'),
('EXP007', 'BDG_BAD_2025', 'Equipment Purchase', 35000, '2025-05-10', 'Badminton rackets and shuttles', 'VERIFIED'),
('EXP008', 'BDG_BAD_2025', 'Travel', 25000, '2025-11-15', 'Travel to tournament', 'VERIFIED'),
('EXP009', 'BDG_BAD_2025', 'Training', 20000, '2025-06-01', 'Coach training sessions', 'VERIFIED'),
('EXP010', 'BDG_BAD_2025', 'Tournament Fee', 40000, '2025-10-20', 'Badminton championship fee', 'VERIFIED'),
('EXP011', 'BDG_BAD_2025', 'Accommodation', 25000, '2025-11-18', 'Team accommodation', 'VERIFIED'),
('EXP012', 'BDG_FOO_2025', 'Equipment Purchase', 60000, '2025-04-20', 'Footballs and training gear', 'VERIFIED'),
('EXP013', 'BDG_FOO_2025', 'Travel', 50000, '2025-08-25', 'Team travel for matches', 'VERIFIED'),
('EXP014', 'BDG_FOO_2025', 'Training', 45000, '2025-05-01', 'Coach salaries', 'VERIFIED'),
('EXP015', 'BDG_FOO_2025', 'Medical', 35000, '2025-09-15', 'Medical staff and first aid', 'VERIFIED'),
('EXP016', 'BDG_FOO_2025', 'Ground Rental', 30000, '2025-06-01', 'Training ground rental', 'VERIFIED'),
('EXP017', 'BDG_VOL_2025', 'Equipment Purchase', 25000, '2025-05-15', 'Volleyballs and nets', 'VERIFIED'),
('EXP018', 'BDG_VOL_2025', 'Travel', 20000, '2025-07-20', 'Team travel', 'VERIFIED'),
('EXP019', 'BDG_VOL_2025', 'Training', 18000, '2025-06-01', 'Coach sessions', 'VERIFIED'),
('EXP020', 'BDG_VOL_2025', 'Facility Rental', 22000, '2025-08-01', 'Hall rental for practice', 'VERIFIED'),
('EXP021', 'BDG_VOL_2025', 'Uniforms', 10000, '2025-09-01', 'Team uniforms', 'VERIFIED'),
('EXP022', 'BDG_TEN_2025', 'Equipment Purchase', 25000, '2025-06-10', 'Tennis rackets and balls', 'VERIFIED'),
('EXP023', 'BDG_TEN_2025', 'Court Rental', 20000, '2025-07-01', 'Court rental fees', 'VERIFIED'),
('EXP024', 'BDG_TEN_2025', 'Training', 15000, '2025-06-15', 'Coach training', 'VERIFIED'),
('EXP025', 'BDG_CRI_2026', 'Equipment Purchase', 50000, '2026-01-15', 'New cricket bats and balls', 'VERIFIED'),
('EXP026', 'BDG_CRI_2026', 'Travel', 60000, '2026-02-20', 'Tournament travel', 'PENDING'),
('EXP027', 'BDG_CRI_2026', 'Training', 40000, '2026-01-25', 'Pre-season training', 'PENDING'),
('EXP028', 'BDG_CRI_2026', 'Ground Maintenance', 30000, '2026-02-10', 'Ground preparation', 'VERIFIED'),
('EXP029', 'BDG_BAD_2026', 'Equipment Purchase', 30000, '2026-02-05', 'New badminton equipment', 'VERIFIED'),
('EXP030', 'BDG_BAD_2026', 'Training', 40000, '2026-02-01', 'Training camp', 'VERIFIED'),
('EXP031', 'BDG_BAD_2026', 'Travel', 25000, '2026-03-10', 'Tournament travel', 'PENDING'),
('EXP032', 'BDG_FOO_2026', 'Equipment Purchase', 50000, '2026-01-15', 'New footballs and gear', 'VERIFIED'),
('EXP033', 'BDG_FOO_2026', 'Training', 50000, '2026-01-20', 'Pre-season coaching', 'VERIFIED'),
('EXP034', 'BDG_FOO_2026', 'Travel', 30000, '2026-02-25', 'Away match travel', 'PENDING'),
('EXP035', 'BDG_VOL_2026', 'Equipment Purchase', 20000, '2026-03-01', 'Volleyballs and nets', 'VERIFIED'),
('EXP036', 'BDG_VOL_2026', 'Training', 25000, '2026-03-05', 'Training sessions', 'VERIFIED'),
('EXP037', 'BDG_VOL_2026', 'Facility Rental', 25000, '2026-03-10', 'Hall rental', 'PENDING'),
('EXP038', 'BDG_TEN_2026', 'Equipment Purchase', 20000, '2026-02-10', 'Tennis rackets', 'VERIFIED'),
('EXP039', 'BDG_TEN_2026', 'Court Rental', 15000, '2026-02-15', 'Court fees', 'VERIFIED'),
('EXP040', 'BDG_TEN_2026', 'Training', 15000, '2026-02-20', 'Training camp', 'PENDING');

-- ========================================
-- 12. PRACTICE SESSIONS
-- ========================================
DELETE FROM practice_session WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `practice_session` (`practice_id`, `sport_id`, `date`, `time`, `coach_id`, `location`, `notes`) VALUES
(1, 'CRI', '2025-10-08', '10:00', 'COACH01', 'Main Sports Ground', 'Pre-tournament preparation'),
(2, 'CRI', '2025-10-09', '10:00', 'COACH01', 'Main Sports Ground', 'Batting practice'),
(3, 'CRI', '2025-10-10', '10:00', 'COACH01', 'Main Sports Ground', 'Fielding drills'),
(4, 'BAD', '2025-11-15', '15:00', 'COACH02', 'Badminton Hall', 'Doubles practice'),
(5, 'BAD', '2025-11-18', '15:00', 'COACH02', 'Badminton Hall', 'Singles practice'),
(6, 'FOO', '2025-08-25', '16:00', 'COACH03', 'Main Sports Ground', 'Formation practice'),
(7, 'FOO', '2025-08-28', '16:00', 'COACH03', 'Main Sports Ground', 'Tactical training'),
(8, 'VOL', '2025-11-10', '18:00', 'COACH04', 'Indoor Gym Hall A', 'Serving and blocking'),
(9, 'VOL', '2025-11-12', '18:00', 'COACH04', 'Indoor Gym Hall A', 'Match simulation'),
(10, 'TEN', '2025-05-20', '17:00', 'COACH05', 'Tennis Court 1', 'Forehand drills');

-- ========================================
-- 13. ATTENDANCE (Sample data)
-- ========================================
DELETE FROM attendance WHERE practice_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
INSERT INTO `attendance` (`attendance_id`, `practice_id`, `user_id`, `status`) VALUES
('ATD001', 1, 'STU101', 'PRESENT'),
('ATD002', 1, 'STU102', 'PRESENT'),
('ATD003', 1, 'STU103', 'ABSENT'),
('ATD004', 2, 'STU101', 'PRESENT'),
('ATD005', 2, 'STU102', 'PRESENT'),
('ATD006', 3, 'STU101', 'PRESENT'),
('ATD007', 3, 'STU102', 'ABSENT'),
('ATD008', 4, 'STU103', 'PRESENT'),
('ATD009', 4, 'STU104', 'PRESENT'),
('ATD010', 5, 'STU103', 'PRESENT'),
('ATD011', 6, 'STU105', 'PRESENT'),
('ATD012', 6, 'STU106', 'PRESENT'),
('ATD013', 7, 'STU105', 'ABSENT'),
('ATD014', 7, 'STU106', 'PRESENT'),
('ATD015', 8, 'STU107', 'PRESENT'),
('ATD016', 8, 'STU108', 'PRESENT'),
('ATD017', 9, 'STU107', 'PRESENT'),
('ATD018', 10, 'STU109', 'PRESENT'),
('ATD019', 10, 'STU110', 'PRESENT');

-- ========================================
-- 14. SPORTS ACHIEVEMENTS
-- ========================================
DELETE FROM sports_achievements WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `sports_achievements` (`achievement_id`, `sport_id`, `title`, `achieved_by`, `date_achieved`, `achieve_category`) VALUES
('ACH001', 'CRI', 'Inter-Faculty Champion 2025', 'Cricket Team', '2025-10-17', 'TOURNAMENT_WIN'),
('ACH002', 'CRI', 'Best Batting Average', 'STU101', '2025-10-15', 'INDIVIDUAL'),
('ACH003', 'CRI', 'Best Bowling', 'STU102', '2025-10-16', 'INDIVIDUAL'),
('ACH004', 'BAD', 'Doubles Runner-up', 'Badminton Pair', '2025-11-21', 'TOURNAMENT_PLACE'),
('ACH005', 'BAD', 'Best Overhead Shot', 'STU103', '2025-11-20', 'INDIVIDUAL'),
('ACH006', 'FOO', 'League Champions', 'Football Team', '2025-09-02', 'TOURNAMENT_WIN'),
('ACH007', 'FOO', 'Golden Boot Award', 'STU105', '2025-09-02', 'INDIVIDUAL'),
('ACH008', 'FOO', 'Best Defense', 'STU106', '2025-09-01', 'INDIVIDUAL'),
('ACH009', 'VOL', 'Best Setter', 'STU107', '2025-11-15', 'INDIVIDUAL'),
('ACH010', 'VOL', 'Best Spiker', 'STU108', '2025-11-12', 'INDIVIDUAL'),
('ACH011', 'TEN', 'Singles Finalist', 'STU109', '2025-12-01', 'TOURNAMENT_PLACE'),
('ACH012', 'TEN', 'Doubles Champion', 'STU109 & STU110', '2025-12-02', 'TOURNAMENT_WIN');

-- ========================================
-- 15. CAPTAIN SPORT ASSIGNMENTS
-- ========================================
DELETE FROM captain_sport WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `captain_sport` (`user_id`, `sport_id`, `date_started`, `date_relieved`) VALUES
('CAP001', 'CRI', '2025-06-01', NULL),
('CAP002', 'BAD', '2025-05-01', NULL),
('CAP003', 'FOO', '2025-04-01', NULL),
('CAP004', 'VOL', '2025-05-15', NULL),
('CAP005', 'TEN', '2025-06-01', NULL);

-- ========================================
-- 16. COACH SPORT ASSIGNMENTS
-- ========================================
DELETE FROM coach_sport WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `coach_sport` (`user_id`, `sport_id`, `date_started`, `date_relieved`) VALUES
('COACH01', 'CRI', '2025-01-01', NULL),
('COACH02', 'BAD', '2025-01-01', NULL),
('COACH03', 'FOO', '2025-01-01', NULL),
('COACH04', 'VOL', '2025-01-01', NULL),
('COACH05', 'TEN', '2025-01-01', NULL);

-- ========================================
-- 17. EQUIPMENT BOOKING REQUESTS
-- ========================================
DELETE FROM equipment_booking_request WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `equipment_booking_request` (`request_id`, `sport_id`, `equipment_id`, `quantity_needed`, `date_requested`, `start_date`, `end_date`, `purpose`, `status`, `approved_by`) VALUES
('EBR001', 'CRI', 'EQP001', 5, '2026-02-15', '2026-03-01', '2026-03-15', 'Training camp', 'APPROVED', 'MGR001'),
('EBR002', 'CRI', 'EQP002', 100, '2026-02-20', '2026-03-10', '2026-03-20', 'Tournament preparation', 'APPROVED', 'MGR001'),
('EBR003', 'BAD', 'EQP003', 3, '2026-02-25', '2026-03-05', '2026-03-12', 'Practice sessions', 'PENDING', NULL),
('EBR004', 'FOO', 'EQP005', 2, '2026-02-28', '2026-03-15', '2026-03-25', 'Match preparation', 'APPROVED', 'MGR003'),
('EBR005', 'VOL', 'EQP006', 2, '2026-03-01', '2026-03-10', '2026-03-15', 'Tournament', 'APPROVED', 'MGR004'),
('EBR006', 'TEN', 'EQP007', 4, '2026-03-05', '2026-03-20', '2026-04-05', 'Championship training', 'PENDING', NULL);

-- ========================================
-- 18. FACILITY BOOKINGS
-- ========================================
DELETE FROM `facility-booking` WHERE sport_id IN ('CRI', 'BAD', 'FOO', 'VOL', 'TEN');
INSERT INTO `facility-booking` (`booking_id`, `facility_id`, `sport_id`, `date`, `time_start`, `time_end`, `status`, `cost`, `booking_date`) VALUES
(1, 1, 'CRI', '2025-10-08', '10:00', '12:00', 'APPROVED', 10000, '2025-10-01'),
(2, 1, 'CRI', '2025-10-09', '10:00', '12:00', 'APPROVED', 10000, '2025-10-01'),
(3, 1, 'CRI', '2025-10-10', '10:00', '12:00', 'APPROVED', 10000, '2025-10-01'),
(4, 4, 'BAD', '2025-11-15', '15:00', '17:00', 'APPROVED', 4000, '2025-11-01'),
(5, 4, 'BAD', '2025-11-18', '15:00', '17:00', 'APPROVED', 4000, '2025-11-01'),
(6, 1, 'FOO', '2025-08-25', '16:00', '18:00', 'APPROVED', 10000, '2025-08-15'),
(7, 1, 'FOO', '2025-08-28', '16:00', '18:00', 'APPROVED', 10000, '2025-08-15'),
(8, 2, 'VOL', '2025-11-10', '18:00', '20:00', 'APPROVED', 6000, '2025-11-01'),
(9, 2, 'VOL', '2025-11-12', '18:00', '20:00', 'APPROVED', 6000, '2025-11-01'),
(10, 3, 'TEN', '2025-05-20', '17:00', '18:30', 'APPROVED', 3750, '2025-05-10'),
(11, 1, 'CRI', '2026-03-01', '10:00', '12:00', 'SCHEDULED', 10000, '2026-02-15'),
(12, 1, 'CRI', '2026-03-02', '10:00', '12:00', 'SCHEDULED', 10000, '2026-02-15'),
(13, 4, 'BAD', '2026-03-05', '15:00', '17:00', 'SCHEDULED', 4000, '2026-02-25'),
(14, 1, 'FOO', '2026-03-15', '16:00', '18:00', 'SCHEDULED', 10000, '2026-02-28'),
(15, 2, 'VOL', '2026-03-10', '18:00', '20:00', 'SCHEDULED', 6000, '2026-03-01');

-- ========================================
-- 19. NEWSFEED POSTS
-- ========================================
DELETE FROM newsfeed_post WHERE post_id < 1000;
INSERT INTO `newsfeed_post` (`post_id`, `title`, `content`, `posted_by`, `post_date`, `status`, `commenting`, `image_path`) VALUES
(100, 'Cricket Championship Victory', 'Congratulations to our cricket team for winning the Inter-Faculty Championship 2025! Great display of skill and teamwork.', 'MGR001', '2025-10-17', 'ACTIVE', 'YES', 'cricket_win.jpg'),
(101, 'Badminton Tournament Update', 'Our badminton team put up a strong performance in the recent tournament. Multiple medals won by our athletes!', 'MGR002', '2025-11-21', 'ACTIVE', 'YES', 'badminton.jpg'),
(102, 'Football League Results', 'Football team clinches the league title with impressive victories throughout the season.', 'MGR003', '2025-09-02', 'ACTIVE', 'YES', 'football.jpg'),
(103, 'Volleyball Championship Announcement', 'Mark your calendars! Volleyball championship coming up on 15th March 2026. Registration open now!', 'MGR004', '2025-12-01', 'ACTIVE', 'YES', 'volleyball_ann.jpg'),
(104, 'Tennis Training Camp', 'New tennis training camp scheduled for February. Open to all interested students. Apply today!', 'MGR005', '2025-12-10', 'ACTIVE', 'YES', 'tennis_camp.jpg'),
(105, 'Sports Facility Maintenance', 'Main sports ground undergoing routine maintenance during 2-8 March 2026. Use alternate facilities.', 'COACH01', '2025-12-15', 'ACTIVE', 'NO', NULL),
(106, 'Equipment Donation', 'Thanks to our sponsors for the donation of new sports equipment. We appreciate your continued support!', 'SPT004', '2025-12-20', 'ACTIVE', 'YES', 'donation.jpg'),
(107, 'Athlete of the Month', 'Congratulations to our athletes recognized as Athletes of the Month for outstanding performances!', 'MGR001', '2026-01-05', 'ACTIVE', 'YES', 'athlete_month.jpg');

-- ========================================
-- 20. COMMENTS
-- ========================================
DELETE FROM comment WHERE post_id >= 100 AND post_id < 200;
INSERT INTO `comment` (`comment_id`, `post_id`, `comment_from`, `content`, `comment_date`) VALUES
('CM001', 100, 'STU101', 'Fantastic performance! So proud of the team!', '2025-10-17'),
('CM002', 100, 'STU102', 'Amazing victory. Well deserved!', '2025-10-17'),
('CM003', 101, 'STU103', 'Great showing at nationals!', '2025-11-21'),
('CM004', 102, 'STU105', 'Our football team rocks!', '2025-09-02'),
('CM005', 103, 'STU107', 'Looking forward to the championship!', '2025-12-01'),
('CM006', 104, 'STU109', 'Count me in for the training camp!', '2025-12-10'),
('CM007', 106, 'COACH01', 'This is great news for all teams.', '2025-12-20');

-- ========================================
-- 21. INQUIRIES
-- ========================================
DELETE FROM inquiry WHERE inquiry_id < 100;
INSERT INTO `inquiry` (`inquiry_id`, `email`, `subject`, `date`, `status`, `inquiry_category`, `description`) VALUES
(1, 'student1@uoc.lk', 'Sports Registration Query', '2025-12-01', 'RESOLVED', 'REGISTRATION', 'How to register for cricket team?'),
(2, 'student2@uoc.lk', 'Facility Booking', '2025-12-02', 'NOT-RESOLVED', 'BOOKING', 'Need to book badminton hall for training'),
(3, 'student3@uoc.lk', 'Equipment Request', '2025-12-03', 'RESOLVED', 'EQUIPMENT', 'Need tennis rackets for tournament'),
(4, 'student4@uoc.lk', 'Facility Access', '2025-12-04', 'NOT-RESOLVED', 'OTHER', 'Swimming pool access during exam period'),
(5, 'student5@uoc.lk', 'Membership Inquiry', '2025-12-05', 'RESOLVED', 'REGISTRATION', 'Information about sports club membership'),
(6, 'parent@uoc.lk', 'Safety Concern', '2025-12-06', 'NOT-RESOLVED', 'OTHER', 'Safety equipment at cricket ground'),
(7, 'coach@uoc.lk', 'Training Resources', '2025-12-07', 'RESOLVED', 'OTHER', 'Request for additional training materials');

-- ========================================
-- 22. LOST ITEMS
-- ========================================
DELETE FROM lost_item WHERE lost_item_id < 100;
INSERT INTO `lost_item` (`lost_item_id`, `item_name`, `location_lost`, `date_lost`, `reported_date`, `status`, `reported_by`, `description`, `image_path`) VALUES
(1, 'Blue backpack', 'Main Sports Ground', '2025-10-15', '2025-10-16', 'FOUND', 'STU101', 'Blue backpack with sports equipment', 'backpack.jpg'),
(2, 'Cricket gloves', 'Badminton Hall', '2025-11-10', '2025-11-11', 'LOST', 'STU103', 'Brown cricket gloves', NULL),
(3, 'Sports watch', 'Tennis Court 1', '2025-05-18', '2025-05-19', 'FOUND', 'STU109', 'Black sports watch with heart rate monitor', 'watch.jpg'),
(4, 'Water bottle', 'Indoor Gym Hall A', '2025-11-08', '2025-11-09', 'LOST', 'STU107', 'Red water bottle with name tag', NULL),
(5, 'Training shoes', 'Main Sports Ground', '2025-12-10', '2025-12-11', 'LOST', 'STU105', 'White training shoes size 10', NULL);

-- ========================================
-- 23. LOST ITEM CLAIMS
-- ========================================
DELETE FROM lost_item_claim WHERE lost_item_id < 100;
INSERT INTO `lost_item_claim` (`claim_id`, `lost_item_id`, `claimed_by`, `claim_date`, `description`) VALUES
('CLM001', 1, 'STU101', '2025-10-16', 'Identified by serial number'),
('CLM002', 3, 'STU109', '2025-05-20', 'GPS tracked to lost property office'),
('CLM003', 2, 'STU103', '2025-11-14', 'Await verification of ownership');

-- ========================================
-- 24. BUDGET REQUEST HISTORY (Transactions)
-- ========================================
DELETE FROM transactions WHERE transaction_id < 100;
INSERT INTO `transactions` (`transaction_id`, `budget_id`, `transaction_type`, `amount`, `remark`, `transaction_date`, `processed_by`) VALUES
(1, 'BDG_CRI_2025', 'DEBIT', 75000, 'Equipment purchase', '2025-06-01', 'MGR001'),
(2, 'BDG_CRI_2025', 'DEBIT', 45000, 'Travel expenses', '2025-08-15', 'MGR001'),
(3, 'BDG_BAD_2025', 'DEBIT', 35000, 'Badminton equipment', '2025-05-10', 'MGR002'),
(4, 'BDG_FOO_2025', 'DEBIT', 60000, 'Football equipment and uniforms', '2025-04-20', 'MGR003'),
(5, 'BDG_VOL_2025', 'DEBIT', 25000, 'Volleyball equipment', '2025-05-15', 'MGR004'),
(6, 'BDG_TEN_2025', 'DEBIT', 25000, 'Tennis equipment', '2025-06-10', 'MGR005'),
(7, 'BDG_CRI_2026', 'DEBIT', 50000, 'New cricket bats', '2026-01-15', 'MGR001'),
(8, 'BDG_CRI_2026', 'DEBIT', 60000, 'Tournament travel', '2026-02-20', 'MGR001'),
(9, 'BDG_BAD_2026', 'DEBIT', 30000, 'Badminton equipment 2026', '2026-02-05', 'MGR002'),
(10, 'BDG_FOO_2026', 'DEBIT', 50000, 'Football equipment 2026', '2026-01-15', 'MGR003');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- ========================================
-- SUMMARY
-- ========================================
-- Total insertions:
-- - 8 Faculties
-- - 24 Users (Coaches, Managers, Captains, Students, Executives)
-- - 5 Sports with faculty assignments
-- - 10 Budget records (2025-2026)
-- - 5 Facility types
-- - 8 Equipment types
-- - 8 Equipment inventory records
-- - 10 Good Received Notes
-- - 5 Tournaments
-- - 10 Tournament Matches
-- - 40 Sport Expenses
-- - 10 Practice Sessions
-- - 19 Attendance records
-- - 12 Sports Achievements
-- - 5 Captain assignments
-- - 5 Coach assignments
-- - 6 Equipment booking requests
-- - 15 Facility bookings
-- - 8 Newsfeed posts
-- - 7 Comments
-- - 7 Inquiries
-- - 5 Lost items
-- - 3 Lost item claims
-- - 10 Transactions
-- ========================================
