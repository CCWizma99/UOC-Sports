<?php

require_once '../app/models/User.php';
require_once '../app/models/Facility.php';
require_once '../app/models/Equipment.php';
require_once '../app/models/Tournament.php';
require_once '../app/models/Budget.php';
require_once '../app/models/Post.php';
require_once '../app/models/Inquiry.php';
require_once '../app/models/SportAchievements.php';

class DashboardApiController {
    private $userModel;
    private $facilityModel;
    private $equipmentModel;
    private $tournamentModel;
    private $budgetModel;
    private $postModel;
    private $inquiryModel;
    private $achievementsModel;
    
    // Context variables for date filtering
    private $yearFilter = null;
    private $facultySportsCache = [];

    public function __construct() {
        $this->userModel = new User();
        $this->facilityModel = new Facility();
        $this->equipmentModel = new Equipment();
        $this->tournamentModel = new Tournament();
        $this->budgetModel = new Budget();
        $this->postModel = new Post();
        $this->inquiryModel = new Inquiry();
        $this->achievementsModel = new SportAchievements();
    }

    /**
     * Get executive summary data for top management dashboard
     */
    public function getExecutiveSummary() {
        header('Content-Type: application/json');

        try {
            $this->yearFilter = $_GET['year'] ?? date('Y');
            
            $counts = $this->getReservationStats();
            $summary = [
                'users' => $this->getUserStats(),
                'reservations' => [
                    'total' => $counts['total'],
                    'range_total' => $counts['range_total'],
                    'avg_utilization' => $counts['avg_utilization']
                ],
                'equipment' => $this->getEquipmentStats(),
                'events' => $this->getEventStats(),
                'budget' => $this->getBudgetStats(),
                'insights' => $this->getAdditionalInsights(),
                'community' => $this->getCommunityStats(),
                'achievements' => $this->getAchievementStats(),
                'facility_analytics' => $this->getFacilityAnalytics(),
                'equipment_analytics' => $this->getEquipmentDetailedAnalytics()
            ];

            echo json_encode([
                'status' => 'success',
                'data' => $summary
            ]);
        } catch (Exception $e) {
            error_log("Executive summary error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to load dashboard data: ' . $e->getMessage()
            ]);
        }
    }

    // ══════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════

    private function getPeriodFilter($column, $defaultToCurrentYear = false) {
        $year = $this->yearFilter;

        // If 'all' is selected, we want NO year filter.
        if ($year === 'all') {
            return "";
        }

        // Only default to current year if the input is completely empty or missing.
        if (empty($year)) {
            if ($defaultToCurrentYear) {
                $year = date('Y');
            } else {
                return "";
            }
        }

        return " AND YEAR({$column}) = '{$year}'";
    }

    // ══════════════════════════════════════════
    // CORE ANALYTICS METHODS
    // ══════════════════════════════════════════

    private function getUserStats() {
        $db = Database::getConnection();
        $totalUsers = $this->userModel->getTotalUsersCount();
        
        $typeSQL = "SELECT type, COUNT(*) as count FROM user WHERE status = 'ACTIVE' GROUP BY type";
        $typeDistribution = $db->query($typeSQL)->fetchAll(PDO::FETCH_ASSOC);
        
        $periodFilter = $this->getPeriodFilter('joined_date', true);
        $newSQL = "SELECT COUNT(*) as count FROM user WHERE status = 'ACTIVE'" . $periodFilter;
        $newInRange = $db->query($newSQL)->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        return [
            'total' => $totalUsers,
            'new_this_month' => $newInRange,
            'type_distribution' => $typeDistribution
        ];
    }

    private function getReservationStats() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('date', true);
        
        $total = $db->query("SELECT COUNT(*) FROM `facility-booking` WHERE 1=1" . $periodFilter)->fetchColumn();
        
        $days = ($this->yearFilter === 'all') ? 365 * 2 : 365; // Approximate for historical, or 365 for year
        $yearCondition = ($this->yearFilter && $this->yearFilter !== 'all') ? "AND YEAR(fb.date) = '{$this->yearFilter}'" : "";
        
        $utilSQL = "SELECT ROUND(AVG(booking_count), 1) as avg_utilization FROM (
                        SELECT fr.id, (COUNT(fb.booking_id) / {$days} * 100) as booking_count
                        FROM facility_rates fr
                        LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id {$yearCondition}
                        GROUP BY fr.id
                    ) as util_data";
        $avgUtilization = $db->query($utilSQL)->fetchColumn();

        return [
            'total' => (int)$total,
            'range_total' => (int)$total,
            'avg_utilization' => round((float)$avgUtilization, 1)
        ];
    }

    private function getEquipmentStats() {
        $db = Database::getConnection();
        $totals = $db->query("SELECT COUNT(DISTINCT equipment_id) as total, SUM(quantity) as total_quantity FROM equipment_inventory")->fetch(PDO::FETCH_ASSOC);
        $lowStock = $db->query("SELECT COUNT(*) FROM equipment_inventory WHERE quantity < 10")->fetchColumn();

        return [
            'total_types' => $totals['total'] ?? 0,
            'total_quantity' => $totals['total_quantity'] ?? 0,
            'needs_attention' => $lowStock
        ];
    }

    private function getEventStats() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('start_date', true);

        try {
            $active = $db->query("SELECT COUNT(*) FROM tournament WHERE status = 'ONGOING'" . $periodFilter)->fetchColumn();
            $total = $db->query("SELECT COUNT(*) FROM tournament WHERE 1=1" . $periodFilter)->fetchColumn();
            $completed = $db->query("SELECT COUNT(*) FROM tournament WHERE status = 'COMPLETE'" . $periodFilter)->fetchColumn();
            $upcoming = $db->query("SELECT COUNT(*) FROM tournament WHERE status = 'UPCOMING'" . $periodFilter)->fetchColumn();
        } catch (Exception $e) {
            $active = $total = $completed = $upcoming = 0;
        }

        return [
            'total' => (int)$total, 'active' => (int)$active, 'completed_this_year' => (int)$completed, 'upcoming' => (int)$upcoming
        ];
    }

    private function getBudgetStats() {
        $year = ($this->yearFilter && $this->yearFilter !== 'all') ? $this->yearFilter : date('Y');
        $summary = $this->budgetModel->getBudgetSummary($year);
        
        $allocated = floatval($summary['total_allocated'] ?? 0);
        $spent = floatval($summary['total_spent'] ?? 0);
        
        return [
            'allocated' => $allocated,
            'spent' => $spent,
            'remaining' => $allocated - $spent,
            'percent_used' => $allocated > 0 ? round(($spent / $allocated) * 100, 1) : 0,
            'year' => $year
        ];
    }

    private function getAdditionalInsights() {
        return [
            'budget_efficiency' => $this->getBudgetEfficiency(),
            'facility_demand' => $this->getFacilityDemand(),
            'athlete_engagement' => $this->getAthleteEngagement(),
            'action_required' => $this->getActionRequired()
        ];
    }

    private function getBudgetEfficiency() {
        $db = Database::getConnection();
        $year = ($this->yearFilter && $this->yearFilter !== 'all') ? $this->yearFilter : date('Y');
        
        $sql = "SELECT s.sport_name, b.allocated_amount, b.spent_amount, ROUND((b.spent_amount / b.allocated_amount) * 100, 1) as utilization
                FROM budget b JOIN sport s ON b.sport_id = s.sport_id
                WHERE b.year = {$year} AND b.allocated_amount > 0";
        $sports = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $overspend = 0; $underspend = 0; $onTrack = 0;
        foreach ($sports as $s) {
            if ($s['utilization'] > 80) $overspend++;
            elseif ($s['utilization'] < 30) $underspend++;
            else $onTrack++;
        }
        
        return [
            'sports' => array_slice($sports, 0, 5),
            'summary' => ['overspend_risk' => $overspend, 'underspend' => $underspend, 'on_track' => $onTrack, 'total' => count($sports)]
        ];
    }

    private function getFacilityDemand() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('fb.date', true);
        
        $sql = "SELECT fr.facility_name, COUNT(*) as total_bookings,
                SUM(CASE WHEN fb.status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted,
                ROUND(SUM(CASE WHEN fb.status = 'ACCEPTED' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as approval_rate
                FROM `facility-booking` fb JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE 1=1" . $periodFilter . " GROUP BY fr.id ORDER BY total_bookings DESC LIMIT 5";
        $top = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        $overall = $db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted FROM `facility-booking` fb WHERE 1=1" . $periodFilter)->fetch(PDO::FETCH_ASSOC);
        
        return [
            'top_facilities' => $top,
            'overall' => ['approval_rate' => $overall['total'] > 0 ? round(($overall['accepted'] / $overall['total']) * 100, 1) : 0, 'total' => (int)($overall['total'] ?? 0)]
        ];
    }

    private function getAthleteEngagement() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('u.joined_date', true);
        
        $totalAthletes = $db->query("SELECT COUNT(DISTINCT st.student_id) FROM `sports-team` st JOIN user u ON st.student_id = u.user_id WHERE 1=1" . $periodFilter)->fetchColumn();
        $totalStudents = $db->query("SELECT COUNT(*) FROM user u WHERE u.type = 'STUDENT' AND u.status = 'ACTIVE'" . $periodFilter)->fetchColumn();
        
        $multiSportAthletes = $db->query("SELECT COUNT(*) FROM (SELECT st.student_id FROM `sports-team` st JOIN user u ON st.student_id = u.user_id WHERE 1=1" . $periodFilter . " GROUP BY st.student_id HAVING COUNT(DISTINCT st.sport_id) > 1) as multi")->fetchColumn();

        return [
            'total_athletes' => $totalAthletes,
            'total_students' => $totalStudents,
            'participation_rate' => $totalStudents > 0 ? round(($totalAthletes / $totalStudents) * 100, 1) : 0,
            'active_sports' => $db->query("SELECT COUNT(DISTINCT st.sport_id) FROM `sports-team` st JOIN user u ON st.student_id = u.user_id WHERE 1=1" . $periodFilter)->fetchColumn(),
            'multi_sport_athletes' => $multiSportAthletes
        ];
    }

    private function getActionRequired() {
        $db = Database::getConnection();
        $unresolvedInq = $db->query("SELECT COUNT(*) FROM inquiry WHERE status = 'NOT-RESOLVED'")->fetchColumn();
        $lowStock = $db->query("SELECT COUNT(*) FROM equipment_inventory WHERE quantity < 5")->fetchColumn();
        return [
            'unresolved_inquiries' => $unresolvedInq,
            'low_stock_items' => $lowStock,
            'total_actions' => $unresolvedInq + $lowStock
        ];
    }

    private function getCommunityStats() {
        $db = Database::getConnection();
        // Comment table does not have a date column, so we show the most recent 5 in general
        $recentComments = $db->query("SELECT c.content, CONCAT(u.fname, ' ', u.lname) as user_name FROM comment c JOIN user u ON c.comment_from = u.user_id ORDER BY c.comment_id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $inquiryStats = $db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'NOT-RESOLVED' THEN 1 ELSE 0 END) as unresolved FROM inquiry WHERE 1=1" . $this->getPeriodFilter('date', true))->fetch(PDO::FETCH_ASSOC);
        return ['recent_comments' => $recentComments, 'inquiry_stats' => $inquiryStats];
    }

    private function getAchievementStats() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('c.date', true);
        
        try {
            $total = $db->query("SELECT COUNT(*) FROM achievement a JOIN user u ON a.user_id = u.user_id JOIN competition c ON a.competition_id = c.competition_id WHERE 1=1" . $periodFilter)->fetchColumn();
            $recent = $db->query("SELECT a.achievement as achievement_type, a.points, CONCAT(u.fname, ' ', u.lname) as student_name FROM achievement a JOIN user u ON a.user_id = u.user_id JOIN competition c ON a.competition_id = c.competition_id WHERE 1=1" . $periodFilter . " ORDER BY c.date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Achievement stats error: " . $e->getMessage());
            $total = 0; $recent = [];
        }
        return ['total' => (int)$total, 'recent' => $recent];
    }

    private function getFacilityAnalytics() {
        $db = Database::getConnection();
        $year = ($this->yearFilter && $this->yearFilter !== 'all') ? $this->yearFilter : null;
        $yearFilter = $year ? " AND YEAR(fb.date) = '{$year}'" : "";
        $generalYearFilter = $year ? " AND YEAR(date) = '{$year}'" : "";
        
        $days = $year ? (date('L', strtotime($year . "-01-01")) ? 366 : 365) : 365; // Use 365 as default for all-time
        
        // 1. Facility utilization (DESC) with rate
        $utilizationSQL = "
            SELECT fr.facility_name, COUNT(fb.booking_id) as total_bookings,
                   ROUND(COUNT(fb.booking_id) * 100.0 / {$days}, 1) as utilization_rate
            FROM facility_rates fr 
            LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id {$yearFilter}
            GROUP BY fr.id 
            ORDER BY total_bookings DESC";
        $utilization = $db->query($utilizationSQL)->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Peak booking days
        $peakSQL = "
            SELECT DAYNAME(date) as day_name, COUNT(booking_id) as count
            FROM `facility-booking`
            WHERE 1=1 {$generalYearFilter}
            GROUP BY DAYNAME(date)
            ORDER BY FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
        $peakDays = $db->query($peakSQL)->fetchAll(PDO::FETCH_ASSOC);

        // 3. Bookings by user type
        $userTypeSQL = "
            SELECT u.type as user_type, COUNT(fb.booking_id) as count
            FROM `facility-booking` fb
            JOIN user u ON fb.user_id = u.user_id
            WHERE 1=1 {$yearFilter}
            GROUP BY u.type";
        $byUserType = $db->query($userTypeSQL)->fetchAll(PDO::FETCH_ASSOC);

        // 4. Monthly trend
        $trendSQL = "
            SELECT DATE_FORMAT(date, '%b %y') as month_label, COUNT(booking_id) as count
            FROM `facility-booking`
            WHERE 1=1 {$generalYearFilter}
            GROUP BY DATE_FORMAT(date, '%Y-%m')
            ORDER BY DATE_FORMAT(date, '%Y-%m')";
        $monthlyTrend = $db->query($trendSQL)->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'utilization' => $utilization,
            'peak_days' => $peakDays,
            'by_user_type' => $byUserType,
            'monthly_trend' => $monthlyTrend
        ];
    }

    private function getEquipmentDetailedAnalytics() {
        $db = Database::getConnection();
        $periodFilter = $this->getPeriodFilter('grn.date', true);
        
        // Equipment by sport (Stock level is usually real-time, but we can filter by when added)
        $bySportSQL = "SELECT s.sport_name, SUM(ei.quantity) as total_quantity FROM equipment_inventory ei JOIN sport s ON ei.sport_id = s.sport_id GROUP BY ei.sport_id ORDER BY total_quantity DESC";
        $bySport = $db->query($bySportSQL)->fetchAll(PDO::FETCH_ASSOC);
        
        // Low stock items
        $lowStock = $db->query("SELECT e.equipment_name, s.sport_name, ei.quantity FROM equipment_inventory ei JOIN equipment e ON ei.equipment_id = e.equipment_id JOIN sport s ON ei.sport_id = s.sport_id WHERE ei.quantity < 10 ORDER BY ei.quantity ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

        // Recent activity filtered by period
        $activitySQL = "SELECT grn.*, e.equipment_name, s.sport_name FROM `good_received_notes` grn JOIN equipment e ON grn.equipment_id = e.equipment_id JOIN sport s ON grn.sport_id = s.sport_id WHERE 1=1" . $periodFilter . " ORDER BY grn.date DESC LIMIT 10";
        $recentActivity = $db->query($activitySQL)->fetchAll(PDO::FETCH_ASSOC);

        return [
            'by_sport' => $bySport,
            'low_stock' => $lowStock,
            'recent_activity' => $recentActivity
        ];
    }
}
