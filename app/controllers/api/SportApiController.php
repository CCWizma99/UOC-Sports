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

    /**
     * Get form configuration for a sport (sport-specific fields)
     */
    public function getSportFields() {
        header('Content-Type: application/json');
        try {
            $sportId = $_GET['sport_id'] ?? '';
            if(!$sportId) {
                echo json_encode(['status'=>'error','message'=>'Missing sport_id']);
                return;
            }
            $model = new Sport();
            $config = $model->getFormConfig($sportId);
            
            if ($config) {
                echo json_encode(['status'=>'success','data'=>$config]);
            } else {
                echo json_encode(['status'=>'error','message'=>'Sport not found or no configuration available']);
            }
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }
    
    /**
     * Get sport details by ID including category
     */
    public function getSportById() {
        header('Content-Type: application/json');
        try {
            $sportId = $_GET['sport_id'] ?? '';
            if(!$sportId) {
                echo json_encode(['status'=>'error','message'=>'Missing sport_id']);
                return;
            }
            $model = new Sport();
            $sport = $model->getSportById($sportId);
            
            if ($sport) {
                echo json_encode(['status'=>'success','data'=>$sport]);
            } else {
                echo json_encode(['status'=>'error','message'=>'Sport not found']);
            }
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    /**
     * Get player match history
     */
    public function getPlayerMatchHistory() {
        header('Content-Type: application/json');
        try {
            $userId = $_GET['user_id'] ?? '';
            if (empty($userId)) {
                echo json_encode(['status'=>'error','message'=>'Missing user_id']);
                return;
            }
            
            $model = new Sport();
            $history = $model->getPlayerMatchHistory($userId);
            
            echo json_encode([
                'status' => 'success',
                'data' => $history,
                'total' => count($history)
            ]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    /**
     * Search matches
     */
    public function searchMatches() {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            $sportId = $_GET['sport_id'] ?? '';
            $tournamentId = $_GET['tournament_id'] ?? '';
            $limit = $_GET['limit'] ?? 20;
            
            $model = new Sport();
            $matches = $model->searchMatches($query, $sportId, $tournamentId, $limit);
            
            echo json_encode([
                'status' => 'success',
                'data' => $matches,
                'total' => count($matches)
            ]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    /**
     * Get match details with participants
     */
    public function getMatchDetails() {
        header('Content-Type: application/json');
        try {
            $matchId = $_GET['match_id'] ?? '';
            if (empty($matchId)) {
                echo json_encode(['status'=>'error','message'=>'Missing match_id']);
                return;
            }
            
            $model = new Sport();
            $match = $model->getMatchDetails($matchId);
            
            if (!$match) {
                echo json_encode(['status'=>'error','message'=>'Match not found']);
                return;
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $match
            ]);
        } catch(Exception $e) {
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
    }
}

