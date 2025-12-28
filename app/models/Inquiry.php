<?php
class Inquiry {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new inquiry
     */
    public function create($data) {
        $sql = "
            INSERT INTO inquiry (inquiry_id, user_id, email, subject, message, date, status)
            VALUES (:inquiry_id, :user_id, :email, :subject, :message, :date, :status)
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'inquiry_id' => $this->generateInquiryId(),
            'user_id' => $data['user_id'] ?? 'GUEST',
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'date' => date('Y-m-d'),
            'status' => 'NOT-RESOLVED'
        ]);
    }

    /**
     * Get all inquiries with optional limit
     */
    public function getAll($limit = 50) {
        $sql = "
            SELECT inquiry_id, user_id, email, subject, message, date, status
            FROM inquiry
            ORDER BY date DESC, inquiry_id DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific inquiry by ID
     */
    public function getById($inquiryId) {
        $sql = "
            SELECT inquiry_id, user_id, email, subject, message, date, status
            FROM inquiry
            WHERE inquiry_id = :inquiry_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['inquiry_id' => $inquiryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update inquiry status
     */
    public function updateStatus($inquiryId, $status) {
        $sql = "
            UPDATE inquiry
            SET status = :status
            WHERE inquiry_id = :inquiry_id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'inquiry_id' => $inquiryId
        ]);
    }

    /**
     * Delete an inquiry
     */
    public function delete($inquiryId) {
        $sql = "DELETE FROM inquiry WHERE inquiry_id = :inquiry_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['inquiry_id' => $inquiryId]);
    }

    /**
     * Search inquiries by inquiry_id, email, subject, or status
     */
    public function search($query) {
        $sql = "
            SELECT inquiry_id, user_id, email, subject, message, date, status
            FROM inquiry
            WHERE inquiry_id LIKE :q
               OR email LIKE :q
               OR subject LIKE :q
               OR status LIKE :q
            ORDER BY date DESC
            LIMIT 4
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['q' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate unique inquiry ID
     */
    private function generateInquiryId() {
        do {
            $id = 'INQ' . strtoupper(substr(uniqid(), -9));
            $stmt = $this->db->prepare("SELECT inquiry_id FROM inquiry WHERE inquiry_id = ?");
            $stmt->execute([$id]);
        } while ($stmt->fetch());

        return $id;
    }
}
