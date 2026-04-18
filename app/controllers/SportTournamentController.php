<?php

class SportTournamentController {
    
    /**
     * Display list of tournaments
     */
    public function index() {
        $model = new TournamentParticipant();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Get selected sport from URL parameter
        $selectedSportId = $_GET['sport'] ?? null;
        
        // If no sport selected, get the first managed sport as default
        if (!$selectedSportId && $userId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT s.sport_id FROM manager_sport ms
                                  JOIN sport s ON ms.sport_id = s.sport_id
                                  WHERE ms.user_id = ?
                                  ORDER BY s.sport_name LIMIT 1");
            $stmt->execute([$userId]);
            $selectedSportId = $stmt->fetchColumn();
        }
        
        // Filter by sport if selected
        $filters = [];
        if ($selectedSportId) {
            $filters['sport_id'] = $selectedSportId;
        }
        
        $tournaments = $model->getAll($filters);
        view('sports-manager/tournaments', [
            'tournaments' => $tournaments,
            'selectedSportId' => $selectedSportId
        ]);
    }

    /**
     * Show add participants form
     */
    public function create() {
        $model = new TournamentParticipant();
        $sports = $model->getAllSports();
        $selectedSport = $_GET['sport'] ?? null;
        $tournamentId = $_GET['tournament_id'] ?? null;
        
        $tournament = null;
        $students = [];
        $existingParticipants = [];
        
        if ($tournamentId) {
            $tournament = $model->getById($tournamentId);
            if ($tournament) {
                if (!$selectedSport) {
                    $selectedSport = $tournament['sport_id'];
                }
                $students = $model->getStudentsBySport($tournament['sport_id']);
                $participants = $model->getParticipants($tournamentId);
                $existingParticipants = array_column($participants, 'user_id');
            }
        } elseif ($selectedSport) {
            $students = $model->getStudentsBySport($selectedSport);
        }
        
        // Get all tournaments for the sport to allow switching in the form
        $tournaments = $selectedSport ? $model->getAll(['sport_id' => $selectedSport]) : [];
        
        view('sports-manager/add-participants', [
            'sports' => $sports, 
            'selectedSport' => $selectedSport,
            'tournament' => $tournament,
            'tournaments' => $tournaments,
            'students' => $students,
            'existingParticipants' => $existingParticipants
        ]);
    }

    /**
     * Save tournament participants
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            header('Location: /uoc-sports/public/sport-manager/tournaments');
            exit();
        }

        try {
            $model = new TournamentParticipant();
            $tournamentId = $_POST['tournament_id'] ?? null;
            $selectedSport = $_POST['sport_id'] ?? null;
            $competitionName = $_POST['competitionName'] ?? '';
            $managerId = $_SESSION['user_id'];

            if (!$tournamentId) {
                $_SESSION['error_message'] = 'Please select a tournament';
                header('Location: /uoc-sports/public/sport-manager/add-participants?sport=' . urlencode($selectedSport));
                exit();
            }

            // Handle file upload if provided
            $filename = null;
            if (isset($_FILES['participantsFile']) && $_FILES['participantsFile']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['participantsFile']);
                if ($uploadResult['success']) {
                    $filename = $uploadResult['filename'];
                } else {
                    $_SESSION['error_message'] = $uploadResult['message'];
                    header('Location: /uoc-sports/public/sport-manager/add-participants?tournament_id=' . urlencode($tournamentId));
                    exit();
                }
            }

            // Get selected participants (User IDs)
            $selectedUserIds = $_POST['selectedParticipants'] ?? [];

            // Sync participants with the tournament
            $result = $model->syncParticipants($tournamentId, $selectedUserIds, $managerId);

            // If you have a place to store competitionName and filename, do it here. 
            // For now, we'll just log or assume the sync is the primary goal.
            // If the database has columns for these in tournament_participants or a separate table, 
            // the model should be updated. Assuming sync is the core functionality requested.

            if ($result) {
                $_SESSION['success_message'] = 'Tournament participants updated successfully!';
                header('Location: /uoc-sports/public/sport-manager/tournaments?sport=' . urlencode($selectedSport));
            } else {
                $_SESSION['error_message'] = 'Failed to update tournament participants';
                header('Location: /uoc-sports/public/sport-manager/add-participants?tournament_id=' . urlencode($tournamentId));
            }

        } catch (Exception $e) {
            error_log("Error saving tournament participants: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            header('Location: /uoc-sports/public/sport-manager/tournaments');
        }
        exit();
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file) {
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }

        $allowedTypes = ['application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only PDF is allowed'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'tournament_reg_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadDir = '../app/internal/Tournament_Registrations/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return ['success' => true, 'filename' => $filename];
        }
        return ['success' => false, 'message' => 'Failed to save uploaded file'];
    }

}
