<?php
// app/models/Schedule.php
require_once __DIR__ . '/../../core/Database.php'; // or the path where your Database class file is

class Schedule {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions ORDER BY session_date, session_time");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($facility, $session_date, $session_time, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO practice_sessions (facility, session_date, session_time, description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $facility,
            $session_date,
            $session_time,
            $description
        ]);
    }

    public function update($id, $facility, $session_date, $session_time, $description) {
        $stmt = $this->pdo->prepare("UPDATE practice_sessions SET facility = ?, session_date = ?, session_time = ?, description = ? WHERE id = ?");
        return $stmt->execute([
            $facility,
            $session_date,
            $session_time,
            $description,
            (int)$id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM practice_sessions WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
}
