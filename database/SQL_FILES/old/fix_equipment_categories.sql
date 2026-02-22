-- Migration script to fix equipment_categories table structure
-- Run this to update your existing database

-- Step 1: Drop and recreate equipment_categories table with correct structure
DROP TABLE IF EXISTS `equipment_categories`;

CREATE TABLE `equipment_categories` (
  `category_id` varchar(8) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `equipment_id` INT DEFAULT NULL,
  `sport_id` VARCHAR(10) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `idx_equipment_id` (`equipment_id`),
  KEY `idx_sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Step 2: Insert base categories
INSERT INTO `equipment_categories` (`category_id`, `category_name`, `description`) VALUES
('CAT001', 'Balls', 'Various types of balls for different sports'),
('CAT002', 'Bats & Rackets', 'Batting and racket equipment'),
('CAT003', 'Protective Gear', 'Safety and protective equipment'),
('CAT004', 'Nets & Goals', 'Nets, goals, and target equipment'),
('CAT005', 'Training Equipment', 'Practice and training gear'),
('CAT006', 'Mats & Flooring', 'Mats, tatami, and floor equipment'),
('CAT007', 'Clothing & Shoes', 'Sports apparel and footwear'),
('CAT008', 'Other', 'Miscellaneous equipment');

-- Step 3: Create category entries for each equipment item and link them
-- For Ball equipment
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT001_', e.equipment_id),
    'Balls',
    e.equipment_id,
    e.sport_id,
    CONCAT('Ball equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%ball%';

-- For Bat & Racket equipment
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT002_', e.equipment_id),
    'Bats & Rackets',
    e.equipment_id,
    e.sport_id,
    CONCAT('Bat/Racket equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%bat%' OR e.equipment_name LIKE '%racket%';

-- For Protective Gear
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT003_', e.equipment_id),
    'Protective Gear',
    e.equipment_id,
    e.sport_id,
    CONCAT('Protective equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%pad%' OR e.equipment_name LIKE '%guard%' OR e.equipment_name LIKE '%helmet%';

-- For Nets & Goals
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT004_', e.equipment_id),
    'Nets & Goals',
    e.equipment_id,
    e.sport_id,
    CONCAT('Net/Goal equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%net%' OR e.equipment_name LIKE '%goal%';

-- For Mats & Flooring
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT006_', e.equipment_id),
    'Mats & Flooring',
    e.equipment_id,
    e.sport_id,
    CONCAT('Mat equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%mat%' OR e.equipment_name LIKE '%tatami%';

-- For Clothing & Shoes
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT007_', e.equipment_id),
    'Clothing & Shoes',
    e.equipment_id,
    e.sport_id,
    CONCAT('Clothing/Shoe equipment for ', e.equipment_name)
FROM equipment e
WHERE e.equipment_name LIKE '%shoe%' OR e.equipment_name LIKE '%jersey%';

-- For Other/Uncategorized equipment
INSERT INTO equipment_categories (category_id, category_name, equipment_id, sport_id, description)
SELECT 
    CONCAT('CAT008_', e.equipment_id),
    'Other',
    e.equipment_id,
    e.sport_id,
    CONCAT('Other equipment: ', e.equipment_name)
FROM equipment e
WHERE e.equipment_id NOT IN (
    SELECT equipment_id FROM equipment_categories WHERE equipment_id IS NOT NULL
)
AND e.equipment_name NOT LIKE '%ball%'
AND e.equipment_name NOT LIKE '%bat%'
AND e.equipment_name NOT LIKE '%racket%'
AND e.equipment_name NOT LIKE '%pad%'
AND e.equipment_name NOT LIKE '%guard%'
AND e.equipment_name NOT LIKE '%helmet%'
AND e.equipment_name NOT LIKE '%net%'
AND e.equipment_name NOT LIKE '%goal%'
AND e.equipment_name NOT LIKE '%mat%'
AND e.equipment_name NOT LIKE '%tatami%'
AND e.equipment_name NOT LIKE '%shoe%'
AND e.equipment_name NOT LIKE '%jersey%';

-- Verification query (optional - run this to check the results)
-- SELECT 
--     ec.category_id,
--     ec.category_name,
--     e.equipment_name,
--     e.sport_id,
--     ec.description
-- FROM equipment_categories ec
-- LEFT JOIN equipment e ON ec.equipment_id = e.equipment_id
-- ORDER BY ec.category_name, e.equipment_name;
