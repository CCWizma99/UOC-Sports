<?php

require_once __DIR__ . '/../../models/InjuryReport.php';
require_once __DIR__ . '/../../models/Schedule.php';

class InjuryApiController {
    
    public function getPastSessions() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['sport_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Sport ID required']);
            return;
        }

        $sportId = $_GET['sport_id'];
        $scheduleModel = new Schedule();
        $sessions = $scheduleModel->getPastSessions($sportId, 5);

        echo json_encode(['status' => 'success', 'sessions' => $sessions]);
    }

    public function getReports($sportId) {
        header('Content-Type: application/json');
        
        $injuryModel = new InjuryReport();
        $reports = $injuryModel->getReportsBySport($sportId);

        echo json_encode(['status' => 'success', 'data' => $reports]);
    }

    public function createReport() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['user_id']) || !isset($data['practice_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            return;
        }

        // Add coach ID from session if available
        if (isset($_SESSION['user_id'])) {
            $data['coach_id'] = $_SESSION['user_id'];
        }

        $injuryModel = new InjuryReport();
        $result = $injuryModel->saveReport($data);

        echo json_encode($result);
    }

    public function deleteReport() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['report_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing report ID']);
            return;
        }

        $injuryModel = new InjuryReport();
        if ($injuryModel->deleteReport($data['report_id'])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete report']);
        }
    }

    public function updateReport() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['report_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing report ID']);
            return;
        }

        $injuryModel = new InjuryReport();
        if ($injuryModel->updateReport($data['report_id'], $data)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update report']);
        }
    }
}
