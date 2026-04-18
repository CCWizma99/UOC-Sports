<?php
class Budget {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    /**
     * Search budget by sport name
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "
            SELECT 
                b.budget_id,
                s.sport_name,
                COALESCE(CONCAT(m.fname, ' ', m.lname), 'Not Assigned') AS manager_name,
                COALESCE(m.contact_no, 'N/A') AS manager_contact,
                b.year,
                b.allocated_amount,
                b.spent_amount,
                b.allocation_date,
                (b.allocated_amount - b.spent_amount) AS remaining_amount,
                t.transaction_id,
                t.amount AS transaction_amount,
                t.purpose,
                t.timestamp,
                t.proof_doc
            FROM budget b
            INNER JOIN sport s ON b.sport_id = s.sport_id
            LEFT JOIN user m ON s.manager_id = m.user_id
            LEFT JOIN transaction t ON b.budget_id = t.budget_id
            WHERE s.sport_name LIKE :query
            ORDER BY b.allocation_date DESC, t.timestamp DESC
            LIMIT 4
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new budget allocation
     * @param string $sport_id
     * @param int $year
     * @param float $allocated_amount
     * @param float $spent_amount
     * @param string $description
     * @return string Inserted Budget ID
     */
    public function addBudget($sport_id, $year, $allocated_amount, $spent_amount, $description) {
        $budget_id = $this->generateBudgetId();
        
        $sql = "
            INSERT INTO budget (budget_id, sport_id, year, allocated_amount, spent_amount, allocation_date, description)
            VALUES (:budget_id, :sport_id, :year, :allocated_amount, :spent_amount, CURDATE(), :description)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'budget_id' => $budget_id,
            'sport_id' => $sport_id,
            'year' => $year,
            'allocated_amount' => $allocated_amount,
            'spent_amount' => $spent_amount,
            'description' => $description
        ]);

        return $budget_id;
    }

    /**
     * Generate a unique budget ID
     * @return string
     */
    private function generateBudgetId() {
        return 'BDG' . strtoupper(substr(uniqid(), -9));
    }

    public function updateTransaction($transactionId, $budgetId, $newAmount, $purpose, $remarks, $changeReason, $proofDoc = null) {
        try {
            $this->db->beginTransaction();
    
            // Get existing transaction
            $stmt = $this->db->prepare("SELECT amount FROM `transaction` WHERE transaction_id = :id");
            $stmt->execute(['id' => $transactionId]);
            $oldTransaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$oldTransaction) throw new Exception("Transaction not found.");
    
            $oldAmount = (int)$oldTransaction['amount'];
            $newAmount = (int)$newAmount;
    
            // Get remaining budget
            $stmt2 = $this->db->prepare("SELECT allocated_amount, spent_amount FROM budget WHERE budget_id = :budget_id");
            $stmt2->execute(['budget_id' => $budgetId]);
            $budget = $stmt2->fetch(PDO::FETCH_ASSOC);
    
            if (!$budget) throw new Exception("Budget not found.");
    
            $remaining = $budget['allocated_amount'] - $budget['spent_amount'] + $oldAmount;
    
            if ($newAmount > $remaining) {
                $this->db->rollBack();
                return false; // Not enough budget
            }
    
            // Update transaction
            $sql = "UPDATE `transaction` SET amount = :amount, purpose = :purpose, remarks = :remarks, change_reason = :change_reason";
            $params = [
                'amount' => $newAmount,
                'purpose' => $purpose,
                'remarks' => $remarks,
                'change_reason' => $changeReason,
                'transaction_id' => $transactionId
            ];
    
            if ($proofDoc) {
                $sql .= ", proof_doc = :proof_doc";
                $params['proof_doc'] = $proofDoc;
            }
            $sql .= " WHERE transaction_id = :transaction_id";
    
            $stmt3 = $this->db->prepare($sql);
            $stmt3->execute($params);
    
            // Update budget spent_amount
            $diff = $newAmount - $oldAmount; // can be negative if reducing
            $stmt4 = $this->db->prepare("UPDATE budget SET spent_amount = spent_amount + :diff WHERE budget_id = :budget_id");
            $stmt4->execute(['diff' => $diff, 'budget_id' => $budgetId]);
    
            $this->db->commit();
            return true;
    
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    

    public function getBudgetBySport($sportId) {
        $sql = "SELECT * FROM budget WHERE sport_id = :sport_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBudgetById($bid) {
        $sql = "SELECT * FROM budget WHERE budget_id = :budget_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['budget_id' => $bid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function addTransaction($budgetId, $amount, $purpose, $proofDoc) {
        try {
            $this->db->beginTransaction();
    
            // Insert transaction
            $transactionId = $this->generateTransactionId();
            $stmt = $this->db->prepare("
                INSERT INTO `transaction` (transaction_id, budget_id, amount, purpose, timestamp, proof_doc)
                VALUES (:transaction_id, :budget_id, :amount, :purpose, NOW(), :proof_doc)
            ");
            $stmt->execute([
                'transaction_id' => $transactionId,
                'budget_id' => $budgetId,
                'amount' => $amount,
                'purpose' => $purpose,
                'proof_doc' => $proofDoc
            ]);
    
            // Update spent_amount in budget
            $stmt2 = $this->db->prepare("
                UPDATE budget
                SET spent_amount = spent_amount + :amount
                WHERE budget_id = :budget_id
            ");
            $stmt2->execute([
                'amount' => $amount,
                'budget_id' => $budgetId
            ]);
    
            $this->db->commit();
            return true;
    
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    

    private function generateTransactionId() {
        $sql = "SELECT transaction_id FROM `transaction` ORDER BY transaction_id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last && preg_match('/T(\d+)/', $last['transaction_id'], $m)) {
            $num = (int)$m[1] + 1;
        } else {
            $num = 1;
        }

        return sprintf('T%04d', $num);
    }

    public function getTransactions($budgetId) {
        $sql = "SELECT * FROM `transaction` WHERE budget_id = :budget_id ORDER BY timestamp DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['budget_id' => $budgetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRemainingBySport($sportId) {
        $stmt = $this->db->prepare("
            SELECT allocated_amount - spent_amount AS remaining, budget_id
            FROM budget
            WHERE sport_id = :sport_id
            ORDER BY year DESC
            LIMIT 1
        ");
        $stmt->execute(['sport_id' => $sportId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getRemainingById($bid) {
        $stmt = $this->db->prepare("
            SELECT allocated_amount - spent_amount AS remaining, budget_id
            FROM budget
            WHERE budget_id = :budget_id
            ORDER BY year DESC
            LIMIT 1
        ");
        $stmt->execute(['sport_id' => $bid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }
    /**
     * Get all transactions with related sport name
     */
    public function getAllTransactions() {
        $sql = "
            SELECT t.transaction_id, t.budget_id, t.amount, t.purpose, t.timestamp, t.proof_doc, t.remarks,
                   b.sport_id, s.sport_name
            FROM `transaction` t
            JOIN budget b ON t.budget_id = b.budget_id
            JOIN sport s ON b.sport_id = s.sport_id
            ORDER BY t.timestamp DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTransactionById($transactionId) {
        $sql = "
            SELECT t.transaction_id, t.budget_id, t.amount, t.purpose, t.timestamp, t.proof_doc,
                   t.remarks, b.sport_id, s.sport_name
            FROM `transaction` t
            JOIN budget b ON t.budget_id = b.budget_id
            JOIN sport s ON b.sport_id = s.sport_id
            WHERE t.transaction_id = :transaction_id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['transaction_id' => $transactionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    /**
     * Get budget summary (total spent and remaining for current year)
     */
    public function getBudgetSummary($year = null) {
        if ($year === null) {
            $year = date('Y');
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                SUM(spent_amount) AS total_spent, 
                SUM(allocated_amount - spent_amount) AS total_remaining,
                SUM(allocated_amount) AS total_allocated
            FROM budget
            WHERE year = ?
        ");
        $stmt->execute([$year]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function getBudgetUtilization($year = null) {
    if ($year === null) {
        $year = date('Y');
    }

    try {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(t.timestamp, '%b') AS month,
                YEAR(t.timestamp) AS year,
                SUM(t.amount) AS spent_amount
            FROM `transaction` t
            JOIN budget b ON t.budget_id = b.budget_id
            WHERE YEAR(t.timestamp) = ?
            GROUP BY YEAR(t.timestamp), MONTH(t.timestamp), DATE_FORMAT(t.timestamp, '%b')
            ORDER BY MONTH(t.timestamp)
        ");
        $stmt->execute([$year]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        return [];
    }
}
    
}
