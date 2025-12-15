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
        $lostitemModel = new Lostitem();
        $lostitems = $lostitemModel->getAll();
        
        view('equipment-manager/lostitem', ['lostitems' => $lostitems]);
    }

    public function practiceschedule() {
        view('equipment-manager/practiceschedule');
    }

    public function addLostItem() {
        view('equipment-manager/add-lostitem');
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