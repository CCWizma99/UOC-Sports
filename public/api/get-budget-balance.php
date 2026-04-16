<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/Budget.php';

try {
    // Get parameters
    $sportId = $_GET['sport_id'] ?? null;
    $year = $_GET['year'] ?? date('Y');
    
    if (!$sportId) {
        throw new Exception('Sport ID is required');
    }
    
    // Validate year
    if (!is_numeric($year) || $year < 2000 || $year > 2100) {
        throw new Exception('Invalid year parameter');
    }
    
    // Get database connection
    $db = Database::getConnection();
    $budgetModel = new Budget();
    
    // Get budget for the sport and year
    $sql = "SELECT 
                allocated_amount
            FROM budget 
            WHERE sport_id = :sport_id AND year = :year
            ORDER BY allocation_date DESC
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'sport_id' => $sportId,
        'year' => (int)$year
    ]);
    
    $budget = $stmt->fetch(PDO::FETCH_ASSOC);
    $allocatedAmount = $budget ? floatval($budget['allocated_amount']) : "0.00";
    
    // Always calculate spent amount from sport_expenses table to ensure consistency with expense chart
    $expenseSql = "SELECT 
                    COALESCE(SUM(se.amount), 0) AS total_spent
                   FROM sport_expenses se
                   INNER JOIN sport s ON se.sport COLLATE utf8mb4_unicode_ci = s.sport_name COLLATE utf8mb4_unicode_ci
                   WHERE s.sport_id = :sport_id 
                   AND YEAR(se.expense_date) = :year";
    
    $expenseStmt = $db->prepare($expenseSql);
    $expenseStmt->execute([
        'sport_id' => $sportId,
        'year' => (int)$year
    ]);
    
    $expenseData = $expenseStmt->fetch(PDO::FETCH_ASSOC);
    $totalSpent = floatval($expenseData['total_spent'] ?? 0);
    
    $spentPercentage = $allocatedAmount > 0 ? round(($totalSpent / $allocatedAmount) * 100, 1) : 0;
    $remainingAmount = $allocatedAmount - $totalSpent;
    
    error_log("Balance calculation - Sport: $sportId, Year: $year, Allocated: $allocatedAmount, Spent: $totalSpent, Percentage: $spentPercentage");
    
    echo json_encode([
        'success' => true,
        'data' => [
            'allocated_amount' => $allocatedAmount,
            'spent_amount' => $totalSpent,
            'remaining_amount' => $remainingAmount,
            'spent_percentage' => $spentPercentage
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Budget Balance API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
