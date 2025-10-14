<?php

class EquipmentManagerController {

    public function index() {
        view('equipment-manager/index');
    }

    public function equipmentReport() {
        view('equipment-manager/equipment-report');
    }

    public function equipment() {
        view('equipment-manager/equipment');
    }

    public function addEquipment() {
        view('equipment-manager/addequipment');
    }

}