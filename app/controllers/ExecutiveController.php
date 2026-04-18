<?php

require_once '../app/models/Faculty.php';
require_once '../app/services/ReportService.php';

class ExecutiveController {
    private $facultyModel;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Only ADMIN and EXECUTIVE users
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'] ?? '', ['ADMIN', 'EXECUTIVE'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
        $this->facultyModel = new Faculty();
    }

    public function index() {
        view('admin/executive-dashboard', [
            'title' => 'Executive Dashboard',
            'userIsAdmin' => $_SESSION['user_type'] === 'ADMIN'
        ]);
    }

    public function exportCsv() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dashboard-export-' . date('Y-m-d-His') . '.csv"');
        
        // Get dashboard data from API
        $dashboardData = $this->getDashboardData();
        
        // Generate CSV
        $csv = ReportService::exportToCsv($dashboardData, null);
        echo $csv;
        exit;
    }

    public function exportPdf() {
        // Get dashboard data from API
        $dashboardData = $this->getDashboardData();
        
        // Generate PDF HTML
        $html = ReportService::exportToPdfHtml($dashboardData, null);
        
        // Set headers for PDF download using browser print-to-PDF
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="dashboard-export-' . date('Y-m-d-His') . '.html"');
        
        echo $html;
        exit;
    }

    private function getDashboardData() {
        // Build API call internally
        $db = Database::getConnection();
        
        // Reuse DashboardApiController logic by instantiating it
        require_once '../app/controllers/api/DashboardApiController.php';
        $controller = new DashboardApiController();
        
        // Capture output from API call
        ob_start();
        $controller->getExecutiveSummary();
        $jsonOutput = ob_get_clean();
        
        $response = json_decode($jsonOutput, true);
        return $response['data'] ?? [];
    }

    public function sportPerformanceView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $sportId = $_GET['sport_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-sport.php';
    }

    public function budgetTrendsView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-budget.php';
    }

    public function utilizationTrendsView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-utilization.php';
    }
}
