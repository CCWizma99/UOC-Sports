<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * CombatMatch - Model for combat sports
 * Sports: Boxing, Taekwondo, Karate, Wrestling, Kabaddi
 */
class CombatMatch extends BaseMatchModel {
    protected string $tableName = 'match_combat';
    
    public function addDetails(string $matchId, array $details): bool {
        $roundScores = isset($details['round_scores']) && is_array($details['round_scores']) 
            ? json_encode($details['round_scores']) 
            : $details['round_scores'] ?? null;
            
        $stmt = $this->db->prepare("
            INSERT INTO match_combat 
            (match_id, fighter_a_name, fighter_b_name, weight_category,
             round_scores, total_rounds, rounds_completed, final_score_a, final_score_b,
             result_type, knockdowns_a, knockdowns_b, warnings_a, warnings_b,
             pins_a, pins_b, raid_points_a, raid_points_b, tackle_points_a, tackle_points_b, notes)
            VALUES 
            (:match_id, :fighter_a_name, :fighter_b_name, :weight_category,
             :round_scores, :total_rounds, :rounds_completed, :final_score_a, :final_score_b,
             :result_type, :knockdowns_a, :knockdowns_b, :warnings_a, :warnings_b,
             :pins_a, :pins_b, :raid_points_a, :raid_points_b, :tackle_points_a, :tackle_points_b, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'fighter_a_name' => $details['fighter_a_name'] ?? null,
            'fighter_b_name' => $details['fighter_b_name'] ?? null,
            'weight_category' => $details['weight_category'] ?? null,
            'round_scores' => $roundScores,
            'total_rounds' => $details['total_rounds'] ?? 3,
            'rounds_completed' => $details['rounds_completed'] ?? 0,
            'final_score_a' => $details['final_score_a'] ?? 0,
            'final_score_b' => $details['final_score_b'] ?? 0,
            'result_type' => $details['result_type'] ?? 'POINTS',
            'knockdowns_a' => $details['knockdowns_a'] ?? 0,
            'knockdowns_b' => $details['knockdowns_b'] ?? 0,
            'warnings_a' => $details['warnings_a'] ?? 0,
            'warnings_b' => $details['warnings_b'] ?? 0,
            'pins_a' => $details['pins_a'] ?? 0,
            'pins_b' => $details['pins_b'] ?? 0,
            'raid_points_a' => $details['raid_points_a'] ?? null,
            'raid_points_b' => $details['raid_points_b'] ?? null,
            'tackle_points_a' => $details['tackle_points_a'] ?? null,
            'tackle_points_b' => $details['tackle_points_b'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_combat WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['round_scores']) {
            $result['round_scores'] = json_decode($result['round_scores'], true);
        }
        
        return $result ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $roundScores = isset($details['round_scores']) && is_array($details['round_scores']) 
            ? json_encode($details['round_scores']) 
            : $details['round_scores'] ?? null;
            
        $stmt = $this->db->prepare("
            UPDATE match_combat SET
                fighter_a_name = :fighter_a_name, fighter_b_name = :fighter_b_name,
                weight_category = :weight_category, round_scores = :round_scores,
                total_rounds = :total_rounds, rounds_completed = :rounds_completed,
                final_score_a = :final_score_a, final_score_b = :final_score_b,
                result_type = :result_type, knockdowns_a = :knockdowns_a,
                knockdowns_b = :knockdowns_b, warnings_a = :warnings_a,
                warnings_b = :warnings_b, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'fighter_a_name' => $details['fighter_a_name'] ?? null,
            'fighter_b_name' => $details['fighter_b_name'] ?? null,
            'weight_category' => $details['weight_category'] ?? null,
            'round_scores' => $roundScores,
            'total_rounds' => $details['total_rounds'] ?? 3,
            'rounds_completed' => $details['rounds_completed'] ?? 0,
            'final_score_a' => $details['final_score_a'] ?? 0,
            'final_score_b' => $details['final_score_b'] ?? 0,
            'result_type' => $details['result_type'] ?? 'POINTS',
            'knockdowns_a' => $details['knockdowns_a'] ?? 0,
            'knockdowns_b' => $details['knockdowns_b'] ?? 0,
            'warnings_a' => $details['warnings_a'] ?? 0,
            'warnings_b' => $details['warnings_b'] ?? 0,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        $resultOptions = [
            ['value' => 'POINTS', 'label' => 'Points Decision'],
            ['value' => 'KO', 'label' => 'Knockout'],
            ['value' => 'TKO', 'label' => 'Technical Knockout'],
            ['value' => 'DISQUALIFICATION', 'label' => 'Disqualification'],
            ['value' => 'WALKOVER', 'label' => 'Walkover']
        ];
        
        // Add sport-specific result types
        if (in_array($sportId, ['TKD', 'KRT'])) {
            $resultOptions[] = ['value' => 'IPPON', 'label' => 'Ippon'];
            $resultOptions[] = ['value' => 'WAZA_ARI', 'label' => 'Waza-ari'];
        }
        if ($sportId === 'WRE') {
            $resultOptions[] = ['value' => 'PIN', 'label' => 'Pin/Fall'];
            $resultOptions[] = ['value' => 'SUBMISSION', 'label' => 'Submission'];
        }
        
        $config = [
            'category' => 'COMBAT',
            'title' => 'Combat Sport Match',
            'sections' => [
                [
                    'title' => 'Competitors',
                    'fields' => [
                        ['name' => 'fighter_a_name', 'label' => 'Fighter A', 'type' => 'text', 'required' => true],
                        ['name' => 'fighter_b_name', 'label' => 'Fighter B', 'type' => 'text', 'required' => true],
                        ['name' => 'weight_category', 'label' => 'Weight Category', 'type' => 'text', 'placeholder' => 'e.g., 60kg']
                    ]
                ],
                [
                    'title' => 'Rounds',
                    'fields' => [
                        ['name' => 'total_rounds', 'label' => 'Total Rounds', 'type' => 'number', 'min' => 1, 'default' => 3],
                        ['name' => 'rounds_completed', 'label' => 'Rounds Completed', 'type' => 'number', 'min' => 0],
                        ['name' => 'round_scores', 'label' => 'Round Scores', 'type' => 'round_scores']
                    ]
                ],
                [
                    'title' => 'Final Score',
                    'fields' => [
                        ['name' => 'final_score_a', 'label' => 'Fighter A Score', 'type' => 'number', 'min' => 0],
                        ['name' => 'final_score_b', 'label' => 'Fighter B Score', 'type' => 'number', 'min' => 0],
                        ['name' => 'result_type', 'label' => 'Result Type', 'type' => 'select', 'options' => $resultOptions]
                    ]
                ],
                [
                    'title' => 'Stats',
                    'collapsible' => true,
                    'fields' => [
                        ['name' => 'knockdowns_a', 'label' => 'Knockdowns (A)', 'type' => 'number', 'min' => 0],
                        ['name' => 'knockdowns_b', 'label' => 'Knockdowns (B)', 'type' => 'number', 'min' => 0],
                        ['name' => 'warnings_a', 'label' => 'Warnings (A)', 'type' => 'number', 'min' => 0],
                        ['name' => 'warnings_b', 'label' => 'Warnings (B)', 'type' => 'number', 'min' => 0]
                    ]
                ]
            ]
        ];
        
        // Add wrestling-specific fields
        if ($sportId === 'WRE') {
            $config['sections'][] = [
                'title' => 'Wrestling Stats',
                'fields' => [
                    ['name' => 'pins_a', 'label' => 'Pins (A)', 'type' => 'number', 'min' => 0],
                    ['name' => 'pins_b', 'label' => 'Pins (B)', 'type' => 'number', 'min' => 0]
                ]
            ];
        }
        
        // Add kabaddi-specific fields
        if ($sportId === 'KBD') {
            $config['sections'][] = [
                'title' => 'Kabaddi Stats',
                'fields' => [
                    ['name' => 'raid_points_a', 'label' => 'Raid Points (A)', 'type' => 'number', 'min' => 0],
                    ['name' => 'raid_points_b', 'label' => 'Raid Points (B)', 'type' => 'number', 'min' => 0],
                    ['name' => 'tackle_points_a', 'label' => 'Tackle Points (A)', 'type' => 'number', 'min' => 0],
                    ['name' => 'tackle_points_b', 'label' => 'Tackle Points (B)', 'type' => 'number', 'min' => 0]
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
