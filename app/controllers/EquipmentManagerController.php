<?php

class EquipmentManagerController {

    public function index() {
        $lostitemModel = new Lostitem(Database::getConnection());
        $lostitem = $lostitemModel->getUnclaimedItemsCurrentMonth();
        
        // Get today's reservations
        require_once '../app/models/EquipmentBookigRequest.php';
        $bookingModel = new EquipmentBookigRequest();
        
        // Get today's date
        $today = date('Y-m-d');
        
        // Debug: Log today's date
        error_log("Fetching reservations for date: " . $today);
        
        // Fetch today's reservations
        $todayReservations = $bookingModel->getAllRequests(['date_from' => $today, 'date_to' => $today]);
        
        // Debug: Log count of reservations
        error_log("Today's reservations count: " . count($todayReservations));
        if (!empty($todayReservations)) {
            error_log("First reservation: " . print_r($todayReservations[0], true));
        }
        
        // Get statistics
        $statistics = $bookingModel->getStatistics();
        
        view('equipment-manager/index', [
            'lostitem' => $lostitem,
            'todayReservations' => $todayReservations,
            'statistics' => $statistics,
            'todayDate' => $today
        ]);
    }

    public function equipmentReport() {
        view('equipment-manager/equipment-reservations');
    }
    
    public function equipments() {
        view('equipment-manager/equipment');
    }

    public function schedules() {
        view('equipment-manager/schedules');
    }

    public function lostitem() {     
        view('equipment-manager/lostitem');
    }

    public function practiceschedule() {
        view('equipment-manager/practiceschedule');
    }

    public function addLostItem() {
        $editData = null;
        $isEdit = false;
        
        if (isset($_GET['id'])) {
            $lostitemModel = new Lostitem(Database::getConnection());
            $editData = $lostitemModel->getById($_GET['id']);
            $isEdit = true;
        }
        
        view('equipment-manager/add-lostitem', ['editData' => $editData, 'isEdit' => $isEdit]);
    }

    public function addBooking() {
        $editData = null;
        $isEdit = false;
        
        if (isset($_GET['id'])) {
            require_once '../app/models/EquipmentBookigRequest.php';
            $bookingModel = new EquipmentBookigRequest();
            $editData = $bookingModel->getRequestById($_GET['id']);
            $isEdit = true;
        }
        
        view('equipment-manager/add-booking', ['editData' => $editData, 'isEdit' => $isEdit]);
    }

    public function saveBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method';
            header('Location: /uoc-sports/public/equipment-manager/add-booking');
            exit();
        }

        try {
            require_once '../app/models/EquipmentBookigRequest.php';
            $model = new EquipmentBookigRequest();

            // Check if this is an update (edit mode)
            $isEdit = !empty($_POST['request_id']);
            $requestId = $_POST['request_id'] ?? null;

            // Get user identification
            $studentId = !empty($_POST['student_id']) ? $_POST['student_id'] : null;
            $requesterName = $_POST['requester_name'] ?? '';

            // Check if user already has an active or accepted reservation (skip check if editing same request)
            if (!$isEdit && $model->hasActiveReservation($studentId, $requesterName)) {
                $_SESSION['error_message'] = 'This user already has an active or accepted equipment reservation. Please complete or cancel the existing reservation before creating a new one.';
                header('Location: /uoc-sports/public/equipment-manager/add-booking');
                exit();
            }

            // Get selected equipment items
            $selectedEquipment = $_POST['equipment'] ?? [];
            $quantities = $_POST['quantity'] ?? [];

            // Validate that at least one equipment is selected
            if (empty($selectedEquipment)) {
                $_SESSION['error_message'] = 'Please select at least one equipment item';
                header('Location: /uoc-sports/public/equipment-manager/add-booking' . ($isEdit ? '?id=' . $requestId : ''));
                exit();
            }

            // Common data for all requests
            $commonData = [
                'student_id' => $studentId,
                'sport_id' => $_POST['sport'] ?? '',
                'request_date' => $_POST['request_date'] ?? '',
                'start_time' => $_POST['start_time'] ?? '',
                'end_time' => $_POST['end_time'] ?? '',
                'reserved_location' => $_POST['reserved_location'] ?? '',
                'requester_name' => $_POST['requester_name'] ?? '',
                'status' => $isEdit ? ($_POST['status'] ?? 'PENDING') : 'PENDING'
            ];

            // Validate required fields
            if (empty($commonData['requester_name']) || empty($commonData['request_date']) || 
                empty($commonData['start_time']) || empty($commonData['end_time'])) {
                $_SESSION['error_message'] = 'Please fill in all required fields';
                header('Location: /uoc-sports/public/equipment-manager/add-booking' . ($isEdit ? '?id=' . $requestId : ''));
                exit();
            }

            // Prepare equipment items array with quantities
            $equipmentItems = [];
            foreach ($selectedEquipment as $equipmentName) {
                $quantity = isset($quantities[$equipmentName]) ? intval($quantities[$equipmentName]) : 1;
                $equipmentItems[] = [
                    'equipment_name' => $equipmentName,
                    'quantity' => $quantity
                ];
            }

            // Create summary for category_name field (for backward compatibility)
            $categoryNameSummary = implode(', ', array_map(function($item) {
                return $item['equipment_name'] . ' (x' . $item['quantity'] . ')';
            }, $equipmentItems));

            // Prepare request data with multiple equipment items
            $data = array_merge($commonData, [
                'category_name' => $categoryNameSummary,
                'equipment_items' => $equipmentItems,
                'notes' => $_POST['notes'] ?? ''
            ]);

            // Enforce server-side conflict checks for each selected equipment item.
            $conflicts = $model->getItemConflicts(
                $commonData['sport_id'],
                $commonData['request_date'],
                $commonData['start_time'],
                $commonData['end_time'],
                $equipmentItems,
                $isEdit ? $requestId : null,
                true
            );

            if (!empty($conflicts)) {
                $selectedDisplayMap = [];
                foreach ($equipmentItems as $item) {
                    $name = trim((string)($item['equipment_name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }
                    $selectedDisplayMap[strtolower($name)] = [
                        'name' => $name,
                        'qty' => max(1, (int)($item['quantity'] ?? 1)),
                    ];
                }

                $selectedSummary = [];
                foreach ($selectedDisplayMap as $selected) {
                    $selectedSummary[] = $selected['name'] . ' (x' . $selected['qty'] . ')';
                }

                $conflictDetails = [];
                foreach ($conflicts as $conflict) {
                    $rawName = trim((string)($conflict['equipment_name'] ?? 'Selected equipment'));
                    $lookupKey = strtolower($rawName);
                    $displayName = $selectedDisplayMap[$lookupKey]['name'] ?? $rawName;
                    $selectedQty = $selectedDisplayMap[$lookupKey]['qty'] ?? 1;

                    $start = !empty($conflict['start_time']) ? date('H:i', strtotime($conflict['start_time'])) : '--:--';
                    $end = !empty($conflict['end_time']) ? date('H:i', strtotime($conflict['end_time'])) : '--:--';
                    $status = strtoupper((string)($conflict['status'] ?? 'UNKNOWN'));
                    $source = ($conflict['source'] ?? '') === 'practice' ? 'practice session' : 'booking request';

                    $conflictDetails[] = $displayName
                        . ' (selected x' . $selectedQty . ')'
                        . ' -> conflict with ' . $source
                        . ' [' . $status . ']'
                        . ' at ' . $start . ' - ' . $end;
                }

                $conflictDetails = array_values(array_unique($conflictDetails));

                $_SESSION['error_message'] = 'Selected equipment: '
                    . implode(', ', $selectedSummary)
                    . '. Conflicts: '
                    . implode('; ', $conflictDetails)
                    . '. Please choose another time or remove conflicting items.';
                header('Location: /uoc-sports/public/equipment-manager/add-booking' . ($isEdit ? '?id=' . $requestId : ''));
                exit();
            }

            if ($isEdit) {
                // Update existing booking request
                $result = $model->updateRequest($requestId, $data);
                
                if ($result) {
                    $_SESSION['success_message'] = 'Booking request updated successfully with ' . count($equipmentItems) . ' equipment item(s)!';
                    header('Location: /uoc-sports/public/equipment-manager/bookingrequests');
                } else {
                    $_SESSION['error_message'] = 'Failed to update booking request';
                    header('Location: /uoc-sports/public/equipment-manager/add-booking?id=' . $requestId);
                }
            } else {
                // Create new booking request
                $newRequestId = $model->createRequest($data);

                if ($newRequestId) {
                    $_SESSION['success_message'] = 'Booking request created successfully with ' . count($equipmentItems) . ' equipment item(s)!';
                    header('Location: /uoc-sports/public/equipment-manager/bookingrequests');
                } else {
                    $_SESSION['error_message'] = 'Failed to create booking request';
                    header('Location: /uoc-sports/public/equipment-manager/add-booking');
                }
            }

        } catch (Exception $e) {
            error_log("Error saving booking: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while saving the booking';
            $redirectUrl = '/uoc-sports/public/equipment-manager/add-booking';
            if (isset($requestId) && $requestId) {
                $redirectUrl .= '?id=' . $requestId;
            }
            header('Location: ' . $redirectUrl);
        }
        exit();
    }

    public function bookingRequests() {
        // Mock data for frontend display
        $bookingRequests = [];
        
        view('equipment-manager/bookingrequests', ['bookingRequests' => $bookingRequests]);
    }

    public function manageEquipment() {
        $sportId = $_GET['sport_id'] ?? null;
        
        if (!$sportId) {
            $_SESSION['error_message'] = 'Sport ID is required';
            header('Location: /uoc-sports/public/equipment-manager/equipments');
            exit();
        }

        $equipmentModel = new SportEquipment();
        
        // Get equipment data
        $equipment = $equipmentModel->getEquipmentBySport($sportId);
        
        // Get sport name
        $sportQuery = "SELECT sport_name FROM sport WHERE sport_id = ?";
        $stmt = Database::getConnection()->prepare($sportQuery);
        $stmt->execute([$sportId]);
        $sportData = $stmt->fetch(PDO::FETCH_ASSOC);
        $sportName = $sportData['sport_name'] ?? 'Unknown Sport';
        
        // Get summary
        $summary = $equipmentModel->getSportSummary($sportId);
        
        view('equipment-manager/manage-equipment', [
            'sport' => $sportName,
            'sportId' => $sportId,
            'equipment' => $equipment,
            'summary' => $summary
        ]);
    }
}