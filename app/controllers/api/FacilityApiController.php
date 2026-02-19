<?php

class FacilityApiController {

    public function createBooking() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'redirect' => '/uoc-sports/public/sign-in'
            ]);
            return;
        }
    
        try {
            $model = new Facility();
    
            $facility_id = $_POST['facility_id'];
            $date = $_POST['date'];
            $slot = $_POST['slot_id'];
            $purpose = trim($_POST['purpose']);
            $user_id = $_SESSION['user_id'];
    
            // Check slot availability
            if ($model->isSlotTaken($facility_id, $date, $slot)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'This slot is already booked.'
                ]);
                return;
            }
    
            $booking_id = $model->createBooking([
                'user_id' => $user_id,
                'facility_id' => $facility_id,
                'date' => $date,
                'slot' => $slot,
                'purpose' => $purpose
            ]);
    
            if ($booking_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Reservation created successfully. Redirecting to payment...',
                    'booking_id' => $booking_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking failed.'
                ]);
            }
    
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function viewMyReservations() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['data' => []]);
            return;
        }
    
        try {
            $model = new Facility();
            $user_id = $_SESSION['user_id'];
    
            $data = $model->getMyReservations($user_id);
    
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancelFacilityReservation() {
        header('Content-Type: text/plain');

        if (!isset($_SESSION['user_id'])) {
            echo "Please log in to cancel reservations.";
            return;
        }

        try {
            $booking_id = $_POST['booking_id'] ?? '';
            
            if (empty($booking_id)) {
                echo "Invalid booking ID.";
                return;
            }

            $model = new Facility();
            $success = $model->cancelBooking($booking_id, $_SESSION['user_id']);
            
            if ($success) {
                echo "Reservation cancelled successfully.";
            } else {
                echo "Unable to cancel reservation.";
            }
    
        } catch (Exception $e) {
            echo "Cancel failed: " . $e->getMessage();
        }
    }

    public function getReservedSlots() {
        header('Content-Type: application/json');
    
        if (!isset($_GET['facility_id']) || !isset($_GET['date'])) {
            echo json_encode([]);
            return;
        }
    
        try {
            $model = new Facility();
            $data = $model->getReservedSlots($_GET['facility_id'], $_GET['date']);
            
            // Return ALL slots (taken or not) so frontend can render the full chart/list
            // The frontend will handle disabling/hiding taken slots if needed
            echo json_encode($data['slots']);
            
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    /**
     * NEW METHOD: Get chart data for reservation visualization
     */
    public function getReservationChart() {
        // Start output buffering to prevent any stray output from breaking JSON
        ob_start();
        
        header('Content-Type: application/json');
        
        if (!isset($_GET['facility_id'])) {
            ob_end_clean();
            echo json_encode([]);
            return;
        }
        
        try {
            $model = new Facility();
            $facility_id = $_GET['facility_id'];
            
            // Default to current month if no date provided
            $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
            
            // Calculate First and Last Day of the Month for the given date
            $startDate = date('Y-m-01', strtotime($date));
            $endDate = date('Y-m-t', strtotime($date));
            
            // Get data for the whole month
            $chartData = $model->getReservationChartData($facility_id, $startDate, $endDate);
            
            // Clean any stray output and send JSON
            ob_end_clean();
            echo json_encode([
                'chart_data' => $chartData,
                'parallel_booking' => false // This will be handled by the specialized heartbeat now
            ]);
            
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode([
                'chart_data' => [],
                'parallel_booking' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * HEARTBEAT: Real-time concurrency check with slot-level tracking
     */
    public function heartbeat() {
        header('Content-Type: application/json');

        if (!isset($_POST['facility_id'])) {
            echo json_encode(['success' => false, 'message' => 'Facility ID missing']);
            return;
        }

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $model = new Facility();
            $facility_id = $_POST['facility_id'];
            $session_id = session_id();
            $date = isset($_POST['date']) ? $_POST['date'] : null;
            $slot = isset($_POST['slot']) ? $_POST['slot'] : null;

            // Update heartbeat with date/slot selection
            $model->updateHeartbeat($session_id, $facility_id, $date, $slot);

            // Check if others are booking
            $isParallel = $model->checkParallelStatus($session_id, $facility_id);

            // Get slot-level interest from other users
            $slotInterest = $model->getSlotInterest($session_id, $facility_id);

            // Get confirmed bookings
            $bookedSlots = $model->getBookedSlots($facility_id);

            echo json_encode([
                'success' => true,
                'parallel_booking' => $isParallel,
                'slot_interest' => (object)$slotInterest,
                'booked_slots' => (object)$bookedSlots
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get reservations for this week and next week
     */
    public function getWeekReservations() {
        header('Content-Type: application/json');
        
        try {
            $model = new Facility();
            $reservations = $model->getThisAndNextWeekReservations();
            
            echo json_encode([
                'status' => 'success',
                'data' => $reservations
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'data' => [],
                'message' => $e->getMessage()
            ]);
        }
    }
}