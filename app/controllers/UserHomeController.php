<?php

class UserHomeController {
    public function index() {
        view('user-home');
    }
    public function news() {
        view('general/news');
    }
    public function facilityReservation() {
        view('general/facility-reservation');
    }
    public function contactUs() {
        view('general/contact-us');
    }
}
