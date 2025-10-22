<?php

class sportManagerController {
    public function index() {
        view('sports-manager/index');
    }

    public function schedule() {
        view('sports-manager/schedule');
    }

    public function schedules() {
        view('sports-manager/schedules');
    }

    public function addExpenses() {
        view('sports-manager/add-expenses');
    }

    public function messages() {
        view('sports-manager/message');
    }
    
}