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
    public function isSlotTaken($facility_id, $date, $slot) {
        $sql = "SELECT booking_id 
                FROM `facility-booking`
                WHERE facility_id = :facility_id
                AND date = :date
                AND slot = :slot
                AND status = 'BOOKED'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':facility_id' => $facility_id,
            ':date' => $date,
            ':slot' => $slot
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
    
        // Get existing bookings
        $sql2 = "SELECT slot FROM `facility-booking`
                 WHERE facility_id = :facility_id 
                 AND date = :date 
                 AND status = 'BOOKED'";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([':facility_id' => $facility_id, ':date' => $date]);
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
    public function getReservationChartData($facility_id, $days = 7) {
        $chartData = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            
            // Get bookings for this date
            $sql = "SELECT slot FROM `facility-booking`
                    WHERE facility_id = :facility_id 
                    AND date = :date 
                    AND status = 'BOOKED'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':facility_id' => $facility_id,
                ':date' => $date
            ]);
            
            $bookings = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Build slots status
            $slots = [
                'MORNING' => in_array('MORNING', $bookings),
                'AFTERNOON' => in_array('AFTERNOON', $bookings),
                'FULL' => in_array('FULL', $bookings)
            ];
            
            $chartData[] = [
                'date' => $date,
                'slots' => $slots
            ];
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
}
