<?php

class StudentController {
    public function index() {
        view('student/student-portal');
    }
    public function profile() {
        view('student/student-profile');
    }
}