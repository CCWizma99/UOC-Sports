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

    public function expenses() {
        view('sports-manager/expenses');
    }

    public function messages() {
        view('sports-manager/message');
    }
    
    public function practicesessions() {
        view('sports-manager/practicesessions');
    }

    public function competitions() {
        view('sports-manager/competitions');
    }

    public function addPractice() {
        view('sports-manager/add-practice');
    }
}