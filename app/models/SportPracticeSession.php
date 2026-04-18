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
                    ps.updated_at,
                    ps.physical_facility_id,
                    pf.facility_name AS physical_facility_name
                  FROM practice_sessions ps
                  LEFT JOIN sport s ON ps.sport_id = s.sport_id
                  LEFT JOIN physical_facility pf ON ps.physical_facility_id = pf.facility_id
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
    public function create($data) {
        $sportId = $this->getSportIdByName($data['sport_name']);

        if (!$sportId) {
            throw new Exception("Invalid sport selected");
        }
        
        try {
            $this->db->beginTransaction();

            // Lock the PHYSICAL facility to prevent concurrent threads from racing
            if (!empty($data['physical_facility_id'])) {
                $lockSql = "SELECT facility_id FROM physical_facility WHERE facility_id = ? FOR UPDATE";
                $stmtLock = $this->db->prepare($lockSql);
                $stmtLock->execute([$data['physical_facility_id']]);
                
                // Re-verify the conflict INSIDE the atomic lock
                $conflictMessage = $this->checkTimeConflict(
                    $data['physical_facility_id'],
                    $data['session_date'],
                    $data['start_time'],
                    $data['end_time']
                );
                
                if ($conflictMessage) {
                    $this->db->rollBack();
                    throw new Exception("Slot was just taken: " . $conflictMessage);
                }
            }

            $query = "INSERT INTO practice_sessions 
                      (sport_id, added_by, facility, location, physical_facility_id, session_date, start_time, end_time, need_equipment, status)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $sportId,
                $data['added_by'] ?? 'MANAGER',
                $data['facility'] ?? '',
                $data['location'] ?? '',
                $data['physical_facility_id'] ?? null,
                $data['session_date'],
                $data['start_time'],
                $data['end_time'],
                    $data['need_equipment'] ?? 'No',
                $data['status'] ?? 'PENDING'
            ]);
            
            if ($result) {
                $lastId = $this->db->lastInsertId();
                $this->db->commit();
                return $lastId;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
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
        
        try {
            $this->db->beginTransaction();

            if (!empty($data['physical_facility_id'])) {
                $lockSql = "SELECT facility_id FROM physical_facility WHERE facility_id = ? FOR UPDATE";
                $stmtLock = $this->db->prepare($lockSql);
                $stmtLock->execute([$data['physical_facility_id']]);
                
                $conflictMessage = $this->checkTimeConflict(
                    $data['physical_facility_id'],
                    $data['session_date'],
                    $data['start_time'],
                    $data['end_time'],
                    $id
                );
                
                if ($conflictMessage) {
                    $this->db->rollBack();
                    throw new Exception("Slot was just taken: " . $conflictMessage);
                }
            }

            $query = "UPDATE practice_sessions 
                      SET sport_id = ?,
                          facility = ?,
                          location = ?,
                          physical_facility_id = ?,
                          session_date = ?,
                          start_time = ?,
                          end_time = ?,
                              need_equipment = ?,
                          status = ?
                      WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['sport_id'] ?? null,
                $data['facility'] ?? '',
                $data['location'] ?? '',
                $data['physical_facility_id'] ?? null,
                $data['session_date'],
                $data['start_time'],
                $data['end_time'],
             
                $data['need_equipment'] ?? 'No',
                $data['status'] ?? 'ACTIVE',
                $id
            ]);

            if ($result) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
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
     * Check for time conflicts including practice sessions and facility bookings
     */
    public function checkTimeConflict($locationOrId, $date, $startTime, $endTime, $excludeId = null) {
        // 1. Check for 30-minute intervals
        $startSeconds = strtotime($startTime);
        $endSeconds = strtotime($endTime);
        if ($startSeconds % 1800 !== 0 || $endSeconds % 1800 !== 0) {
            return "Times must be in 30-minute intervals (e.g., 08:00, 08:30).";
        }

        $isNumericId = is_numeric($locationOrId);
        $physicalFacilityId = $isNumericId ? $locationOrId : null;
        $locationName = !$isNumericId ? $locationOrId : null;

        // 2. Check for other practice sessions
        $query1 = "SELECT ps.id, s.sport_name, ps.start_time, ps.end_time 
                   FROM practice_sessions ps
                   LEFT JOIN sport s ON ps.sport_id = s.sport_id
                   WHERE session_date = ? 
                   AND (ps.start_time < ? AND ps.end_time > ?)
                   AND ps.status NOT IN ('CANCELED', 'CANCELLED')";
        
        $params1 = [$date, $endTime, $startTime];
        
        if ($physicalFacilityId) {
            $query1 .= " AND ps.physical_facility_id = ?";
            $params1[] = $physicalFacilityId;
        } else {
            $query1 .= " AND ps.location = ?";
            $params1[] = $locationName;
        }

        if ($excludeId) {
            $query1 .= " AND ps.id != ?";
            $params1[] = $excludeId;
        }
        
        $stmt1 = $this->db->prepare($query1);
        $stmt1->execute($params1);
        $conflict = $stmt1->fetch(PDO::FETCH_ASSOC);
        
        if ($conflict) {
            return $conflict; // Return conflict details for better error message
        }

        // 3. Check for facility bookings (only if we have physical_facility_id)
        if ($physicalFacilityId) {
            $query2 = "SELECT fb.slot 
                       FROM `facility-booking` fb
                       INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                       WHERE fr.facility_id = ?
                       AND fb.date = ?
                       AND fb.status IN ('BOOKED', 'ACCEPTED', 'RESERVED')";
            
            $stmt2 = $this->db->prepare($query2);
            $stmt2->execute([$physicalFacilityId, $date]);
            $bookings = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $slots = [
                'MORNING' => ['08:00:00', '12:00:00'],
                'AFTERNOON' => ['13:00:00', '17:00:00'],
                'FULL' => ['08:00:00', '17:00:00']
            ];

            foreach ($bookings as $b) {
                $slotRange = $slots[$b['slot']] ?? null;
                if ($slotRange) {
                    if ($startTime < $slotRange[1] && $endTime > $slotRange[0]) {
                        return "Conflict with a facility reservation (" . $b['slot'] . " slot).";
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Check if a practice session already exists for the given sport on a specific date
     */
    public function checkDateConflictForSport($sportName, $date, $excludeId = null) {
        $sportId = $this->getSportIdByName($sportName);
        if (!$sportId) return false;

        $query = "SELECT COUNT(*) FROM practice_sessions 
                  WHERE sport_id = ? 
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
     * Get all physical facilities for dropdown
     */
    public function getPhysicalFacilities() {
        $query = "SELECT facility_id, facility_name, location FROM physical_facility ORDER BY facility_name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
