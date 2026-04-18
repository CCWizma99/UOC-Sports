<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getConnection();
    
    // 1. Fix sport_expenses table
    echo "Updating sport_expenses table...\n";
    try {
        $db->exec("ALTER TABLE `sport_expenses` ADD COLUMN `sport_event` varchar(255) NULL AFTER `expense_title` ");
        echo "Column 'sport_event' added successfully.\n";
    } catch (PDOException $e) {
        if ($e->getCode() == '42S21' || strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Column 'sport_event' already exists in sport_expenses.\n";
        } else {
            throw $e;
        }
    }

    // 2. Map equipment to categories
    echo "Ensuring equipment categories are mapped...\n";
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT002' WHERE (`equipment_name` LIKE '%Racket%' OR `equipment_name` LIKE '%Bat%' OR `equipment_name` LIKE '%Stick%') AND `category_id` IS NULL");
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT001' WHERE (`equipment_name` LIKE '%Ball%' OR `equipment_name` LIKE '%Shuttlecock%') AND `category_id` IS NULL");
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT004' WHERE (`equipment_name` LIKE '%Net%' OR `equipment_name` LIKE '%Goal%' OR `equipment_name` LIKE '%Post%' OR `equipment_name` LIKE '%Hoop%') AND `category_id` IS NULL");
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT003' WHERE (`equipment_name` LIKE '%Pad%' OR `equipment_name` LIKE '%Glove%' OR `equipment_name` LIKE '%Guard%' OR `equipment_name` LIKE '%Helmet%' OR `equipment_name` LIKE '%Cap%' OR `equipment_name` LIKE '%Gi%') AND `category_id` IS NULL");
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT006' WHERE (`equipment_name` LIKE '%Mat%' OR `equipment_name` LIKE '%Tatami%') AND `category_id` IS NULL");
    $db->exec("UPDATE `equipment` SET `category_id` = 'CAT007' WHERE (`equipment_name` LIKE '%Shoes%') AND `category_id` IS NULL");
    
    echo "Migration script finished.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
