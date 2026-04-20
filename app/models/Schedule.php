<?php
require_once __DIR__ . '/../../core/Database.php';

class Schedule {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll($sport_id = null) {
        if ($sport_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions WHERE sport_id = ? ORDER BY session_date");
            $stmt->execute([$sport_id]);
        } else {
            $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions ORDER BY session_date, start_time");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPastSessions($sport_id, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM practice_sessions 
            WHERE sport_id = :sport_id 
            AND (session_date < CURDATE() OR (session_date = CURDATE() AND start_time < CURTIME()))
            ORDER BY session_date DESC, start_time DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':sport_id', $sport_id, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM practice_sessions WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSessionCountById($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM practice_sessions WHERE session_date >= CURDATE() AND sport_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }

    public function create($facility, $session_date, $start_time, $end_time, $equipment, $location, $notes, $sport_id, $added_by = '') {
    $stmt = $this->pdo->prepare("
        INSERT INTO practice_sessions 
        (sport_id, added_by, facility, session_date, start_time, end_time, need_equipment, location, notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $sport_id,
        $added_by,
        $facility,
        $session_date,
        $start_time,
        $end_time,
        $equipment,
        $location,
        $notes,
        ''
    ]);
}

  public function update($id, $facility, $date, $startTime, $endTime, $equipment, $location, $notes)
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        UPDATE practice_sessions
        SET facility = ?, 
            session_date = ?, 
            start_time = ?, 
            end_time = ?, 
            need_equipment = ?, 
            location = ?, 
            notes = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $facility,
        $date,
        $startTime,
        $endTime,
        $equipment,
        $location,
        $notes,
        $id
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
                ORDER BY session_date ASC, start_time ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':sport_id', $sportId, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT * FROM practice_sessions 
                WHERE session_date >= CURDATE()
                ORDER BY session_date ASC, start_time ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasTimeConflict($sport_id, $date, $start_time, $end_time, $excludeId = null)
{
    $query = "
        SELECT * FROM practice_sessions
        WHERE sport_id = :sport_id
        AND session_date = :date
    ";

    // Exclude current session when updating
    if ($excludeId) {
        $query .= " AND id != :excludeId";
    }

    $stmt = $this->pdo->prepare($query);

    $stmt->bindValue(':sport_id', $sport_id);
    $stmt->bindValue(':date', $date);

    if ($excludeId) {
        $stmt->bindValue(':excludeId', $excludeId);
    }

    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sessions as $session) {
        $existingStart = strtotime($session['start_time']);
        $existingEnd   = strtotime($session['end_time']);

        $newStart = strtotime($start_time);
        $newEnd   = strtotime($end_time);

        // Add 10 min buffer (600 seconds)
        $buffer = 600;

        // Check overlap + buffer
        if (
            ($newStart < ($existingEnd + $buffer)) &&
            ($newEnd > ($existingStart - $buffer))
        ) {
            return true; // Conflict exists
        }
    }

    return false; // No conflict
}

  // app/models/Schedule.php
public function getPreviousSessions($sportId) {
    $stmt = $this->pdo->prepare("
        SELECT id, facility, session_date, start_time, end_time, need_equipment, location, notes
        FROM practice_sessions
        WHERE sport_id = :sport_id AND session_date <= CURDATE()
        ORDER BY session_date DESC, start_time DESC
        LIMIT 15
    ");
    $stmt->bindParam(':sport_id', $sportId);
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
            ORDER BY session_date ASC
        ");
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRecentAndUpcomingSessions($sport_id, $limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM practice_sessions 
            WHERE sport_id = :sport_id
            ORDER BY session_date DESC, start_time DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':sport_id', $sport_id, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}