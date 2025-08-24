<?php

class CaptainController {
    public function index() {
        view('captain/index');
    }
    public function MarkAttendance() {
        view('captain/mark-attendance');
    }
    public function AddMembers() {
        view('captain/add-members');
    }
    public function SchedulePractice() {
        view('captain/schedule-practice');
    }
    public function Communication() {
        view('captain/communication');
    }
}