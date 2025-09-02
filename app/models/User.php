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
    
}
