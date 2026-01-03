<?php
class Faculty {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all faculties
     */
    public function getAllFaculties() {
        $stmt = $this->db->query("SELECT faculty_id, faculty_name FROM faculty ORDER BY faculty_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
