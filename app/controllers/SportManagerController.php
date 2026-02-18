<?php

class SportManagerController {
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
        $expenseModel = new SportExpense(Database::getConnection());
        $expenses = $expenseModel->getAll();
        view('sports-manager/expenses', ['expenses' => $expenses]);
    }

    public function messages() {
        view('sports-manager/message');
    }
    
    public function practicesessions() {
        view('sports-manager/practicesessions');
    }

    public function competitions() {
        // TODO: Fetch actual competition data from database
        $schedules = []; // Empty for now, will show dummy data
        view('sports-manager/competitions', ['Schedules' => $schedules]);
    }

    public function addPractice() {
        view('sports-manager/add-practice');
    }

    public function addParticipants() {
        view('sports-manager/add-participants');
    }

    public function addExpense() {
        view('sports-manager/add-expense');
    }

    public function team() {
        $userId = $_SESSION['user_id'] ?? null;
        $managedSports = [];
        $selectedSportId = $_GET['sport'] ?? null;
        $selectedSportName = 'Unknown Sport';
        
        if ($userId) {
            $db = Database::getConnection();
            
            // Get all sports this user manages
            $stmt = $db->prepare("SELECT sport_id, sport_name FROM sport WHERE manager_id = ?");
            $stmt->execute([$userId]);
            $managedSports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If no sport selected or invalid, use first managed sport
            if (!empty($managedSports)) {
                if (!$selectedSportId) {
                    $selectedSportId = $managedSports[0]['sport_id'];
                    $selectedSportName = $managedSports[0]['sport_name'];
                } else {
                    // Validate selected sport is in managed list
                    foreach ($managedSports as $sport) {
                        if ($sport['sport_id'] == $selectedSportId) { // Use == for comparison as $selectedSportId might be string
                            $selectedSportName = $sport['sport_name'];
                            break;
                        }
                    }
                }
            }
        }
        
        view('sports-manager/team', [
            'sportName' => $selectedSportName,
            'sportId' => $selectedSportId,
            'managedSports' => $managedSports
        ]);
    }
}