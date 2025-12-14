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
}