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
    
            $success = $model->createBooking([
                'user_id' => $user_id,
                'facility_id' => $facility_id,
                'date' => $date,
                'slot' => $slot,
                'purpose' => $purpose
            ]);
    
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Reservation created successfully.'
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
            
            // Return only available slots
            $availableSlots = array_filter($data['slots'], function($slot) {
                return !$slot['taken'];
            });
            
            // Re-index array
            echo json_encode(array_values($availableSlots));
            
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    /**
     * NEW METHOD: Get chart data for reservation visualization
     */
    public function getReservationChart() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['facility_id'])) {
            echo json_encode([]);
            return;
        }
        
        try {
            $model = new Facility();
            $facility_id = $_GET['facility_id'];
            
            // Get next 7 days of data
            $chartData = $model->getReservationChartData($facility_id, 7);
            
            echo json_encode($chartData);
            
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }
}