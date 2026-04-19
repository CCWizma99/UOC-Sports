<?php
require_once __DIR__ . '/../services/EmailService.php';

class SportExpensesController {

    public function index() {
        $expenseModel = new SportExpense(Database::getConnection());
        $expenses = $expenseModel->getAll();
        
        view('sports-manager/expenses', ['expenses' => $expenses]);
    }

    public function create() {
        $editData = null;
        $isEdit = false;
        $selectedSportName = null;
        
        // Get sport name from sport_id parameter
        if (isset($_GET['sport'])) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
            $stmt->execute([$_GET['sport']]);
            $sport = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($sport) {
                $selectedSportName = $sport['sport_name'];
            }
        }
        
        if (isset($_GET['id'])) {
            $expenseModel = new SportExpense(Database::getConnection());
            $editData = $expenseModel->getById($_GET['id']);
            $isEdit = true;
        }
        
        $remainingBalance = null;
        $sportIdToCheck = $_GET['sport'] ?? ($_SESSION['selected_sport_id'] ?? null);
        
        if ($sportIdToCheck) {
            $budgetModel = new Budget();
            $budgetInfo = $budgetModel->getRemainingBySport($sportIdToCheck);
            if ($budgetInfo) {
                $remainingBalance = $budgetInfo['remaining'];
            } else {
                $remainingBalance = 'unallocated';
            }
        }

        view('sports-manager/add-expense', [
            'editData' => $editData, 
            'isEdit' => $isEdit,
            'selectedSportName' => $selectedSportName,
            'remainingBalance' => $remainingBalance
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /uoc-sports/public/sport-manager/expenses');
            exit();
        }

        $expenseModel = new SportExpense(Database::getConnection());
        
        $data = [
            'sport' => $_POST['sport'] ?? '',
            'expense_title' => $_POST['expense'] ?? '',
            'sport_event' => $_POST['sportEvent'] ?? NULL,
            'amount' => $_POST['amount'] ?? 0,
            'submitted_by' => $_POST['submittedBy'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];

        if (!is_numeric($data['amount']) || floatval($data['amount']) <= 0) {
            $_SESSION['error_message'] = 'Amount must be a positive number.';
            $sportParam = isset($_POST['sport_param']) ? '?sport=' . urlencode($_POST['sport_param']) : (isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '');
            header('Location: /uoc-sports/public/sport-manager/add-expense' . $sportParam);
            exit();
        }
        
        $file = isset($_FILES['receipt']) ? $_FILES['receipt'] : null;
        
        // Get sport parameter to preserve in redirect
        $sportParam = isset($_POST['sport_param']) ? '?sport=' . urlencode($_POST['sport_param']) : (isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '');
        
        try {
            if (isset($_POST['expense_id']) && !empty($_POST['expense_id'])) {
                $result = $expenseModel->updateExpense($_POST['expense_id'], $data, $file);
                $_SESSION['success_message'] = 'Expense updated successfully!';
            } else {
                $result = $expenseModel->addExpense($data, $file);
                $_SESSION['success_message'] = 'Expense added successfully!';

                // Notify Administrator(s) if limit is low or exceeded
                try {
                    $db = Database::getConnection();
                    
                    // Get sport ID from sport name
                    $sportStmt = $db->prepare("SELECT sport_id FROM sport WHERE sport_name = ?");
                    $sportStmt->execute([$data['sport']]);
                    $sportData = $sportStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($sportData) {
                        $sportId = $sportData['sport_id'];
                        $budgetModel = new Budget();
                        $budgetInfo = $budgetModel->getRemainingBySport($sportId);
                        
                        $remainingBefore = $budgetInfo ? $budgetInfo['remaining'] : 0;
                        $remainingAfter = $remainingBefore - $data['amount'];
                        
                        if ($remainingAfter < 10000) {
                            $stmt = $db->query("SELECT email, fname FROM user WHERE type = 'ADMIN' AND status = 'ACTIVE'");
                            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $emailService = new EmailService();
                            foreach ($admins as $admin) {
                                // Send individually
                                $emailService->sendExpenseAddedEmail($admin['email'], $admin['fname'], $data, $remainingAfter);
                            }
                        }
                    }
                } catch (Exception $e) {
                    error_log("Failed to send expense notification email: " . $e->getMessage());
                }
            }
            
            header('Location: /uoc-sports/public/sport-manager/expenses' . $sportParam);
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error saving expense: ' . $e->getMessage();
            header('Location: /uoc-sports/public/sport-manager/add-expense' . $sportParam);
            exit();
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['expenseId'])) {
            echo json_encode(['success' => false, 'message' => 'Missing expense ID']);
            exit();
        }
        
        $expenseModel = new SportExpense(Database::getConnection());
        $result = $expenseModel->delete($input['expenseId']);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Expense deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete expense']);
        }
        exit();
    }

    public function search() {
        header('Content-Type: application/json');
        
        $query = $_GET['query'] ?? '';
        
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            exit();
        }
        
        $expenseModel = new SportExpense(Database::getConnection());
        $results = $expenseModel->search($query);
        
        echo json_encode(['success' => true, 'data' => $results]);
        exit();
    }

    public function getBySport() {
        header('Content-Type: application/json');
        
        $sport = $_GET['sport'] ?? '';
        
        if (empty($sport)) {
            echo json_encode(['success' => false, 'message' => 'Sport is required']);
            exit();
        }
        
        $expenseModel = new SportExpense(Database::getConnection());
        $results = $expenseModel->getBySport($sport);
        
        echo json_encode(['success' => true, 'data' => $results]);
        exit();
    }

    public function statistics() {
        header('Content-Type: application/json');
        
        $expenseModel = new SportExpense(Database::getConnection());
        $stats = $expenseModel->getStatistics();
        
        echo json_encode(['success' => true, 'data' => $stats]);
        exit();
    }

    public function recent() {
        header('Content-Type: application/json');
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        
        $expenseModel = new SportExpense(Database::getConnection());
        $results = $expenseModel->getRecent($limit);
        
        echo json_encode(['success' => true, 'data' => $results]);
        exit();
    }
}
