<?php

class CoachController {
    public function TeamSchedules() {
        view('coach/team-schedules');
    }
     public function CoachCommunicate() {
        view('coach/coach-communicate');
    }
     public function ReportInjury() {
        view('coach/report-injury');
    }
}