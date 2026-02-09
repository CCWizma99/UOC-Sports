<?php

class SportManagerController {
    public function index() {
        $userId = $_SESSION['user_id'] ?? null;
        $practiceModel = new SportPracticeSession();
        $competitionModel = new SportCompetition();
        
        // Get the sport ID from URL parameter or from managed sport
        $selectedSportId = $_GET['sport'] ?? null;
        $managedSportId = null;
        
        if ($userId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT sport_id FROM manager_sport WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $managedSportId = $stmt->fetchColumn();
        }
        
        // Use URL parameter if provided, otherwise use managed sport
        $filterSportId = $selectedSportId ?: $managedSportId;
        
        // Get current month
        $currentMonth = date('m');
        
        // Get today's practice sessions filtered by selected sport
        $todaySessions = $practiceModel->getTodaySessions($filterSportId);
        
        // Get competitions for current month filtered by selected sport
        $upcomingCompetitions = $competitionModel->getCompetitionsByMonth($filterSportId, $currentMonth, 10);
        
        // Get all sports for the dropdown
        $db = Database::getConnection();
        $stmt = $db->query("SELECT sport_id, sport_name FROM sport ORDER BY sport_name");
        $sports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug log - MORE DETAILED
        error_log("========================================");
        error_log("Sport Manager Dashboard INDEX");
        error_log("User ID: $userId");
        error_log("URL Parameter sport: " . ($selectedSportId ?? 'NULL'));
        error_log("Managed Sport ID from DB: " . ($managedSportId ?? 'NULL'));
        error_log("FINAL Filter Sport ID: " . ($filterSportId ?? 'NULL'));
        error_log("Today's Sessions Count: " . count($todaySessions));
        error_log("Competitions Count: " . count($upcomingCompetitions));
        if (!empty($upcomingCompetitions)) {
            error_log("First competition: " . json_encode($upcomingCompetitions[0]));
        }
        error_log("========================================");
        
        view('sports-manager/index', [
            'todaySessions' => $todaySessions,
            'upcomingCompetitions' => $upcomingCompetitions,
            'sports' => $sports,
            'managedSportId' => $filterSportId
        ]);
    }

    public function schedule() {
        view('sports-manager/schedule');
    }

    public function schedules() {
        view('sports-manager/schedules');
    }

    public function expenses() {
        $expenseModel = new SportExpense(Database::getConnection());
        
        // Get selected sport from URL parameter
        $selectedSportId = $_GET['sport'] ?? null;
        
        // If sport is selected, get sport name and filter expenses by that sport
        $filters = [];
        if ($selectedSportId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT sport_name FROM sport WHERE sport_id = ?");
            $stmt->execute([$selectedSportId]);
            $sportName = $stmt->fetchColumn();
            
            if ($sportName) {
                $filters['sport'] = $sportName;
            }
        }
        
        $expenses = $expenseModel->getAll($filters);
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
        require_once __DIR__ . '/../models/SportAchievements.php';
        
        $userId = $_SESSION['user_id'] ?? null;
        $managedSports = [];
        $selectedSportId = $_GET['sport'] ?? null;
        $selectedSportName = 'Unknown Sport';
        $rankings = [];
        
        if ($userId) {
            $db = Database::getConnection();
            
            // Get all sports this user manages from manager_sport table
            $stmt = $db->prepare("SELECT s.sport_id, s.sport_name 
                                  FROM manager_sport ms
                                  JOIN sport s ON ms.sport_id = s.sport_id
                                  WHERE ms.user_id = ?");
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
            
            // Fetch student rankings for the selected sport
            if ($selectedSportId) {
                try {
                    $achievementsModel = new SportAchievements();
                    $rankings = $achievementsModel->getStudentRankings($selectedSportId, 100);
                } catch (Exception $e) {
                    error_log("Error fetching rankings: " . $e->getMessage());
                }
            }
        }
        
        view('sports-manager/team', [
            'sportName' => $selectedSportName,
            'sportId' => $selectedSportId,
            'managedSports' => $managedSports,
            'rankings' => $rankings
        ]);
    }
}