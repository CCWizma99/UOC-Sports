<?php

class SportCompetitionsController {
    
    /**
     * Display list of competitions
     */
    public function index() {
        $model = new SportCompetition();
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
        
        $competitions = $model->getAll($filters);
        view('sports-manager/competitions', [
            'competitions' => $competitions,
            'selectedSportId' => $selectedSportId
        ]);
    }

    /**
     * Show add participants form
     */
    public function create() {
        $model = new SportCompetition();
        $sports = $model->getAllSports();
        $selectedSport = $_GET['sport'] ?? null;
        $competitionId = $_GET['competition_id'] ?? null;
        $competition = null;
        $students = [];
        
        // If competition_id is provided, fetch the competition details
        if ($competitionId) {
            $competition = $model->getById($competitionId);
            // Get students for the competition's sport
            if ($competition) {
                $students = $model->getStudentsBySport($competition['sport_id']);
            }
        } elseif ($selectedSport) {
            // Get students for the selected sport from URL
            $students = $model->getStudentsBySport($selectedSport);
        }
        
        view('sports-manager/add-participants', [
            'sports' => $sports, 
            'selectedSport' => $selectedSport,
            'competition' => $competition,
            'students' => $students
        ]);
    }

    /**
     * Save new competition with participants
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            header('Location: /uoc-sports/public/sport-manager/add-participants');
            exit();
        }

        try {
            $model = new SportCompetition();
            
            // Check if updating existing competition
            $competitionId = $_POST['competition_id'] ?? null;

            // Get sport_id from sport name
            $sportName = $_POST['sport'] ?? '';
            $sportId = $model->getSportIdByName($sportName);
            
            if (!$sportId) {
                $_SESSION['error_message'] = 'Invalid sport selected';
                header('Location: /uoc-sports/public/sport-manager/add-participants');
                exit();
            }

            // Prepare base data
            $data = [
                'competition_name' => $_POST['competitionName'] ?? '',
                'sport_id' => $sportId,
                'participant_pdf' => null,
                'participants' => null
            ];

            // Validate required fields
            if (empty($data['competition_name'])) {
                $_SESSION['error_message'] = 'Please enter competition name';
                header('Location: /uoc-sports/public/sport-manager/add-participants');
                exit();
            }

            // If updating existing competition, get current data
            if ($competitionId) {
                $existingCompetition = $model->getById($competitionId);
                if ($existingCompetition) {
                    // Keep existing PDF if no new upload
                    $data['participant_pdf'] = $existingCompetition['participant_pdf'];
                    // Keep existing participants to append to
                    $existingParticipants = $existingCompetition['participants'];
                }
            }

            // Handle file upload if provided
            $fileUploaded = false;
            if (isset($_FILES['participantsFile']) && $_FILES['participantsFile']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['participantsFile']);
                
                if ($uploadResult['success']) {
                    // Delete old file if updating and a new file is uploaded
                    if ($competitionId && !empty($data['participant_pdf'])) {
                        $oldFilePath = '../app/internal/Sport_competitions/' . $data['participant_pdf'];
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $data['participant_pdf'] = $uploadResult['filename'];
                    $fileUploaded = true;
                } else {
                    $_SESSION['error_message'] = $uploadResult['message'];
                    header('Location: /uoc-sports/public/sport-manager/add-participants');
                    exit();
                }
            }

            // Handle selected participants from checkboxes
            $hasNewParticipants = false;
            if (!empty($_POST['selectedParticipants']) && is_array($_POST['selectedParticipants'])) {
                $newParticipants = $_POST['selectedParticipants'];
                
                // If updating, check for duplicates
                if ($competitionId && !empty($existingParticipants)) {
                    // Convert existing participants to array
                    $existingArray = array_map('trim', explode(',', $existingParticipants));
                    
                    // Filter out duplicates
                    $uniqueNewParticipants = [];
                    $duplicates = [];
                    
                    foreach ($newParticipants as $participant) {
                        if (!in_array($participant, $existingArray)) {
                            $uniqueNewParticipants[] = $participant;
                        } else {
                            $duplicates[] = $participant;
                        }
                    }
                    
                    // If all participants are duplicates, show error and stop
                    if (empty($uniqueNewParticipants) && !empty($duplicates)) {
                        $_SESSION['error_message'] = 'All selected participant(s) are already in this competition. No changes were made.';
                        $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
                        header('Location: /uoc-sports/public/sport-manager/competitions' . $sportParam);
                        exit();
                    }
                    
                    // If some duplicates but also new participants, show warning
                    if (!empty($duplicates)) {
                        $_SESSION['warning_message'] = count($duplicates) . ' participant(s) were already in this competition and were skipped.';
                    }
                    
                    // Only add unique participants
                    if (!empty($uniqueNewParticipants)) {
                        $data['participants'] = $existingParticipants . ', ' . implode(', ', $uniqueNewParticipants);
                        $hasNewParticipants = true;
                    } else {
                        $data['participants'] = $existingParticipants;
                    }
                } else {
                    // New competition - just add all participants
                    $data['participants'] = implode(', ', $newParticipants);
                    $hasNewParticipants = true;
                }
            }

            // Validate that at least one method is provided (for new competitions only)
            if (!$competitionId && empty($data['participant_pdf']) && empty($data['participants'])) {
                $_SESSION['error_message'] = 'Please upload a file or select participants';
                header('Location: /uoc-sports/public/sport-manager/add-participants');
                exit();
            }

            // For updates, ensure something changed
            if ($competitionId && !$fileUploaded && !$hasNewParticipants) {
                $_SESSION['error_message'] = 'No changes were made. Please upload a file or select new participants.';
                $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
                header('Location: /uoc-sports/public/sport-manager/competitions' . $sportParam);
                exit();
            }

            // Update or create the competition
            if ($competitionId) {
                $result = $model->update($competitionId, $data);
                $successMessage = 'Competition participants updated successfully!';
            } else {
                $competitionId = $model->create($data);
                $result = $competitionId;
                $successMessage = 'Competition participants added successfully!';
            }

            if ($result) {
                $_SESSION['success_message'] = $successMessage;
                $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
                header('Location: /uoc-sports/public/sport-manager/competitions' . $sportParam);
            } else {
                $_SESSION['error_message'] = 'Failed to save competition participants';
                header('Location: /uoc-sports/public/sport-manager/add-participants');
            }

        } catch (Exception $e) {
            error_log("Error creating competition: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            header('Location: /uoc-sports/public/sport-manager/add-participants');
        }
        exit();
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error'];
        }

        // Validate file size (5MB max)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and PDF are allowed'];
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Generate unique filename
        $filename = 'competition_' . time() . '_' . uniqid() . '.' . $extension;

        // Create directory if it doesn't exist
        $uploadDir = '../app/internal/Sport_competitions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        $destination = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to save uploaded file'];
        }
    }

    /**
     * Delete competition
     */
    public function delete() {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error_message'] = 'Invalid competition';
            header('Location: /uoc-sports/public/sport-manager/competitions');
            exit();
        }

        try {
            $model = new SportCompetition();
            $result = $model->delete($id);

            if ($result) {
                $_SESSION['success_message'] = 'Competition deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete competition';
            }

        } catch (Exception $e) {
            error_log("Error deleting competition: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while deleting the competition';
        }

        // Preserve sport filter if it exists
        $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
        header('Location: /uoc-sports/public/sport-manager/competitions' . $sportParam);
        exit();
    }
}
