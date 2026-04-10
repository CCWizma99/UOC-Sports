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

    public function getAnalytics() {
        header('Content-Type: application/json');
        
        try {
            $analytics = $this->facilityModel->getAnalytics();
            
            echo json_encode([
                'status' => 'success',
                'data' => $analytics
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to load analytics data'
            ]);
        }
    }

    public function search() {
        header('Content-Type: application/json');

        $query = $_GET['q'] ?? '';
        $filters = [
            'date' => $_GET['date'] ?? '',
            'location' => $_GET['location'] ?? '',
            'user_type' => $_GET['user_type'] ?? ''
        ];

        try {
            $results = $this->facilityModel->searchReservations($query, $filters);
            echo json_encode($results);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getLocations() {
        header('Content-Type: application/json');

        try {
            $locations = $this->facilityModel->getPhysicalFacilities();
            echo json_encode($locations);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to fetch locations',
                'message' => $e->getMessage()
            ]);
        }
    }
}

