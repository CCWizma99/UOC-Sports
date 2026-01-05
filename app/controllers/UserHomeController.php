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

        // Calculate prices for bookings (simplified - you may need to enhance this)
        foreach ($bookings as &$booking) {
            // Add a default price - in a real scenario, you'd calculate this based on facility rates
            $booking['price'] = 2000; // Default price
            
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
        view('general/payment');
    }
     


}
