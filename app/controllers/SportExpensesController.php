<?php

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
        
        view('sports-manager/add-expense', [
            'editData' => $editData, 
            'isEdit' => $isEdit,
            'selectedSportName' => $selectedSportName
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
            'amount' => $_POST['amount'] ?? 0,
            'submitted_by' => $_POST['submittedBy'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];
        
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
