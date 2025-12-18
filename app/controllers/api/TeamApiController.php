<?php
class TeamApiController {
    public function search() {
        $query = $_GET['q'] ?? '';
        $model = new SportTeam();
        $results = $model->search($query);
        echo json_encode(['status'=>'success', 'data'=>$results]);
    }
    
    public function removeMember() {
        header('Content-Type: application/json');
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['sport_id']) || !isset($input['user_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
            return;
        }
        
        $sport_id = $input['sport_id'];
        $user_id = $input['user_id'];
        
        $model = new SportTeam();
        $result = $model->removeMember($sport_id, $user_id);
        
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Member removed successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to remove member'
            ]);
        }
    }
}
