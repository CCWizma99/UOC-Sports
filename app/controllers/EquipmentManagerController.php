<?php

class EquipmentManagerController {

    public function index() {
        view('equipment-manager/index');
    }

    public function equipmentReport() {
        view('equipment-manager/equipment-report');
    }
    
    public function equipments() {
        view('equipment-manager/equipment');
    }

    public function schedules() {
        view('equipment-manager/schedules');
    }
}