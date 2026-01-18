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

    /**
     * Update user information
     */
    public function updateUser() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
            return;
        }
        
        $userId = $input['user_id'];
        unset($input['user_id']);
        
        $result = $this->userModel->updateUser($userId, $input);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['user_id']) || !isset($input['status'])) {
            echo json_encode(['status' => 'error', 'message' => 'User ID and status are required']);
            return;
        }
        
        $result = $this->userModel->updateUserStatus($input['user_id'], $input['status']);
        
        if ($result) {
            $action = $input['status'] === 'ACTIVE' ? 'activated' : 'deactivated';
            echo json_encode(['status' => 'success', 'message' => "User $action successfully"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user status']);
        }
    }
}
