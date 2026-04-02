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
                'data' => $summary
            ]);
        } catch (Exception $e) {
            error_log("Executive summary error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to load dashboard data'
            ]);
        }
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
        
        // Total equipment items from equipment_inventory table
        $totalSQL = "SELECT COUNT(DISTINCT equipment_id) as total, SUM(quantity) as total_quantity FROM equipment_inventory";
        $stmt = $db->query($totalSQL);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Low stock items (less than 10 quantity)
        $lowStockSQL = "SELECT COUNT(*) as count FROM equipment_inventory WHERE quantity < 10";
        $stmt = $db->query($lowStockSQL);
        $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

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
                WHERE b.year = YEAR(CURDATE()) AND b.allocated_amount > 0
                ORDER BY utilization DESC";
        $stmt = $db->query($sql);
        $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
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
        
        // Pending reservations
        $pendingSQL = "SELECT COUNT(*) as count FROM `facility-booking` WHERE status = 'BOOKED'";
        $stmt = $db->query($pendingSQL);
        $pendingReservations = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Unresolved inquiries
        $inquirySQL = "SELECT COUNT(*) as count FROM inquiry WHERE status = 'NOT-RESOLVED'";
        $stmt = $db->query($inquirySQL);
        $unresolvedInquiries = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Upcoming events in next 7 days
        $eventsSQL = "SELECT COUNT(*) as count FROM tournament WHERE start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $stmt = $db->query($eventsSQL);
        $upcomingEvents = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Low stock equipment
        $equipSQL = "SELECT COUNT(*) as count FROM equipment_inventory WHERE quantity < 5";
        $stmt = $db->query($equipSQL);
        $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Pending equipment requests
        $eqRequestSQL = "SELECT COUNT(*) as count FROM `equipment-requests` WHERE status = 'ACTIVE'";
        $stmt = $db->query($eqRequestSQL);
        $pendingEqRequests = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
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
        
        // Total tournaments
        $totalSQL = "SELECT COUNT(*) as total FROM tournament";
        $stmt = $db->query($totalSQL);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Completed this year
        $completedSQL = "SELECT COUNT(*) as count FROM tournament WHERE status = 'COMPLETE' AND YEAR(end_date) = YEAR(CURDATE())";
        $stmt = $db->query($completedSQL);
        $completedThisYear = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Upcoming (next 30 days)
        $upcomingSQL = "SELECT COUNT(*) as count FROM tournament WHERE start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $db->query($upcomingSQL);
        $upcoming = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

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
        
        // Total achievements
        $totalSQL = "SELECT COUNT(*) as total FROM sport_achievements";
        try {
            $stmt = $db->query($totalSQL);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (Exception $e) {
            $total = 0;
        }
        
        // Recent achievements (last 5)
        $recentSQL = "SELECT sa.id, sa.achievement_type, sa.points, sa.date_achieved,
                             CONCAT(u.fname, ' ', u.lname) as student_name,
                             s.sport_name
                      FROM sport_achievements sa
                      JOIN user u ON sa.user_id = u.user_id
                      LEFT JOIN sport s ON sa.sport_id = s.sport_id
                      ORDER BY sa.date_achieved DESC
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
                   JOIN user u ON sa.user_id = u.user_id
                   GROUP BY sa.user_id
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
                       JOIN sport s ON sa.sport_id = s.sport_id
                       GROUP BY sa.sport_id
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
        
        // Equipment by sport
        $bySportSQL = "SELECT s.sport_name, 
                              COUNT(DISTINCT ei.equipment_id) as item_types,
                              SUM(ei.quantity) as total_quantity
                       FROM equipment_inventory ei
                       JOIN sport s ON ei.sport_id = s.sport_id
                       GROUP BY ei.sport_id
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
                        JOIN sport s ON ei.sport_id = s.sport_id
                        WHERE ei.quantity < 10
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
                      JOIN sport s ON g.sport_id = s.sport_id
                      ORDER BY g.date DESC
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
}
