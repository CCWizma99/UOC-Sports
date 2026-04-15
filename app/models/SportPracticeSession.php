<?php

class SportPracticeSession
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Get all practice sessions with optional filters
     */
    public function getAll($filters = [])
    {
        $query = "SELECT 
                    ps.id,
                    ps.sport_id,
                    s.sport_name,
                    ps.added_by,
                    ps.facility,
                    ps.location,
                    ps.session_date,
                    ps.start_time,
                    ps.end_time,
                  
                    ps.need_equipment,
                    ps.status,
                    ps.created_at,
                    ps.updated_at
                  FROM practice_sessions ps
                  LEFT JOIN sport s ON ps.sport_id = s.sport_id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['sport_id'])) {
            $query .= " AND ps.sport_id = ?";
            $params[] = $filters['sport_id'];
            // Debug log
            error_log("Practice Sessions Filter - sport_id: " . $filters['sport_id']);
        }

        if (!empty($filters['status'])) {
            $query .= " AND ps.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND ps.session_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND ps.session_date <= ?";
            $params[] = $filters['date_to'];
        }

        $query .= " ORDER BY ps.session_date DESC, ps.start_time DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentPracticeSessions($month, $year)
    {
        $query = "SELECT 
                ps.id,
                ps.sport_id,
                s.sport_name,
                ps.location,
                ps.session_date,
                ps.start_time,
                ps.end_time,
                ps.status,
                ps.facility,
                ps.notes
              FROM practice_sessions ps
              LEFT JOIN sport s ON ps.sport_id = s.sport_id
              WHERE MONTH(ps.session_date) = ?
                AND YEAR(ps.session_date) = ?";

        $params = [$month, $year];

        $query .= " ORDER BY ps.session_date ASC, ps.start_time ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single practice session by ID
     */
    public function getById($id)
    {
        $query = "SELECT 
                    ps.*,
                    s.sport_name
                  FROM practice_sessions ps
                  LEFT JOIN sport s ON ps.sport_id = s.sport_id
                  WHERE ps.id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new practice session
     */
    public function create($data)
    {
        // Get sport_id from sport_name
        $sportId = $this->getSportIdByName($data['sport_name']);

        if (!$sportId) {
            throw new Exception("Invalid sport selected");
        }

        $query = "INSERT INTO practice_sessions 
<<<<<<< Updated upstream
                  (sport_id, added_by, facility, location, session_date, start_time, end_time, need_equipment, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
=======
                  (sport_id, added_by, facility, location, session_date, start_time, end_time, notes, need_equipment, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

>>>>>>> Stashed changes
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            $sportId,
            $data['added_by'] ?? 'MANAGER',
            $data['facility'] ?? '',
            $data['location'] ?? '',
            $data['session_date'],
            $data['start_time'],
            $data['end_time'],
            $data['need_equipment'] ?? 'No',
            $data['status'] ?? 'PENDING'
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update a practice session
     */
    public function update($id, $data)
    {
        // Get sport_id from sport_name if provided
        if (isset($data['sport_name'])) {
            $sportId = $this->getSportIdByName($data['sport_name']);
            if (!$sportId) {
                throw new Exception("Invalid sport selected");
            }
            $data['sport_id'] = $sportId;
        }

        $query = "UPDATE practice_sessions 
                  SET sport_id = ?,
                      facility = ?,
                      location = ?,
                      session_date = ?,
                      start_time = ?,
                      end_time = ?,
                      need_equipment = ?,
                      status = ?
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['sport_id'] ?? null,
            $data['facility'] ?? '',
            $data['location'] ?? '',
            $data['session_date'],
            $data['start_time'],
            $data['end_time'],
         
            $data['need_equipment'] ?? 'No',
            $data['status'] ?? 'ACTIVE',
            $id
        ]);
    }

    /**
     * Delete a practice session
     */
    public function delete($id)
    {
        $query = "DELETE FROM practice_sessions WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Update only the status of a practice session
     */
    public function updateStatus($id, $status)
    {
        $query = "UPDATE practice_sessions SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    /**
     * Get sport_id from sport_name
     */
    private function getSportIdByName($sportName)
    {
        $query = "SELECT sport_id FROM sport WHERE LOWER(sport_name) = LOWER(?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportName]);
        return $stmt->fetchColumn();
    }

    /**
     * Get all sports for dropdown
     */
    public function getAllSports()
    {
        $query = "SELECT sport_id, sport_name FROM sport ORDER BY sport_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check for time conflicts
     */
    public function checkTimeConflict($location, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = "SELECT COUNT(*) FROM practice_sessions 
                  WHERE location = ? 
                  AND session_date = ? 
                  AND (
                      (start_time <= ? AND end_time > ?) OR
                      (start_time < ? AND end_time >= ?) OR
                      (start_time >= ? AND end_time <= ?)
                  )
                  AND status != 'CANCELLED'";

        $params = [$location, $date, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get today's practice sessions for a sport manager
     */
    public function getTodaySessions($sportId = null)
    {
        $today = date('Y-m-d');

        $query = "SELECT 
                    ps.id,
                    ps.sport_id,
                    s.sport_name,
                    ps.location,
                    ps.start_time,
                    ps.end_time,
                    ps.status,
                    ps.session_date
                  FROM practice_sessions ps
                  LEFT JOIN sport s ON ps.sport_id = s.sport_id
                  WHERE DATE(ps.session_date) = ?
                    AND ps.status IN ('ACTIVE', 'ACCEPTED', 'PENDING')";

        $params = [$today];

        if ($sportId) {
            $query .= " AND ps.sport_id = ?";
            $params[] = $sportId;
        }

        $query .= " ORDER BY ps.start_time ASC";

        error_log("getTodaySessions Query: " . $query);
        error_log("getTodaySessions Params: " . json_encode($params));

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("getTodaySessions Results: " . count($results) . " sessions found");

        return $results;
    }
}
