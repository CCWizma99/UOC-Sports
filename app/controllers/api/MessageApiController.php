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
            if (!in_array($recipientType, ['COACH', 'MANAGER'])) {
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
}
