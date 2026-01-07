<?php

class MessageController {
    
    /**
     * Display messages page with conversations
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit();
        }
        
        $messageModel = new Message(Database::getConnection());
        $userId = $_SESSION['user_id'];
        
        // Get conversation partners and available recipients
        $conversations = $messageModel->getConversationPartners($userId);
        $recipients = $messageModel->getAvailableRecipients($userId);
        
        view('sports-manager/message', [
            'conversations' => $conversations,
            'recipients' => $recipients,
            'userId' => $userId
        ]);
    }
    
    /**
     * Send a new message
     */
    public function send() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Not authenticated. Please log in.';
            header('Location: /uoc-sports/public/sign-in');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            header('Location: /uoc-sports/public/sport-manager/messages');
            exit();
        }
        
        $senderId = $_SESSION['user_id'];
        $receiverId = $_POST['receiver_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $messageText = $_POST['message'] ?? '';
        
        if (!$receiverId || !$title || !$messageText) {
            $_SESSION['error_message'] = 'All fields are required. Please fill in recipient, title, and message.';
            header('Location: /uoc-sports/public/sport-manager/messages');
            exit();
        }
        
        $messageModel = new Message(Database::getConnection());
        $result = $messageModel->sendMessage($senderId, $receiverId, $title, $messageText);
        
        if ($result) {
            $_SESSION['success_message'] = 'Message sent successfully!';
            header('Location: /uoc-sports/public/sport-manager/messages');
            exit();
        } else {
            $_SESSION['error_message'] = 'Failed to send message. Please try again.';
            header('Location: /uoc-sports/public/sport-manager/messages');
            exit();
        }
    }
    
    /**
     * Get conversation between two users (AJAX)
     */
    public function getConversation() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $partnerId = $_GET['partner_id'] ?? null;
        
        if (!$partnerId) {
            echo json_encode(['success' => false, 'message' => 'Partner ID required']);
            exit();
        }
        
        $messageModel = new Message(Database::getConnection());
        $messages = $messageModel->getConversation($userId, $partnerId);
        
        echo json_encode(['success' => true, 'messages' => $messages]);
    }
    
    /**
     * Delete a message
     */
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $messageId = $data['message_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$messageId) {
            echo json_encode(['success' => false, 'message' => 'Message ID required']);
            exit();
        }
        
        $messageModel = new Message(Database::getConnection());
        $result = $messageModel->deleteMessage($messageId, $userId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete message']);
        }
    }
    
    /**
     * Get all messages for current user (AJAX)
     */
    public function getAllMessages() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $userId = $_SESSION['user_id'];
        $messageModel = new Message(Database::getConnection());
        $messages = $messageModel->getUserMessages($userId);
        
        echo json_encode(['success' => true, 'messages' => $messages]);
    }
    
    /**
     * Get message details by ID (AJAX)
     */
    public function getMessageDetails() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit();
        }
        
        $messageId = $_GET['message_id'] ?? null;
        
        if (!$messageId) {
            echo json_encode(['success' => false, 'message' => 'Message ID required']);
            exit();
        }
        
        $messageModel = new Message(Database::getConnection());
        $message = $messageModel->getMessageById($messageId);
        
        if ($message) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Message not found']);
        }
    }
}
