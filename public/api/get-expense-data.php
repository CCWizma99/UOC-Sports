<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

require_once '../../config/config.php';
require_once '../../core/Database.php';
require_once '../../app/models/SportExpense.php';

try {
    // Get parameters
    $sportId = $_GET['sport_id'] ?? null;
    $year = $_GET['year'] ?? date('Y');
    
    // Log request for debugging
    error_log("Expense API called - Sport ID: " . ($sportId ?: 'null') . ", Year: " . $year);
    
    // Validate year
    if (!is_numeric($year) || $year < 2000 || $year > 2100) {
        throw new Exception('Invalid year parameter');
    }
    
    // Get database connection
    $db = Database::getConnection();
    
    // Create model instance
    $expenseModel = new SportExpense($db);
    
    // Get cumulative expenses for line chart
    $cumulativeExpenses = $expenseModel->getCumulativeExpenses($sportId, (int)$year);
    
    // Log result count
    error_log("Found " . count($cumulativeExpenses) . " expenses");
    
    echo json_encode([
        'success' => true,
        'data' => $cumulativeExpenses,
        'year' => (int)$year,
        'sport_id' => $sportId,
        'count' => count($cumulativeExpenses)
    ]);
    
} catch (Exception $e) {
    error_log("Expense API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
