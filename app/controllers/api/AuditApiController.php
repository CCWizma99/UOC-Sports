<?php

require_once __DIR__ . '/../../models/SystemAudit.php';

class AuditApiController {

    public function getLogs() {
        // Start session if not existing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in and has admin rights (simplified)
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $auditModel = new SystemAudit();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [];
        if (!empty($_GET['table_name'])) {
            $filters['table_name'] = $_GET['table_name'];
        }
        if (!empty($_GET['action'])) {
            $filters['action'] = $_GET['action'];
        }
        if (!empty($_GET['date'])) {
            $filters['date'] = $_GET['date'];
        }
        
        $logs = $auditModel->getLogsWithDetails($limit, $offset, $filters);
        $totalLogs = $auditModel->getTotalLogsCount($filters);
        
        $totalPages = ceil($totalLogs / $limit);
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'logs' => $logs,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_logs' => $totalLogs,
                    'limit' => $limit
                ]
            ]
        ]);
    }
}
