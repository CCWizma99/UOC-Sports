<?php
require_once __DIR__ . '/../../services/EmailService.php';

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
    
            if ($booking_id === 'TAKEN') {
                echo json_encode([
                    'success' => false,
                    'message' => 'This slot was just booked by someone else! Please choose another slot.'
                ]);
            } else if ($booking_id) {
                // Send confirmation email
                try {
                    $userModel = new User();
                    $user = $userModel->getUserProfile($user_id);
                    $facility = $model->getFacilityById($facility_id);
                    $slotDetails = $model->getSlotDetails($slot); // Assuming this method exists or I'll add it

                    if ($user) {
                        $emailService = new EmailService();
                        $emailService->sendBookingConfirmationEmail($user['email'], $user['fname'], [
                            'booking_id' => $booking_id,
                            'facility_name' => $facility['name'],
                            'date' => $date,
                            'start_time' => $slotDetails['start_time'] ?? 'N/A',
                            'end_time' => $slotDetails['end_time'] ?? 'N/A'
                        ]);
                    }
                } catch (Exception $e) {
                    error_log("Failed to send booking email: " . $e->getMessage());
                }

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

    /**
     * Get reservations for a specific month
     */
    public function getMonthlyBookings() {
        header('Content-Type: application/json');
        
        try {
            $month = $_GET['month'] ?? date('m');
            $year = $_GET['year'] ?? date('Y');
            
            $model = new Facility();
            $bookings = $model->getMonthlyBookings($month, $year);
            
            echo json_encode([
                'success' => true,
                'data' => $bookings
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching monthly bookings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle manual payment slip upload
     */
    public function submitPaymentSlip() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in to submit payment proof.']);
            return;
        }

        try {
            $booking_id = $_POST['booking_id'] ?? '';
            $user_id = $_SESSION['user_id'];

            if (empty($booking_id)) {
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID.']);
                return;
            }

            $model = new Facility();
            
            // SECURITY PATCH: Verify Ownership
            $bookingDetails = $model->getReservationDetails($booking_id);
            if (!$bookingDetails || $bookingDetails['user_id'] !== $user_id) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this booking.']);
                return;
            }

            // Check if file was uploaded
            if (!isset($_FILES['paymentSlip']) || $_FILES['paymentSlip']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error.']);
                return;
            }

            $file = $_FILES['paymentSlip'];
            
            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
                return;
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            $fileType = $file['type'];
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and PDF files are allowed.']);
                return;
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = "SLIP_" . $booking_id . "_" . time() . "." . $extension;
            $uploadDir = __DIR__ . '/../../internal/payment_slips/';
            $uploadPath = $uploadDir . $newFilename;

            // Ensure directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $model = new Facility();
                $success = $model->updatePaymentSlip($booking_id, $newFilename);

                if ($success) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Payment proof submitted successfully! Admin will verify and confirm your booking.',
                        'filename' => $newFilename
                    ]);
                } else {
                    // Cleanup file if DB update fails
                    unlink($uploadPath);
                    echo json_encode(['success' => false, 'message' => 'Failed to update database record.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin: Manually verify payment slip
     */
    public function verifyPayment() {
        header('Content-Type: application/json');

        // Security Check: Ensure user is logged in and is an ADMIN
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
            return;
        }

        try {
            $booking_id = $_POST['booking_id'] ?? '';
            
            if (empty($booking_id)) {
                echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
                return;
            }

            $model = new Facility();
            
            // Generate a manual verification ID
            $payment_id = "VERIFIED_BY_ADMIN_" . time();
            
            $success = $model->updatePaymentStatus($booking_id, 'COMPLETE', $payment_id);

            if ($success) {
                // Send success email
                try {
                    $bookingDetails = $model->getReservationDetails($booking_id);
                    if ($bookingDetails) {
                        $userModel = new User();
                        $user = $userModel->getUserProfile($bookingDetails['user_id']);
                        if ($user) {
                            $emailService = new EmailService();
                            $emailService->sendPaymentUpdateEmail($user['email'], $user['fname'], 'BOOKED', $booking_id);
                        }
                    }
                } catch (Exception $e) {
                    error_log("Failed to send payment verification email: " . $e->getMessage());
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Payment verified successfully. Booking status updated to COMPLETE.',
                    'payment_id' => $payment_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update payment status in database.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin: Flag a problematic payment slip
     */
    public function flagBooking() {
        header('Content-Type: application/json');

        // Security Check: Ensure user is logged in and is an ADMIN
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
            return;
        }

        try {
            $booking_id = $_POST['booking_id'] ?? '';
            $reason = $_POST['reason'] ?? 'Problem with the payment slip.';
            
            if (empty($booking_id)) {
                echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
                return;
            }

            $model = new Facility();
            $success = $model->flagBooking($booking_id, $reason);

            if ($success) {
                // Send flagging email
                try {
                    $bookingDetails = $model->getReservationDetails($booking_id);
                    if ($bookingDetails) {
                        $userModel = new User();
                        $user = $userModel->getUserProfile($bookingDetails['user_id']);
                        if ($user) {
                            $emailService = new EmailService();
                            $emailService->sendPaymentUpdateEmail($user['email'], $user['fname'], 'FLAGGED', $booking_id);
                        }
                    }
                } catch (Exception $e) {
                    error_log("Failed to send payment flagging email: " . $e->getMessage());
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Booking flagged successfully. User will have 3 days to fix the issue.',
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to flag the booking.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
}