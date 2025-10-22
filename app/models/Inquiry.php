<?php
class Inquiry {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
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
            LIMIT 20
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['q' => "%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
