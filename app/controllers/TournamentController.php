<?php

class TournamentController extends BaseController {
    
    /**
     * Create a new tournament
     */
    public function createTournament() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $name = trim($input['name'] ?? '');
            $sportId = trim($input['sport_id'] ?? '');
            $startDate = trim($input['start_date'] ?? '');
            $endDate = trim($input['end_date'] ?? '');
            
            // Validation
            if (empty($name) || empty($sportId) || empty($startDate)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tournament name, sport, and start date are required.'
                ]);
                return;
            }
            
            // Validate dates
            if (!empty($endDate) && strtotime($endDate) < strtotime($startDate)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'End date cannot be before start date.'
                ]);
                return;
            }
            
            $tournamentModel = new Tournament();
            $tournamentId = $tournamentModel->createTournament($name, $sportId, $startDate, $endDate);
            
            if ($tournamentId) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tournament created successfully.',
                    'tournament_id' => $tournamentId
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create tournament.'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Send tournament invitation email
     */
    public function sendInvitation() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $email = trim($input['email'] ?? '');
            $recipientName = trim($input['recipient_name'] ?? '');
            $tournamentId = trim($input['tournament_id'] ?? '');
            $saveRecipient = $input['save_recipient'] ?? false;
            
            // Validation
            if (empty($email) || empty($recipientName) || empty($tournamentId)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Email, recipient name, and tournament ID are required.'
                ]);
                return;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email format.'
                ]);
                return;
            }
            
            // Get tournament details
            $tournamentModel = new Tournament();
            $tournament = $tournamentModel->getTournamentById($tournamentId);
            
            if (!$tournament) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tournament not found.'
                ]);
                return;
            }
            
            // Save recipient if requested
            if ($saveRecipient) {
                $saved = $tournamentModel->saveEmail($email, $recipientName);
                if (!$saved) {
                    // Email might already exist, but continue with sending
                    error_log("Email $email already exists or failed to save");
                }
            }
            
            // Send invitation email
            $emailService = new EmailService();
            $result = $emailService->sendTournamentInvitation($email, $recipientName, $tournament);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all saved email recipients
     */
    public function getSavedRecipients() {
        header('Content-Type: application/json');
        
        try {
            $tournamentModel = new Tournament();
            $recipients = $tournamentModel->getSavedEmails();
            
            echo json_encode([
                'status' => 'success',
                'recipients' => $recipients
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Save a new email recipient
     */
    public function saveRecipient() {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $email = trim($input['email'] ?? '');
            $recipientName = trim($input['recipient_name'] ?? '');
            
            // Validation
            if (empty($email) || empty($recipientName)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Email and recipient name are required.'
                ]);
                return;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid email format.'
                ]);
                return;
            }
            
            $tournamentModel = new Tournament();
            
            // Check if email already exists
            if ($tournamentModel->emailExists($email)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'This email is already saved.'
                ]);
                return;
            }
            
            $saved = $tournamentModel->saveEmail($email, $recipientName);
            
            if ($saved) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Recipient saved successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save recipient.'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get all tournaments
     */
    public function getTournaments() {
        header('Content-Type: application/json');
        
        try {
            $tournamentModel = new Tournament();
            $tournaments = $tournamentModel->getAllTournaments();
            
            echo json_encode([
                'status' => 'success',
                'data' => $tournaments
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
