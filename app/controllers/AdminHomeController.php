<?php

class AdminHomeController {
    public function index() {
        view('admin-home', ['title' => 'Home']);
    }
    public function users() {
        $sportModel = new Sport();
        $sports = $sportModel->getSports();
        view('admin/users', [
            'title' => 'Users',
            'sport_data' => $sports  // Pass the $data variable to the view
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
        view('admin/players', ['title' => 'Users']);
    }
    public function equipments() {
        view('admin/equipments', ['title' => 'Users']);
    }
    public function events() {
        view('admin/events', ['title' => 'Users']);
    }
    public function teams() {
        view('admin/teams', ['title' => 'Users']);
    }
    public function teamDetails() {
        view('admin/team-details', ['title' => 'Team Details']);
    }
    public function budget() {
        view('admin/budget', ['title' => 'Users']);
    }
    public function news() {
        view('admin/news', ['title' => 'Users']);
    }
    public function inquiry() {
        view('admin/inquiry', ['title' => 'Users']);
    }
    public function reservationDetails() {
        view('admin/reservation', ['title' => 'Reservation Details']);
    }
}
