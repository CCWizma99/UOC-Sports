<?php

class EquipmentManagerController {
    public function index() {
        view('equipment-manager/index');
    }
        public function equipmentReport() {
             $equipment = new Equipment();
        $allEquipments = $equipment->getAll();
        
        view('equipment-manager/equipment-report');
    }
}
