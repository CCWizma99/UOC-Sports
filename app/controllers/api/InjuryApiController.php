<?php
// app/controllers/api/InjuryApiController.php
require_once __DIR__ . '/../../models/InjuryReport.php';
require_once __DIR__ . '/../../models/Schedule.php';

class InjuryApiController {
    private $injuryModel;
    private $scheduleModel;

    public function __construct() {
        $this->injuryModel = new InjuryReport();
        $this->scheduleModel = new Schedule();
    }

    /**
     * POST /api/injury/report
     * Body: JSON { user_id, practice_id, date, description, need_substitude, substitude_id }
     */
    public function reportInjury() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['user_id']) || !isset($input['practice_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields: user_id or practice_id']);
                return;
            }

            // Prefer session coach id if available
            session_start();
            if (!isset($input['coach_id']) && isset($_SESSION['user_id'])) {
                $input['coach_id'] = $_SESSION['user_id'];
            }

            $result = $this->injuryModel->saveReport($input);

            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/injury/upcoming-sessions
     * Returns upcoming practice sessions (no sport filter)
     */
    public function getUpcomingSessions() {
        header('Content-Type: application/json');
        try {
            // Allow optional sport filter via GET or session
            session_start();
            $sportId = $_GET['sport_id'] ?? $_SESSION['coach_sport_id'] ?? null;

            if ($sportId) {
                $sessions = $this->scheduleModel->getSessionsBySport($sportId);
            } else {
                $sessions = $this->scheduleModel->getUpcomingSessions(null, 50);
            }

            echo json_encode(['status' => 'success', 'sessions' => $sessions]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/injury/reports/{sport_id}
     */
    public function getReportsBySport($sportId) {
        header('Content-Type: application/json');
        try {
            $reports = $this->injuryModel->getReportsBySport($sportId);
            echo json_encode(['status' => 'success', 'data' => $reports]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
