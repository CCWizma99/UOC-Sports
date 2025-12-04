<?php

class EquipmentManagerController {

    public function index() {
        view('equipment-manager/index');
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
}