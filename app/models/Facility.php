<?php
class Facility {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Your PDO connection
    }

    public function createBooking($booking_id, $user_id, $facility_id, $date, $start_time, $end_time, $purpose) {

        $sql = "INSERT INTO `facility-booking` 
                (booking_id, user_id, facility_id, date, start_time, ent_time, purpose) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$booking_id, $user_id, $facility_id, $date, $start_time, $end_time, $purpose]);
    }

    public function getReservationsByUser($userId) {
        $sql = "
            SELECT fb.booking_id, fb.facility_id, fr.facility_name, fr.facility_type,
                   fb.date, fb.start_time, fb.ent_time AS end_time, fb.purpose, fb.status, fb.payment_status
            FROM `facility-booking` fb
            JOIN facility_rates fr ON fb.facility_id = fr.id
            WHERE fb.user_id = :user_id
            ORDER BY fb.date DESC, fb.start_time DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
