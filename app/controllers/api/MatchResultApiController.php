<?php
class MatchResultApiController {
    /**
     * Get all results for the admin panel
     */
    public function getAllAdminResults() {
        header('Content-Type: application/json');
        try {
            $model = new ResultModel();
            $results = $model->getAllResults();
            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Toggle the publish status of a single match
     */
    public function togglePublish() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $matchId = $input['match_id'] ?? '';
            $status = $input['status'] ?? 0;

            if (empty($matchId)) {
                echo json_encode(['status' => 'error', 'message' => 'Match ID is required.']);
                return;
            }

            $model = new ResultModel();
            $success = $model->togglePublish($matchId, $status);

            if ($success) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => $status == 1 ? 'Match published successfully.' : 'Match unpublished.'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error when toggling publish status.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Publish all pending results globally for a given tournament and revoke captain access
     */
    public function publishTournament() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $tournamentId = $input['tournament_id'] ?? '';

            if (empty($tournamentId)) {
                echo json_encode(['status' => 'error', 'message' => 'Tournament ID is required.']);
                return;
            }

            $model = new ResultModel();
            $success = $model->publishEntireTournament($tournamentId);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Tournament results successfully published. Captain access revoked.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to publish tournament results.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get published match results for the public page
     */
    public function getPublicResults() {
        header('Content-Type: application/json');
        try {
            $model = new ResultModel();
            $results = $model->getPublishedResults();
            echo json_encode(['status' => 'success', 'data' => $results]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get detailed info for a specific match
     */
    public function getMatchDetails($id = null) {
        header('Content-Type: application/json');
        try {
            if (!$id) {
                echo json_encode(['status' => 'error', 'message' => 'Match ID is missing.']);
                return;
            }

            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT sport_category FROM tournament_match WHERE match_id = :id');
            $stmt->execute(['id' => $id]);
            $matchInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$matchInfo) {
                echo json_encode(['status' => 'error', 'message' => 'Match not found.']);
                return;
            }

            require_once '../app/models/MatchResultFactory.php';
            $sportModel = MatchResultFactory::getModel($matchInfo['sport_category']);
            $matchDetails = $sportModel->getMatch($id);

            echo json_encode(['status' => 'success', 'data' => $matchDetails]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
