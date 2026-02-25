<?php

class ExecutiveController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Only ADMIN and EXECUTIVE users
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'] ?? '', ['ADMIN', 'EXECUTIVE'])) {
            header('Location: /uoc-sports/public/sign-in');
            exit;
        }
    }

    public function index() {
        view('admin/executive-dashboard', ['title' => 'Executive Dashboard']);
    }
}
