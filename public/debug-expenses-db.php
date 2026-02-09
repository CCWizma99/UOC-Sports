<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
require_once '../core/Database.php';

echo "<h1>Database Debug - Sport Expenses</h1>";

try {
    $db = Database::getConnection();
    
    // Check sport table
    echo "<h2>1. Sports Table</h2>";
    $stmt = $db->query("SELECT * FROM sport LIMIT 5");
    $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($sports, true) . "</pre>";
    
    // Check sport_expenses table
    echo "<h2>2. Sport Expenses Table (Sample)</h2>";
    $stmt = $db->query("SELECT * FROM sport_expenses ORDER BY expense_date DESC LIMIT 10");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($expenses, true) . "</pre>";
    
    // Check what values are in the sport column
    echo "<h2>3. Unique Sport Values in Expenses</h2>";
    $stmt = $db->query("SELECT DISTINCT sport FROM sport_expenses");
    $sportValues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($sportValues, true) . "</pre>";
    
    // Test the JOIN query
    echo "<h2>4. Test JOIN Query (All Expenses)</h2>";
    $sql = "SELECT 
                se.expense_id,
                se.sport as sport_column,
                se.expense_title,
                se.amount,
                s.sport_id,
                s.sport_name
            FROM sport_expenses se
            LEFT JOIN sport s ON se.sport COLLATE utf8mb4_unicode_ci = s.sport_name COLLATE utf8mb4_unicode_ci
            ORDER BY se.expense_date DESC
            LIMIT 5";
    $stmt = $db->query($sql);
    $joinTest = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($joinTest, true) . "</pre>";
    
    // Test with specific sport_id
    echo "<h2>5. Test with Sport ID Filter</h2>";
    $testSportId = 'BAD'; // badminton
    $sql = "SELECT 
                se.expense_id,
                se.sport,
                se.expense_title,
                se.amount,
                s.sport_id,
                s.sport_name
            FROM sport_expenses se
            INNER JOIN sport s ON se.sport COLLATE utf8mb4_unicode_ci = s.sport_name COLLATE utf8mb4_unicode_ci
            WHERE s.sport_id = ?
            ORDER BY se.expense_date DESC
            LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->execute([$testSportId]);
    $filtered = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Testing with sport_id = '$testSportId'</p>";
    echo "<pre>" . print_r($filtered, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
