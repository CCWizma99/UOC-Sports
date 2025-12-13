<?php

class RegistrarController {
    public function RegistrarDashboard() {
        view('registrar/registrar-dashboard');
    }

    public function VerifyStudents() {
        view('registrar/verify-students');
    }

     public function VerifyStaff() {
        view('registrar/verify-staff');
    }

     public function VerifyBookings() {
        view('registrar/verify-bookings');
    }

}