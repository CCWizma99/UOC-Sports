<?php

class EmailVerification {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS `student_email_verifications` (
            `email` varchar(100) NOT NULL,
            `otp_code` varchar(6) NOT NULL,
            `expires_at` timestamp NOT NULL,
            `is_verified` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        $this->db->exec($sql);
    }

    public function generateOTP($email) {
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        $sql = "INSERT INTO student_email_verifications (email, otp_code, expires_at, is_verified) 
                VALUES (:email, :otp, DATE_ADD(NOW(), INTERVAL 2 MINUTE), 0)
                ON DUPLICATE KEY UPDATE 
                otp_code = :otp_update, 
                expires_at = DATE_ADD(NOW(), INTERVAL 2 MINUTE), 
                is_verified = 0";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':email' => $email,
            ':otp' => $otp,
            ':otp_update' => $otp
        ]);

        return $result ? $otp : false;
    }

    public function verifyOTP($email, $code) {
        $sql = "SELECT * FROM student_email_verifications 
                WHERE email = :email 
                AND otp_code = :code 
                AND expires_at > NOW()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':code' => $code
        ]);

        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verification) {
            $updateSql = "UPDATE student_email_verifications SET is_verified = 1 WHERE email = :email";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->execute([':email' => $email]);
            return true;
        }

        return false;
    }

    public function isVerified($email) {
        $sql = "SELECT is_verified FROM student_email_verifications 
                WHERE email = :email AND is_verified = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetch();
    }
}
