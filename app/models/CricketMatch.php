<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * CricketMatch - Model for cricket matches
 * Sports: Cricket
 */
class CricketMatch extends BaseMatchModel {
    protected string $tableName = 'match_cricket';
    
    public function addDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            INSERT INTO match_cricket 
            (match_id, team_a_name, team_b_name, match_format, overs_per_innings,
             innings_1_team, innings_1_runs, innings_1_wickets, innings_1_overs, innings_1_extras,
             innings_2_team, innings_2_runs, innings_2_wickets, innings_2_overs, innings_2_extras,
             result_type, win_margin, winning_team, super_over_team_a, super_over_team_b,
             potm_user_id, toss_won_by, toss_decision, notes)
            VALUES 
            (:match_id, :team_a_name, :team_b_name, :match_format, :overs_per_innings,
             :innings_1_team, :innings_1_runs, :innings_1_wickets, :innings_1_overs, :innings_1_extras,
             :innings_2_team, :innings_2_runs, :innings_2_wickets, :innings_2_overs, :innings_2_extras,
             :result_type, :win_margin, :winning_team, :super_over_team_a, :super_over_team_b,
             :potm_user_id, :toss_won_by, :toss_decision, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'match_format' => $details['match_format'] ?? 'T20',
            'overs_per_innings' => $details['overs_per_innings'] ?? null,
            'innings_1_team' => $details['innings_1_team'] ?? null,
            'innings_1_runs' => $details['innings_1_runs'] ?? 0,
            'innings_1_wickets' => $details['innings_1_wickets'] ?? 0,
            'innings_1_overs' => $details['innings_1_overs'] ?? 0,
            'innings_1_extras' => $details['innings_1_extras'] ?? 0,
            'innings_2_team' => $details['innings_2_team'] ?? null,
            'innings_2_runs' => $details['innings_2_runs'] ?? 0,
            'innings_2_wickets' => $details['innings_2_wickets'] ?? 0,
            'innings_2_overs' => $details['innings_2_overs'] ?? 0,
            'innings_2_extras' => $details['innings_2_extras'] ?? 0,
            'result_type' => $details['result_type'] ?? null,
            'win_margin' => $details['win_margin'] ?? null,
            'winning_team' => $details['winning_team'] ?? null,
            'super_over_team_a' => $details['super_over_team_a'] ?? null,
            'super_over_team_b' => $details['super_over_team_b'] ?? null,
            'potm_user_id' => $details['potm_user_id'] ?? null,
            'toss_won_by' => $details['toss_won_by'] ?? null,
            'toss_decision' => $details['toss_decision'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_cricket WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            UPDATE match_cricket SET
                team_a_name = :team_a_name, team_b_name = :team_b_name,
                match_format = :match_format, overs_per_innings = :overs_per_innings,
                innings_1_team = :innings_1_team, innings_1_runs = :innings_1_runs,
                innings_1_wickets = :innings_1_wickets, innings_1_overs = :innings_1_overs,
                innings_1_extras = :innings_1_extras, innings_2_team = :innings_2_team,
                innings_2_runs = :innings_2_runs, innings_2_wickets = :innings_2_wickets,
                innings_2_overs = :innings_2_overs, innings_2_extras = :innings_2_extras,
                result_type = :result_type, win_margin = :win_margin, winning_team = :winning_team,
                super_over_team_a = :super_over_team_a, super_over_team_b = :super_over_team_b,
                potm_user_id = :potm_user_id, toss_won_by = :toss_won_by,
                toss_decision = :toss_decision, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'match_format' => $details['match_format'] ?? 'T20',
            'overs_per_innings' => $details['overs_per_innings'] ?? null,
            'innings_1_team' => $details['innings_1_team'] ?? null,
            'innings_1_runs' => $details['innings_1_runs'] ?? 0,
            'innings_1_wickets' => $details['innings_1_wickets'] ?? 0,
            'innings_1_overs' => $details['innings_1_overs'] ?? 0,
            'innings_1_extras' => $details['innings_1_extras'] ?? 0,
            'innings_2_team' => $details['innings_2_team'] ?? null,
            'innings_2_runs' => $details['innings_2_runs'] ?? 0,
            'innings_2_wickets' => $details['innings_2_wickets'] ?? 0,
            'innings_2_overs' => $details['innings_2_overs'] ?? 0,
            'innings_2_extras' => $details['innings_2_extras'] ?? 0,
            'result_type' => $details['result_type'] ?? null,
            'win_margin' => $details['win_margin'] ?? null,
            'winning_team' => $details['winning_team'] ?? null,
            'super_over_team_a' => $details['super_over_team_a'] ?? null,
            'super_over_team_b' => $details['super_over_team_b'] ?? null,
            'potm_user_id' => $details['potm_user_id'] ?? null,
            'toss_won_by' => $details['toss_won_by'] ?? null,
            'toss_decision' => $details['toss_decision'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        return [
            'category' => 'CRICKET',
            'title' => 'Cricket Match',
            'sections' => [
                [
                    'title' => 'Teams',
                    'fields' => [
                        ['name' => 'team_a_name', 'label' => 'Team A', 'type' => 'text', 'required' => true],
                        ['name' => 'team_b_name', 'label' => 'Team B', 'type' => 'text', 'required' => true]
                    ]
                ],
                [
                    'title' => 'Match Format',
                    'fields' => [
                        ['name' => 'match_format', 'label' => 'Format', 'type' => 'select', 'options' => [
                            ['value' => 'T20', 'label' => 'T20'],
                            ['value' => 'T10', 'label' => 'T10'],
                            ['value' => 'ODI', 'label' => 'ODI (50 Overs)'],
                            ['value' => 'TEST', 'label' => 'Test Match'],
                            ['value' => 'OTHER', 'label' => 'Other']
                        ]],
                        ['name' => 'overs_per_innings', 'label' => 'Overs per Innings', 'type' => 'number', 'step' => '0.1', 'min' => 1]
                    ]
                ],
                [
                    'title' => 'Toss',
                    'fields' => [
                        ['name' => 'toss_won_by', 'label' => 'Toss Won By', 'type' => 'select', 'options' => [
                            ['value' => 'A', 'label' => 'Team A'],
                            ['value' => 'B', 'label' => 'Team B']
                        ]],
                        ['name' => 'toss_decision', 'label' => 'Elected To', 'type' => 'select', 'options' => [
                            ['value' => 'BAT', 'label' => 'Bat'],
                            ['value' => 'BOWL', 'label' => 'Bowl']
                        ]]
                    ]
                ],
                [
                    'title' => 'First Innings',
                    'fields' => [
                        ['name' => 'innings_1_team', 'label' => 'Batting Team', 'type' => 'select', 'options' => [
                            ['value' => 'A', 'label' => 'Team A'],
                            ['value' => 'B', 'label' => 'Team B']
                        ]],
                        ['name' => 'innings_1_runs', 'label' => 'Runs', 'type' => 'number', 'min' => 0],
                        ['name' => 'innings_1_wickets', 'label' => 'Wickets', 'type' => 'number', 'min' => 0, 'max' => 10],
                        ['name' => 'innings_1_overs', 'label' => 'Overs', 'type' => 'number', 'step' => '0.1', 'min' => 0],
                        ['name' => 'innings_1_extras', 'label' => 'Extras', 'type' => 'number', 'min' => 0]
                    ]
                ],
                [
                    'title' => 'Second Innings',
                    'fields' => [
                        ['name' => 'innings_2_team', 'label' => 'Batting Team', 'type' => 'select', 'options' => [
                            ['value' => 'A', 'label' => 'Team A'],
                            ['value' => 'B', 'label' => 'Team B']
                        ]],
                        ['name' => 'innings_2_runs', 'label' => 'Runs', 'type' => 'number', 'min' => 0],
                        ['name' => 'innings_2_wickets', 'label' => 'Wickets', 'type' => 'number', 'min' => 0, 'max' => 10],
                        ['name' => 'innings_2_overs', 'label' => 'Overs', 'type' => 'number', 'step' => '0.1', 'min' => 0],
                        ['name' => 'innings_2_extras', 'label' => 'Extras', 'type' => 'number', 'min' => 0]
                    ]
                ],
                [
                    'title' => 'Result',
                    'fields' => [
                        ['name' => 'result_type', 'label' => 'Result Type', 'type' => 'select', 'options' => [
                            ['value' => 'WIN_RUNS', 'label' => 'Won by Runs'],
                            ['value' => 'WIN_WICKETS', 'label' => 'Won by Wickets'],
                            ['value' => 'TIE', 'label' => 'Tie'],
                            ['value' => 'DRAW', 'label' => 'Draw'],
                            ['value' => 'NO_RESULT', 'label' => 'No Result'],
                            ['value' => 'SUPER_OVER', 'label' => 'Super Over']
                        ]],
                        ['name' => 'winning_team', 'label' => 'Winning Team', 'type' => 'select', 'options' => [
                            ['value' => 'A', 'label' => 'Team A'],
                            ['value' => 'B', 'label' => 'Team B']
                        ]],
                        ['name' => 'win_margin', 'label' => 'Margin (Runs/Wickets)', 'type' => 'number', 'min' => 0],
                        ['name' => 'potm_user_id', 'label' => 'Player of the Match', 'type' => 'player_select']
                    ]
                ],
                [
                    'title' => 'Super Over',
                    'collapsible' => true,
                    'conditional' => "result_type === 'SUPER_OVER'",
                    'fields' => [
                        ['name' => 'super_over_team_a', 'label' => 'Team A Super Over Runs', 'type' => 'number', 'min' => 0],
                        ['name' => 'super_over_team_b', 'label' => 'Team B Super Over Runs', 'type' => 'number', 'min' => 0]
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
