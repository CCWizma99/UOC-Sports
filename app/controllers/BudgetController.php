<?php
class BudgetController {

    public function viewBudget($sportId) {
        $budgetModel = new Budget();
        $budget = $budgetModel->getBudgetBySport($sportId);

        if ($budget) {
            view('budget/view', ['budget' => $budget]);
        } else {
            view('404', ['message' => 'Budget not found for this sport']);
        }
    }
    public function updateTransaction(){
        view('sports-manager/update-transaction');
    }

    public function handleUpdateTransaction() {
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You must be logged in.'
                ]);
                return;
            }
    
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid request method.'
                ]);
                return;
            }
    
            $transactionId = trim($_POST['transaction_id'] ?? '');
            $budgetId = trim($_POST['budget_id'] ?? '');
            $amount = trim($_POST['amount'] ?? '');
            $purpose = trim($_POST['purpose'] ?? '');
            $remarks = trim($_POST['remarks'] ?? '');
            $changeReason = trim($_POST['change_reason'] ?? '');
    
            if (!$transactionId || !$budgetId || !$amount || !$changeReason) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Transaction ID, Budget ID, Amount, and Change Reason are required.'
                ]);
                return;
            }
    
            // File upload (optional)
            $proofDoc = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../internal/transactions/';  
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    
                $fileTmp = $_FILES['receipt']['tmp_name'];
                $fileExt = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('tx_', true) . '.' . $fileExt;
                $filePath = $uploadDir . $fileName;
    
                if (!move_uploaded_file($fileTmp, $filePath)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to upload receipt.'
                    ]);
                    return;
                }
                $proofDoc = $fileName;
            }
    
            $budgetModel = new Budget();
            $success = $budgetModel->updateTransaction($transactionId, $budgetId, $amount, $purpose, $remarks, $changeReason, $proofDoc);
    
            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Transaction updated successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update transaction. Check budget availability.'
                ]);
            }
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    

    public function addTransaction() {
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You must be logged in to perform this action.'
                ]);
                return;
            }
    
            // Retrieve POST data
            $budgetId = trim($_POST['budget_id'] ?? '');
            $amount = trim($_POST['Amount'] ?? '');
            $purpose = trim($_POST['Title'] ?? '');
            $remarks = trim($_POST['Remarks'] ?? '');
    
            // File upload handling
            if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Receipt file is required.'
                ]);
                return;
            }
    
            $uploadDir = __DIR__ . '/../internal/transactions/';  
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
    
            $fileTmp = $_FILES['receipt']['tmp_name'];
            $fileExt = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('tx_', true) . '.' . $fileExt;
            $filePath = $uploadDir . $fileName;

    
            if (!move_uploaded_file($fileTmp, $filePath)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to upload receipt file.'
                ]);
                return;
            }
    
            // Save transaction in DB
            $budgetModel = new Budget();
            $success = $budgetModel->addTransaction($budgetId, $amount, $purpose, $fileName);
    
            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Transaction added successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to add transaction.'
                ]);
            }
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function remaining() {
        if(!isset($_GET['sid'])){
           return null; 
        }
        $sportId = $_GET['sid'];
        $budgetModel = new Budget();
        $remaining = $budgetModel->getRemainingBySport($sportId);

        if($remaining !== null) {
            echo json_encode([
                'status' => 'success',
                'remaining_amount' => $remaining['remaining'],
                'budget_id' => $remaining['budget_id']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Budget not found'
            ]);
        }
    }

    public function remainingBudget() {
        if(!isset($_GET['bid'])){
           return null; 
        }
        $bid = $_GET['bid'];
        $budgetModel = new Budget();
        $remaining = $budgetModel->getRemainingById($bid);

        if($remaining !== null) {
            echo json_encode([
                'status' => 'success',
                'remaining_amount' => $remaining['remaining'],
                'budget_id' => $remaining['budget_id']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Budget not found'
            ]);
        }
    }

}
