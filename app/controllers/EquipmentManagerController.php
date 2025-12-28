<?php

class EquipmentManagerController {

    public function index() {
        $lostitemModel = new Lostitem();
        $lostitem = $lostitemModel->getUnclaimedItemsCurrentMonth();
        
        view('equipment-manager/index', ['lostitem' => $lostitem]);
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
        view('equipment-manager/add-booking');
    }

    public function bookingRequests() {
        // Mock data for frontend display
        $bookingRequests = [];
        
        view('equipment-manager/bookingrequests', ['bookingRequests' => $bookingRequests]);
    }

    public function manageEquipment() {
        $sport = $_GET['sport'] ?? 'General';
        
        view('equipment-manager/manage-equipment', ['sport' => $sport]);
    }
}