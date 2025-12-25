<?php

class AdminHomeController {
    public function index() {
        $budgetModel = new Budget();
        $budgetSummary = $budgetModel->getBudgetSummary();
        
        view('admin-home', [
            'title' => 'Home',
            'budget_summary' => $budgetSummary
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
