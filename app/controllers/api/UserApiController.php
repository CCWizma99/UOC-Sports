<?php

class UserApiController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function getRegistrationStats() {
        header('Content-Type: application/json');

        $period = $_GET['period'] ?? 'monthly';
        $year = $_GET['year'] ?? date('Y');

        $chartData = $this->userModel->getRegistrationData($period, $year) ?? [];

        // Calculate analytics
        $totalUsers = !empty($chartData) ? array_sum(array_column($chartData, 'user_count')) : 0;
        $avgUsers = !empty($chartData) ? round($totalUsers / count($chartData), 1) : 0;
        $maxValue = !empty($chartData) ? max(array_column($chartData, 'user_count')) : 100;

        echo json_encode([
            'chart_data' => $chartData,
            'total_users' => $totalUsers,
            'avg_users' => $avgUsers,
            'max_value' => $maxValue,
            'current_period' => $period,
            'selected_year' => $year
        ]);
    }
}
