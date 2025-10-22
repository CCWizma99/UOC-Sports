<?php

class UserHomeController {
    public function index() {
        view('user-home');
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
        $reservationModel = new Facility();
        $reservations = [];

        // Load reservations only if logged in
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $reservations = $reservationModel->getReservationsByUser($userId);
        }

        view('general/facility-reservation', [
            'reservations' => $reservations
        ]);
    }
    public function contactUs() {
        view('general/contact-us');
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
}
