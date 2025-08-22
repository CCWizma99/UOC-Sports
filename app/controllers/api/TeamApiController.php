<?php
class TeamApiController {
    public function search() {
        $query = $_GET['q'] ?? '';
        $model = new SportTeam();
        $results = $model->search($query);
        echo json_encode(['status'=>'success', 'data'=>$results]);
    }
}
