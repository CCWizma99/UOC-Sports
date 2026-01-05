<?php
/**
 * VerificationRequest Model
 * Handles student verification requests from sport managers
 */
class VerificationRequest {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new verification request
     */
    public function create($requestedBy, $sportId, $studentIds, $notes = null) {
        $requestId = 'VR' . strtoupper(substr(uniqid(), -10));
        
        try {
            $this->db->beginTransaction();
            
            // Create request
            $sql = "INSERT INTO verification_requests (request_id, requested_by, sport_id, notes) 
                    VALUES (:request_id, :requested_by, :sport_id, :notes)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':request_id' => $requestId,
                ':requested_by' => $requestedBy,
                ':sport_id' => $sportId,
                ':notes' => $notes
            ]);

            // Add students to the request
            $sql2 = "INSERT INTO verification_request_students (request_id, student_id, faculty_id) 
                     SELECT :request_id, u.student_id, u.faculty_id 
                     FROM user u WHERE u.student_id = :student_id";
            $stmt2 = $this->db->prepare($sql2);

            foreach ($studentIds as $studentId) {
                $stmt2->execute([
                    ':request_id' => $requestId,
                    ':student_id' => $studentId
                ]);
            }

            $this->db->commit();
            return $requestId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get pending verifications for a faculty
     */
    public function getPendingForFaculty($facultyId) {
        $sql = "SELECT 
                    vrs.request_id,
                    vrs.student_id,
                    vrs.verification_status,
                    u.fname, u.lname, u.email, u.student_id as uni_student_id,
                    sic.image_name as id_card_image,
                    vr.created_at as request_date,
                    vr.sport_id,
                    s.sport_name,
                    CONCAT(req.fname, ' ', req.lname) as requested_by_name
                FROM verification_request_students vrs
                INNER JOIN verification_requests vr ON vrs.request_id = vr.request_id
                INNER JOIN user u ON vrs.student_id = u.student_id
                LEFT JOIN student_id_cards sic ON vrs.student_id = sic.student_id
                LEFT JOIN sport s ON vr.sport_id = s.sport_id
                LEFT JOIN user req ON vr.requested_by = req.user_id
                WHERE vrs.faculty_id = :faculty_id 
                AND vrs.verification_status = 'PENDING'
                AND vr.status = 'PENDING'
                ORDER BY vr.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':faculty_id' => $facultyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all pending verifications for a registrar (based on their faculty)
     */
    public function getPendingForRegistrar($registrarUserId) {
        // First get the faculty ID for this registrar
        $sql = "SELECT faculty_id FROM faculty WHERE registrar_id = :registrar_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':registrar_id' => $registrarUserId]);
        $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$faculty) {
            return [];
        }

        return $this->getPendingForFaculty($faculty['faculty_id']);
    }

    /**
     * Verify a student (approve or reject)
     */
    public function verifyStudent($requestId, $studentId, $registrarId, $status, $reason = null) {
        $sql = "UPDATE verification_request_students 
                SET verification_status = :status,
                    verified_by = :verified_by,
                    verified_at = NOW(),
                    rejection_reason = :reason
                WHERE request_id = :request_id AND student_id = :student_id";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':status' => $status,
            ':verified_by' => $registrarId,
            ':reason' => $reason,
            ':request_id' => $requestId,
            ':student_id' => $studentId
        ]);

        // Check if all students in request are verified
        if ($result) {
            $this->checkAndCompleteRequest($requestId);
        }

        return $result;
    }

    /**
     * Check if all students in a request are verified and mark as complete
     */
    private function checkAndCompleteRequest($requestId) {
        $sql = "SELECT COUNT(*) as pending FROM verification_request_students 
                WHERE request_id = :request_id AND verification_status = 'PENDING'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':request_id' => $requestId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['pending'] == 0) {
            $sql2 = "UPDATE verification_requests SET status = 'COMPLETED' WHERE request_id = :request_id";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute([':request_id' => $requestId]);
        }
    }

    /**
     * Get verification requests by sport manager
     */
    public function getRequestsBySportManager($userId) {
        $sql = "SELECT 
                    vr.*,
                    s.sport_name,
                    (SELECT COUNT(*) FROM verification_request_students WHERE request_id = vr.request_id) as total_students,
                    (SELECT COUNT(*) FROM verification_request_students WHERE request_id = vr.request_id AND verification_status = 'VERIFIED') as verified_count,
                    (SELECT COUNT(*) FROM verification_request_students WHERE request_id = vr.request_id AND verification_status = 'REJECTED') as rejected_count
                FROM verification_requests vr
                LEFT JOIN sport s ON vr.sport_id = s.sport_id
                WHERE vr.requested_by = :user_id
                ORDER BY vr.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get student ID card image path
     */
    public function getStudentIdCard($studentId) {
        $sql = "SELECT image_name FROM student_id_cards WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':student_id' => $studentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['image_name'] : null;
    }

    /**
     * Get faculties with pending students for email notification
     */
    public function getFacultiesForRequest($requestId) {
        $sql = "SELECT DISTINCT f.faculty_id, f.faculty_name, f.registrar_id, f.registrar_email
                FROM verification_request_students vrs
                INNER JOIN faculty f ON vrs.faculty_id = f.faculty_id
                WHERE vrs.request_id = :request_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':request_id' => $requestId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
