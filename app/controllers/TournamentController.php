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
            $matchLevel = trim($input['match_level'] ?? 'UNIVERSITY');
            
            // Validation
            if (empty($name) || empty($sportId) || empty($startDate)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Tournament name, sport, and start date are required.'
                ]);
                return;
            }

            // Ensure start date is not in the past
            if (strtotime($startDate) < strtotime(date('Y-m-d'))) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Start date cannot be in the past.'
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
            $tournamentId = $tournamentModel->createTournament($name, $sportId, $startDate, $endDate, $matchLevel);
            
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
     * Get past tournaments (for history page)
     */
    public function getPastTournaments() {
        header('Content-Type: application/json');
        
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            $tournamentModel = new Tournament();
            $tournaments = $tournamentModel->getPastTournaments($limit, $offset, $search);
            $totalCount = $tournamentModel->getPastTournamentsCount($search);
            
            echo json_encode([
                'status' => 'success',
                'data' => $tournaments,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalCount / $limit),
                    'total_count' => $totalCount,
                    'limit' => $limit
                ]
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
                'result_status' => $resultStatus,
                'is_published' => 1,
                'submitted_by' => 'ADMIN'
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
                // Auto-complete tournament if it's the Final match
                $lowerMatchName = strtolower($matchName);
                if (strpos($lowerMatchName, 'final') !== false && 
                    strpos($lowerMatchName, 'semi') === false && 
                    strpos($lowerMatchName, 'quarter') === false) {
                    $tournamentModel = new Tournament();
                    $tournamentModel->updateStatus($tournamentId, 'COMPLETE');
                }

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
    /**
     * Get all tournaments that have already started (for admin grant-permission panel)
     */
    public function getStartedTournaments() {
        header('Content-Type: application/json');
        try {
            $permModel = new EventResultPermission();
            $tournaments = $permModel->getStartedTournaments();
            echo json_encode(['status' => 'success', 'data' => $tournaments]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all granted permissions (for admin view)
     */
    public function getGrantedPermissions() {
        header('Content-Type: application/json');
        try {
            $permModel = new EventResultPermission();
            $permissions = $permModel->getAllGrantedPermissions();
            echo json_encode(['status' => 'success', 'data' => $permissions]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Grant a captain permission to submit results for a tournament
     * Sends an email notification to the captain
     */
    public function grantCaptainPermission() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $tournamentId = trim($input['tournament_id'] ?? '');
            $adminId      = $_SESSION['user_id'] ?? '';

            if (empty($tournamentId) || empty($adminId)) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament ID and admin session are required.']);
                return;
            }

            // Verify tournament exists and has started
            $tournamentModel = new Tournament();
            $tournament      = $tournamentModel->getTournamentById($tournamentId);
            if (!$tournament) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament not found.']);
                return;
            }
            if (strtotime($tournament['start_date']) > time()) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament has not started yet. Permission can only be granted after the start date.']);
                return;
            }

            // Get sport and captain info
            $sportId   = $tournament['sport_id'];
            $sportModel = new Sport();
            $sportFull  = $sportModel->getSportWithStaff($sportId);

            if (empty($sportFull['captain_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'No captain is assigned to this sport. Please assign a captain first.']);
                return;
            }

            $captainId    = $sportFull['captain_id'];
            $captainName  = trim(($sportFull['captain_fname'] ?? '') . ' ' . ($sportFull['captain_lname'] ?? ''));
            $captainEmail = $sportFull['captain_email'] ?? '';

            // Grant permission
            $permModel = new EventResultPermission();
            $result    = $permModel->grantPermission($tournamentId, $captainId, $sportId, $adminId);

            if ($result === false) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to grant permission.']);
                return;
            }

            // If newly granted (not previously existed as active), send email
            $emailResult = ['status' => 'skipped', 'message' => 'Permission already active, email not re-sent.'];
            if (!$result['exists'] && !empty($captainEmail)) {
                $emailService = new EmailService();
                $emailResult  = $emailService->sendCaptainPermissionEmail($captainEmail, $captainName, [
                    'tournament_name' => $tournament['tournament_name'],
                    'sport_name'      => $tournament['sport_name'],
                    'start_date'      => $tournament['start_date'],
                    'end_date'        => $tournament['end_date'],
                ]);
                if ($emailResult['status'] === 'success') {
                    $permModel->markEmailSent($result['id']);
                }
            }

            echo json_encode([
                'status'       => 'success',
                'message'      => $result['exists']
                    ? 'Permission was already active for this captain.'
                    : 'Permission granted and email sent to captain.',
                'email_result' => $emailResult,
                'captain_name' => $captainName,
                'captain_email'=> $captainEmail,
            ]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Revoke a captain's permission for a tournament
     */
    public function revokeCaptainPermission() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $permId = intval($input['permission_id'] ?? 0);

            if (!$permId) {
                echo json_encode(['status' => 'error', 'message' => 'Permission ID is required.']);
                return;
            }

            $permModel = new EventResultPermission();
            $success   = $permModel->revokePermission($permId);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Permission revoked successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to revoke permission.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark a tournament as COMPLETE
     */
    public function completeTournament() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $tournamentId = trim($input['tournament_id'] ?? '');

            if (empty($tournamentId)) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament ID is required.']);
                return;
            }

            $tournamentModel = new Tournament();
            $success = $tournamentModel->updateStatus($tournamentId, 'COMPLETE');

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Tournament marked as complete.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to mark tournament as complete.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
