<?php

class UserHomeController {
    public function index() {
        view('home');
    }
    public function news() {
        $postModel = new Post();
        $lostFoundModel = new Post();
    
        $recentNews = $postModel->getRecentPosts(6);
        $recentLostFound = $lostFoundModel->getRecentCases(6);
    
        view('general/news', [
            'recentNews' => $recentNews,
            'recentLostFound' => $recentLostFound
        ]);
    }
    public function facilityReservation() {
        view('general/facility-reservation');
    }
    public function contactUs() {
        view('general/contact-us');
    }

    public function services() {
        view('general/services');
    }

    public function stories() {
        view('general/stories');
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
        $userModel = new User();
        $facilityModel = new Facility();
    
        // Get complete user profile
        $user = $userModel->getUserProfile($user_id);
        
        if (!$user) {
            // User not found, redirect to login
            header("Location: /uoc-sports/public/sign-in");
            exit();
        }

        // Get profile image URL
        $profileImage = $userModel->getProfileImage($user_id);
        if ($profileImage) {
            $user['profile_image_url'] = '/uoc-sports/app/internal/profile_img/' . $profileImage . '?t=' . time();
        } else {
            // Default avatar
            $user['profile_image_url'] = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=400';
        }

        // Get enrolled sports (only for students)
        $enrolledSports = [];
        if ($user['type'] === 'STUDENT' || $user['type'] === 'CAPTAIN') {
            $enrolledSports = $userModel->getEnrolledSports($user_id);
        }

        // Get facility bookings
        $bookings = $facilityModel->getMyReservations($user_id);

        // Calculate prices for bookings based on facility rates
        foreach ($bookings as &$booking) {
            // Calculate price based on facility rates, slot type, and booking date
            $booking['price'] = $facilityModel->calculateBookingRate($booking);
            
            // Determine booking status for display
            $bookingDate = strtotime($booking['date']);
            $today = strtotime(date('Y-m-d'));
            
            if ($bookingDate < $today) {
                $booking['display_status'] = 'PAST';
            } else if ($booking['payment_status'] === 'INCOMPLETE') {
                $booking['display_status'] = 'PENDING';
            } else {
                $booking['display_status'] = 'PAID';
            }
        }
    
        // Pass data to the view
        view('general/profile', [
            'userDetails' => $user,
            'enrolledSports' => $enrolledSports,
            'bookings' => $bookings
        ]);
    }
    
    public function getFaculties() {
        header('Content-Type: application/json');
        try {
            $user = new User();
            $faculties = $user->getFaculties();

            echo json_encode(['status' => 'success', 'faculties' => $faculties]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function payment() {
        if (!isset($_GET['booking_id'])) {
            // Redirect back if no booking ID provided
            header("Location: /uoc-sports/public/");
            exit();
        }

        $booking_id = $_GET['booking_id'];
        $facilityModel = new Facility();
        
        // Fetch booking details
        $booking = $facilityModel->getReservationDetails($booking_id);

        if (!$booking) {
            // Handle invalid booking ID
            echo "Invalid Booking ID";
            exit();
        }

        // Calculate the amount based on facility rates, slot type, and booking date
        $booking['amount'] = $facilityModel->calculateBookingRate($booking);

        // Generate PayHere hash for secure payment
        $merchant_id = PAYHERE_MERCHANT_ID;
        $merchant_secret = PAYHERE_MERCHANT_SECRET;
        $order_id = $booking['booking_id'];
        $amount = number_format($booking['amount'], 2, '.', '');
        $currency = 'LKR';

        // PayHere hash formula: strtoupper(md5(merchant_id + order_id + amount + currency + strtoupper(md5(merchant_secret))))
        $hash = strtoupper(
            md5(
                $merchant_id . 
                $order_id . 
                $amount . 
                $currency . 
                strtoupper(md5($merchant_secret))
            )
        );

        // Determine PayHere endpoint (sandbox or live)
        $payhere_url = (PAYHERE_SANDBOX === 'true') 
            ? 'https://sandbox.payhere.lk/pay/checkout' 
            : 'https://www.payhere.lk/pay/checkout';

        // Pass data to view
        view('general/payment', [
            'booking' => $booking,
            'payhere' => [
                'merchant_id' => $merchant_id,
                'hash' => $hash,
                'amount_formatted' => $amount,
                'url' => $payhere_url
            ]
        ]);
    }
     


}
