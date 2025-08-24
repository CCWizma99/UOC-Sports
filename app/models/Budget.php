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
                CONCAT(m.fname, ' ', m.lname) AS manager_name,
                m.contact_no AS manager_contact,
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
            INNER JOIN user m ON s.manager_id = m.user_id
            LEFT JOIN transaction t ON b.budget_id = t.budget_id
            WHERE s.sport_name LIKE :query
            ORDER BY b.allocation_date DESC, t.timestamp DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new budget allocation
     * @param int $sport_id
     * @param int $year
     * @param float $allocated_amount
     * @param string $description
     * @return int Inserted Budget ID
     */
    public function addBudget($sport_id, $year, $allocated_amount, $description) {
        $sql = "
            INSERT INTO budget (sport_id, year, allocated_amount, spent_amount, allocation_date, description)
            VALUES (:sport_id, :year, :allocated_amount, 0, NOW(), :description)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'sport_id' => $sport_id,
            'year' => $year,
            'allocated_amount' => $allocated_amount,
            'description' => $description
        ]);

        return $this->db->lastInsertId();
    }
}
