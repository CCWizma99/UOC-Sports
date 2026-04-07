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
        // Get the current user's faculty (if they have one)
        $userFacultyId = $_SESSION['user_faculty_id'] ?? null;
        
        // Allow faculty_id override via URL parameter (for ADMIN users to view different faculties)
        $selectedFacultyId = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : $userFacultyId;
        
        // Restrict non-admin users from viewing other faculties
        if ($_SESSION['user_type'] !== 'ADMIN' && $selectedFacultyId && $selectedFacultyId !== $userFacultyId) {
            $selectedFacultyId = $userFacultyId;
        }
        
        // Get all faculties for the selector dropdown
        $faculties = $this->facultyModel->getAllFaculties();
        
        // Store selected faculty in session for API calls
        $_SESSION['dashboard_faculty_id'] = $selectedFacultyId;
        
        view('admin/executive-dashboard', [
            'title' => 'Executive Dashboard',
            'selectedFacultyId' => $selectedFacultyId,
            'faculties' => $faculties,
            'userIsAdmin' => $_SESSION['user_type'] === 'ADMIN'
        ]);
    }

    /**
     * Export dashboard to CSV format
     */
    public function exportCsv() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dashboard-export-' . date('Y-m-d-His') . '.csv"');
        
        // Get dashboard data from API
        $dashboardData = $this->getDashboardData();
        
        // Get faculty name if scoped
        $facultyName = null;
        $facultyId = $_GET['faculty_id'] ?? null;
        if ($facultyId) {
            $faculties = $this->facultyModel->getAllFaculties();
            foreach ($faculties as $f) {
                if ($f['faculty_id'] === $facultyId) {
                    $facultyName = $f['faculty_name'];
                    break;
                }
            }
        }
        
        // Generate CSV
        $csv = ReportService::exportToCsv($dashboardData, $facultyName);
        echo $csv;
        exit;
    }

    /**
     * Export dashboard to PDF (returns HTML viewable/printable as PDF)
     */
    public function exportPdf() {
        // Get dashboard data from API
        $dashboardData = $this->getDashboardData();
        
        // Get faculty name if scoped
        $facultyName = null;
        $facultyId = $_GET['faculty_id'] ?? null;
        if ($facultyId) {
            $faculties = $this->facultyModel->getAllFaculties();
            foreach ($faculties as $f) {
                if ($f['faculty_id'] === $facultyId) {
                    $facultyName = $f['faculty_name'];
                    break;
                }
            }
        }
        
        // Generate PDF HTML
        $html = ReportService::exportToPdfHtml($dashboardData, $facultyName);
        
        // Set headers for PDF download using browser print-to-PDF
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="dashboard-export-' . date('Y-m-d-His') . '.html"');
        
        echo $html;
        exit;
    }

    /**
     * Fetch dashboard data from the API
     * Used by export functions to get current metrics
     */
    private function getDashboardData() {
        // Build API call internally
        $db = Database::getConnection();
        
        // Reuse DashboardApiController logic by instantiating it
        require_once '../app/controllers/api/DashboardApiController.php';
        $controller = new DashboardApiController();
        
        // Call the method via reflection to get dashboard data
        // Since we can't directly call it, we'll make an internal API call
        $facultyId = $_GET['faculty_id'] ?? null;
        
        // Simulate the DashboardApiController response
        $_GET['faculty_id'] = $facultyId;
        
        // Capture output from API call
        ob_start();
        $controller->getExecutiveSummary();
        $jsonOutput = ob_get_clean();
        
        $response = json_decode($jsonOutput, true);
        return $response['data'] ?? [];
    }

    /**
     * Sport Performance Drill-down View
     * Shows detailed KPIs for a specific sport with budget, events, achievements
     */
    public function sportPerformanceView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $sportId = $_GET['sport_id'] ?? null;
        $facultyId = $_GET['faculty_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-sport.php';
    }

    /**
     * Budget Trends Drill-down View
     * Shows budget analysis by sport with date range filtering
     */
    public function budgetTrendsView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $facultyId = $_GET['faculty_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-budget.php';
    }

    /**
     * Utilization Trends Drill-down View
     * Shows facility and equipment usage analytics
     */
    public function utilizationTrendsView() {
        // Check authorization
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'EXECUTIVE') {
            header('HTTP/1.0 403 Forbidden');
            echo "Unauthorized access";
            exit;
        }

        $facultyId = $_GET['faculty_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-01-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        include '../app/views/admin/executive-drill-down-utilization.php';
    }
}
