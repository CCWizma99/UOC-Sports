<?php

class ExecutiveController {
    
    public function __construct() {
        // No authentication check for now - add as needed
    }

    public function index() {
        view('executive/dashboard', ['title' => 'Executive Dashboard']);
    }
}
