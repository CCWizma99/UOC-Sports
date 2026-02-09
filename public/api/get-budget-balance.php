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
                COALESCE(allocated_amount, 100000.00) AS allocated_amount, 
                COALESCE(spent_amount, 0) AS spent_amount,
                (COALESCE(allocated_amount, 100000.00) - COALESCE(spent_amount, 0)) AS remaining_amount,
                CASE 
                    WHEN COALESCE(allocated_amount, 100000.00) > 0 
                    THEN ROUND((COALESCE(spent_amount, 0) / COALESCE(allocated_amount, 100000.00)) * 100, 1)
                    ELSE 0 
                END AS spent_percentage
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
    
    // If no budget found, calculate spent from sport_expenses table
    if (!$budget) {
        error_log("No budget found for sport $sportId year $year, checking sport_expenses");
        
        // Get total spent from sport_expenses table
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
        
        $allocatedAmount = 100000.00;
        $spentPercentage = $allocatedAmount > 0 ? round(($totalSpent / $allocatedAmount) * 100, 1) : 0;
        
        error_log("Calculated from expenses: spent=$totalSpent, percentage=$spentPercentage");
        
        echo json_encode([
            'success' => true,
            'data' => [
                'allocated_amount' => $allocatedAmount,
                'spent_amount' => $totalSpent,
                'remaining_amount' => $allocatedAmount - $totalSpent,
                'spent_percentage' => $spentPercentage
            ],
            'message' => 'Using default budget of Rs 100,000.00 with calculated expenses'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => $budget
        ]);
    }
    
} catch (Exception $e) {
    error_log("Budget Balance API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
