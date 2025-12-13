<?php

class CoachController {
    public function TeamSchedules() {
        view('coach/team-schedules');
    }
    public function ReportInjury() {
        view('coach/injuries');
    }
    public function CoachCommunicate() {
        view('coach/communications');
    }
}