<?php

class ChartController {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getActivityAnalysis() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // internal user ID vs student ID check
        // Assuming user_id is the link, but some tables use student_id.
        // I need to resolve student_id if needed.
        // Let's first get student_id from user table if it exists
        $studentId = $this->getStudentId($userId);

        // Get last 6 months
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('Y-m', strtotime("-$i months"));
        }

        $practiceData = $this->getPracticeData($userId, $studentId, $months);
        $bookingData = $this->getBookingData($userId, $months);
        $equipmentData = $this->getEquipmentData($studentId, $months);

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => array_map(function($m) { return date('M Y', strtotime($m . '-01')); }, $months),
            'datasets' => [
                [
                    'label' => 'Practice Participation',
                    'data' => $practiceData,
                    'borderColor' => '#4b0082',
                    'backgroundColor' => 'rgba(75, 0, 130, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Facility Bookings',
                    'data' => $bookingData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Equipment Reservations',
                    'data' => $equipmentData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4
                ]
            ]
        ]);
    }

    private function getStudentId($userId) {
        $stmt = $this->db->prepare("SELECT student_id FROM user WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['student_id'] : $userId; // Fallback or handle null
    }

    private function getPracticeData($userId, $studentId, $months) {
        $data = [];
        // Based on Attendance.php, a.user_id joins with u.user_id (User table PK)
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(ps.session_date, '%Y-%m') as month, COUNT(*) as count
            FROM attendance a
            JOIN practice_sessions ps ON a.practice_id = ps.id
            WHERE a.user_id = :uid AND a.status = 'PRESENT'
            AND ps.session_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
        ");
        $stmt->execute(['uid' => $userId]); 
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($months as $m) {
            $data[] = $results[$m] ?? 0;
        }
        return $data;
    }

    private function getBookingData($userId, $months) {
        // Table is `facility-booking` based on Facility.php
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as count
            FROM `facility-booking`
            WHERE user_id = :uid
            AND date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
        ");
        $stmt->execute(['uid' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($months as $m) {
            $data[] = $results[$m] ?? 0;
        }
        return $data;
    }

    private function getEquipmentData($studentId, $months) {
         // Table `equipment-requests`. Column `student_id`.
        $stmt = $this->db->prepare("
            SELECT DATE_FORMAT(request_date, '%Y-%m') as month, COUNT(*) as count
            FROM `equipment-requests`
            WHERE student_id = :sid
            AND request_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
        ");
        $stmt->execute(['sid' => $studentId]);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($months as $m) {
            $data[] = $results[$m] ?? 0;
        }
        return $data;
    }
}
