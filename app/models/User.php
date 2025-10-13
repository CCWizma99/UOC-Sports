<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
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
        return $userId;
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
    
    
}
