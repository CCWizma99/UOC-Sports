<?php
class MessageApiController {

    /**
     * Get available recipients (coach and manager) for the captain's sport
     */
    public function getRecipients() {
        header('Content-Type: application/json');
        
        try {
            // Check if user is logged in and is a captain
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $userId = $_SESSION['user_id'];
            
            // Get captain's sport
            $sportModel = new Sport();
            $sport = $sportModel->getSportByCaptain($userId);
            
            if (!$sport) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a captain for any sport']);
                return;
            }

            $messageModel = new Message();
            $recipients = $messageModel->getRecipientsBySport($sport['sport_id']);

            // Fetch Admins
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT user_id, fname, lname FROM user WHERE type = 'ADMIN'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($admins as $admin) {
                 $recipients[] = [
                    'user_id' => $admin['user_id'],
                    'name' => trim($admin['fname'] . ' ' . $admin['lname']) ?: 'Admin',
                    'type' => 'ADMIN',
                    'label' => 'Admin - ' . (trim($admin['fname'] . ' ' . $admin['lname']) ?: 'System Administrator')
                ];
            }

            // Fetch Coach
$pdo = Database::getConnection();

$stmt = $pdo->prepare("
    SELECT u.user_id, u.fname, u.lname
    FROM user u
    INNER JOIN sport s ON s.coach_id = u.user_id
    WHERE s.sport_id = :sport_id
");

$stmt->execute(['sport_id' => $sport['sport_id']]);

$coaches = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($coaches as $coach) {
    $recipients[] = [
        'user_id' => $coach['user_id'],
        'name' => trim($coach['fname'] . ' ' . $coach['lname']) ?: 'Coach',
        'type' => 'COACH',
        'label' => 'Coach - ' . (trim($coach['fname'] . ' ' . $coach['lname']) ?: 'Not Assigned')
    ];
}

            if (empty($recipients)) {
                echo json_encode([
                    'status' => 'empty', 
                    'message' => 'No coach or manager assigned to your sport yet',
                    'data' => []
                ]);
                return;
            }

            echo json_encode(['status' => 'success', 'data' => $recipients]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Send a new message
     */
    public function sendMessage() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $senderId = $_SESSION['user_id'];
            
            // Get POST data
            $recipientId = $_POST['recipient_id'] ?? '';
            $recipientType = $_POST['recipient_type'] ?? '';
            $title = $_POST['title'] ?? '';
            $message = $_POST['message'] ?? '';

            // Validate required fields
            if (empty($recipientId) || empty($recipientType) || empty($title) || empty($message)) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
                return;
            }

            // Validate recipient type
            if (!in_array($recipientType, ['COACH', 'MANAGER', 'ADMIN'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid recipient type']);
                return;
            }

            // Get captain's sport
            $sportModel = new Sport();
            $sport = $sportModel->getSportByCaptain($senderId);
            
            if (!$sport) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a captain for any sport']);
                return;
            }

            // Create message
            $messageModel = new Message();
            $messageId = $messageModel->create($senderId, $recipientId, $recipientType, $sport['sport_id'], $title, $message);

            if ($messageId) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Message sent successfully',
                    'message_id' => $messageId
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all messages sent by the captain
     */
    public function getMessages() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $senderId = $_SESSION['user_id'];
            
            $messageModel = new Message();
            $messages = $messageModel->getMessagesBySender($senderId);
            $count = count($messages);

            echo json_encode([
                'status' => 'success', 
                'data' => $messages,
                'count' => $count
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a message
     */
    public function deleteMessage() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $senderId = $_SESSION['user_id'];
            $messageId = $_POST['message_id'] ?? '';

            if (empty($messageId)) {
                echo json_encode(['status' => 'error', 'message' => 'Message ID is required']);
                return;
            }

            $messageModel = new Message();
            $deleted = $messageModel->deleteMessage($messageId, $senderId);

            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete message or message not found']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get inbox messages for a coach or manager
     */
    public function getInbox() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $recipientId = $_SESSION['user_id'];
            
            $messageModel = new Message();
            $messages = $messageModel->getMessagesByRecipient($recipientId);
            $count = count($messages);

            echo json_encode([
                'status' => 'success', 
                'data' => $messages,
                'count' => $count
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Mark a message as read
     */
    public function markRead() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $recipientId = $_SESSION['user_id'];
            $messageId = $_POST['message_id'] ?? '';

            if (empty($messageId)) {
                echo json_encode(['status' => 'error', 'message' => 'Message ID is required']);
                return;
            }

            $messageModel = new Message();
            $marked = $messageModel->markAsRead($messageId, $recipientId);

            if ($marked) {
                echo json_encode(['status' => 'success', 'message' => 'Message marked as read']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to mark message as read']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get available recipients for coach to send messages (captain and manager of their sport)
     */
    public function getCoachRecipients() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $userId = $_SESSION['user_id'];
            
            // Get coach's sport
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT s.sport_id, s.sport_name, s.captain_id, s.manager_id,
                       captain.fname AS captain_fname, captain.lname AS captain_lname,
                       manager.fname AS manager_fname, manager.lname AS manager_lname
                FROM sport s
                LEFT JOIN user captain ON s.captain_id = captain.user_id
                LEFT JOIN user manager ON s.manager_id = manager.user_id
                WHERE s.coach_id = :coach_id
            ");
            $stmt->execute(['coach_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a coach for any sport']);
                return;
            }

            $recipients = [];

            // Add captain if exists
            if (!empty($result['captain_id'])) {
                $recipients[] = [
                    'user_id' => $result['captain_id'],
                    'name' => trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Captain',
                    'type' => 'CAPTAIN',
                    'label' => 'Captain - ' . (trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Not Assigned')
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

            // Fetch Admins
            $stmt = $pdo->prepare("SELECT user_id, fname, lname FROM user WHERE type = 'ADMIN'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($admins as $admin) {
                 $recipients[] = [
                    'user_id' => $admin['user_id'],
                    'name' => trim($admin['fname'] . ' ' . $admin['lname']) ?: 'Admin',
                    'type' => 'ADMIN',
                    'label' => 'Admin - ' . (trim($admin['fname'] . ' ' . $admin['lname']) ?: 'System Administrator')
                ];
            }

            if (empty($recipients)) {
                echo json_encode([
                    'status' => 'empty', 
                    'message' => 'No captain or manager assigned to your sport yet',
                    'data' => []
                ]);
                return;
            }

            echo json_encode(['status' => 'success', 'data' => $recipients, 'sport_id' => $result['sport_id']]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Send a message from coach to captain or manager
     */
    public function sendCoachMessage() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $senderId = $_SESSION['user_id'];
            
            // Get POST data
            $recipientId = $_POST['recipient_id'] ?? '';
            $recipientType = $_POST['recipient_type'] ?? '';
            $title = $_POST['title'] ?? '';
            $message = $_POST['message'] ?? '';

            // Validate required fields
            if (empty($recipientId) || empty($recipientType) || empty($title) || empty($message)) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
                return;
            }

            // Validate recipient type
            if (!in_array($recipientType, ['CAPTAIN', 'MANAGER', 'ADMIN'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid recipient type']);
                return;
            }

            // Get coach's sport
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE coach_id = :coach_id");
            $stmt->execute(['coach_id' => $senderId]);
            $sport = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$sport) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a coach for any sport']);
                return;
            }

            // Create message (using the message table with sender as coach)
            $messageModel = new Message();
            $messageId = $messageModel->create($senderId, $recipientId, $recipientType, $sport['sport_id'], $title, $message);

            if ($messageId) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Message sent successfully',
                    'message_id' => $messageId
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
