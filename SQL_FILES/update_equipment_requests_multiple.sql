-- Add columns to support multiple equipment items in one request
ALTER TABLE `equipment-requests` 
ADD COLUMN `equipment_items` TEXT COMMENT 'JSON array of equipment items with quantities',
ADD COLUMN `sport_id` varchar(5) DEFAULT NULL AFTER `student_id`,
ADD COLUMN `reserved_location` varchar(100) DEFAULT NULL AFTER `end_time`,
ADD COLUMN `requester_name` varchar(100) DEFAULT NULL AFTER `student_id`;

-- Update existing records to have equipment_items JSON format
UPDATE `equipment-requests` 
SET equipment_items = JSON_ARRAY(
    JSON_OBJECT(
        'equipment_name', category_name,
        'quantity', 1
    )
)
WHERE equipment_items IS NULL;
