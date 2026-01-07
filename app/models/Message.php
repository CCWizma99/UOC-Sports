<?php

class Message {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    /**
     * Send a new message
     */
    public function sendMessage($senderId, $receiverId, $title, $message) {
        $sql = "INSERT INTO message (sender_id, receiver_id, title, message, sent_at, is_deleted) 
                VALUES (:sender_id, :receiver_id, :title, :message, NOW(), 0)";
        
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':sender_id' => $senderId,
            ':receiver_id' => $receiverId,
            ':title' => $title,
            ':message' => $message
        ]);
        
        return $result;
    }

    /**
     * Get all messages for a user (both sent and received)
     */
    public function getUserMessages($userId) {
        $sql = "SELECT m.*, 
                       CONCAT(sender.fname, ' ', sender.lname) as sender_name, 
                       sender.type as sender_role,
                       CONCAT(receiver.fname, ' ', receiver.lname) as receiver_name,
                       receiver.type as receiver_role
                FROM message m
                INNER JOIN user sender ON m.sender_id = sender.user_id
                INNER JOIN user receiver ON m.receiver_id = receiver.user_id
                WHERE (m.sender_id = :user_id OR m.receiver_id = :user_id) 
                AND m.is_deleted = 0
                ORDER BY m.sent_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get received messages for a user
     */
    public function getReceivedMessages($userId) {
        $sql = "SELECT m.*, 
                       CONCAT(sender.fname, ' ', sender.lname) as sender_name, 
                       sender.type as sender_role
                FROM message m
                INNER JOIN user sender ON m.sender_id = sender.user_id
                WHERE m.receiver_id = :user_id 
                AND m.is_deleted = 0
                ORDER BY m.sent_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get sent messages for a user
     */
    public function getSentMessages($userId) {
        $sql = "SELECT m.*, 
                       CONCAT(receiver.fname, ' ', receiver.lname) as receiver_name, 
                       receiver.type as receiver_role
                FROM message m
                INNER JOIN user receiver ON m.receiver_id = receiver.user_id
                WHERE m.sender_id = :user_id 
                AND m.is_deleted = 0
                ORDER BY m.sent_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get message by ID
     */
    public function getMessageById($messageId) {
        $sql = "SELECT m.*, 
                       CONCAT(sender.fname, ' ', sender.lname) as sender_name, 
                       sender.type as sender_role,
                       CONCAT(receiver.fname, ' ', receiver.lname) as receiver_name,
                       receiver.type as receiver_role
                FROM message m
                INNER JOIN user sender ON m.sender_id = sender.user_id
                INNER JOIN user receiver ON m.receiver_id = receiver.user_id
                WHERE m.id = :id AND m.is_deleted = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $messageId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get conversation between two users
     */
    public function getConversation($userId1, $userId2) {
        $sql = "SELECT m.*, 
                       CONCAT(sender.fname, ' ', sender.lname) as sender_name, 
                       sender.type as sender_role,
                       CONCAT(receiver.fname, ' ', receiver.lname) as receiver_name,
                       receiver.type as receiver_role
                FROM message m
                INNER JOIN user sender ON m.sender_id = sender.user_id
                INNER JOIN user receiver ON m.receiver_id = receiver.user_id
                WHERE ((m.sender_id = :user1 AND m.receiver_id = :user2) 
                   OR (m.sender_id = :user2 AND m.receiver_id = :user1))
                AND m.is_deleted = 0
                ORDER BY m.sent_at ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user1' => $userId1,
            ':user2' => $userId2
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users except current user (for recipient selection)
     */
    public function getAvailableRecipients($currentUserId) {
        $sql = "SELECT user_id, CONCAT(fname, ' ', lname) as name, type as role, email 
                FROM user 
                WHERE user_id != :current_user_id 
                AND type IN ('SPORT_MGR', 'CAPTAIN', 'COACH', 'EQUIP_MGR', 'PUBLIC')
                ORDER BY fname ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':current_user_id' => $currentUserId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Soft delete a message
     */
    public function deleteMessage($messageId, $userId) {
        // Verify the user is either sender or receiver
        $sql = "UPDATE message 
                SET is_deleted = 1 
                WHERE id = :id 
                AND (sender_id = :user_id OR receiver_id = :user_id)";
        
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':id' => $messageId,
            ':user_id' => $userId
        ]);
        
        return $result;
    }

    /**
     * Get unique conversation partners for a user
     */
    public function getConversationPartners($userId) {
        $sql = "SELECT DISTINCT 
                    CASE 
                        WHEN m.sender_id = :user_id THEN m.receiver_id
                        ELSE m.sender_id
                    END as partner_id,
                    CONCAT(u.fname, ' ', u.lname) as partner_name,
                    u.type as partner_role,
                    (SELECT message FROM message m2 
                     WHERE ((m2.sender_id = :user_id AND m2.receiver_id = partner_id) 
                        OR (m2.sender_id = partner_id AND m2.receiver_id = :user_id))
                     AND m2.is_deleted = 0
                     ORDER BY m2.sent_at DESC LIMIT 1) as last_message,
                    (SELECT sent_at FROM message m2 
                     WHERE ((m2.sender_id = :user_id AND m2.receiver_id = partner_id) 
                        OR (m2.sender_id = partner_id AND m2.receiver_id = :user_id))
                     AND m2.is_deleted = 0
                     ORDER BY m2.sent_at DESC LIMIT 1) as last_message_time
                FROM message m
                INNER JOIN user u ON u.user_id = CASE 
                    WHEN m.sender_id = :user_id THEN m.receiver_id
                    ELSE m.sender_id
                END
                WHERE (m.sender_id = :user_id OR m.receiver_id = :user_id)
                AND m.is_deleted = 0
                GROUP BY partner_id, partner_name, partner_role
                HAVING last_message IS NOT NULL
                ORDER BY last_message_time DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get unread message count for a user
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM message 
                WHERE receiver_id = :user_id 
                AND is_deleted = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
