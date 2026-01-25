-- Create equipment_categories table if it doesn't exist
CREATE TABLE IF NOT EXISTS `equipment_categories` (
  `category_id` varchar(8) NOT NULL,
  `category_name` varchar(64) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert sample categories
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

-- Update equipment table to add category_id if it doesn't exist
-- Note: Run this only if the column doesn't exist
-- ALTER TABLE equipment ADD COLUMN category_id VARCHAR(8) AFTER sport_id;

-- Populate equipment with sample category assignments based on equipment names
-- This is optional and for demonstration
UPDATE equipment SET category_id = 'CAT001' WHERE equipment_name LIKE '%ball%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT002' WHERE equipment_name LIKE '%bat%' OR equipment_name LIKE '%racket%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT003' WHERE equipment_name LIKE '%pad%' OR equipment_name LIKE '%guard%' OR equipment_name LIKE '%helmet%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT004' WHERE equipment_name LIKE '%net%' OR equipment_name LIKE '%goal%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT006' WHERE equipment_name LIKE '%mat%' OR equipment_name LIKE '%tatami%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT007' WHERE equipment_name LIKE '%shoe%' OR equipment_name LIKE '%jersey%' AND category_id IS NULL;
UPDATE equipment SET category_id = 'CAT008' WHERE category_id IS NULL;
