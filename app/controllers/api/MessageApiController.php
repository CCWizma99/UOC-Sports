<?php
class MessageApiController {

    /**
     * Get available recipients for the captain's sport
     * Filtered by sport (Coach & Manager) + Admins
     */
    public function getRecipients() {
        header('Content-Type: application/json');
        
        try {
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
            // This already filters by sport_id in the model
            $recipients = $messageModel->getRecipientsBySport($sport['sport_id']);

            // Fetch Admins (Global)
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT user_id, fname, lname FROM user WHERE type = 'ADMIN' AND status = 'ACTIVE'");
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

            echo json_encode(['status' => 'success', 'data' => $recipients]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Send a message - Universal method for all roles
     */
    public function sendMessage() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $senderId = $_SESSION['user_id'];
            $senderType = $_SESSION['user_type'] ?? 'PUBLIC';
            
            $recipientId = $_POST['recipient_id'] ?? '';
            $recipientType = $_POST['recipient_type'] ?? '';
            $title = $_POST['title'] ?? '';
            $message = $_POST['message'] ?? '';

            if (!$recipientId || !$recipientType || !$title || !$message) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
                return;
            }

            // Allowed recipient types based on sender role
            $allowedTargets = [];
            if ($senderType === 'CAPTAIN') $allowedTargets = ['COACH', 'SPT', 'ADMIN'];
            elseif ($senderType === 'COACH') $allowedTargets = ['CAPTAIN', 'SPT', 'ADMIN'];
            elseif ($senderType === 'SPT') $allowedTargets = ['COACH', 'CAPTAIN', 'ADMIN'];
            elseif ($senderType === 'ADMIN') $allowedTargets = ['COACH', 'CAPTAIN', 'SPT', 'ADMIN', 'EQP', 'REG', 'EXECUTIVE', 'STUDENT'];

            // Harmonize 'MANAGER' to 'SPT' if sent from frontend
            if ($recipientType === 'MANAGER') $recipientType = 'SPT';

            // Auto-deduce recipient type if needed (e.g. from Admin Inquiry page)
            if (empty($recipientType) || $senderType === 'ADMIN') {
                $stmt = $pdo->prepare("SELECT type FROM user WHERE user_id = ?");
                $stmt->execute([$recipientId]);
                $foundType = $stmt->fetchColumn();
                if ($foundType) $recipientType = $foundType;
                if ($recipientType === 'MANAGER') $recipientType = 'SPT'; // System-wide harmonization
            }

            if (!in_array($recipientType, $allowedTargets)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid recipient type for your role']);
                return;
            }

            // Determine sport context
            $sportId = null;
            $pdo = Database::getConnection();

            if ($senderType === 'CAPTAIN') {
                $stmt = $pdo->prepare("SELECT sport_id FROM captain_sport WHERE user_id = ? AND date_relieved IS NULL");
                $stmt->execute([$senderId]);
                $sportId = $stmt->fetchColumn();
            } elseif ($senderType === 'COACH') {
                $stmt = $pdo->prepare("SELECT sport_id FROM coach_sport WHERE user_id = ? AND date_relieved IS NULL");
                $stmt->execute([$senderId]);
                $sportId = $stmt->fetchColumn();
            } elseif ($senderType === 'SPT') {
                $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE manager_id = ?");
                $stmt->execute([$senderId]);
                $sportId = $stmt->fetchColumn();
            } else {
                // Admin or other - default to a generic sport or null if allowed by schema
                $sportId = $_POST['sport_id'] ?? 'GEN'; 
            }

            // Create message
            $messageModel = new Message();
            $messageId = $messageModel->create($senderId, $recipientId, $recipientType, $sportId, $title, $message);

            if ($messageId) {
                // ── Email Notification ────────────────────────────────────────
                // Fire-and-forget: errors here must never block portal delivery.
                try {
                    require_once APP_ROOT . '/app/services/EmailService.php';

                    // Fetch sender info
                    $stmt = $pdo->prepare("SELECT fname, lname, type FROM user WHERE user_id = ?");
                    $stmt->execute([$senderId]);
                    $sender = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Fetch recipient info
                    $stmt = $pdo->prepare("SELECT fname, lname, email FROM user WHERE user_id = ?");
                    $stmt->execute([$recipientId]);
                    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($sender && $recipient && !empty($recipient['email'])) {
                        // Human-readable role labels
                        $roleLabels = [
                            'CAPTAIN'   => 'Team Captain',
                            'COACH'     => 'Coach',
                            'SPT'       => 'Sports Manager',
                            'ADMIN'     => 'System Administrator',
                            'EQP'       => 'Equipment Manager',
                            'REG'       => 'Registrar',
                            'EXECUTIVE' => 'Executive',
                            'STUDENT'   => 'Student',
                        ];

                        // Role-specific deep-links to the recipient's inbox
                        $inboxUrls = [
                            'CAPTAIN'   => 'http://localhost/uoc-sports/public/captain/communication',
                            'COACH'     => 'http://localhost/uoc-sports/public/coach/coach-communicate',
                            'SPT'       => 'http://localhost/uoc-sports/public/sport-manager/messages',
                            'ADMIN'     => 'http://localhost/uoc-sports/public/admin-index',
                            'EQP'       => 'http://localhost/uoc-sports/public/equipment-manager',
                            'REG'       => 'http://localhost/uoc-sports/public/registrar',
                            'EXECUTIVE' => 'http://localhost/uoc-sports/public/',
                            'STUDENT'   => 'http://localhost/uoc-sports/public/student',
                        ];

                        $senderName    = trim($sender['fname'] . ' ' . $sender['lname']);
                        $senderRole    = $roleLabels[$sender['type']] ?? $sender['type'];
                        $recipientName = trim($recipient['fname'] . ' ' . $recipient['lname']);
                        // $recipientType is already correctly resolved above (harmonized, deduce-corrected)
                        $inboxUrl = $inboxUrls[$recipientType] ?? 'http://localhost/uoc-sports/public/';

                        $emailService = new EmailService();
                        $emailService->sendMessageNotification(
                            $recipient['email'],
                            $recipientName,
                            $senderName,
                            $senderRole,
                            $title,
                            $message,
                            $inboxUrl
                        );
                    }
                } catch (Exception $emailEx) {
                    // Log silently — do not expose to client
                    error_log('[Messaging Email] Failed to send notification: ' . $emailEx->getMessage());
                }
                // ─────────────────────────────────────────────────────────────

                echo json_encode(['status' => 'success', 'message' => 'Message sent successfully', 'message_id' => $messageId]);
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
     * Get available recipients for coach to send messages
     * Filtered by same sport (Captain and Manager) + Admins
     */
    public function getCoachRecipients() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $userId = $_SESSION['user_id'];
            
            // Get coach's sport (prioritizing selected sport if available)
            $selectedSportId = $_SESSION['selected_sport_id'] ?? null;
            $pdo = Database::getConnection();
            
            $query = "
                SELECT s.sport_id, s.sport_name, s.captain_id, s.manager_id,
                       captain.fname AS captain_fname, captain.lname AS captain_lname,
                       manager.fname AS manager_fname, manager.lname AS manager_lname
                FROM sport s
                LEFT JOIN user captain ON s.captain_id = captain.user_id
                LEFT JOIN user manager ON s.manager_id = manager.user_id
                WHERE s.coach_id = :coach_id
            ";
            $params = ['coach_id' => $userId];

            if ($selectedSportId) {
                $query .= " AND s.sport_id = :sport_id";
                $params['sport_id'] = $selectedSportId;
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a coach for the selected sport']);
                return;
            }

            // If no sport was specified, just use the first one from the results to stay "filtered"
            if (!$selectedSportId && count($results) > 1) {
                $results = [$results[0]];
            }

            $recipients = [];
            foreach ($results as $result) {
                // Add captain if exists
                if (!empty($result['captain_id'])) {
                    $recipients[] = [
                        'user_id' => $result['captain_id'],
                        'name' => trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Captain',
                        'type' => 'CAPTAIN',
                        'label' => 'Captain - ' . (trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Not Assigned') . " (" . $result['sport_name'] . ")"
                    ];
                }

                // Add manager if exists
                if (!empty($result['manager_id'])) {
                    $recipients[] = [
                        'user_id' => $result['manager_id'],
                        'name' => trim($result['manager_fname'] . ' ' . $result['manager_lname']) ?: 'Sports Manager',
                        'type' => 'SPT',
                        'label' => 'Sports Manager - ' . (trim($result['manager_fname'] . ' ' . $result['manager_lname']) ?: 'Not Assigned') . " (" . $result['sport_name'] . ")"
                    ];
                }
            }

            // Fetch Admins (Global)
            $stmt = $pdo->prepare("SELECT user_id, fname, lname FROM user WHERE type = 'ADMIN' AND status = 'ACTIVE'");
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

            echo json_encode(['status' => 'success', 'data' => $recipients]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get available recipients for sports manager to send messages
     * Filtered by same sport (Coach and Captain) + Admins
     */
    public function getManagerRecipients() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
                return;
            }

            $userId = $_SESSION['user_id'];
            
            // Get manager's sport (prioritizing selected sport if available)
            $selectedSportId = $_SESSION['selected_sport_id'] ?? null;
            $pdo = Database::getConnection();
            
            $query = "
                SELECT s.sport_id, s.sport_name, s.coach_id, s.captain_id,
                       coach.fname AS coach_fname, coach.lname AS coach_lname,
                       captain.fname AS captain_fname, captain.lname AS captain_lname
                FROM sport s
                LEFT JOIN user coach ON s.coach_id = coach.user_id
                LEFT JOIN user captain ON s.captain_id = captain.user_id
                WHERE s.manager_id = :manager_id
            ";
            $params = ['manager_id' => $userId];

            if ($selectedSportId) {
                $query .= " AND s.sport_id = :sport_id";
                $params['sport_id'] = $selectedSportId;
            }

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                echo json_encode(['status' => 'error', 'message' => 'You are not assigned as a manager for the selected sport']);
                return;
            }

            // If no sport was specified, just use the first one from the results to stay "filtered"
            if (!$selectedSportId && count($results) > 1) {
                $results = [$results[0]];
            }

            $recipients = [];
            foreach ($results as $result) {
                // Add coach if exists
                if (!empty($result['coach_id'])) {
                    $recipients[] = [
                        'user_id' => $result['coach_id'],
                        'name' => trim($result['coach_fname'] . ' ' . $result['coach_lname']) ?: 'Coach',
                        'type' => 'COACH',
                        'label' => 'Coach - ' . (trim($result['coach_fname'] . ' ' . $result['coach_lname']) ?: 'Not Assigned') . " (" . $result['sport_name'] . ")"
                    ];
                }

                // Add captain if exists
                if (!empty($result['captain_id'])) {
                    $recipients[] = [
                        'user_id' => $result['captain_id'],
                        'name' => trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Captain',
                        'type' => 'CAPTAIN',
                        'label' => 'Captain - ' . (trim($result['captain_fname'] . ' ' . $result['captain_lname']) ?: 'Not Assigned') . " (" . $result['sport_name'] . ")"
                    ];
                }
            }

            // Fetch Admins (Global)
            $stmt = $pdo->prepare("SELECT user_id, fname, lname FROM user WHERE type = 'ADMIN' AND status = 'ACTIVE'");
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

            echo json_encode(['status' => 'success', 'data' => $recipients]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all available recipients for admin (Global access)
     */
    public function getAdminRecipients() {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'ADMIN') {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                return;
            }

            $pdo = Database::getConnection();
            
            // Fetch all active Coaches, Managers, Captains, EQP, REG, EXECUTIVE
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.fname, u.lname, u.type, s.sport_name
                FROM user u
                LEFT JOIN sport s ON (u.user_id = s.coach_id OR u.user_id = s.manager_id OR u.user_id = s.captain_id)
                WHERE u.type IN ('COACH', 'SPT', 'CAPTAIN', 'EQP', 'REG', 'EXECUTIVE') AND u.status = 'ACTIVE'
                ORDER BY s.sport_name, u.type
            ");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $recipients = [];
            foreach ($users as $user) {
                $roleLabel = ucfirst(strtolower($user['type']));
                if ($user['type'] === 'SPT') $roleLabel = 'Sports Manager';
                if ($user['type'] === 'EQP') $roleLabel = 'Equipment Manager';
                if ($user['type'] === 'REG') $roleLabel = 'Registrar';
                
                $recipients[] = [
                    'user_id' => $user['user_id'],
                    'name' => trim($user['fname'] . ' ' . $user['lname']),
                    'type' => $user['type'],
                    'label' => $roleLabel . ' - ' . trim($user['fname'] . ' ' . $user['lname']) . ($user['sport_name'] ? " ({$user['sport_name']})" : "")
                ];
            }

            echo json_encode(['status' => 'success', 'data' => $recipients]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
