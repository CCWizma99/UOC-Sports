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
    
    // Context variables for faculty/date filtering
    private $currentFacultyId = null;
    private $startDate = null;
    private $endDate = null;
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
     * Supports optional faculty filtering via $facultyId
     */
    public function getExecutiveSummary() {
        header('Content-Type: application/json');

        try {
            // Get optional faculty_id from request (for scoped dashboards)
            $facultyId = isset($_GET['faculty_id']) ? $_GET['faculty_id'] : null;
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            
            // Store for use in helper methods
            $this->currentFacultyId = $facultyId;
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            
            $summary = [];

            // 1. USER STATS
            $summary['users'] = $this->getUserStats();

            // 2. RESERVATION STATS
            $summary['reservations'] = $this->getReservationStats();

            // 3. EQUIPMENT STATS
            $summary['equipment'] = $this->getEquipmentStats();

            // 4. EVENTS STATS
            $summary['events'] = $this->getEventStats();

            // 5. BUDGET STATS
            $summary['budget'] = $this->getBudgetStats();

            // 6. ADDITIONAL INSIGHTS
            $summary['insights'] = $this->getAdditionalInsights();

            // 7. COMMUNITY STATS
            $summary['community'] = $this->getCommunityStats();

            // 8. ACHIEVEMENTS
            $summary['achievements'] = $this->getAchievementStats();

            // 9. FACILITY ANALYTICS (detailed)
            $summary['facility_analytics'] = $this->getFacilityAnalytics();

            // 10. EQUIPMENT ANALYTICS (detailed)
            $summary['equipment_analytics'] = $this->getEquipmentDetailedAnalytics();

            echo json_encode([
                'status' => 'success',
                'data' => $summary,
                'faculty_id' => $facultyId,
                'scoped' => $facultyId !== null
            ]);
        } catch (Exception $e) {
            error_log("Executive summary error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to load dashboard data'
            ]);
        }
    }

    /**
     * Helper: Get all sport IDs for a given faculty
     * Returns array of sport_ids, or empty if faculty not found
     */
    private function getFacultySportIds($facultyId) {
        if (!$facultyId) return [];
        
        $db = Database::getConnection();
        $sql = "SELECT sport_id FROM sport WHERE faculty_id = :faculty_id";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([':faculty_id' => $facultyId]);
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $results;
        } catch (Exception $e) {
            error_log("Error fetching sports for faculty $facultyId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Quote a value for SQL (simple wrapper for faculty_id)
     */
    private function quoteFacultyId($id) {
        return "'" . str_replace("'", "''", $id) . "'";
    }

    /**
     * Helper: Build WHERE clause for faculty filtering
     * If facultyId is set, restricts queries to that faculty's sports
     */
    private function getFacultyWhereClause($table = 's') {
        if (!$this->currentFacultyId) return '';
        
        $sportIds = $this->getFacultySportIds($this->currentFacultyId);
        if (empty($sportIds)) return "AND {$table}.sport_id IS NULL"; // No sports - return no results
        
        $placeholders = implode(',', array_map(function($id) { return "'" . str_replace("'", "''", $id) . "'"; }, $sportIds));
        return "AND {$table}.sport_id IN ({$placeholders})";
    }

    /**
     * Helper: Get sports for current faculty context
     */
    private function getCurrentFacultySports() {
        if (!isset($this->facultySportsCache)) {
            $this->facultySportsCache = $this->getFacultySportIds($this->currentFacultyId);
        }
        return $this->facultySportsCache;
    }

    private function getUserStats() {
        $db = Database::getConnection();
        
        // Total active users
        $totalUsers = $this->userModel->getTotalUsersCount();
        
        // User type distribution
        $typeSQL = "SELECT type, COUNT(*) as count FROM user WHERE status = 'ACTIVE' GROUP BY type";
        $stmt = $db->query($typeSQL);
        $typeDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // New registrations this month (using joined_date)
        $newSQL = "SELECT COUNT(*) as count FROM user WHERE MONTH(joined_date) = MONTH(CURDATE()) AND YEAR(joined_date) = YEAR(CURDATE())";
        $stmt = $db->query($newSQL);
        $newThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        return [
            'total' => $totalUsers,
            'new_this_month' => $newThisMonth,
            'type_distribution' => $typeDistribution
        ];
    }

    private function getReservationStats() {
        $db = Database::getConnection();
        
        // Total reservations
        $totalSQL = "SELECT COUNT(*) as total FROM `facility-booking`";
        $stmt = $db->query($totalSQL);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // This month's reservations
        $monthSQL = "SELECT COUNT(*) as count FROM `facility-booking` WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        $stmt = $db->query($monthSQL);
        $thisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Pending approvals
        $pendingSQL = "SELECT COUNT(*) as count FROM `facility-booking` WHERE status = 'BOOKED'";
        $stmt = $db->query($pendingSQL);
        $pending = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Calculate simple utilization (bookings per facility in last 30 days)
        $utilizationSQL = "
            SELECT ROUND(AVG(booking_count), 1) as avg_utilization FROM (
                SELECT 
                    fr.id,
                    (COUNT(fb.booking_id) / 30.0 * 100) as booking_count
                FROM facility_rates fr
                LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id 
                    AND fb.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY fr.id
            ) as util_data
        ";
        $stmt = $db->query($utilizationSQL);
        $avgUtilization = $stmt->fetch(PDO::FETCH_ASSOC)['avg_utilization'] ?? 0;

        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'pending' => $pending,
            'avg_utilization' => $avgUtilization ? round($avgUtilization, 1) : 0
        ];
    }

    private function getEquipmentStats() {
        $db = Database::getConnection();
        
        // Build query with optional faculty filtering
        $whereClause = '';
        if ($this->currentFacultyId) {
            $whereClause = " JOIN sport s ON ei.sport_id = s.sport_id WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId);
        }
        
        // Total equipment items from equipment_inventory table
        $totalSQL = "SELECT COUNT(DISTINCT ei.equipment_id) as total, SUM(ei.quantity) as total_quantity 
                    FROM equipment_inventory ei" . ($whereClause ? substr($whereClause, 0, 4) . " " : "") . $whereClause;
        
        try {
            $stmt = $db->query($totalSQL);
            $totals = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Equipment stats query error: " . $e->getMessage());
            $totals = ['total' => 0, 'total_quantity' => 0];
        }
        
        // Low stock items (less than 10 quantity)
        $lowStockSQL = "SELECT COUNT(*) as count FROM equipment_inventory ei" . 
                       ($whereClause ? " JOIN sport s ON ei.sport_id = s.sport_id" : "") .
                       " WHERE ei.quantity < 10" . 
                       ($this->currentFacultyId ? " AND s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "");
        
        try {
            $stmt = $db->query($lowStockSQL);
            $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $lowStock = 0;
        }

        return [
            'total_types' => $totals['total'] ?? 0,
            'total_quantity' => $totals['total_quantity'] ?? 0,
            'needs_attention' => $lowStock
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

    /**
     * Budget Efficiency - Shows utilization rates and overspend risks
     */
    private function getBudgetEfficiency() {
        $db = Database::getConnection();
        
        // Get budget utilization by sport
        $sql = "SELECT 
                    s.sport_name,
                    b.allocated_amount,
                    b.spent_amount,
                    ROUND((b.spent_amount / b.allocated_amount) * 100, 1) as utilization
                FROM budget b
                JOIN sport s ON b.sport_id = s.sport_id
                WHERE b.year = YEAR(CURDATE()) AND b.allocated_amount > 0";
        
        // Add faculty filtering if needed
        if ($this->currentFacultyId) {
            $sql .= " AND s.faculty_id = :faculty_id";
            try {
                $stmt = $db->prepare($sql);
                $stmt->execute([':faculty_id' => $this->currentFacultyId]);
                $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Budget efficiency query error: " . $e->getMessage());
                $sports = [];
            }
        } else {
            try {
                $stmt = $db->query($sql);
                $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Budget efficiency query error: " . $e->getMessage());
                $sports = [];
            }
        }
        
        // Calculate aggregates
        $overspendRisk = 0;  // >80% utilized
        $underspend = 0;     // <30% utilized
        $onTrack = 0;        // 30-80% utilized
        
        foreach ($sports as $s) {
            $util = floatval($s['utilization']);
            if ($util > 80) $overspendRisk++;
            elseif ($util < 30) $underspend++;
            else $onTrack++;
        }
        
        return [
            'sports' => array_slice($sports, 0, 5), // Top 5 by utilization
            'summary' => [
                'overspend_risk' => $overspendRisk,
                'underspend' => $underspend,
                'on_track' => $onTrack,
                'total' => count($sports)
            ]
        ];
    }

    /**
     * Facility Demand - Shows most popular facilities and approval rates
     */
    private function getFacilityDemand() {
        $db = Database::getConnection();
        
        // Top facilities by bookings with approval rate
        $sql = "SELECT 
                    fr.facility_name,
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN fb.status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN fb.status = 'REJECTED' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN fb.status = 'BOOKED' THEN 1 ELSE 0 END) as pending,
                    ROUND(SUM(CASE WHEN fb.status = 'ACCEPTED' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 1) as approval_rate
                FROM `facility-booking` fb
                JOIN facility_rates fr ON fb.facility_id = fr.id
                GROUP BY fb.facility_id, fr.facility_name
                ORDER BY total_bookings DESC
                LIMIT 5";
        $stmt = $db->query($sql);
        $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Overall stats
        $overallSQL = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted,
                        SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected
                       FROM `facility-booking`";
        $stmt = $db->query($overallSQL);
        $overall = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $approvalRate = $overall['total'] > 0 
            ? round(($overall['accepted'] / $overall['total']) * 100, 1) 
            : 0;
        $rejectionRate = $overall['total'] > 0 
            ? round(($overall['rejected'] / $overall['total']) * 100, 1) 
            : 0;
        
        return [
            'top_facilities' => $facilities,
            'overall' => [
                'approval_rate' => $approvalRate,
                'rejection_rate' => $rejectionRate,
                'total' => $overall['total'] ?? 0
            ]
        ];
    }

    /**
     * Athlete Engagement - Participation metrics
     */
    private function getAthleteEngagement() {
        $db = Database::getConnection();
        
        // Total unique athletes enrolled
        $athletesSQL = "SELECT COUNT(DISTINCT student_id) as total FROM `sports-team`";
        $stmt = $db->query($athletesSQL);
        $totalAthletes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Total students (for participation rate)
        $studentsSQL = "SELECT COUNT(*) as total FROM user WHERE type = 'STUDENT' AND status = 'ACTIVE'";
        $stmt = $db->query($studentsSQL);
        $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Participation rate
        $participationRate = $totalStudents > 0 
            ? round(($totalAthletes / $totalStudents) * 100, 1) 
            : 0;
        
        // Multi-sport athletes
        $multiSportSQL = "SELECT COUNT(*) as count FROM (
                            SELECT student_id FROM `sports-team` GROUP BY student_id HAVING COUNT(*) > 1
                          ) as multi";
        $stmt = $db->query($multiSportSQL);
        $multiSport = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Total sports with active members
        $activeSportsSQL = "SELECT COUNT(DISTINCT sport_id) as count FROM `sports-team`";
        $stmt = $db->query($activeSportsSQL);
        $activeSports = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        return [
            'total_athletes' => $totalAthletes,
            'total_students' => $totalStudents,
            'participation_rate' => $participationRate,
            'multi_sport_athletes' => $multiSport,
            'active_sports' => $activeSports
        ];
    }

    /**
     * Action Required - Items needing executive attention
     */
    private function getActionRequired() {
        $db = Database::getConnection();
        
        // Pending reservations (not faculty-scoped - facility bookings not linked to sports)
        $pendingSQL = "SELECT COUNT(*) as count FROM `facility-booking` WHERE status = 'BOOKED'";
        try {
            $stmt = $db->query($pendingSQL);
            $pendingReservations = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $pendingReservations = 0;
        }
        
        // Unresolved inquiries (not faculty-scoped)
        $inquirySQL = "SELECT COUNT(*) as count FROM inquiry WHERE status = 'NOT-RESOLVED'";
        try {
            $stmt = $db->query($inquirySQL);
            $unresolvedInquiries = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $unresolvedInquiries = 0;
        }
        
        // Upcoming events in next 7 days (faculty-scoped if needed)
        $eventsSQL = "SELECT COUNT(*) as count FROM tournament t" .
                    ($this->currentFacultyId ? " JOIN sport s ON t.sport_id = s.sport_id" : "") .
                    " WHERE t.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)" .
                    ($this->currentFacultyId ? " AND s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "");
        try {
            $stmt = $db->query($eventsSQL);
            $upcomingEvents = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $upcomingEvents = 0;
        }
        
        // Low stock equipment (faculty-scoped if needed)
        $equipSQL = "SELECT COUNT(*) as count FROM equipment_inventory ei" .
                   ($this->currentFacultyId ? " JOIN sport s ON ei.sport_id = s.sport_id" : "") .
                   " WHERE ei.quantity < 5" .
                   ($this->currentFacultyId ? " AND s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "");
        try {
            $stmt = $db->query($equipSQL);
            $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $lowStock = 0;
        }
        
        // Pending equipment requests (may not be faculty-scoped if not linked to sports)
        $eqRequestSQL = "SELECT COUNT(*) as count FROM `equipment-requests` WHERE status = 'ACTIVE'";
        try {
            $stmt = $db->query($eqRequestSQL);
            $pendingEqRequests = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $pendingEqRequests = 0;
        }
        
        return [
            'pending_reservations' => $pendingReservations,
            'unresolved_inquiries' => $unresolvedInquiries,
            'upcoming_events' => $upcomingEvents,
            'low_stock_items' => $lowStock,
            'pending_equipment_requests' => $pendingEqRequests,
            'total_actions' => $pendingReservations + $unresolvedInquiries + $lowStock + $pendingEqRequests
        ];
    }

    private function getEventStats() {
        $db = Database::getConnection();
        
        // Active events count
        $activeCount = $this->tournamentModel->getActiveEventsCount();
        
        // Adjust for faculty if needed (tournaments joined with sports)
        $whereClause = '';
        if ($this->currentFacultyId) {
            $whereClause = " JOIN sport s ON t.sport_id = s.sport_id WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId);
        }
        
        // Total tournaments
        $totalSQL = "SELECT COUNT(*) as total FROM tournament t" . $whereClause;
        try {
            $stmt = $db->query($totalSQL);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            $total = 0;
        }
        
        // Completed this year
        $completedSQL = "SELECT COUNT(*) as count FROM tournament t" .
                       ($this->currentFacultyId ? " JOIN sport s ON t.sport_id = s.sport_id" : "") .
                       " WHERE t.status = 'COMPLETE' AND YEAR(t.end_date) = YEAR(CURDATE())" .
                       ($this->currentFacultyId ? " AND s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "");
        try {
            $stmt = $db->query($completedSQL);
            $completedThisYear = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $completedThisYear = 0;
        }
        
        // Upcoming (next 30 days)
        $upcomingSQL = "SELECT COUNT(*) as count FROM tournament t" .
                      ($this->currentFacultyId ? " JOIN sport s ON t.sport_id = s.sport_id" : "") .
                      " WHERE t.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)" .
                      ($this->currentFacultyId ? " AND s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "");
        try {
            $stmt = $db->query($upcomingSQL);
            $upcoming = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $upcoming = 0;
        }

        return [
            'total' => $total,
            'active' => $activeCount,
            'completed_this_year' => $completedThisYear,
            'upcoming' => $upcoming
        ];
    }

    private function getBudgetStats() {
        // Get current year budget summary
        $summary = $this->budgetModel->getBudgetSummary();
        
        $allocated = floatval($summary['total_allocated'] ?? 0);
        $spent = floatval($summary['total_spent'] ?? 0);
        $remaining = floatval($summary['total_remaining'] ?? 0);
        
        // Calculate percentage used
        $percentUsed = $allocated > 0 ? round(($spent / $allocated) * 100, 1) : 0;

        return [
            'allocated' => $allocated,
            'spent' => $spent,
            'remaining' => $remaining,
            'percent_used' => $percentUsed,
            'year' => date('Y')
        ];
    }

    /**
     * Community Stats - Recent comments, inquiries, post engagement
     */
    private function getCommunityStats() {
        $db = Database::getConnection();
        
        // Recent comments (last 10)
        $commentsSQL = "SELECT c.comment_id, c.content, 
                               CONCAT(u.fname, ' ', u.lname) as user_name,
                               p.title as post_title
                        FROM comment c
                        JOIN user u ON c.comment_from = u.user_id
                        JOIN newsfeed_post p ON c.post_id = p.post_id
                        ORDER BY c.comment_id DESC
                        LIMIT 10";
        try {
            $stmt = $db->query($commentsSQL);
            $recentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recentComments = [];
        }
        
        // Post stats
        $postStatsSQL = "SELECT 
                            COUNT(*) as total_posts,
                            SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END) as active_posts,
                            SUM(CASE WHEN commenting = 'YES' THEN 1 ELSE 0 END) as commenting_enabled
                         FROM newsfeed_post";
        try {
            $stmt = $db->query($postStatsSQL);
            $postStats = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $postStats = ['total_posts' => 0, 'active_posts' => 0, 'commenting_enabled' => 0];
        }
        
        // Total comments count
        $totalCommentsSQL = "SELECT COUNT(*) as total FROM comment";
        try {
            $stmt = $db->query($totalCommentsSQL);
            $totalComments = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            $totalComments = 0;
        }
        
        // Inquiry stats
        $inquiryStatsSQL = "SELECT 
                              COUNT(*) as total,
                              SUM(CASE WHEN status = 'NOT-RESOLVED' THEN 1 ELSE 0 END) as unresolved,
                              SUM(CASE WHEN status = 'RESOLVED' THEN 1 ELSE 0 END) as resolved
                            FROM inquiry";
        try {
            $stmt = $db->query($inquiryStatsSQL);
            $inquiryStats = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $inquiryStats = ['total' => 0, 'unresolved' => 0, 'resolved' => 0];
        }
        
        // Recent inquiries (last 5)
        $recentInquiriesSQL = "SELECT inquiry_id, email, subject, date, status 
                               FROM inquiry 
                               ORDER BY date DESC 
                               LIMIT 5";
        try {
            $stmt = $db->query($recentInquiriesSQL);
            $recentInquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recentInquiries = [];
        }
        
        return [
            'recent_comments' => $recentComments,
            'total_comments' => $totalComments,
            'post_stats' => $postStats,
            'inquiry_stats' => $inquiryStats,
            'recent_inquiries' => $recentInquiries
        ];
    }

    /**
     * Achievement Stats - Recent achievements, top performers, counts
     */
    private function getAchievementStats() {
        $db = Database::getConnection();
        
        // Build WHERE clause for faculty filtering
        $whereClause = '';
        if ($this->currentFacultyId) {
            $whereClause = " JOIN sport s ON sa.sport_id = s.sport_id WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId);
        }
        
        // Total achievements
        $totalSQL = "SELECT COUNT(*) as total FROM sport_achievements sa" . $whereClause;
        try {
            $stmt = $db->query($totalSQL);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Achievement stats error: " . $e->getMessage());
            $total = 0;
        }
        
        // Recent achievements (last 5)
        $recentSQL = "SELECT sa.id, sa.achievement_type, sa.points, sa.date_achieved,
                             CONCAT(u.fname, ' ', u.lname) as student_name,
                             s.sport_name
                      FROM sport_achievements sa
                      JOIN user u ON sa.user_id = u.user_id
                      LEFT JOIN sport s ON sa.sport_id = s.sport_id" .
                     ($this->currentFacultyId ? " WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "") .
                     " ORDER BY sa.date_achieved DESC
                      LIMIT 5";
        try {
            $stmt = $db->query($recentSQL);
            $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recent = [];
        }
        
        // Top performers
        $topSQL = "SELECT CONCAT(u.fname, ' ', u.lname) as student_name,
                          SUM(sa.points) as total_points,
                          COUNT(*) as achievement_count
                   FROM sport_achievements sa
                   JOIN user u ON sa.user_id = u.user_id" .
                  ($this->currentFacultyId ? " WHERE sa.sport_id IN (" .
                      "SELECT sport_id FROM sport WHERE faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) . ")"
                      : "") .
                  " GROUP BY sa.user_id
                   ORDER BY total_points DESC
                   LIMIT 5";
        try {
            $stmt = $db->query($topSQL);
            $topPerformers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $topPerformers = [];
        }
        
        // Achievements by sport
        $bySportSQL = "SELECT s.sport_name, COUNT(*) as count, SUM(sa.points) as total_points
                       FROM sport_achievements sa
                       JOIN sport s ON sa.sport_id = s.sport_id" .
                      ($this->currentFacultyId ? " WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "") .
                      " GROUP BY sa.sport_id
                       ORDER BY count DESC
                       LIMIT 8";
        try {
            $stmt = $db->query($bySportSQL);
            $bySport = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bySport = [];
        }
        
        return [
            'total' => $total,
            'recent' => $recent,
            'top_performers' => $topPerformers,
            'by_sport' => $bySport
        ];
    }

    /**
     * Detailed Facility Analytics for the Facilities tab
     */
    private function getFacilityAnalytics() {
        $db = Database::getConnection();
        
        // Facility utilization rates
        $utilizationSQL = "SELECT fr.facility_name,
                                  COUNT(fb.booking_id) as total_bookings,
                                  SUM(CASE WHEN fb.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as last_30_days
                           FROM facility_rates fr
                           LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id
                           GROUP BY fr.id, fr.facility_name
                           ORDER BY total_bookings DESC";
        try {
            $stmt = $db->query($utilizationSQL);
            $utilization = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $utilization = [];
        }
        
        // Bookings by status
        $statusSQL = "SELECT status, COUNT(*) as count 
                      FROM `facility-booking` 
                      GROUP BY status";
        try {
            $stmt = $db->query($statusSQL);
            $byStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $byStatus = [];
        }
        
        // Monthly trend (last 6 months)
        $trendSQL = "SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as bookings
                     FROM `facility-booking`
                     WHERE date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                     GROUP BY DATE_FORMAT(date, '%Y-%m')
                     ORDER BY month ASC";
        try {
            $stmt = $db->query($trendSQL);
            $monthlyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $monthlyTrend = [];
        }
        
        return [
            'utilization' => $utilization,
            'by_status' => $byStatus,
            'monthly_trend' => $monthlyTrend
        ];
    }

    /**
     * Detailed Equipment Analytics for the Equipment tab
     */
    private function getEquipmentDetailedAnalytics() {
        $db = Database::getConnection();
        
        // Equipment by sport (faculty-scoped if needed)
        $bySportSQL = "SELECT s.sport_name, 
                              COUNT(DISTINCT ei.equipment_id) as item_types,
                              SUM(ei.quantity) as total_quantity
                       FROM equipment_inventory ei
                       JOIN sport s ON ei.sport_id = s.sport_id" .
                      ($this->currentFacultyId ? " WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "") .
                      " GROUP BY ei.sport_id
                       ORDER BY total_quantity DESC";
        try {
            $stmt = $db->query($bySportSQL);
            $bySport = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bySport = [];
        }
        
        // Low stock items (less than 10)
        $lowStockSQL = "SELECT e.equipment_name, s.sport_name, ei.quantity
                        FROM equipment_inventory ei
                        JOIN equipment e ON ei.equipment_id = e.equipment_id
                        JOIN sport s ON ei.sport_id = s.sport_id" .
                       ($this->currentFacultyId ? " WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "") .
                       " AND ei.quantity < 10
                        ORDER BY ei.quantity ASC
                        LIMIT 10";
        try {
            $stmt = $db->query($lowStockSQL);
            $lowStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $lowStock = [];
        }
        
        // Recent GRN activity (last 5)
        $recentGRN = "SELECT g.date, g.quantity, g.unit_price, 
                             e.equipment_name, s.sport_name
                      FROM `good_received_notes` g
                      JOIN equipment e ON g.equipment_id = e.equipment_id
                      JOIN sport s ON g.sport_id = s.sport_id" .
                     ($this->currentFacultyId ? " WHERE s.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "") .
                     " ORDER BY g.date DESC
                      LIMIT 5";
        try {
            $stmt = $db->query($recentGRN);
            $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recentActivity = [];
        }
        
        return [
            'by_sport' => $bySport,
            'low_stock' => $lowStock,
            'recent_activity' => $recentActivity
        ];
    }

    /**
     * Get sport performance details for drill-down view
     * Shows budget, events, achievements, equipment for specific sport
     */
    public function getSportPerformanceDetails() {
        header('Content-Type: application/json');
        try {
            $sportId = $_GET['sport_id'] ?? null;
            $facultyId = $_GET['faculty_id'] ?? null;
            $startDate = $_GET['start_date'] ?? date('Y-01-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
            
            if (!$sportId) {
                throw new Exception('Sport ID required');
            }

            $this->currentFacultyId = $facultyId;
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            
            $db = Database::getConnection();
            $sportId = intval($sportId);
            
            // Sport info
            $sportSQL = "SELECT sport_id, sport_name, faculty_id FROM sport WHERE sport_id = $sportId";
            $stmt = $db->query($sportSQL);
            $sportInfo = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['sport_name' => 'Unknown Sport'];
            
            // Budget breakdown for this sport
            $budgetSQL = "SELECT 
                            b.budget_id, b.amount as allocated,
                            COALESCE(SUM(se.expense_amount), 0) as spent,
                            b.amount - COALESCE(SUM(se.expense_amount), 0) as remaining
                         FROM budget b
                         LEFT JOIN sport_expenses se ON b.budget_id = se.budget_id 
                            AND se.expense_date BETWEEN '$startDate' AND '$endDate'
                         WHERE b.sport_id = $sportId
                         GROUP BY b.budget_id, b.amount";
            try {
                $stmt = $db->query($budgetSQL);
                $budgetData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $budgetData = [];
            }
            
            // Events for this sport
            $eventsSQL = "SELECT te.tournament_id, te.tournament_name, te.date as event_date,
                                 COUNT(DISTINCT tm.match_id) as total_matches,
                                 SUM(CASE WHEN tm.status = 'COMPLETED' THEN 1 ELSE 0 END) as completed_matches
                          FROM tournament te
                          LEFT JOIN `tournament-match` tm ON te.tournament_id = tm.tournament_id
                          WHERE te.sport_id = $sportId 
                            AND te.date BETWEEN '$startDate' AND '$endDate'
                          GROUP BY te.tournament_id
                          ORDER BY te.date DESC";
            try {
                $stmt = $db->query($eventsSQL);
                $eventsData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $eventsData = [];
            }
            
            // Achievements for this sport
            $achievementsSQL = "SELECT achievement_id, title, achieved_by, date_achieved, achieve_category
                                FROM sports_achievements
                                WHERE sport_id = $sportId 
                                  AND date_achieved BETWEEN '$startDate' AND '$endDate'
                                ORDER BY date_achieved DESC";
            try {
                $stmt = $db->query($achievementsSQL);
                $achievementData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $achievementData = [];
            }
            
            // Equipment for this sport
            $equipmentSQL = "SELECT e.equipment_id, e.equipment_name, ei.quantity,
                                    CASE WHEN ei.quantity < 5 THEN 'CRITICAL'
                                         WHEN ei.quantity < 10 THEN 'LOW'
                                         ELSE 'SUFFICIENT' END as stock_status
                             FROM equipment_inventory ei
                             JOIN equipment e ON ei.equipment_id = e.equipment_id
                             WHERE ei.sport_id = $sportId
                             ORDER BY ei.quantity ASC";
            try {
                $stmt = $db->query($equipmentSQL);
                $equipmentData = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $equipmentData = [];
            }
            
            echo json_encode([
                'status' => 'success',
                'sport' => $sportInfo,
                'budget' => $budgetData,
                'events' => $eventsData,
                'achievements' => $achievementData,
                'equipment' => $equipmentData,
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ]);
        } catch (Exception $e) {
            error_log("Sport performance details error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get budget trends by sport with date range filtering
     */
    public function getBudgetTrendsByDateRange() {
        header('Content-Type: application/json');
        try {
            $facultyId = $_GET['faculty_id'] ?? null;
            $startDate = $_GET['start_date'] ?? date('Y-01-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
            
            $this->currentFacultyId = $facultyId;
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            
            $db = Database::getConnection();
            $dateFilter = "se.expense_date BETWEEN '$startDate' AND '$endDate'";
            $facultyFilter = $facultyId ? "AND s.faculty_id = " . $this->quoteFacultyId($facultyId) : "";
            
            // Budget vs Spending by sport
            $trendSQL = "SELECT s.sport_id, s.sport_name,
                                COALESCE(SUM(b.amount), 0) as allocated_budget,
                                COALESCE(SUM(se.expense_amount), 0) as spent_amount,
                                COALESCE(SUM(b.amount), 0) - COALESCE(SUM(se.expense_amount), 0) as remaining
                         FROM sport s
                         LEFT JOIN budget b ON s.sport_id = b.sport_id
                         LEFT JOIN sport_expenses se ON b.budget_id = se.budget_id AND $dateFilter
                         WHERE 1=1 $facultyFilter
                         GROUP BY s.sport_id, s.sport_name
                         ORDER BY spent_amount DESC";
            try {
                $stmt = $db->query($trendSQL);
                $trends = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $trends = [];
            }
            
            // Monthly spending trend
            $monthlySQL = "SELECT DATE_FORMAT(se.expense_date, '%Y-%m') as month,
                                  s.sport_name, s.sport_id,
                                  SUM(se.expense_amount) as monthly_expense
                           FROM sport_expenses se
                           JOIN budget b ON se.budget_id = b.budget_id
                           JOIN sport s ON b.sport_id = s.sport_id
                           WHERE $dateFilter $facultyFilter
                           GROUP BY DATE_FORMAT(se.expense_date, '%Y-%m'), s.sport_id
                           ORDER BY month DESC";
            try {
                $stmt = $db->query($monthlySQL);
                $monthly = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $monthly = [];
            }
            
            echo json_encode([
                'status' => 'success',
                'by_sport' => $trends,
                'monthly_trend' => $monthly,
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ]);
        } catch (Exception $e) {
            error_log("Budget trends error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get facility and equipment usage analytics
     */
    public function getUtilizationTrends() {
        header('Content-Type: application/json');
        try {
            $facultyId = $_GET['faculty_id'] ?? null;
            $startDate = $_GET['start_date'] ?? date('Y-01-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
            
            $this->currentFacultyId = $facultyId;
            $this->startDate = $startDate;
            $this->endDate = $endDate;
            
            $db = Database::getConnection();
            $dateFilter = "fb.date BETWEEN '$startDate' AND '$endDate'";
            $facultyFilter = $facultyId ? 
                "AND s.faculty_id = " . $this->quoteFacultyId($facultyId) : "";
            
            // Facility booking trends
            $facilitySQL = "SELECT s.sport_name, s.sport_id,
                                   fr.facility_name,
                                   COUNT(fb.booking_id) as total_bookings,
                                   SUM(CASE WHEN fb.status = 'APPROVED' THEN 1 ELSE 0 END) as approved_bookings,
                                   SUM(CASE WHEN fb.status = 'REJECTED' THEN 1 ELSE 0 END) as rejected_bookings
                            FROM facility_rates fr
                            LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id AND $dateFilter
                            LEFT JOIN sport s ON fb.sport_id = s.sport_id
                            WHERE 1=1 $facultyFilter
                            GROUP BY fr.id, s.sport_id
                            ORDER BY total_bookings DESC";
            try {
                $stmt = $db->query($facilitySQL);
                $facilityUsage = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $facilityUsage = [];
            }
            
            // Equipment GRN activity by sport
            $equipmentSQL = "SELECT s.sport_name, s.sport_id,
                                    e.equipment_name,
                                    COUNT(grn.grn_id) as procurement_count,
                                    SUM(grn.quantity) as total_quantity_received,
                                    SUM(grn.quantity * grn.unit_price) as total_cost
                             FROM `good_received_notes` grn
                             JOIN equipment e ON grn.equipment_id = e.equipment_id
                             JOIN sport s ON grn.sport_id = s.sport_id
                             WHERE grn.date BETWEEN '$startDate' AND '$endDate' $facultyFilter
                             GROUP BY s.sport_id, e.equipment_id
                             ORDER BY total_cost DESC";
            try {
                $stmt = $db->query($equipmentSQL);
                $equipmentUsage = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Exception $e) {
                $equipmentUsage = [];
            }
            
            echo json_encode([
                'status' => 'success',
                'facility_usage' => $facilityUsage,
                'equipment_activity' => $equipmentUsage,
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ]);
        } catch (Exception $e) {
            error_log("Utilization trends error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
