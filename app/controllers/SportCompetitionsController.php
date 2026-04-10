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
        $existingParticipants = [];
        
        // If competition_id is provided, fetch the competition details
        if ($competitionId) {
            $competition = $model->getById($competitionId);
            // Get students for the competition's sport
            if ($competition) {
                if (!$selectedSport && !empty($competition['sport_id'])) {
                    $selectedSport = $competition['sport_id'];
                }
                $students = $model->getStudentsBySport($competition['sport_id']);
                if (!empty($competition['participants'])) {
                    $existingParticipants = array_filter(array_map('trim', explode(',', $competition['participants'])));
                }
            }
        } elseif ($selectedSport) {
            // Get students for the selected sport from URL
            $students = $model->getStudentsBySport($selectedSport);
        }
        
        view('sports-manager/add-participants', [
            'sports' => $sports, 
            'selectedSport' => $selectedSport,
            'competition' => $competition,
            'students' => $students,
            'existingParticipants' => $existingParticipants
        ]);
    }

    /**
     * Save new competition with participants
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
            header('Location: /uoc-sports/public/sport-manager/add-participants' . $sportParam);
            exit();
        }

        try {
            $model = new SportCompetition();
            
            // Check if updating existing competition
            $competitionId = $_POST['competition_id'] ?? null;
            $requestedSport = $_GET['sport'] ?? ($_POST['sport_param'] ?? null);

            // Get sport_id from sport name
            $sportName = $_POST['sport'] ?? '';
            $sportId = $model->getSportIdByName($sportName);
            
            if (!$sportId) {
                $_SESSION['error_message'] = 'Invalid sport selected';
                $query = [];
                if ($requestedSport) {
                    $query[] = 'sport=' . urlencode($requestedSport);
                }
                if ($competitionId) {
                    $query[] = 'competition_id=' . urlencode($competitionId);
                }
                $redirectUrl = '/uoc-sports/public/sport-manager/add-participants' . (!empty($query) ? '?' . implode('&', $query) : '');
                header('Location: ' . $redirectUrl);
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
                $query = ['sport=' . urlencode($sportId)];
                if ($competitionId) {
                    $query[] = 'competition_id=' . urlencode($competitionId);
                }
                header('Location: /uoc-sports/public/sport-manager/add-participants?' . implode('&', $query));
                exit();
            }

            // If updating existing competition, get current data
            $existingParticipants = '';
            if ($competitionId) {
                $existingCompetition = $model->getById($competitionId);
                if ($existingCompetition) {
                    // Keep existing PDF if no new upload
                    $data['participant_pdf'] = $existingCompetition['participant_pdf'];
                    // Keep existing participants for comparison/update
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
                    $query = ['sport=' . urlencode($sportId)];
                    if ($competitionId) {
                        $query[] = 'competition_id=' . urlencode($competitionId);
                    }
                    header('Location: /uoc-sports/public/sport-manager/add-participants?' . implode('&', $query));
                    exit();
                }
            }

            // Handle selected participants from checkboxes (full sync for updates)
            $selectedParticipants = [];
            if (isset($_POST['selectedParticipants']) && is_array($_POST['selectedParticipants'])) {
                foreach ($_POST['selectedParticipants'] as $participant) {
                    $trimmed = trim($participant);
                    if ($trimmed !== '') {
                        $selectedParticipants[] = $trimmed;
                    }
                }
            }

            // Remove duplicates while preserving order
            $selectedParticipants = array_values(array_unique($selectedParticipants));
            $data['participants'] = !empty($selectedParticipants) ? implode(', ', $selectedParticipants) : null;

            $hasParticipantChanges = false;
            if ($competitionId) {
                $existingArray = array_values(array_filter(array_map('trim', explode(',', (string)$existingParticipants))));
                $normalize = function($arr) {
                    $normalized = array_map(function($name) {
                        return strtolower(trim($name));
                    }, $arr);
                    sort($normalized);
                    return $normalized;
                };
                $hasParticipantChanges = $normalize($existingArray) !== $normalize($selectedParticipants);
            } else {
                $hasParticipantChanges = !empty($selectedParticipants);
            }

            // Validate that at least one method is provided (for new competitions only)
            if (!$competitionId && empty($data['participant_pdf']) && empty($data['participants'])) {
                $_SESSION['error_message'] = 'Please upload a file or select participants';
                header('Location: /uoc-sports/public/sport-manager/add-participants?sport=' . urlencode($sportId));
                exit();
            }

            // For updates, ensure something changed
            if ($competitionId && !$fileUploaded && !$hasParticipantChanges) {
                $_SESSION['error_message'] = 'No changes were made.';
                $sportParam = ($requestedSport ?: $sportId) ? '?sport=' . urlencode($requestedSport ?: $sportId) : '';
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
                $sportParam = ($requestedSport ?: $sportId) ? '?sport=' . urlencode($requestedSport ?: $sportId) : '';
                header('Location: /uoc-sports/public/sport-manager/competitions' . $sportParam);
            } else {
                $_SESSION['error_message'] = 'Failed to save competition participants';
                $query = ['sport=' . urlencode($sportId)];
                if ($competitionId) {
                    $query[] = 'competition_id=' . urlencode($competitionId);
                }
                header('Location: /uoc-sports/public/sport-manager/add-participants?' . implode('&', $query));
            }

        } catch (Exception $e) {
            error_log("Error creating competition: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            $sportParam = isset($_GET['sport']) ? '?sport=' . urlencode($_GET['sport']) : '';
            header('Location: /uoc-sports/public/sport-manager/add-participants' . $sportParam);
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
