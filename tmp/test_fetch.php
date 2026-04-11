<?php
require_once 'core/Database.php';
require_once 'config/config.php';
require_once 'app/models/Facility.php';

try {
    $facility = new Facility();
    $reservations = $facility->getMyReservations('L3NCL2J4');
    echo "SUCCESS: Fetched " . count($reservations) . " reservations without error.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
