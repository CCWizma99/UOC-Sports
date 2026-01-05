<?php

class ReservationApiController extends BaseController {
    private $facilityModel;

    public function __construct() {
        $this->facilityModel = new Facility();
    }

    public function getReservationStats() {
        header('Content-Type: application/json');

        $period = $_GET['period'] ?? 'monthly';
        $year = $_GET['year'] ?? date('Y');

        $chartData = $this->facilityModel->getReservationData($period, $year) ?? [];

        // Calculate analytics
        $totalReservations = !empty($chartData) ? array_sum(array_column($chartData, 'res_count')) : 0;
        $avgReservations = !empty($chartData) ? round($totalReservations / count($chartData), 1) : 0;
        // Ensure max_value is at least 1 to prevent rendering issues
        $maxValue = !empty($chartData) ? max(1, max(array_column($chartData, 'res_count'))) : 10;

        echo json_encode([
            'chart_data' => $chartData,
            'total_reservations' => $totalReservations,
            'avg_reservations' => $avgReservations,
            'max_value' => $maxValue,
            'current_period' => $period,
            'selected_year' => $year
        ]);
    }
}
