<?php
class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Generate a unique message ID
     */
    private function generateMessageId() {
        return 'MSG' . substr(strtoupper(uniqid()), 0, 9);
    }

    /**
     * Get coach and manager for a sport
     * @param string $sportId
     * @return array
     */
    public function getRecipientsBySport($sportId) {
        $recipients = [];

        // Get sport details with coach and manager IDs
        $stmt = $this->db->prepare("
            SELECT s.sport_name, s.coach_id, s.manager_id,
                   coach.fname AS coach_fname, coach.lname AS coach_lname,
                   manager.fname AS manager_fname, manager.lname AS manager_lname
            FROM sport s
            LEFT JOIN user coach ON s.coach_id = coach.user_id
            LEFT JOIN user manager ON s.manager_id = manager.user_id
            WHERE s.sport_id = :sport_id
        ");
        $stmt->execute(['sport_id' => $sportId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Add coach if exists
            if (!empty($result['coach_id'])) {
                $recipients[] = [
                    'user_id' => $result['coach_id'],
                    'name' => trim($result['coach_fname'] . ' ' . $result['coach_lname']) ?: 'Coach',
                    'type' => 'COACH',
                    'label' => 'Coach - ' . (trim($result['coach_fname'] . ' ' . $result['coach_lname']) ?: 'Not Assigned')
                ];
            }

            // Add manager if exists
            if (!empty($result['manager_id'])) {
                $recipients[] = [
                    'user_id' => $result['manager_id'],
                    'name' => trim($result['manager_fname'] . ' ' . $result['manager_lname']) ?: 'Sports Manager',
                    'type' => 'MANAGER',
                    'label' => 'Sports Manager - ' . (trim($result['manager_fname'] . ' ' . $result['manager_lname']) ?: 'Not Assigned')
                ];
            }
        }

        return $recipients;
    }

    /**
     * Create a new message
     * @param string $senderId Captain's user_id
     * @param string $recipientId Coach or Manager user_id
     * @param string $recipientType 'COACH', 'MANAGER' or 'CAPTAIN'
     * @param string $sportId
     * @param string $title
     * @param string $message
     * @return string|false Message ID on success, false on failure
     */
    public function create($senderId, $recipientId, $recipientType, $sportId, $title, $message) {
        try {
            $messageId = $this->generateMessageId();
            
            $stmt = $this->db->prepare("
                INSERT INTO message (message_id, sender_id, recipient_id, recipient_type, sport_id, title, message)
                VALUES (:message_id, :sender_id, :recipient_id, :recipient_type, :sport_id, :title, :message)
            ");
            
            $stmt->execute([
                'message_id' => $messageId,
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
                'sport_id' => $sportId,
                'title' => $title,
                'message' => $message
            ]);

            return $messageId;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all messages sent by a sender
     * @param string $senderId
     * @return array
     */
    public function getMessagesBySender($senderId) {
        $stmt = $this->db->prepare("
            SELECT m.message_id, m.title, m.message, m.recipient_type, m.recipient_id, m.sent_at,
                   u.fname, u.lname
            FROM message m
            LEFT JOIN user u ON m.recipient_id = u.user_id
            WHERE m.status = 'ACTIVE'
            ORDER BY m.sent_at DESC
        ");
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filter only messages from this sender and format
        $result = [];
        foreach ($messages as $msg) {
            $stmt2 = $this->db->prepare("SELECT sender_id FROM message WHERE message_id = ?");
            $stmt2->execute([$msg['message_id']]);
            $check = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($check && $check['sender_id'] === $senderId) {
                $recipientName = trim($msg['fname'] . ' ' . $msg['lname']);
                if (empty($recipientName)) {
                    $recipientName = $msg['recipient_type'] === 'COACH' ? 'Coach' : ($msg['recipient_type'] === 'CAPTAIN' ? 'Captain' : ($msg['recipient_type'] === 'ADMIN' ? 'Admin' : 'Sports Manager'));
                }
                
                $result[] = [
                    'id' => $msg['message_id'],
                    'sender' => $msg['recipient_type'] === 'COACH' ? 'Coach' : ($msg['recipient_type'] === 'CAPTAIN' ? 'Captain' : ($msg['recipient_type'] === 'ADMIN' ? 'Admin' : 'Sports Manager')),
                    'recipient_id' => $msg['recipient_id'],
                    'recipient_name' => $recipientName,
                    'title' => $msg['title'],
                    'text' => substr($msg['message'], 0, 50) . (strlen($msg['message']) > 50 ? '...' : ''),
                    'full_message' => $msg['message'],
                    'date' => date('d M', strtotime($msg['sent_at'])),
                    'timestamp' => $msg['sent_at']
                ];
            }
        }

        return $result;
    }

    /**
     * Delete a message (only if sender owns it)
     * @param string $messageId
     * @param string $senderId
     * @return bool
     */
    public function deleteMessage($messageId, $senderId) {
        try {
            $stmt = $this->db->prepare("UPDATE message SET status = 'DELETED' WHERE message_id = :message_id AND sender_id = :sender_id");
            $stmt->execute([
                'message_id' => $messageId,
                'sender_id' => $senderId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get message count by sender
     * @param string $senderId
     * @return int
     */
    public function getMessageCount($senderId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM message WHERE sender_id = :sender_id AND status = 'ACTIVE'");
        $stmt->execute(['sender_id' => $senderId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get all messages received by a user (inbox)
     * @param string $recipientId
     * @return array
     */
    public function getMessagesByRecipient($recipientId) {
        $stmt = $this->db->prepare("
            SELECT m.message_id, m.title, m.message, m.recipient_type, m.sender_id, m.sent_at, m.is_read,
                   u.fname, u.lname, u.type as user_type, s.sport_name
            LEFT JOIN user u ON m.sender_id = u.user_id
            LEFT JOIN sport s ON m.sport_id = s.sport_id
            WHERE m.recipient_id = :recipient_id AND m.status = 'ACTIVE'
            ORDER BY m.sent_at DESC
        ");
        $stmt->execute(['recipient_id' => $recipientId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($messages as $msg) {
            $senderName = trim($msg['fname'] . ' ' . $msg['lname']) ?: 'Unknown';
            
            $result[] = [
                'id' => $msg['message_id'],
                'sender' => $senderName,
                'sender_id' => $msg['sender_id'],
                'sender_role' => $msg['user_type'] === 'COACH' ? 'Coach' : ($msg['user_type'] === 'CAPTAIN' ? 'Captain' : ($msg['user_type'] === 'ADMIN' ? 'Admin' : 'Sports Manager')),
                'sport' => $msg['sport_name'] ?? 'Unknown Sport',
                'title' => $msg['title'],
                'text' => substr($msg['message'], 0, 50) . (strlen($msg['message']) > 50 ? '...' : ''),
                'full_message' => $msg['message'],
                'date' => date('d M', strtotime($msg['sent_at'])),
                'timestamp' => $msg['sent_at'],
                'is_read' => (bool)$msg['is_read']
            ];
        }

        return $result;
    }

    /**
     * Mark a message as read
     * @param string $messageId
     * @param string $recipientId
     * @return bool
     */
    public function markAsRead($messageId, $recipientId) {
        try {
            $stmt = $this->db->prepare("UPDATE message SET is_read = 1 WHERE message_id = :message_id AND recipient_id = :recipient_id");
            $stmt->execute([
                'message_id' => $messageId,
                'recipient_id' => $recipientId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
