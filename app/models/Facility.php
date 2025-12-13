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

        return $stmt->execute([
            ':booking_id' => $booking_id,
            ':user_id' => $data['user_id'],
            ':facility_id' => $data['facility_id'],
            ':date' => $data['date'],
            ':slot' => $data['slot'],
            ':purpose' => $data['purpose']
        ]);
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
                AND fb.status = 'BOOKED'
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
}