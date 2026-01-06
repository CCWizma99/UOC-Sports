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

    /**
     * Add match result (Hybrid JSON approach)
     */
    public function addResult() {
        header('Content-Type: application/json');
        
        try {
            $tournamentId = $_POST['tournament_id'] ?? '';
            $sportId = $_POST['sport_id'] ?? '';
            $matchName = trim($_POST['match_name'] ?? '');
            $matchDate = $_POST['match_date'] ?? '';
            $winnerId = $_POST['winner_id'] ?? null;
            $winnerScore = $_POST['winner_score'] ?? null;
            $loserScore = $_POST['loser_score'] ?? null;
            $details = $_POST['details'] ?? [];
            $participants = $_POST['participants'] ?? [];
            
            // Validation
            if (empty($tournamentId) || empty($sportId) || empty($matchName) || empty($matchDate)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tournament, sport, match name, and date are required.'
                ]);
                return;
            }
            
            $sportModel = new Sport();
            
            // Add match result
            $matchId = $sportModel->addResult(
                $tournamentId,
                $sportId,
                $matchName,
                $matchDate,
                $winnerId ?: null,
                $winnerScore ?: null,
                $loserScore ?: null,
                $details
            );
            
            // Add participants if provided
            if (!empty($participants) && is_array($participants)) {
                foreach ($participants as $p) {
                    $sportModel->addMatchParticipant(
                        $matchId,
                        $p['user_id'],
                        $p['team'] ?? 'A',
                        $p['score'] ?? null,
                        $p['performance_data'] ?? [],
                        $p['notes'] ?? null
                    );
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Match result added successfully.',
                'match_id' => $matchId
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Add match result using sport-specific tables
     * New method that replaces the hybrid JSON approach
     */
    public function addMatchResult() {
        header('Content-Type: application/json');
        
        try {
            $tournamentId = $_POST['tournament_id'] ?? '';
            $sportId = $_POST['sport_id'] ?? '';
            $matchName = trim($_POST['match_name'] ?? '');
            $matchDate = $_POST['match_date'] ?? '';
            $winnerId = $_POST['winner_id'] ?? null;
            $resultStatus = $_POST['result_status'] ?? 'COMPLETED';
            $details = $_POST['details'] ?? [];
            
            // Validation
            if (empty($tournamentId) || empty($sportId) || empty($matchName) || empty($matchDate)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tournament, sport, match name, and date are required.'
                ]);
                return;
            }
            
            // Prepare match data
            $matchData = [
                'tournament_id' => $tournamentId,
                'sport_id' => $sportId,
                'match_name' => $matchName,
                'match_date' => $matchDate,
                'winner_id' => $winnerId ?: null,
                'result_status' => $resultStatus
            ];
            
            // Process JSON fields in details
            if (is_array($details)) {
                foreach ($details as $key => $value) {
                    // Decode JSON strings (set_scores, round_scores, period_scores, results)
                    if (is_string($value) && in_array($key, ['set_scores', 'round_scores', 'period_scores', 'results', 'competition_results'])) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $details[$key] = $decoded;
                        }
                    }
                }
            }
            
            $sportModel = new Sport();
            $matchId = $sportModel->addMatchResult($matchData, $details);
            
            if ($matchId) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Match result added successfully.',
                    'match_id' => $matchId
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to add match result. Please check the sport configuration.'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
