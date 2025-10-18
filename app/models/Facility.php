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
}
