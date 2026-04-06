<?php

class CaptainApiController {

    /**
     * Get tournaments where the logged-in captain has ACTIVE permission
     * and the event has already started.
     */
    public function getPermittedTournaments() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
            return;
        }

        try {
            $captainId = $_SESSION['user_id'];
            $permModel = new EventResultPermission();
            $tournaments = $permModel->getActivePermissionsForCaptain($captainId);

            if (empty($tournaments)) {
                echo json_encode(['status' => 'empty', 'data' => [], 'message' => 'No permitted tournaments found.']);
            } else {
                echo json_encode(['status' => 'success', 'data' => $tournaments]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Submit a match result on behalf of a captain.
     * Validates the captain has an ACTIVE permission for the given tournament.
     * Delegates saving to Sport model (same as admin path).
     */
    public function submitResult() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
            return;
        }

        try {
            $captainId    = $_SESSION['user_id'];
            $tournamentId = $_POST['tournament_id'] ?? '';
            $sportId      = $_POST['sport_id'] ?? '';
            $matchName    = trim($_POST['match_name'] ?? '');
            $matchDate    = $_POST['match_date'] ?? '';
            $resultStatus = $_POST['result_status'] ?? 'COMPLETED';
            $winnerId     = $_POST['winner_id'] ?? null;
            $winnerType   = $_POST['winner_type'] ?? 'INTERNAL'; // INTERNAL, TEAM, INVITATIONAL, DRAW
            $winnerName   = $_POST['winner_name'] ?? null;
            $invData      = $_POST['invitational'] ?? null; // For new invitational players
            $details      = $_POST['details'] ?? [];

            // Validate required fields
            if (empty($tournamentId) || empty($sportId) || empty($matchName) || empty($matchDate)) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament, sport, match name, and date are required.']);
                return;
            }

            // Validate the captain actually has permission for this tournament
            $permModel = new EventResultPermission();
            if (!$permModel->hasPermission($captainId, $tournamentId)) {
                echo json_encode(['status' => 'error', 'message' => 'You do not have permission to add results for this tournament.']);
                return;
            }

            // Decode any JSON-encoded detail fields (set_scores, round_scores, etc.)
            if (is_array($details)) {
                foreach ($details as $key => $value) {
                    if (is_string($value) && in_array($key, ['set_scores', 'round_scores', 'period_scores', 'results', 'competition_results'])) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $details[$key] = $decoded;
                        }
                    }
                }
            }

            // Handle auto-saving teams if it's a team sport
            if ($winnerType === 'TEAM' || isset($details['team_a_name']) || isset($details['team_b_name'])) {
                require_once 'PlayingTeamApiController.php';
                $teamApi = new PlayingTeamApiController();
                
                if (!empty($details['team_a_name'])) {
                    // Internal call to ensure team exists
                    $this->internalEnsureTeam($details['team_a_name']);
                }
                if (!empty($details['team_b_name'])) {
                    $this->internalEnsureTeam($details['team_b_name']);
                }
                
                if ($winnerType === 'TEAM' && empty($winnerName)) {
                    $winnerName = $_POST['winner_team_selection'] ?? null;
                }
            }

            // Handle invitational player auto-save
            $winnerInvId = null;
            if ($winnerType === 'INVITATIONAL' && $invData) {
                $winnerInvId = $this->internalEnsureInvitationalPlayer($invData);
                $winnerName = trim(($invData['fname'] ?? '') . ' ' . ($invData['lname'] ?? ''));
            }

            $matchData = [
                'tournament_id' => $tournamentId,
                'sport_id'      => $sportId,
                'match_name'    => $matchName,
                'match_date'    => $matchDate,
                'winner_id'     => $winnerId ?: null,
                'winner_name'   => $winnerName ?: null,
                'winner_type'   => $winnerType,
                'winner_invitational_id' => $winnerInvId,
                'result_status' => $resultStatus,
                'is_published'  => 0,
                'submitted_by'  => 'CAPTAIN'
            ];

            $sportModel = new Sport();
            $matchId    = $sportModel->addMatchResult($matchData, $details);

            if ($matchId) {
                echo json_encode([
                    'status'   => 'success',
                    'message'  => 'Match result submitted successfully!',
                    'match_id' => $matchId,
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save match result. Please check the sport configuration.']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper to ensure team exists without full HTTP request
     */
    private function internalEnsureTeam($name) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT team_id FROM playing_teams WHERE team_name = :name");
        $stmt->execute(['name' => trim($name)]);
        if (!$stmt->fetch()) {
            $stmt = $db->prepare("INSERT INTO playing_teams (team_name, created_by) VALUES (:name, :by)");
            $stmt->execute(['name' => trim($name), 'by' => $_SESSION['user_id']]);
        }
    }

    /**
     * Helper to ensure invitational player exists
     */
    private function internalEnsureInvitationalPlayer($data) {
        $db = Database::getConnection();
        $fname = trim($data['fname'] ?? '');
        $lname = trim($data['lname'] ?? '');
        $uni = trim($data['university'] ?? '');
        
        $stmt = $db->prepare("SELECT inv_player_id FROM invitational_players WHERE fname = :f AND lname = :l AND university = :u");
        $stmt->execute(['f' => $fname, 'l' => $lname, 'u' => $uni]);
        $existing = $stmt->fetch();
        
        if ($existing) return $existing['inv_player_id'];
        
        $stmt = $db->prepare("INSERT INTO invitational_players (fname, lname, university, student_id) VALUES (:f, :l, :u, :s)");
        $stmt->execute(['f' => $fname, 'l' => $lname, 'u' => $uni, 's' => trim($data['student_id'] ?? '')]);
        return $db->lastInsertId();
    }
}
