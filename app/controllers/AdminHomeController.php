<?php

class AdminHomeController {
    public function index() {
        $budgetModel = new Budget();
        $budgetSummary = $budgetModel->getBudgetSummary();
        
        // Fetch dashboard statistics
        $userModel = new User();
        $facilityModel = new Facility();
        $tournamentModel = new Tournament();
        
        $dashboardStats = [
            'total_users' => $userModel->getTotalUsersCount(),
            'pending_reservations' => $facilityModel->getPendingReservationsCount(),
            'active_events' => $tournamentModel->getActiveEventsCount()
        ];
        
        view('admin-home', [
            'title' => 'Home',
            'budget_summary' => $budgetSummary,
            'dashboard_stats' => $dashboardStats
        ]);
    }
    public function users() {
        $sportModel = new Sport();
        $sports = $sportModel->getSports();
        
        $facultyModel = new Faculty();
        $faculties = $facultyModel->getAllFaculties();
        
        view('admin/users', [
            'title' => 'Users',
            'sport_data' => $sports,
            'faculty_data' => $faculties
        ]);
    }
    public function userProfile() {
        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            header('Location: ./admin-users');
            exit;
        }
        
        $userModel = new User();
        $userData = $userModel->getUserProfile($userId);
        
        // Get enrolled sports if user is a student or captain
        $enrolledSports = [];
        if ($userData && in_array($userData['type'], ['STUDENT', 'CAPTAIN'])) {
            $enrolledSports = $userModel->getEnrolledSports($userId);
        }
        
        view('admin/user-profile', [
            'title' => 'User Profile',
            'user_data' => $userData,
            'enrolled_sports' => $enrolledSports
        ]);
    }
    public function reservations() {
        $facilityModel = new Facility();
        $reservations = $facilityModel->getThisAndNextWeekReservations();
        view('admin/reservations', [
            'title' => 'Reservations',
            'reservations' => $reservations
        ]);
    }
    public function players() {
        view('admin/players', ['title' => 'Player Records']);
    }
    public function equipments() {
        view('admin/equipments', ['title' => 'Equipment Inventory']);
    }
    public function events() {
        view('admin/events', ['title' => 'Sports Events']);
    }
    public function teams() {
        view('admin/teams', ['title' => 'UOC Teams']);
    }
    public function teamDetails() {
        $sportId = $_GET['sport_id'] ?? null;
        $sportData = null;
        
        if ($sportId) {
            $sportModel = new Sport();
            $sportData = $sportModel->getSportWithStaff($sportId);
        }
        
        view('admin/team-details', [
            'title' => 'Team Details',
            'sport_data' => $sportData
        ]);
    }
    public function budget() {
        view('admin/budget', ['title' => 'Budget']);
    }
    public function news() {
        view('admin/news', ['title' => 'News Feed']);
    }
    public function inquiry() {
        view('admin/inquiry', ['title' => 'Inquiries']);
    }
    public function reservationDetails() {
        view('admin/reservation', ['title' => 'Reservation Details']);
    }
}
