<?php
class Facility {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /* ---------- CREATE BOOKING ---------- */
    public function createBooking($data) {
        // Generate booking ID
        $booking_id = "BK" . rand(100000, 999999);

        $sql = "INSERT INTO `facility-booking`
                (booking_id, user_id, facility_id, date, slot, purpose, status, payment_status)
                VALUES (:booking_id, :user_id, :facility_id, :date, :slot, :purpose, 'BOOKED', 'INCOMPLETE')";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':booking_id' => $booking_id,
            ':user_id' => $data['user_id'],
            ':facility_id' => $data['facility_id'],
            ':date' => $data['date'],
            ':slot' => $data['slot'],
            ':purpose' => $data['purpose']
        ]);

        return $result ? $booking_id : false;
    }

    /* ---------- CHECK SLOT ALREADY BOOKED ---------- */
    public function isSlotTaken($rate_id, $date, $slot) {
        $sql = "SELECT fb.booking_id 
                FROM `facility-booking` fb
                INNER JOIN facility_rates fr1 ON fb.facility_id = fr1.id
                WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                AND fb.date = :date
                AND (fb.slot = :slot OR fb.slot = 'FULL' OR :slot2 = 'FULL')
                AND fb.status IN ('BOOKED', 'ACCEPTED', 'RESERVED')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rate_id' => $rate_id,
            ':date' => $date,
            ':slot' => $slot,
            ':slot2' => $slot
        ]);

        return $stmt->fetch() !== false;
    }

    /* ---------- VIEW MY RESERVATIONS ---------- */
    public function getMyReservations($user_id) {
        $sql = "SELECT 
                    fb.booking_id,
                    f.facility_name,
                    fb.date,
                    fb.slot,
                    fb.purpose,
                    fb.payment_status,
                    fb.payment_slip,
                    f.practice_working_hours,
                    f.practice_other_hours,
                    f.tournament_full_day_working,
                    f.tournament_half_day_working,
                    f.tournament_full_day_other,
                    f.tournament_half_day_other,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '08:00 AM'
                        WHEN fb.slot = 'AFTERNOON' THEN '01:00 PM'
                        WHEN fb.slot = 'FULL' THEN '08:00 AM'
                        ELSE fb.slot
                    END as start_time,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '12:00 PM'
                        WHEN fb.slot = 'AFTERNOON' THEN '05:00 PM'
                        WHEN fb.slot = 'FULL' THEN '05:00 PM'
                        ELSE fb.slot
                    END as end_time
                FROM `facility-booking` fb
                INNER JOIN facility_rates f ON fb.facility_id = f.id
                WHERE fb.user_id = :user_id
                AND fb.status IN ('BOOKED', 'ACCEPTED')
                ORDER BY fb.date DESC, fb.slot ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- CANCEL BOOKING ---------- */
    public function cancelBooking($booking_id, $user_id) {
        // Verify booking belongs to user
        $sql = "SELECT booking_id FROM `facility-booking`
                WHERE booking_id = :booking_id 
                AND user_id = :user_id
                AND status = 'BOOKED'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':booking_id' => $booking_id,
            ':user_id' => $user_id
        ]);
        
        if (!$stmt->fetch()) {
            return false;
        }

        // Cancel booking
        $sql = "UPDATE `facility-booking`
                SET status = 'CANCELLED'
                WHERE booking_id = :booking_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':booking_id' => $booking_id]);
    }

    /* ---------- GET RESERVED SLOTS ---------- */
    public function getReservedSlots($facility_id, $date) {
        // Check if date is a working day (Mon-Fri)
        $dayOfWeek = date('N', strtotime($date)); // 1=Mon, 7=Sun
        $isWorkingDay = ($dayOfWeek >= 1 && $dayOfWeek <= 5);
    
        // Get facility rates
        $sql = "SELECT *
                FROM facility_rates
                WHERE id = :facility_id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':facility_id' => $facility_id]);
        $facility = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$facility) return ['date' => $date, 'slots' => []];
    
        // Get existing bookings for the same PHYSICAL location
        $sql2 = "SELECT fb.slot 
                 FROM `facility-booking` fb
                 INNER JOIN facility_rates fr1 ON fb.facility_id = fr1.id
                 WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                 AND fb.date = :date 
                 AND fb.status IN ('BOOKED', 'ACCEPTED', 'RESERVED')";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([':rate_id' => $facility_id, ':date' => $date]);
        $bookings = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    
        // Prepare slots with availability and prices
        $slots = [];
    
        // MORNING slot
        if (!is_null($facility['practice_working_hours'])) {
            $slots[] = [
                'id' => 'MORNING',
                'type' => 'Morning (8:00 AM - 12:00 PM)',
                'price' => $isWorkingDay ? $facility['practice_working_hours'] : $facility['practice_other_hours'],
                'taken' => in_array('MORNING', $bookings)
            ];
        }
    
        // AFTERNOON slot
        if (!is_null($facility['practice_working_hours'])) {
            $slots[] = [
                'id' => 'AFTERNOON',
                'type' => 'Afternoon (1:00 PM - 5:00 PM)',
                'price' => $isWorkingDay ? $facility['practice_working_hours'] : $facility['practice_other_hours'],
                'taken' => in_array('AFTERNOON', $bookings)
            ];
        }
    
        // FULL DAY slot
        if (!is_null($facility['tournament_full_day_working'])) {
            $slots[] = [
                'id' => 'FULL',
                'type' => 'Full Day (8:00 AM - 5:00 PM)',
                'price' => $isWorkingDay ? $facility['tournament_full_day_working'] : $facility['tournament_full_day_other'],
                'taken' => in_array('FULL', $bookings)
            ];
        }
    
        return [
            'date' => $date,
            'slots' => $slots
        ];
    }

    /* ---------- GET CHART DATA ---------- */
    public function getReservationChartData($facility_id, $startDate, $endDate) {
        $chartData = [];
        
        $current = strtotime($startDate);
        $end = strtotime($endDate);

        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            
            // Get bookings for this PHYSICAL location
            $sql = "SELECT fb.slot 
                    FROM `facility-booking` fb
                    INNER JOIN facility_rates fr1 ON fb.facility_id = fr1.id
                    WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                    AND fb.date = :date 
                    AND fb.status IN ('BOOKED', 'ACCEPTED', 'RESERVED')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':rate_id' => $facility_id,
                ':date' => $date
            ]);
            
            $bookings = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Build slots status - Handle FULL day logic
            $hasMorning = in_array('MORNING', $bookings);
            $hasAfternoon = in_array('AFTERNOON', $bookings);
            $hasFull = in_array('FULL', $bookings);
            
            $slots = [
                'MORNING' => $hasMorning || $hasFull,
                'AFTERNOON' => $hasAfternoon || $hasFull,
                'FULL' => $hasFull || $hasMorning || $hasAfternoon
            ];
            
            $chartData[] = [
                'date' => $date,
                'slots' => $slots
            ];
            
            // Increment day
            $current = strtotime("+1 day", $current);
        }
        
        return $chartData;
    }

    /* ---------- GET RESERVATION DETAILS ---------- */
    public function getReservationDetails($booking_id) {
        $sql = "SELECT 
                    fb.booking_id,
                    fb.user_id,
                    fb.facility_id,
                    fb.date,
                    fb.slot,
                    fb.purpose,
                    fb.status,
                    fb.payment_status,
                    fb.payment_slip,
                    fb.rejection_reason,
                    CONCAT(u.fname, ' ', u.lname) AS user_name,
                    u.email AS user_email,
                    u.contact_no,
                    u.type AS user_type,
                    fr.facility_name,
                    fr.facility_type,
                    fr.practice_working_hours,
                    fr.practice_other_hours,
                    fr.tournament_full_day_working,
                    fr.tournament_half_day_working,
                    fr.tournament_full_day_other,
                    fr.tournament_half_day_other,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '08:00 AM - 12:00 PM'
                        WHEN fb.slot = 'AFTERNOON' THEN '01:00 PM - 05:00 PM'
                        WHEN fb.slot = 'FULL' THEN '08:00 AM - 05:00 PM'
                        ELSE fb.slot
                    END as time_range
                FROM `facility-booking` fb
                INNER JOIN user u ON fb.user_id = u.user_id
                INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE fb.booking_id = :booking_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':booking_id' => $booking_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ---------- GET MONTHLY BOOKINGS ---------- */
    public function getMonthlyBookings($month, $year) {
        $sql = "SELECT 
                    fb.booking_id,
                    fb.date,
                    fb.slot,
                    fb.status,
                    fr.facility_name
                FROM `facility-booking` fb
                INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE MONTH(fb.date) = :month 
                AND YEAR(fb.date) = :year
                AND fb.status IN ('BOOKED', 'ACCEPTED', 'RESERVED')
                ORDER BY fb.date ASC, fb.slot ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':month' => $month,
            ':year' => $year
        ]);

        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group by date
        $grouped = [];
        foreach ($bookings as $booking) {
            $date = $booking['date'];
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            $grouped[$date][] = $booking;
        }
        
        return $grouped;
    }

    /* ---------- CALCULATE BOOKING RATE ---------- */
    public function calculateBookingRate($booking) {
        // Determine if it's a working day (Mon-Fri) or weekend/holiday
        $dayOfWeek = date('N', strtotime($booking['date'])); // 1=Mon, 7=Sun
        $isWorkingDay = ($dayOfWeek >= 1 && $dayOfWeek <= 5);

        $slot = $booking['slot'];
        $rate = 0;

        if ($slot === 'MORNING' || $slot === 'AFTERNOON') {
            // Half-day slots use practice rates
            if ($isWorkingDay) {
                $rate = $booking['practice_working_hours'] ?? $booking['tournament_half_day_working'] ?? 0;
            } else {
                $rate = $booking['practice_other_hours'] ?? $booking['tournament_half_day_other'] ?? 0;
            }
        } else if ($slot === 'FULL') {
            // Full-day slots use tournament full-day rates
            if ($isWorkingDay) {
                $rate = $booking['tournament_full_day_working'] ?? 0;
            } else {
                $rate = $booking['tournament_full_day_other'] ?? 0;
            }
        }

        return floatval($rate);
    }

    /* ---------- GET WEEK RESERVATIONS ---------- */
    public function getWeekReservations($date) {
        $sql = "SELECT 
                    fb.booking_id,
                    fb.date,
                    fb.slot,
                    fb.status,
                    fb.payment_status,
                    CONCAT(u.fname, ' ', u.lname) AS user_name,
                    u.type AS user_type,
                    fr.facility_name,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '08:00 AM'
                        WHEN fb.slot = 'AFTERNOON' THEN '01:00 PM'
                        WHEN fb.slot = 'FULL' THEN '08:00 AM'
                        ELSE fb.slot
                    END as start_time
                FROM `facility-booking` fb
                INNER JOIN user u ON fb.user_id = u.user_id
                INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE YEARWEEK(fb.date, 1) = YEARWEEK(:date, 1)
                ORDER BY fb.date ASC, fb.slot ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- ACCEPT RESERVATION ---------- */
    public function acceptReservation($booking_id) {
        $sql = "UPDATE `facility-booking`
                SET status = 'ACCEPTED'
                WHERE booking_id = :booking_id
                AND status = 'BOOKED'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':booking_id' => $booking_id]);
    }

    /* ---------- REJECT RESERVATION ---------- */
    public function rejectReservation($booking_id, $reason) {
        $sql = "UPDATE `facility-booking`
                SET status = 'REJECTED',
                    rejection_reason = :reason
                WHERE booking_id = :booking_id
                AND status = 'BOOKED'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':booking_id' => $booking_id,
            ':reason' => $reason
        ]);
    }

    /* ---------- GET THIS WEEK AND NEXT WEEK RESERVATIONS ---------- */
    public function getThisAndNextWeekReservations() {
        $sql = "SELECT 
                    fb.booking_id,
                    fb.user_id,
                    fb.facility_id,
                    fb.date,
                    fb.slot,
                    fb.purpose,
                    fb.status,
                    fb.payment_status,
                    CONCAT(u.fname, ' ', u.lname) AS user_name,
                    fr.facility_name,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '08:00 AM'
                        WHEN fb.slot = 'AFTERNOON' THEN '01:00 PM'
                        WHEN fb.slot = 'FULL' THEN '08:00 AM'
                        ELSE fb.slot
                    END as start_time,
                    CASE 
                        WHEN fb.slot = 'MORNING' THEN '12:00 PM'
                        WHEN fb.slot = 'AFTERNOON' THEN '05:00 PM'
                        WHEN fb.slot = 'FULL' THEN '05:00 PM'
                        ELSE fb.slot
                    END as end_time
                FROM `facility-booking` fb
                INNER JOIN user u ON fb.user_id = u.user_id
                INNER JOIN facility_rates fr ON fb.facility_id = fr.id
                WHERE YEARWEEK(fb.date, 1) = YEARWEEK(CURDATE(), 1)
                   OR YEARWEEK(fb.date, 1) = YEARWEEK(CURDATE(), 1) + 1
                ORDER BY fb.date ASC, fb.slot ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- GET RESERVATION ANALYTICS DATA ---------- */
    public function getReservationData($period = 'monthly', $year = null) {
        if (!$year) $year = date('Y');
        
        try {
            switch ($period) {
                case 'weekly':
                    $sql = "SELECT 
                        WEEK(date) as period_num,
                        CONCAT('Week ', WEEK(date)) as period_label,
                        COUNT(*) as res_count 
                        FROM `facility-booking` 
                        WHERE YEAR(date) = :year 
                        GROUP BY WEEK(date) 
                        ORDER BY period_num";
                    break;
                case 'annually':
                    $sql = "SELECT 
                        YEAR(date) as period_num,
                        YEAR(date) as period_label,
                        COUNT(*) as res_count 
                        FROM `facility-booking` 
                        GROUP BY YEAR(date) 
                        ORDER BY period_num";
                    break;
                default:
                    $sql = "SELECT 
                        MONTH(date) as period_num,
                        MONTHNAME(date) as period_label,
                        COUNT(*) as res_count 
                        FROM `facility-booking` 
                        WHERE YEAR(date) = :year 
                        GROUP BY MONTH(date), MONTHNAME(date) 
                        ORDER BY period_num";
            }
            $stmt = $this->db->prepare($sql);
            if ($period !== 'annually') {
                $stmt->bindParam(':year', $year);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }

    /* ---------- GET PENDING RESERVATIONS COUNT ---------- */
    public function getPendingReservationsCount() {
        try {
            $sql = "SELECT COUNT(*) as total FROM `facility-booking` WHERE status = 'BOOKED'";
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get pending reservations count error: " . $e->getMessage());
            return 0;
        }
    }

    /* ---------- UPDATE PAYMENT STATUS (for PayHere IPN) ---------- */
    public function updatePaymentStatus($booking_id, $status, $payment_id = null) {
        try {
            $sql = "UPDATE `facility-booking`
                    SET payment_status = :status,
                        payment_id = :payment_id
                    WHERE booking_id = :booking_id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':payment_id' => $payment_id,
                ':booking_id' => $booking_id
            ]);
        } catch (PDOException $e) {
            error_log("Update payment status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Track a user's interest in a specific facility and date.
     */
    public function trackBookingAttempt($userId, $facilityId, $date) {
        $sql = "INSERT INTO active_booking_attempts (user_id, facility_id, date, last_active_at)
                VALUES (:user_id, :facility_id, :date, CURRENT_TIMESTAMP)
                ON DUPLICATE KEY UPDATE last_active_at = CURRENT_TIMESTAMP";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':facility_id' => $facilityId,
            ':date' => $date
        ]);
    }

    /**
 * Update heartbeat for parallel booking check.
 */
public function updateHeartbeat($sessionId, $facilityId, $date = null, $slot = null) {
    $sql = "INSERT INTO parallel_checker (session_id, facility_id, selected_date, selected_slot, last_heartbeat)
            VALUES (:session_id, :facility_id, :selected_date, :selected_slot, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE last_heartbeat = CURRENT_TIMESTAMP, selected_date = :selected_date2, selected_slot = :selected_slot2";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':session_id' => $sessionId,
        ':facility_id' => $facilityId,
        ':selected_date' => $date,
        ':selected_slot' => $slot,
        ':selected_date2' => $date,
        ':selected_slot2' => $slot
    ]);
}

    /**
 * Check if other users are booking the same facility.
 */
    public function checkParallelStatus($sessionId, $rate_id) {
        // Cleanup old heartbeats (older than 5 seconds)
        $this->db->query("DELETE FROM parallel_checker WHERE last_heartbeat < (CURRENT_TIMESTAMP - INTERVAL 5 SECOND)");

        // Check if any booking attempt exists for the same PHYSICAL location
        $sql = "SELECT COUNT(*) as count 
                FROM parallel_checker pc
                INNER JOIN facility_rates fr1 ON pc.facility_id = fr1.id
                WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                AND pc.session_id != :session_id 
                AND pc.last_heartbeat >= (CURRENT_TIMESTAMP - INTERVAL 5 SECOND)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rate_id' => $rate_id,
            ':session_id' => $sessionId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['count'] > 0;
    }

    public function getSlotInterest($sessionId, $facilityId) {
        // Get other users' selected dates/slots from parallel_checker for same PHYSICAL location
        $sql = "SELECT pc.selected_date, pc.selected_slot
                FROM parallel_checker pc
                INNER JOIN facility_rates fr1 ON pc.facility_id = fr1.id
                WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                AND pc.session_id != :session_id
                AND pc.selected_date IS NOT NULL
                AND pc.last_heartbeat >= (CURRENT_TIMESTAMP - INTERVAL 5 SECOND)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':rate_id' => $facilityId,
            ':session_id' => $sessionId
        ]);

    $interest = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['selected_date'];
        if (!isset($interest[$date])) {
            $interest[$date] = [];
        }
        // If slot is specified, track that specific slot
        if ($row['selected_slot']) {
            $interest[$date][] = $row['selected_slot'];
        } else {
            // User selected date but no slot yet — mark all slots as interested
            $interest[$date] = array_unique(array_merge($interest[$date], ['MORNING', 'AFTERNOON', 'FULL']));
        }
    }

    return $interest;
}

    public function getBookedSlots($facilityId) {
        // Get confirmed bookings for same PHYSICAL location
        $sql = "SELECT fb.date, fb.slot
                FROM `facility-booking` fb
                INNER JOIN facility_rates fr1 ON fb.facility_id = fr1.id
                WHERE fr1.facility_id = (SELECT facility_id FROM facility_rates WHERE id = :rate_id)
                AND (fb.status = 'BOOKED' OR fb.status = 'RESERVED')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rate_id' => $facilityId]);

        $booked = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['date'];
        if (!isset($booked[$date])) {
            $booked[$date] = [];
        }
        $booked[$date][] = $row['slot'];
    }

    return $booked;
}

    /* ---------- GET FACILITY RESERVATION ANALYTICS ---------- */
    public function getAnalytics() {
        $analytics = [];

        // 1. FACILITY UTILIZATION - How often each facility is booked
        $utilizationSQL = "
            SELECT 
                fr.id as facility_id,
                fr.facility_name,
                fr.facility_type,
                COUNT(fb.booking_id) as total_bookings,
                SUM(CASE WHEN fb.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as bookings_last_30,
                ROUND(
                    SUM(CASE WHEN fb.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) * 100.0 / 30
                , 1) as utilization_rate
            FROM facility_rates fr
            LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id
            GROUP BY fr.id, fr.facility_name, fr.facility_type
            HAVING total_bookings > 0
            ORDER BY utilization_rate DESC
            LIMIT 10
        ";
        $stmt = $this->db->query($utilizationSQL);
        $analytics['utilization'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. HIGH DEMAND FACILITIES - Facilities with most bookings in last 30 days
        $highDemandSQL = "
            SELECT 
                fr.id as facility_id,
                fr.facility_name,
                fr.facility_type,
                COUNT(fb.booking_id) as bookings_last_30_days,
                SUM(CASE WHEN fb.status = 'BOOKED' THEN 1 ELSE 0 END) as pending_count,
                ROUND(COUNT(fb.booking_id) * 100.0 / 30, 1) as daily_demand_rate
            FROM facility_rates fr
            INNER JOIN `facility-booking` fb ON fr.id = fb.facility_id
            WHERE fb.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY fr.id, fr.facility_name, fr.facility_type
            HAVING bookings_last_30_days >= 3
            ORDER BY bookings_last_30_days DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($highDemandSQL);
        $analytics['high_demand'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. UNDERUTILIZED FACILITIES - Rarely booked or never booked
        $underutilizedSQL = "
            SELECT 
                fr.id as facility_id,
                fr.facility_name,
                fr.facility_type,
                COUNT(fb.booking_id) as total_bookings,
                DATEDIFF(CURDATE(), MAX(fb.date)) as days_since_last_booking
            FROM facility_rates fr
            LEFT JOIN `facility-booking` fb ON fr.id = fb.facility_id
            GROUP BY fr.id, fr.facility_name, fr.facility_type
            HAVING total_bookings <= 2 OR days_since_last_booking > 30 OR days_since_last_booking IS NULL
            ORDER BY total_bookings ASC, days_since_last_booking DESC
            LIMIT 8
        ";
        $stmt = $this->db->query($underutilizedSQL);
        $analytics['underutilized'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. PEAK BOOKING DAYS - Which days are busiest
        $peakDaysSQL = "
            SELECT 
                DAYNAME(date) as day_name,
                DAYOFWEEK(date) as day_num,
                COUNT(*) as booking_count
            FROM `facility-booking`
            GROUP BY DAYNAME(date), DAYOFWEEK(date)
            ORDER BY booking_count DESC
        ";
        $stmt = $this->db->query($peakDaysSQL);
        $analytics['peak_days'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. BOOKING BY USER TYPE - Internal vs Public distribution
        $userTypeSQL = "
            SELECT 
                u.type as user_type,
                COUNT(*) as booking_count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM `facility-booking`), 1) as percentage
            FROM `facility-booking` fb
            INNER JOIN user u ON fb.user_id = u.user_id
            GROUP BY u.type
            ORDER BY booking_count DESC
        ";
        $stmt = $this->db->query($userTypeSQL);
        $analytics['user_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 8. FACILITY TYPE DISTRIBUTION - Indoor vs Ground bookings
        $facilityTypeSQL = "
            SELECT 
                fr.facility_type,
                COUNT(fb.booking_id) as booking_count,
                ROUND(COUNT(fb.booking_id) * 100.0 / (SELECT COUNT(*) FROM `facility-booking`), 1) as percentage
            FROM facility_rates fr
            INNER JOIN `facility-booking` fb ON fr.id = fb.facility_id
            GROUP BY fr.facility_type
            ORDER BY booking_count DESC
        ";
        $stmt = $this->db->query($facilityTypeSQL);
        $analytics['facility_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9. MOST ACTIVE USERS - Frequent bookers
        $activeUsersSQL = "
            SELECT 
                fb.user_id,
                CONCAT(u.fname, ' ', u.lname) as user_name,
                u.type as user_type,
                COUNT(*) as total_bookings,
                COUNT(DISTINCT fb.facility_id) as unique_facilities
            FROM `facility-booking` fb
            INNER JOIN user u ON fb.user_id = u.user_id
            GROUP BY fb.user_id
            ORDER BY total_bookings DESC
            LIMIT 5
        ";
        $stmt = $this->db->query($activeUsersSQL);
        $analytics['active_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 10. QUICK SUMMARY STATS
        $summarySQL = "
            SELECT 
                (SELECT COUNT(*) FROM `facility-booking` WHERE status = 'BOOKED') as pending_reservations,
                (SELECT COUNT(*) FROM `facility-booking` WHERE status = 'ACCEPTED') as accepted_reservations,
                (SELECT COUNT(*) FROM `facility-booking` WHERE status = 'REJECTED') as rejected_reservations,
                (SELECT COUNT(DISTINCT facility_id) FROM `facility-booking`) as facilities_used,
                (SELECT COUNT(DISTINCT user_id) FROM `facility-booking`) as unique_users
        ";
        $stmt = $this->db->query($summarySQL);
        $analytics['summary'] = $stmt->fetch(PDO::FETCH_ASSOC);

        return $analytics;
    }

    /* ---------- SEARCH RESERVATIONS ---------- */
    public function searchReservations($query = '', $filters = []) {
        $sql = "
            SELECT r.booking_id,
                   CONCAT(u.fname, ' ', u.lname) AS user_name,
                   r.date,
                   fr.facility_name,
                   p.facility_name AS physical_location,
                   u.type AS user_type,
                   r.payment_status,
                   r.status
            FROM `facility-booking` r
            INNER JOIN user u ON r.user_id = u.user_id
            INNER JOIN facility_rates fr ON r.facility_id = fr.id
            LEFT JOIN physical_facility p ON fr.facility_id = p.facility_id
            WHERE 1
        ";

        $params = [];

        if ($query !== '') {
            $sql .= " AND (r.booking_id LIKE :q OR CONCAT(u.fname, ' ', u.lname) LIKE :q_full)";
            $params[':q'] = "%$query%";
            $params[':q_full'] = "%$query%";
        }

        if (!empty($filters['date'])) {
            $sql .= " AND r.date = :date";
            $params[':date'] = $filters['date'];
        }

        if (!empty($filters['location'])) {
            $sql .= " AND p.facility_id = :location";
            $params[':location'] = $filters['location'];
        }

        if (!empty($filters['user_type'])) {
            $sql .= " AND u.type = :user_type";
            $params[':user_type'] = $filters['user_type'];
        }

        $sql .= " ORDER BY r.date DESC, r.booking_id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- GET PHYSICAL FACILITIES ---------- */
    public function getPhysicalFacilities() {
        $sql = "SELECT facility_id, facility_name FROM physical_facility ORDER BY facility_name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------- UPDATE PAYMENT SLIP ---------- */
    public function updatePaymentSlip($booking_id, $filename) {
        try {
            $sql = "UPDATE `facility-booking`
                    SET payment_slip = :filename,
                        payment_status = 'PENDING'
                    WHERE booking_id = :booking_id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':filename' => $filename,
                ':booking_id' => $booking_id
            ]);
        } catch (PDOException $e) {
            error_log("Update payment slip error: " . $e->getMessage());
            return false;
        }
    }
}

