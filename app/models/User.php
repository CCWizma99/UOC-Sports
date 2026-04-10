<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function createStudent($data) {
        $stmt = $this->db->prepare("INSERT INTO user (user_id, fname, lname, type, student_id, faculty_id, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this -> generateUniqueUserId(),
            $data['fname'],
            $data['lname'],
            "STUDENT",
            $data['student_id'],
            $data['faculty_id'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO user (user_id, fname, lname, email, password) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $this -> generateUniqueUserId(),
            $data['fname'],
            $data['lname'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }


    private function generateUniqueUserId() {
        do {
            $id = $this->generateUserId();
            $stmt = $this->db->prepare("SELECT user_id FROM user WHERE user_id = ?");
            $stmt->execute([$id]);
        } while ($stmt->fetch());
    
        return $id;
    }
    
    function generateUserId($length = 8) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $id;
    }
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Returns associative array or false if not found
    }

    public function findByStudentId($studentId) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE student_id = ? LIMIT 1");
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        
    public function storeRememberToken($user_id, $token, $expiry) {
        $stmt = $this->db->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$user_id, hash('sha256', $token), $expiry]);
    }
    
    public function addUser($fname, $lname, $email, $type, $phone, $sport, $faculty, $tempPass, $shouldChange = 0) {
        $userId = uniqid('usr_', true);
        $hashed = password_hash($tempPass, PASSWORD_BCRYPT);
    
        $sql = "INSERT INTO user (user_id, fname, lname, email, type, contact_no, sport_id, faculty_id, password, must_change_pass)
                VALUES (:user_id, :fname, :lname, :email, :type, :phone, :sport_id, :faculty, :password, :must_change_pass)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'type' => $type,
            'phone' => $phone,
            'sport_id' => $sport,
            'faculty' => $faculty,
            'password' => $hashed,
            'must_change_pass' => $shouldChange
        ]);
        
        // If type is COACH, assign to sport table
        if ($type === 'COACH' && !empty($sport)) {
            $this->assignCoachToSport($sport, $userId);
        }
        
        return $userId;
    }

    /**
     * Assign a coach to a sport
     * @param string $sportId
     * @param string $coachId
     * @return bool
     */
    public function assignCoachToSport($sportId, $coachId) {
        $stmt = $this->db->prepare("UPDATE sport SET coach_id = :coach_id WHERE sport_id = :sport_id");
        return $stmt->execute([
            'coach_id' => $coachId,
            'sport_id' => $sportId
        ]);
    }

    public function getUserById($userId) {
        $stmt = $this->db->prepare("
            SELECT fname, lname
            FROM user 
            WHERE user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFaculties() {
        $stmt = $this->db->prepare("
            SELECT faculty_id, faculty_name
            FROM faculty
        ");
        $stmt->execute();
        return$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentId($userId) {
        $stmt = $this->db->prepare("
            SELECT student_id
            FROM user 
            WHERE user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get complete user profile information
     */
    public function getUserProfile($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.user_id,
                u.fname,
                u.lname,
                CONCAT(u.fname, ' ', u.lname) AS full_name,
                u.email,
                u.type,
                u.joined_date,
                u.contact_no,
                u.sport_id,
                u.student_id,
                u.faculty_id,
                u.status,
                f.faculty_name,
                s.sport_name
            FROM user u
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            LEFT JOIN sport s ON u.sport_id = s.sport_id
            WHERE u.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get enrolled sports for a student or captain
     * Returns sports with coach and schedule information
     */
    public function getEnrolledSports($userId) {
        // First check if user is a student or captain
        $userCheck = $this->db->prepare("SELECT type FROM user WHERE user_id = :user_id");
        $userCheck->execute(['user_id' => $userId]);
        $user = $userCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || ($user['type'] !== 'STUDENT' && $user['type'] !== 'CAPTAIN')) {
            return [];
        }

        // Get sports the student is enrolled in via sports-team table
        $stmt = $this->db->prepare("
            SELECT 
                s.sport_id,
                s.sport_name,
                CONCAT(coach.fname, ' ', coach.lname) AS coach_name,
                st.joined_date
            FROM `sports-team` st
            INNER JOIN sport s ON st.sport_id = s.sport_id
            LEFT JOIN user coach ON s.coach_id = coach.user_id
            WHERE st.student_id = :user_id
            ORDER BY s.sport_name
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update user's profile image in database
     */
    public function updateProfileImage($userId, $imageName) {
        $stmt = $this->db->prepare("
            UPDATE user 
            SET profile_img = :profile_img 
            WHERE user_id = :user_id
        ");
        return $stmt->execute([
            'profile_img' => $imageName,
            'user_id' => $userId
        ]);
    }

    /**
     * Get user's profile image filename from database
     */
    public function getProfileImage($userId) {
        $stmt = $this->db->prepare("
            SELECT profile_img 
            FROM user 
            WHERE user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['profile_img'] : null;
    }

    /**
     * Delete old profile image file from filesystem
     */
    public function deleteOldProfileImage($userId) {
        $oldImage = $this->getProfileImage($userId);
        
        if ($oldImage) {
            $imagePath = __DIR__ . '/../internal/profile_img/' . $oldImage;
            if (file_exists($imagePath)) {
                unlink($imagePath);
                return true;
            }
        }
        return false;
    }

    public function getRegistrationData($period = 'monthly', $year = null) {
        if (!$year) $year = date('Y');
        
        try {
            switch ($period) {
                case 'weekly':
                    $sql = "SELECT 
                        WEEK(joined_date) as period_num,
                        CONCAT('Week ', WEEK(joined_date)) as period_label,
                        COUNT(*) as user_count 
                        FROM user 
                        WHERE YEAR(joined_date) = :year 
                        GROUP BY WEEK(joined_date) 
                        ORDER BY period_num";
                    break;
                case 'annually':
                    $sql = "SELECT 
                        YEAR(joined_date) as period_num,
                        YEAR(joined_date) as period_label,
                        COUNT(*) as user_count 
                        FROM user 
                        GROUP BY YEAR(joined_date) 
                        ORDER BY period_num";
                    break;
                default:
                    $sql = "SELECT 
                        MONTH(joined_date) as period_num,
                        MONTHNAME(joined_date) as period_label,
                        COUNT(*) as user_count 
                        FROM user 
                        WHERE YEAR(joined_date) = :year 
                        GROUP BY MONTH(joined_date), MONTHNAME(joined_date) 
                        ORDER BY period_num";
            }
            $stmt = $this->db->prepare($sql);
            if ($period !== 'annually') {
                $stmt->bindParam(':year', $year);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }

    /**
     * Update user information
     * @param string $userId
     * @param array $data - array with keys: fname, lname, email, contact_no, type
     * @return bool
     */
    public function updateUser($userId, $data) {
        $fields = [];
        $params = ['user_id' => $userId];
        
        $allowedFields = ['fname', 'lname', 'email', 'contact_no', 'type', 'student_id', 'faculty_id', 'sport_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE user SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update user status (ACTIVE/INACTIVE)
     * @param string $userId
     * @param string $status - 'ACTIVE' or 'INACTIVE'
     * @return bool
     */
    public function updateUserStatus($userId, $status) {
        if (!in_array($status, ['ACTIVE', 'INACTIVE'])) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE user SET status = :status WHERE user_id = :user_id");
        return $stmt->execute([
            'status' => $status,
            'user_id' => $userId
        ]);
    }

    /**
     * Get total count of active users for dashboard stats
     * @return int
     */
    public function getTotalUsersCount() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `user` WHERE status = 'ACTIVE'";
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get total users count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all eligible students for a sport (not in the team, active status)
     * @param string $sportId
     * @return array
     */
    public function getEligibleStudents($sportId) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.fname, u.lname, u.student_id, u.email, u.contact_no, f.faculty_name
            FROM user u
            LEFT JOIN faculty f ON u.faculty_id = f.faculty_id
            WHERE u.type = 'STUDENT'
              AND u.status = 'ACTIVE'
              AND (u.sport_id = :sport_id OR u.sport_id IS NULL)
              AND u.user_id NOT IN (
                SELECT st.student_id FROM `sports-team` st WHERE st.sport_id = :sport_id
              )
            ORDER BY u.lname, u.fname
        ");
        $stmt->execute(['sport_id' => $sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
