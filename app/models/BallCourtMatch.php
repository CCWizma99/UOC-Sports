<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * BallCourtMatch - Model for ball court sports
 * Sports: Basketball, Volleyball, Baseball
 */
class BallCourtMatch extends BaseMatchModel {
    protected string $tableName = 'match_ball_court';
    
    public function addDetails(string $matchId, array $details): bool {
        $periodScores = isset($details['period_scores']) && is_array($details['period_scores']) 
            ? json_encode($details['period_scores']) 
            : $details['period_scores'] ?? null;
            
        $stmt = $this->db->prepare("
            INSERT INTO match_ball_court 
            (match_id, team_a_name, team_b_name, sport_subtype, period_scores,
             final_score_a, final_score_b, overtime_periods, sets_won_a, sets_won_b,
             innings_played, notes)
            VALUES 
            (:match_id, :team_a_name, :team_b_name, :sport_subtype, :period_scores,
             :final_score_a, :final_score_b, :overtime_periods, :sets_won_a, :sets_won_b,
             :innings_played, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'sport_subtype' => $details['sport_subtype'] ?? 'BASKETBALL',
            'period_scores' => $periodScores,
            'final_score_a' => $details['final_score_a'] ?? 0,
            'final_score_b' => $details['final_score_b'] ?? 0,
            'overtime_periods' => $details['overtime_periods'] ?? 0,
            'sets_won_a' => $details['sets_won_a'] ?? null,
            'sets_won_b' => $details['sets_won_b'] ?? null,
            'innings_played' => $details['innings_played'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_ball_court WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['period_scores']) {
            $result['period_scores'] = json_decode($result['period_scores'], true);
        }
        
        return $result ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $periodScores = isset($details['period_scores']) && is_array($details['period_scores']) 
            ? json_encode($details['period_scores']) 
            : $details['period_scores'] ?? null;
            
        $stmt = $this->db->prepare("
            UPDATE match_ball_court SET
                team_a_name = :team_a_name, team_b_name = :team_b_name,
                sport_subtype = :sport_subtype, period_scores = :period_scores,
                final_score_a = :final_score_a, final_score_b = :final_score_b,
                overtime_periods = :overtime_periods, sets_won_a = :sets_won_a,
                sets_won_b = :sets_won_b, innings_played = :innings_played, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'sport_subtype' => $details['sport_subtype'] ?? 'BASKETBALL',
            'period_scores' => $periodScores,
            'final_score_a' => $details['final_score_a'] ?? 0,
            'final_score_b' => $details['final_score_b'] ?? 0,
            'overtime_periods' => $details['overtime_periods'] ?? 0,
            'sets_won_a' => $details['sets_won_a'] ?? null,
            'sets_won_b' => $details['sets_won_b'] ?? null,
            'innings_played' => $details['innings_played'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        $sportSubtype = match($sportId) {
            'BAS' => 'BASKETBALL',
            'VOL' => 'VOLLEYBALL',
            'BB' => 'BASEBALL',
            default => 'BASKETBALL'
        };
        
        $periodLabel = match($sportId) {
            'BAS' => 'Quarters',
            'VOL' => 'Sets',
            'BB' => 'Innings',
            default => 'Periods'
        };
        
        $config = [
            'category' => 'BALL_COURT',
            'title' => 'Ball Court Match',
            'sportSubtype' => $sportSubtype,
            'sections' => [
                [
                    'title' => 'Teams',
                    'fields' => [
                        ['name' => 'team_a_name', 'label' => 'Team A', 'type' => 'text', 'required' => true],
                        ['name' => 'team_b_name', 'label' => 'Team B', 'type' => 'text', 'required' => true],
                        ['name' => 'sport_subtype', 'label' => 'Sport', 'type' => 'hidden', 'value' => $sportSubtype]
                    ]
                ],
                [
                    'title' => $periodLabel,
                    'fields' => [
                        ['name' => 'period_scores', 'label' => "$periodLabel Scores", 'type' => 'period_scores', 
                         'periodLabel' => $periodLabel]
                    ]
                ],
                [
                    'title' => 'Final Score',
                    'fields' => [
                        ['name' => 'final_score_a', 'label' => 'Team A Final Score', 'type' => 'number', 'min' => 0],
                        ['name' => 'final_score_b', 'label' => 'Team B Final Score', 'type' => 'number', 'min' => 0]
                    ]
                ]
            ]
        ];
        
        // Basketball-specific fields
        if ($sportId === 'BAS') {
            $config['sections'][] = [
                'title' => 'Overtime',
                'collapsible' => true,
                'fields' => [
                    ['name' => 'overtime_periods', 'label' => 'Overtime Periods', 'type' => 'number', 'min' => 0, 'default' => 0]
                ]
            ];
        }
        
        // Volleyball-specific fields
        if ($sportId === 'VOL') {
            $config['sections'][] = [
                'title' => 'Sets Summary',
                'fields' => [
                    ['name' => 'sets_won_a', 'label' => 'Sets Won (A)', 'type' => 'number', 'min' => 0],
                    ['name' => 'sets_won_b', 'label' => 'Sets Won (B)', 'type' => 'number', 'min' => 0]
                ]
            ];
        }
        
        // Baseball-specific fields
        if ($sportId === 'BB') {
            $config['sections'][] = [
                'title' => 'Innings',
                'fields' => [
                    ['name' => 'innings_played', 'label' => 'Innings Played', 'type' => 'number', 'min' => 1, 'default' => 9]
                ]
            ];
        }
        
        $config['sections'][] = [
            'title' => 'Notes',
            'fields' => [
                ['name' => 'notes', 'label' => 'Match Notes', 'type' => 'textarea']
            ]
        ];
        
        return $config;
    }
}
