<?php
class PlayingTeamApiController {
    /**
     * Search for existing teams by name
     */
    public function search() {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            if (strlen($query) < 2) {
                echo json_encode(['status' => 'success', 'data' => []]);
                return;
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT team_id, team_name FROM playing_teams WHERE team_name LIKE :q LIMIT 10");
            $stmt->execute(['q' => "%$query%"]);
            $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $teams]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Ensure a team exists in the registry
     */
    public function ensure() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $teamName = trim($input['team_name'] ?? '');
            $userId = $_SESSION['user_id'] ?? null;

            if (empty($teamName)) {
                echo json_encode(['status' => 'error', 'message' => 'Team name is required.']);
                return;
            }

            $db = Database::getConnection();
            
            // Check if exists
            $stmt = $db->prepare("SELECT team_id FROM playing_teams WHERE team_name = :name");
            $stmt->execute(['name' => $teamName]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                echo json_encode(['status' => 'success', 'data' => ['team_id' => $existing['team_id'], 'is_new' => false]]);
                return;
            }

            // Create new
            $stmt = $db->prepare("INSERT INTO playing_teams (team_name, created_by) VALUES (:name, :created_by)");
            $stmt->execute(['name' => $teamName, 'created_by' => $userId]);
            $newId = $db->lastInsertId();

            echo json_encode(['status' => 'success', 'data' => ['team_id' => $newId, 'is_new' => true]]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get all teams for the admin panel
     */
    public function getAll() {
        header('Content-Type: application/json');
        try {
            $db = Database::getConnection();
            $stmt = $db->query("
                SELECT pt.team_id, pt.team_name, pt.created_at, u.fname, u.lname 
                FROM playing_teams pt
                LEFT JOIN user u ON pt.created_by = u.user_id
                ORDER BY pt.team_name ASC
            ");
            $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $teams]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a team name
     */
    public function update() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $teamId = $input['team_id'] ?? '';
            $newName = trim($input['team_name'] ?? '');

            if (empty($teamId) || empty($newName)) {
                echo json_encode(['status' => 'error', 'message' => 'Team ID and Name are required.']);
                return;
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE playing_teams SET team_name = :name WHERE team_id = :id");
            $stmt->execute(['name' => $newName, 'id' => $teamId]);

            echo json_encode(['status' => 'success', 'message' => 'Team updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a team
     */
    public function delete() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $teamId = $input['team_id'] ?? '';

            if (empty($teamId)) {
                echo json_encode(['status' => 'error', 'message' => 'Team ID is required.']);
                return;
            }

            $db = Database::getConnection();
            // Check for match references
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM match_ball_court WHERE team_a_name = (SELECT team_name FROM playing_teams WHERE team_id = :id) OR team_b_name = (SELECT team_name FROM playing_teams WHERE team_id = :id)");
            $stmt->execute(['id' => $teamId]);
            $res = $stmt->fetch();
            if ($res['cnt'] > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Cannot delete team: It is being used in existing matches.']);
                return;
            }

            $stmt = $db->prepare("DELETE FROM playing_teams WHERE team_id = :id");
            $stmt->execute(['id' => $teamId]);

            echo json_encode(['status' => 'success', 'message' => 'Team deleted successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
