<?php
require_once '../app/models/Schedule.php';

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
    
     $scheduleModel = new Schedule();

        // Handle Create
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // sanitize or validate as needed
        $facility = $_POST['facility'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $description = $_POST['description'] ?? '';

        $scheduleModel->create($facility, $date, $time, $description);
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

        // Handle Update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = $_POST['id'] ?? null;
            $facility = $_POST['facility'] ?? '';
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $description = $_POST['description'] ?? '';

        if ($id) {
            $scheduleModel->update($id, $facility, $date, $time, $description);
        }
        header("Location: /uoc-sports/public/captain/schedule-practice");
        exit;
    }

        // Handle Delete
        if (isset($_GET['delete'])) {
            $scheduleModel->delete($_GET['delete']);
            header("Location: /uoc-sports/public/captain/schedule-practice");
            exit;
        }

        // Fetch all schedules for display
        $schedules = $scheduleModel->getAll();
        view('captain/schedule-practice', ['schedules' => $schedules]);
    }   

    public function Communication() {
        view('captain/communication');
    }
}