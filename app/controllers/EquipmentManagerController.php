<?php

class EquipmentManagerController {

    public function index() {
        view('equipment-manager/index');
    }

    public function equipmentReport() {
        view('equipment-manager/equipment-report');
    }

    public function addEquipment() {
        view('equipment-manager/addequipment');
    }

    public function calendar() {
        view('equipment-manager/calendar');
    }

}