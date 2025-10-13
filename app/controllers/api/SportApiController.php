<?php
class SportApiController {

    public function getTournaments() {
        header('Content-Type: application/json');
        try {
            $model = new Sport();
            $results = $model->getTournaments();
            if (empty($results)){
                echo json_encode(['status'=>'empty', 'data'=>'All the records are completed.']);
                return;
            }
            echo json_encode(['status'=>'success', 'data'=>$results]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    public function getSports() {
        header('Content-Type: application/json');
        try {
            $model = new Sport();
            $results = $model->getSports();
            echo json_encode(['status'=>'success', 'data'=>$results]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }


    public function getStudents() {
        header('Content-Type: application/json');
        try {
            $model = new Sport();
            $results = $model->getStudents();
            echo json_encode(['status'=>'success', 'data'=>$results]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    public function getSportFields() {
        header('Content-Type: application/json');
        try {
            $sportId = $_GET['sport_id'] ?? '';
            if(!$sportId) {
                echo json_encode(['status'=>'error','message'=>'Missing sport_id']);
                return;
            }
            $model = new Sport();
            $fields = $model->getSportFields($sportId);
            echo json_encode(['status'=>'success','data'=>$fields]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    public function addResult() {
        header('Content-Type: application/json');
        try {
            $SportId = $_POST['Sport_id'] ?? '';
            $sportId = $_POST['sport_id'] ?? '';
            $matchName = $_POST['match_name'] ?? '';
            $matchDate = $_POST['match_date'] ?? '';
            $winnerId = $_POST['winner_id'] ?? null;
            $fieldValues = $_POST['fields'] ?? [];

            if(!$SportId || !$sportId || !$matchName || !$matchDate) {
                echo json_encode(['status'=>'error','message'=>'Missing required fields']);
                return;
            }

            $model = new Sport();
            $matchId = $model->addResult($SportId, $sportId, $matchName, $matchDate, $winnerId, $fieldValues);

            echo json_encode(['status'=>'success','message'=>'Result added successfully', 'match_id'=>$matchId]);

        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }
}
