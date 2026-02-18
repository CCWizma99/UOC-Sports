-- Create equipment_categories table if it doesn't exist
-- This table links equipment items to categories
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `category_id` varchar(8) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `equipment_id` INT DEFAULT NULL,
  `sport_id` VARCHAR(10) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `idx_equipment_id` (`equipment_id`),
  KEY `idx_sport_id` (`sport_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert sample categories (without equipment_id initially)
INSERT INTO `equipment_categories` (`category_id`, `category_name`, `description`) VALUES
('CAT001', 'Balls', 'Various types of balls for different sports'),
('CAT002', 'Bats & Rackets', 'Batting and racket equipment'),
('CAT003', 'Protective Gear', 'Safety and protective equipment'),
('CAT004', 'Nets & Goals', 'Nets, goals, and target equipment'),
('CAT005', 'Training Equipment', 'Practice and training gear'),
('CAT006', 'Mats & Flooring', 'Mats, tatami, and floor equipment'),
('CAT007', 'Clothing & Shoes', 'Sports apparel and footwear'),
('CAT008', 'Other', 'Miscellaneous equipment')
ON DUPLICATE KEY UPDATE 
  category_name = VALUES(category_name),
  description = VALUES(description);

-- Link equipment to categories based on equipment names
-- Update equipment_categories with equipment_id references
UPDATE equipment_categories ec
INNER JOIN equipment e ON (
  (e.equipment_name LIKE '%ball%' AND ec.category_id = 'CAT001') OR
  (e.equipment_name LIKE '%bat%' AND ec.category_id = 'CAT002') OR
  (e.equipment_name LIKE '%racket%' AND ec.category_id = 'CAT002') OR
  (e.equipment_name LIKE '%pad%' AND ec.category_id = 'CAT003') OR
  (e.equipment_name LIKE '%guard%' AND ec.category_id = 'CAT003') OR
  (e.equipment_name LIKE '%helmet%' AND ec.category_id = 'CAT003') OR
  (e.equipment_name LIKE '%net%' AND ec.category_id = 'CAT004') OR
  (e.equipment_name LIKE '%goal%' AND ec.category_id = 'CAT004') OR
  (e.equipment_name LIKE '%mat%' AND ec.category_id = 'CAT006') OR
  (e.equipment_name LIKE '%tatami%' AND ec.category_id = 'CAT006') OR
  (e.equipment_name LIKE '%shoe%' AND ec.category_id = 'CAT007') OR
  (e.equipment_name LIKE '%jersey%' AND ec.category_id = 'CAT007')
)
SET ec.equipment_id = e.equipment_id, ec.sport_id = e.sport_id;
