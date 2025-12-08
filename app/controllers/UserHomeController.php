<?php

class UserHomeController {
    public function index() {
        view('home');
    }
    public function news() {
        $postModel = new Post();
        $lostFoundModel = new Post();
    
        $recentNews = $postModel->getRecentPosts(6);
        $recentLostFound = $lostFoundModel->getRecentCases(6);
    
        view('general/news', [
            'recentNews' => $recentNews,
            'recentLostFound' => $recentLostFound
        ]);
    }
    public function facilityReservation() {
        view('general/facility-reservation');
    }
    public function contactUs() {
        view('general/contact-us');
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /uoc-sports/public/sign-in");
            exit();
        }
    
        $db = Database::getConnection();
        $user_id = $_SESSION['user_id'];
    
        // Default: load the logged-in user's details
        $stmt = $db->prepare("
            SELECT user_id, CONCAT(fname, ' ', lname) AS `name`, email, type 
            FROM user 
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    
        $isAdminViewingStudent = false;
    
        // If admin is viewing another user's profile
        if (isset($_GET['view'])) {
            $view_id = $_GET['view'];
    
            $stmt2 = $db->prepare("
                SELECT user_id, CONCAT(fname, ' ', lname) AS `name`, email, type 
                FROM user 
                WHERE user_id = :view_id
            ");
            $stmt2->execute(['view_id' => $view_id]);
            $viewUser = $stmt2->fetch(PDO::FETCH_ASSOC);
    
            if ($viewUser) {
                $user = $viewUser;
                $isAdminViewingStudent = 
                    (($_SESSION['role'] ?? '') === 'ADMIN') && 
                    ($user['type'] === 'STUDENT');
            }
        }
    
        // Pass data safely to the view
        view('general/profile', [
            'user' => $user,
            'isAdminViewingStudent' => $isAdminViewingStudent
        ]);
    }
    
    
    public function getFaculties() {
        header('Content-Type: application/json');
        try {
            $user = new User();
            $faculties = $user->getFaculties();

            echo json_encode(['status' => 'success', 'faculties' => $faculties]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
