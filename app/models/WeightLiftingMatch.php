<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * WeightLiftingMatch - Model for weight lifting
 * Sports: Weight Lifting
 */
class WeightLiftingMatch extends BaseMatchModel {
    protected string $tableName = 'match_weight_lifting';
    
    public function addDetails(string $matchId, array $details): bool {
        $competitionResults = isset($details['competition_results']) && is_array($details['competition_results']) 
            ? json_encode($details['competition_results']) 
            : $details['competition_results'] ?? null;
            
        $stmt = $this->db->prepare("
            INSERT INTO match_weight_lifting 
            (match_id, athlete_name, weight_category, snatch_1, snatch_1_valid, snatch_2, snatch_2_valid,
             snatch_3, snatch_3_valid, snatch_best, cj_1, cj_1_valid, cj_2, cj_2_valid,
             cj_3, cj_3_valid, cj_best, total_kg, competition_results, final_position, notes)
            VALUES 
            (:match_id, :athlete_name, :weight_category, :snatch_1, :snatch_1_valid, :snatch_2, :snatch_2_valid,
             :snatch_3, :snatch_3_valid, :snatch_best, :cj_1, :cj_1_valid, :cj_2, :cj_2_valid,
             :cj_3, :cj_3_valid, :cj_best, :total_kg, :competition_results, :final_position, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'athlete_name' => $details['athlete_name'] ?? null,
            'weight_category' => $details['weight_category'] ?? null,
            'snatch_1' => $details['snatch_1'] ?? null,
            'snatch_1_valid' => $details['snatch_1_valid'] ?? null,
            'snatch_2' => $details['snatch_2'] ?? null,
            'snatch_2_valid' => $details['snatch_2_valid'] ?? null,
            'snatch_3' => $details['snatch_3'] ?? null,
            'snatch_3_valid' => $details['snatch_3_valid'] ?? null,
            'snatch_best' => $details['snatch_best'] ?? null,
            'cj_1' => $details['cj_1'] ?? null,
            'cj_1_valid' => $details['cj_1_valid'] ?? null,
            'cj_2' => $details['cj_2'] ?? null,
            'cj_2_valid' => $details['cj_2_valid'] ?? null,
            'cj_3' => $details['cj_3'] ?? null,
            'cj_3_valid' => $details['cj_3_valid'] ?? null,
            'cj_best' => $details['cj_best'] ?? null,
            'total_kg' => $details['total_kg'] ?? null,
            'competition_results' => $competitionResults,
            'final_position' => $details['final_position'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_weight_lifting WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['competition_results']) {
            $result['competition_results'] = json_decode($result['competition_results'], true);
        }
        
        return $result ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $competitionResults = isset($details['competition_results']) && is_array($details['competition_results']) 
            ? json_encode($details['competition_results']) 
            : $details['competition_results'] ?? null;
            
        $stmt = $this->db->prepare("
            UPDATE match_weight_lifting SET
                athlete_name = :athlete_name, weight_category = :weight_category,
                snatch_1 = :snatch_1, snatch_1_valid = :snatch_1_valid,
                snatch_2 = :snatch_2, snatch_2_valid = :snatch_2_valid,
                snatch_3 = :snatch_3, snatch_3_valid = :snatch_3_valid,
                snatch_best = :snatch_best, cj_1 = :cj_1, cj_1_valid = :cj_1_valid,
                cj_2 = :cj_2, cj_2_valid = :cj_2_valid, cj_3 = :cj_3,
                cj_3_valid = :cj_3_valid, cj_best = :cj_best, total_kg = :total_kg,
                competition_results = :competition_results, final_position = :final_position, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'athlete_name' => $details['athlete_name'] ?? null,
            'weight_category' => $details['weight_category'] ?? null,
            'snatch_1' => $details['snatch_1'] ?? null,
            'snatch_1_valid' => $details['snatch_1_valid'] ?? null,
            'snatch_2' => $details['snatch_2'] ?? null,
            'snatch_2_valid' => $details['snatch_2_valid'] ?? null,
            'snatch_3' => $details['snatch_3'] ?? null,
            'snatch_3_valid' => $details['snatch_3_valid'] ?? null,
            'snatch_best' => $details['snatch_best'] ?? null,
            'cj_1' => $details['cj_1'] ?? null,
            'cj_1_valid' => $details['cj_1_valid'] ?? null,
            'cj_2' => $details['cj_2'] ?? null,
            'cj_2_valid' => $details['cj_2_valid'] ?? null,
            'cj_3' => $details['cj_3'] ?? null,
            'cj_3_valid' => $details['cj_3_valid'] ?? null,
            'cj_best' => $details['cj_best'] ?? null,
            'total_kg' => $details['total_kg'] ?? null,
            'competition_results' => $competitionResults,
            'final_position' => $details['final_position'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    /**
     * Calculate best lifts and total from attempts
     */
    public function calculateTotals(array $details): array {
        // Calculate snatch best
        $snatchAttempts = [];
        if (isset($details['snatch_1']) && $details['snatch_1_valid']) $snatchAttempts[] = $details['snatch_1'];
        if (isset($details['snatch_2']) && $details['snatch_2_valid']) $snatchAttempts[] = $details['snatch_2'];
        if (isset($details['snatch_3']) && $details['snatch_3_valid']) $snatchAttempts[] = $details['snatch_3'];
        $details['snatch_best'] = !empty($snatchAttempts) ? max($snatchAttempts) : null;
        
        // Calculate C&J best
        $cjAttempts = [];
        if (isset($details['cj_1']) && $details['cj_1_valid']) $cjAttempts[] = $details['cj_1'];
        if (isset($details['cj_2']) && $details['cj_2_valid']) $cjAttempts[] = $details['cj_2'];
        if (isset($details['cj_3']) && $details['cj_3_valid']) $cjAttempts[] = $details['cj_3'];
        $details['cj_best'] = !empty($cjAttempts) ? max($cjAttempts) : null;
        
        // Calculate total
        if ($details['snatch_best'] !== null && $details['cj_best'] !== null) {
            $details['total_kg'] = $details['snatch_best'] + $details['cj_best'];
        }
        
        return $details;
    }
    
    public function getFormConfig(?string $sportId = null): array {
        return [
            'category' => 'WEIGHT',
            'title' => 'Weight Lifting Competition',
            'sections' => [
                [
                    'title' => 'Athlete Details',
                    'fields' => [
                        ['name' => 'athlete_name', 'label' => 'Athlete Name', 'type' => 'text', 'required' => true],
                        ['name' => 'weight_category', 'label' => 'Weight Category', 'type' => 'text', 'placeholder' => 'e.g., 56kg']
                    ]
                ],
                [
                    'title' => 'Snatch Attempts',
                    'fields' => [
                        ['name' => 'snatch_1', 'label' => 'Attempt 1 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'snatch_1_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'snatch_2', 'label' => 'Attempt 2 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'snatch_2_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'snatch_3', 'label' => 'Attempt 3 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'snatch_3_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'snatch_best', 'label' => 'Best Snatch (kg)', 'type' => 'number', 'step' => '0.5', 'readonly' => true]
                    ]
                ],
                [
                    'title' => 'Clean & Jerk Attempts',
                    'fields' => [
                        ['name' => 'cj_1', 'label' => 'Attempt 1 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'cj_1_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'cj_2', 'label' => 'Attempt 2 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'cj_2_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'cj_3', 'label' => 'Attempt 3 (kg)', 'type' => 'number', 'step' => '0.5', 'min' => 0],
                        ['name' => 'cj_3_valid', 'label' => 'Valid', 'type' => 'checkbox'],
                        ['name' => 'cj_best', 'label' => 'Best C&J (kg)', 'type' => 'number', 'step' => '0.5', 'readonly' => true]
                    ]
                ],
                [
                    'title' => 'Total & Position',
                    'fields' => [
                        ['name' => 'total_kg', 'label' => 'Total (kg)', 'type' => 'number', 'step' => '0.5', 'readonly' => true],
                        ['name' => 'final_position', 'label' => 'Final Position', 'type' => 'number', 'min' => 1]
                    ]
                ],
                [
                    'title' => 'Competition Results',
                    'collapsible' => true,
                    'description' => 'For multi-athlete competitions',
                    'fields' => [
                        ['name' => 'competition_results', 'label' => 'All Results', 'type' => 'competition_results']
                    ]
                ],
                [
                    'title' => 'Notes',
                    'fields' => [
                        ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea']
                    ]
                ]
            ]
        ];
    }
}
