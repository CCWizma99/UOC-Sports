<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * TeamGoalMatch - Model for team goal sports
 * Sports: Football, Hockey, Rugby, Netball, Elle
 */
class TeamGoalMatch extends BaseMatchModel {
    protected string $tableName = 'match_team_goal';
    
    public function addDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            INSERT INTO match_team_goal 
            (match_id, team_a_name, team_b_name, team_a_goals, team_b_goals,
             team_a_yellow_cards, team_b_yellow_cards, team_a_red_cards, team_b_red_cards,
             team_a_tries, team_b_tries, team_a_conversions, team_b_conversions,
             team_a_penalties, team_b_penalties, extra_time, penalty_shootout,
             penalty_score_a, penalty_score_b, notes)
            VALUES 
            (:match_id, :team_a_name, :team_b_name, :team_a_goals, :team_b_goals,
             :team_a_yellow_cards, :team_b_yellow_cards, :team_a_red_cards, :team_b_red_cards,
             :team_a_tries, :team_b_tries, :team_a_conversions, :team_b_conversions,
             :team_a_penalties, :team_b_penalties, :extra_time, :penalty_shootout,
             :penalty_score_a, :penalty_score_b, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'team_a_goals' => $details['team_a_goals'] ?? 0,
            'team_b_goals' => $details['team_b_goals'] ?? 0,
            'team_a_yellow_cards' => $details['team_a_yellow_cards'] ?? 0,
            'team_b_yellow_cards' => $details['team_b_yellow_cards'] ?? 0,
            'team_a_red_cards' => $details['team_a_red_cards'] ?? 0,
            'team_b_red_cards' => $details['team_b_red_cards'] ?? 0,
            'team_a_tries' => $details['team_a_tries'] ?? null,
            'team_b_tries' => $details['team_b_tries'] ?? null,
            'team_a_conversions' => $details['team_a_conversions'] ?? null,
            'team_b_conversions' => $details['team_b_conversions'] ?? null,
            'team_a_penalties' => $details['team_a_penalties'] ?? null,
            'team_b_penalties' => $details['team_b_penalties'] ?? null,
            'extra_time' => $details['extra_time'] ?? false,
            'penalty_shootout' => $details['penalty_shootout'] ?? false,
            'penalty_score_a' => $details['penalty_score_a'] ?? null,
            'penalty_score_b' => $details['penalty_score_b'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_team_goal WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            UPDATE match_team_goal SET
                team_a_name = :team_a_name,
                team_b_name = :team_b_name,
                team_a_goals = :team_a_goals,
                team_b_goals = :team_b_goals,
                team_a_yellow_cards = :team_a_yellow_cards,
                team_b_yellow_cards = :team_b_yellow_cards,
                team_a_red_cards = :team_a_red_cards,
                team_b_red_cards = :team_b_red_cards,
                extra_time = :extra_time,
                penalty_shootout = :penalty_shootout,
                penalty_score_a = :penalty_score_a,
                penalty_score_b = :penalty_score_b,
                notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'team_a_name' => $details['team_a_name'] ?? null,
            'team_b_name' => $details['team_b_name'] ?? null,
            'team_a_goals' => $details['team_a_goals'] ?? 0,
            'team_b_goals' => $details['team_b_goals'] ?? 0,
            'team_a_yellow_cards' => $details['team_a_yellow_cards'] ?? 0,
            'team_b_yellow_cards' => $details['team_b_yellow_cards'] ?? 0,
            'team_a_red_cards' => $details['team_a_red_cards'] ?? 0,
            'team_b_red_cards' => $details['team_b_red_cards'] ?? 0,
            'extra_time' => $details['extra_time'] ?? false,
            'penalty_shootout' => $details['penalty_shootout'] ?? false,
            'penalty_score_a' => $details['penalty_score_a'] ?? null,
            'penalty_score_b' => $details['penalty_score_b'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        $config = [
            'category' => 'TEAM_GOAL',
            'title' => 'Team Goal Match',
            'sections' => [
                [
                    'title' => 'Teams',
                    'fields' => [
                        ['name' => 'team_a_name', 'label' => 'Team A Name', 'type' => 'text', 'required' => true],
                        ['name' => 'team_b_name', 'label' => 'Team B Name', 'type' => 'text', 'required' => true]
                    ]
                ],
                [
                    'title' => 'Score',
                    'fields' => [
                        ['name' => 'team_a_goals', 'label' => 'Team A Goals', 'type' => 'number', 'min' => 0, 'default' => 0],
                        ['name' => 'team_b_goals', 'label' => 'Team B Goals', 'type' => 'number', 'min' => 0, 'default' => 0]
                    ]
                ],
                [
                    'title' => 'Cards',
                    'collapsible' => true,
                    'fields' => [
                        ['name' => 'team_a_yellow_cards', 'label' => 'Team A Yellow Cards', 'type' => 'number', 'min' => 0, 'default' => 0],
                        ['name' => 'team_b_yellow_cards', 'label' => 'Team B Yellow Cards', 'type' => 'number', 'min' => 0, 'default' => 0],
                        ['name' => 'team_a_red_cards', 'label' => 'Team A Red Cards', 'type' => 'number', 'min' => 0, 'default' => 0],
                        ['name' => 'team_b_red_cards', 'label' => 'Team B Red Cards', 'type' => 'number', 'min' => 0, 'default' => 0]
                    ]
                ],
                [
                    'title' => 'Extra Time / Penalties',
                    'collapsible' => true,
                    'fields' => [
                        ['name' => 'extra_time', 'label' => 'Extra Time Played', 'type' => 'checkbox'],
                        ['name' => 'penalty_shootout', 'label' => 'Penalty Shootout', 'type' => 'checkbox'],
                        ['name' => 'penalty_score_a', 'label' => 'Team A Penalty Score', 'type' => 'number', 'min' => 0, 'conditional' => 'penalty_shootout'],
                        ['name' => 'penalty_score_b', 'label' => 'Team B Penalty Score', 'type' => 'number', 'min' => 0, 'conditional' => 'penalty_shootout']
                    ]
                ]
            ]
        ];
        
        // Add rugby-specific fields
        if ($sportId === 'RUG') {
            $config['sections'][] = [
                'title' => 'Rugby Details',
                'fields' => [
                    ['name' => 'team_a_tries', 'label' => 'Team A Tries', 'type' => 'number', 'min' => 0],
                    ['name' => 'team_b_tries', 'label' => 'Team B Tries', 'type' => 'number', 'min' => 0],
                    ['name' => 'team_a_conversions', 'label' => 'Team A Conversions', 'type' => 'number', 'min' => 0],
                    ['name' => 'team_b_conversions', 'label' => 'Team B Conversions', 'type' => 'number', 'min' => 0],
                    ['name' => 'team_a_penalties', 'label' => 'Team A Penalty Goals', 'type' => 'number', 'min' => 0],
                    ['name' => 'team_b_penalties', 'label' => 'Team B Penalty Goals', 'type' => 'number', 'min' => 0]
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
