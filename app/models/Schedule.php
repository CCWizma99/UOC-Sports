<?php
require_once __DIR__ . '/../../core/Database.php';

class Schedule {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll($sport_id = null) {
        if ($sport_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions WHERE sport_id = ? ORDER BY session_date, session_time");
            $stmt->execute([$sport_id]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions ORDER BY session_date, session_time");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($facility, $session_date, $session_time, $description, $sport_id = '', $added_by = '') {
        $stmt = $this->pdo->prepare("INSERT INTO practice_sessions (sport_id, added_by, facility, session_date, session_time, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $sport_id,
            $added_by,
            $facility,
            $session_date,
            $session_time,
            $description,
            '' // status defaults to empty string as per existing data
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

    /**
     * Set status for a practice session
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function setStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE practice_sessions SET status = ? WHERE id = ?");
        return $stmt->execute([$status, (int)$id]);
    }

    /**
     * Get upcoming practice sessions
     * @param string $sportId - Optional sport ID to filter by
     * @param int $limit - Number of sessions to retrieve
     * @return array - Upcoming sessions
     */
    public function getUpcomingSessions($sportId = null, $limit = 10) {
        if ($sportId) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM practice_sessions 
                WHERE sport_id = :sport_id
                AND session_date >= CURDATE()
                ORDER BY session_date ASC, session_time ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':sport_id', $sportId, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT * FROM practice_sessions 
                WHERE session_date >= CURDATE()
                ORDER BY session_date ASC, session_time ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get session with full details
     * @param int $id - Session ID
     * @return array|null - Session details or null
     */
    public function getSessionWithDetails($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                ps.*,
                COUNT(a.attendance_id) as attendance_count
            FROM practice_sessions ps
            LEFT JOIN attendance a ON ps.id = a.practice_id
            WHERE ps.id = :id
            GROUP BY ps.id
        ");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get sessions by sport (using facility field as proxy)
     * @param string $sportId - Sport ID
     * @return array - Sessions for the sport
     */
    public function getSessionsBySport($sportId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM practice_sessions
            WHERE sport_id = :sport_id
            AND session_date >= CURDATE()
            ORDER BY session_date ASC, session_time ASC
        ");
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}