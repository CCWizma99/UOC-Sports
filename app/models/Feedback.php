<?php
class Feedback {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new feedback entry
     */
    public function create($data) {
        $sql = "
            INSERT INTO feedback (feedback_id, user_id, name, email, type, message, created_at)
            VALUES (:feedback_id, :user_id, :name, :email, :type, :message, :created_at)
        ";

        $feedbackId = $this->generateFeedbackId();
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'feedback_id' => $feedbackId,
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'type' => $data['feedback_type'],
            'message' => $data['message'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Generate unique feedback ID (matching project pattern FBK + unique string)
     */
    private function generateFeedbackId() {
        do {
            $id = 'FBK' . strtoupper(substr(uniqid(), -9));
            $stmt = $this->db->prepare("SELECT feedback_id FROM feedback WHERE feedback_id = ?");
            $stmt->execute([$id]);
        } while ($stmt->fetch());

        return $id;
    }
}
