<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * RacketMatch - Model for racket sports
 * Sports: Badminton, Tennis, Table Tennis
 */
class RacketMatch extends BaseMatchModel {
    protected string $tableName = 'match_racket';
    
    public function addDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            INSERT INTO match_racket 
            (match_id, player_a_name, player_b_name, match_format, match_type,
             set_scores, sets_won_a, sets_won_b, total_points_a, total_points_b, notes)
            VALUES 
            (:match_id, :player_a_name, :player_b_name, :match_format, :match_type,
             :set_scores, :sets_won_a, :sets_won_b, :total_points_a, :total_points_b, :notes)
        ");
        
        $setScores = isset($details['set_scores']) && is_array($details['set_scores']) 
            ? json_encode($details['set_scores']) 
            : $details['set_scores'] ?? null;
        
        return $stmt->execute([
            'match_id' => $matchId,
            'player_a_name' => $details['player_a_name'] ?? null,
            'player_b_name' => $details['player_b_name'] ?? null,
            'match_format' => $details['match_format'] ?? 'BEST_OF_3',
            'match_type' => $details['match_type'] ?? 'SINGLES',
            'set_scores' => $setScores,
            'sets_won_a' => $details['sets_won_a'] ?? 0,
            'sets_won_b' => $details['sets_won_b'] ?? 0,
            'total_points_a' => $details['total_points_a'] ?? 0,
            'total_points_b' => $details['total_points_b'] ?? 0,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_racket WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['set_scores']) {
            $result['set_scores'] = json_decode($result['set_scores'], true);
        }
        
        return $result ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $setScores = isset($details['set_scores']) && is_array($details['set_scores']) 
            ? json_encode($details['set_scores']) 
            : $details['set_scores'] ?? null;
            
        $stmt = $this->db->prepare("
            UPDATE match_racket SET
                player_a_name = :player_a_name,
                player_b_name = :player_b_name,
                match_format = :match_format,
                match_type = :match_type,
                set_scores = :set_scores,
                sets_won_a = :sets_won_a,
                sets_won_b = :sets_won_b,
                total_points_a = :total_points_a,
                total_points_b = :total_points_b,
                notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'player_a_name' => $details['player_a_name'] ?? null,
            'player_b_name' => $details['player_b_name'] ?? null,
            'match_format' => $details['match_format'] ?? 'BEST_OF_3',
            'match_type' => $details['match_type'] ?? 'SINGLES',
            'set_scores' => $setScores,
            'sets_won_a' => $details['sets_won_a'] ?? 0,
            'sets_won_b' => $details['sets_won_b'] ?? 0,
            'total_points_a' => $details['total_points_a'] ?? 0,
            'total_points_b' => $details['total_points_b'] ?? 0,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        return [
            'category' => 'RACKET',
            'title' => 'Racket Sport Match',
            'sections' => [
                [
                    'title' => 'Players',
                    'fields' => [
                        ['name' => 'player_a_name', 'label' => 'Player/Team A', 'type' => 'text', 'required' => true],
                        ['name' => 'player_b_name', 'label' => 'Player/Team B', 'type' => 'text', 'required' => true]
                    ]
                ],
                [
                    'title' => 'Match Format',
                    'fields' => [
                        ['name' => 'match_format', 'label' => 'Format', 'type' => 'select', 'options' => [
                            ['value' => 'BEST_OF_3', 'label' => 'Best of 3'],
                            ['value' => 'BEST_OF_5', 'label' => 'Best of 5'],
                            ['value' => 'SINGLE_SET', 'label' => 'Single Set']
                        ]],
                        ['name' => 'match_type', 'label' => 'Type', 'type' => 'select', 'options' => [
                            ['value' => 'SINGLES', 'label' => 'Singles'],
                            ['value' => 'DOUBLES', 'label' => 'Doubles'],
                            ['value' => 'MIXED_DOUBLES', 'label' => 'Mixed Doubles']
                        ]]
                    ]
                ],
                [
                    'title' => 'Set Scores',
                    'fields' => [
                        ['name' => 'set_scores', 'label' => 'Set Scores', 'type' => 'set_scores', 'description' => 'Enter scores for each set']
                    ]
                ],
                [
                    'title' => 'Summary',
                    'fields' => [
                        ['name' => 'sets_won_a', 'label' => 'Sets Won (A)', 'type' => 'number', 'min' => 0, 'readonly' => true],
                        ['name' => 'sets_won_b', 'label' => 'Sets Won (B)', 'type' => 'number', 'min' => 0, 'readonly' => true],
                        ['name' => 'total_points_a', 'label' => 'Total Points (A)', 'type' => 'number', 'min' => 0],
                        ['name' => 'total_points_b', 'label' => 'Total Points (B)', 'type' => 'number', 'min' => 0]
                    ]
                ],
                [
                    'title' => 'Notes',
                    'fields' => [
                        ['name' => 'notes', 'label' => 'Match Notes', 'type' => 'textarea']
                    ]
                ]
            ]
        ];
    }
}
