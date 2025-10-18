<?php

class FacilityApiController {
    public function createBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $facility_id = $_POST['facility_id'] ?? '';
        $date = $_POST['date'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        $purpose = $_POST['purpose'] ?? '';

        if (!$facility_id || !$date || !$start_time || !$end_time || !$purpose) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        $model = new Facility();
        $booking_id = uniqid('BK');

        $success = $model->createBooking($booking_id, $user_id, $facility_id, $date, $start_time, $end_time, $purpose);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Facility reserved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reserve facility. Try again.']);
        }
    }
}
