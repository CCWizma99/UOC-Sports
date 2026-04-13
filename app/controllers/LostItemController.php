<?php

class LostItemController {

    public function index() {
        $lostitemModel = new Lostitem(Database::getConnection());
        $lostitems = $lostitemModel->getAll();
        
        view('equipment-manager/lostitem', ['lostitems' => $lostitems]);
    }

    public function create() {
        $editData = null;
        $isEdit = false;
        
        if (isset($_GET['id'])) {
            $lostitemModel = new Lostitem(Database::getConnection());
            $editData = $lostitemModel->getById($_GET['id']);
            $isEdit = true;
        }
        
        view('equipment-manager/add-lostitem', ['editData' => $editData, 'isEdit' => $isEdit]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /uoc-sports/public/equipment-manager/lostitem');
            exit();
        }

        $lostitemModel = new Lostitem(Database::getConnection());
        
        $data = [
            'item_name' => $_POST['itemName'] ?? '',
            'lost_date' => $_POST['foundDate'] ?? '',
            'description' => $_POST['description'] ?? '',
            'lost_location' => $_POST['foundLocation'] ?? '',
            'reported_by' => $_POST['foundBy'] ?? '',
            'contact_number' => $_POST['contactNumber'] ?? '',
            'image' => $_POST['image'] ?? '',
            'item_status' => $_POST['itemStatus'] ?? 'unclaimed'
        ];
        
        $file = isset($_FILES['image']) ? $_FILES['image'] : null;
        
        try {
            if (isset($_POST['lostItem_id']) && !empty($_POST['lostItem_id'])) {
                $result = $lostitemModel->updateLostItem($_POST['lostItem_id'], $data, $file);
                $_SESSION['success_message'] = 'Lost item updated successfully!';
            } else {
                $result = $lostitemModel->addLostItem($data, $file);
                $_SESSION['success_message'] = 'Lost item added successfully!';
            }
            
            header('Location: /uoc-sports/public/equipment-manager/lostitem');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error saving lost item: ' . $e->getMessage();
            header('Location: /uoc-sports/public/equipment-manager/lostitem');
            exit();
        }
    }

    public function updateStatus() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['itemId']) || !isset($input['status'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit();
        }
        
        $lostitemModel = new Lostitem(Database::getConnection());
        $result = $lostitemModel->updateStatus($input['itemId'], $input['status']);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit();
    }

    public function delete() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['itemId'])) {
            echo json_encode(['success' => false, 'message' => 'Missing item ID']);
            exit();
        }
        
        $lostitemModel = new Lostitem(Database::getConnection());
        $result = $lostitemModel->delete($input['itemId']);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
        }
        exit();
    }
}

