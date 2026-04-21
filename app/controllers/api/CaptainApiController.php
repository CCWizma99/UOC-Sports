<?php

class CaptainApiController {

    public function getStudents() {
        header('Content-Type: application/json');
        try {
            $model = new Sport();
            $results = $model->getStudents();
            echo json_encode(['status'=>'success', 'data'=>$results]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

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

                // Validation: Ensure winner name matches one of the teams for TEAM matches
                if ($winnerType === 'TEAM' && !empty($winnerName)) {
                    $teamAName = trim($details['team_a_name'] ?? $details['fighter_a_name'] ?? '');
                    $teamBName = trim($details['team_b_name'] ?? $details['fighter_b_name'] ?? '');
                    
                    if (!empty($teamAName) && !empty($teamBName)) {
                        if (strcasecmp(trim($winnerName), $teamAName) !== 0 && strcasecmp(trim($winnerName), $teamBName) !== 0) {
                            echo json_encode([
                                'status' => 'error', 
                                'message' => "The winner ($winnerName) must be one of the competing teams ($teamAName or $teamBName)."
                            ]);
                            return;
                        }
                    }
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
                // Save match players if provided
                $teamAPlayers = $_POST['team_a_players'] ?? [];
                $teamBPlayers = $_POST['team_b_players'] ?? [];
                
                if (is_string($teamAPlayers)) $teamAPlayers = json_decode($teamAPlayers, true) ?: [];
                if (is_string($teamBPlayers)) $teamBPlayers = json_decode($teamBPlayers, true) ?: [];
                
                $allPlayers = [];
                foreach ($teamAPlayers as $p) {
                    $allPlayers[] = [
                        'user_id'        => $p['user_id'] ?? null,
                        'player_name'    => $p['player_name'] ?? '',
                        'external_id'    => $p['external_id'] ?? null,
                        'team_side'      => 'A',
                        'is_uoc_student' => isset($p['is_uoc_student']) ? (int)$p['is_uoc_student'] : 1
                    ];
                }
                foreach ($teamBPlayers as $p) {
                    $allPlayers[] = [
                        'user_id'        => $p['user_id'] ?? null,
                        'player_name'    => $p['player_name'] ?? '',
                        'external_id'    => $p['external_id'] ?? null,
                        'team_side'      => 'B',
                        'is_uoc_student' => isset($p['is_uoc_student']) ? (int)$p['is_uoc_student'] : 0
                    ];
                }
                
                if (!empty($allPlayers)) {
                    $playerModel = new MatchPlayer();
                    $playerModel->addPlayers($matchId, $allPlayers);
                    
                    // Auto-award points to UOC students
                    $this->autoAwardPoints($matchId, $tournamentId, $sportId, $resultStatus, $winnerType, $winnerName);
                }

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
     * Get a team roster (Squad for UOC, last match for others)
     */
    public function getTeamRoster() {
        header('Content-Type: application/json');
        try {
            $tournamentId = $_GET['tournament_id'] ?? '';
            $teamName     = trim($_GET['team_name'] ?? '');

            if (empty($tournamentId) || empty($teamName)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing tournament_id or team_name']);
                return;
            }

            $isUoc = (strcasecmp($teamName, 'University of Colombo') === 0 || strcasecmp($teamName, 'UOC') === 0);

            if ($isUoc) {
                $tpModel = new TournamentParticipant();
                $squad = $tpModel->getParticipants($tournamentId);
                
                if (empty($squad)) {
                    echo json_encode(['status' => 'empty', 'message' => 'You haven\'t added the UOC Team card for this.']);
                    return;
                }

                $roster = array_map(function($p) {
                    return [
                        'user_id'        => $p['user_id'],
                        'player_name'    => $p['first_name'] . ' ' . $p['last_name'],
                        'is_uoc_student' => 1,
                        'external_id'    => null
                    ];
                }, $squad);

                echo json_encode(['status' => 'success', 'source' => 'SQUAD', 'data' => $roster]);
            } else {
                // Find latest match for this team in this tournament
                $db = Database::getConnection();
                $matchId = null;
                $side = null;

                // Search through common team sport tables
                $tables = [
                    'match_team_goal', 'match_ball_court', 'match_cricket', 'match_racket'
                ];

                foreach ($tables as $table) {
                    $stmt = $db->prepare("
                        SELECT m.match_id, 
                               CASE WHEN m.team_a_name = :n1 THEN 'A' ELSE 'B' END as side
                        FROM $table m
                        JOIN tournament_match tm ON m.match_id = tm.match_id
                        WHERE tm.tournament_id = :t1 AND (m.team_a_name = :n2 OR m.team_b_name = :n3)
                        ORDER BY tm.created_at DESC LIMIT 1
                    ");
                    $stmt->execute(['n1' => $teamName, 't1' => $tournamentId, 'n2' => $teamName, 'n3' => $teamName]);
                    if ($res = $stmt->fetch()) {
                        $matchId = $res['match_id'];
                        $side = $res['side'];
                        break;
                    }
                }

                if ($matchId) {
                    $playerModel = new MatchPlayer();
                    $players = $playerModel->getPlayersByMatch($matchId);
                    
                    // Filter by side
                    $roster = array_values(array_filter($players, function($p) use ($side) {
                        return $p['team_side'] === $side;
                    }));

                    echo json_encode(['status' => 'success', 'source' => 'PREVIOUS_MATCH', 'data' => $roster]);
                } else {
                    echo json_encode(['status' => 'empty', 'message' => 'No previous roster found for this team in this tournament.']);
                }
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Auto-award participation and win points to UOC students
     */
    private function autoAwardPoints($matchId, $tournamentId, $sportId, $resultStatus, $winnerType, $winnerName) {
        try {
            $playerModel = new MatchPlayer();
            $uocPlayers = $playerModel->getUocPlayersByMatch($matchId);
            
            if (empty($uocPlayers)) return;
            
            $db = Database::getConnection();

            // Determine winning side or student
            $winningSide = null;
            $winningStudentId = null;

            if ($resultStatus === 'COMPLETED') {
                if ($winnerType === 'TEAM' && !empty($winnerName)) {
                    // Compare winner_name with team names from match details
                    $teamAName = $_POST['details']['team_a_name'] ?? $_POST['details']['fighter_a_name'] ?? '';
                    $teamBName = $_POST['details']['team_b_name'] ?? $_POST['details']['fighter_b_name'] ?? '';
                    
                    if (strcasecmp(trim($winnerName), trim($teamAName)) === 0) {
                        $winningSide = 'A';
                    } elseif (strcasecmp(trim($winnerName), trim($teamBName)) === 0) {
                        $winningSide = 'B';
                    }
                } elseif ($winnerType === 'INTERNAL') {
                    // Specific student was selected as winner
                    $winningStudentId = $_POST['winner_id'] ?? null;
                }
            }
            
            // Check if user_points table exists
            $db->exec("CREATE TABLE IF NOT EXISTS `user_points` (
                `user_id` varchar(12) NOT NULL,
                `user_points` int NOT NULL DEFAULT 0,
                PRIMARY KEY (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            foreach ($uocPlayers as $player) {
                $userId = $player['user_id'];
                $side   = $player['team_side'];
                
                // Determine if they are a winner
                $isWinner = ($winningSide !== null && $side === $winningSide) || ($winningStudentId !== null && $userId === $winningStudentId);
                
                if ($isWinner) {
                    // 1. Award win points (2 pts)
                    $stmt = $db->prepare("INSERT INTO achievement (user_id, sport_id, tournament_id, match_id, achievement, points, status)
                                          VALUES (?, ?, ?, ?, 'Match Winner', 2, 'ACTIVE')");
                    $stmt->execute([$userId, $sportId, $tournamentId, $matchId]);
                } else {
                    // 2. Award participation point (1 pt)
                    $stmt = $db->prepare("INSERT INTO achievement (user_id, sport_id, tournament_id, match_id, achievement, points, status)
                                          VALUES (?, ?, ?, ?, 'Participant', 1, 'ACTIVE')");
                    $stmt->execute([$userId, $sportId, $tournamentId, $matchId]);
                }
            }
        } catch (Exception $e) {
            error_log("Auto-award points error: " . $e->getMessage());
        }
    }

    /**
     * Submit overall tournament awards (sport-specific titles)
     */
    public function submitOverallAwards() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
            return;
        }

        try {
            $captainId    = $_SESSION['user_id'];
            $input        = json_decode(file_get_contents('php://input'), true);
            $tournamentId = $input['tournament_id'] ?? '';
            $sportId      = $input['sport_id'] ?? '';
            $awards       = $input['awards'] ?? [];

            if (empty($tournamentId) || empty($sportId) || empty($awards)) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament, sport, and at least one award are required.']);
                return;
            }

            // Verify permission
            $permModel = new EventResultPermission();
            if (!$permModel->hasPermission($captainId, $tournamentId)) {
                echo json_encode(['status' => 'error', 'message' => 'You do not have permission for this tournament.']);
                return;
            }

            $awardModel = new TournamentAward();
            $results = [];
            $errors  = [];

            foreach ($awards as $award) {
                $userId     = $award['user_id'] ?? '';
                $awardTitle = $award['award_title'] ?? '';

                if (empty($userId) || empty($awardTitle)) continue;

                $result = $awardModel->addAward([
                    'tournament_id' => $tournamentId,
                    'sport_id'      => $sportId,
                    'user_id'       => $userId,
                    'award_title'   => $awardTitle,
                    'awarded_by'    => $captainId
                ]);

                if ($result === 'duplicate') {
                    $errors[] = "$awardTitle has already been awarded to this student.";
                } elseif ($result === false) {
                    $errors[] = "Failed to add $awardTitle.";
                } else {
                    $results[] = $awardTitle;
                }
            }

            if (!empty($results)) {
                $msg = count($results) . ' award(s) submitted successfully.';
                if (!empty($errors)) $msg .= ' ' . implode(' ', $errors);
                echo json_encode(['status' => 'success', 'message' => $msg]);
            } else {
                echo json_encode(['status' => 'error', 'message' => implode(' ', $errors) ?: 'No awards were submitted.']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get previously used team rosters for a tournament
     */
    public function getMatchTeamsForTournament() {
        header('Content-Type: application/json');
        try {
            $tournamentId = $_GET['tournament_id'] ?? '';
            if (empty($tournamentId)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing tournament_id']);
                return;
            }

            $playerModel = new MatchPlayer();
            $teams = $playerModel->getDistinctTeams($tournamentId);

            echo json_encode(['status' => 'success', 'data' => $teams]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get available award titles for a sport category
     */
    public function getAwardTitlesForSport() {
        header('Content-Type: application/json');
        try {
            $sportCategory = $_GET['sport_category'] ?? '';
            if (empty($sportCategory)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing sport_category']);
                return;
            }
            $titles = TournamentAward::getTitlesForCategory($sportCategory);
            echo json_encode(['status' => 'success', 'data' => $titles]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get student achievements and points for profile display
     */
    public function getStudentAchievements() {
        header('Content-Type: application/json');
        try {
            $userId = $_GET['user_id'] ?? ($_SESSION['user_id'] ?? '');
            if (empty($userId)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
                return;
            }
            $achModel = new SportAchievements();
            $profile = $achModel->getFullStudentProfile($userId);
            echo json_encode(['status' => 'success', 'data' => $profile]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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

