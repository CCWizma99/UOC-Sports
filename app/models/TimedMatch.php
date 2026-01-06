<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * TimedMatch - Model for track & field / timed sports
 * Sports: Athletics, Swimming, Rowing, Road Race
 */
class TimedMatch extends BaseMatchModel {
    protected string $tableName = 'match_timed';
    
    public function addDetails(string $matchId, array $details): bool {
        $results = isset($details['results']) && is_array($details['results']) 
            ? json_encode($details['results']) 
            : $details['results'] ?? null;
            
        $stmt = $this->db->prepare("
            INSERT INTO match_timed 
            (match_id, event_type, event_name, results, winning_time, winning_distance,
             winner_user_id, is_record, record_type, weather_conditions, wind_speed, notes)
            VALUES 
            (:match_id, :event_type, :event_name, :results, :winning_time, :winning_distance,
             :winner_user_id, :is_record, :record_type, :weather_conditions, :wind_speed, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'event_type' => $details['event_type'] ?? 'SPRINT',
            'event_name' => $details['event_name'] ?? null,
            'results' => $results,
            'winning_time' => $details['winning_time'] ?? null,
            'winning_distance' => $details['winning_distance'] ?? null,
            'winner_user_id' => $details['winner_user_id'] ?? null,
            'is_record' => $details['is_record'] ?? false,
            'record_type' => $details['record_type'] ?? null,
            'weather_conditions' => $details['weather_conditions'] ?? null,
            'wind_speed' => $details['wind_speed'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_timed WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['results']) {
            $result['results'] = json_decode($result['results'], true);
        }
        
        return $result ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $results = isset($details['results']) && is_array($details['results']) 
            ? json_encode($details['results']) 
            : $details['results'] ?? null;
            
        $stmt = $this->db->prepare("
            UPDATE match_timed SET
                event_type = :event_type, event_name = :event_name, results = :results,
                winning_time = :winning_time, winning_distance = :winning_distance,
                winner_user_id = :winner_user_id, is_record = :is_record,
                record_type = :record_type, weather_conditions = :weather_conditions,
                wind_speed = :wind_speed, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'event_type' => $details['event_type'] ?? 'SPRINT',
            'event_name' => $details['event_name'] ?? null,
            'results' => $results,
            'winning_time' => $details['winning_time'] ?? null,
            'winning_distance' => $details['winning_distance'] ?? null,
            'winner_user_id' => $details['winner_user_id'] ?? null,
            'is_record' => $details['is_record'] ?? false,
            'record_type' => $details['record_type'] ?? null,
            'weather_conditions' => $details['weather_conditions'] ?? null,
            'wind_speed' => $details['wind_speed'] ?? null,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        $eventTypes = [];
        
        if ($sportId === 'ATH') {
            $eventTypes = [
                ['value' => 'SPRINT', 'label' => 'Sprint (100m, 200m, 400m)'],
                ['value' => 'MIDDLE_DISTANCE', 'label' => 'Middle Distance (800m, 1500m)'],
                ['value' => 'LONG_DISTANCE', 'label' => 'Long Distance (5000m, 10000m)'],
                ['value' => 'RELAY', 'label' => 'Relay'],
                ['value' => 'FIELD_THROW', 'label' => 'Throw (Shot Put, Discus, Javelin)'],
                ['value' => 'FIELD_JUMP', 'label' => 'Jump (Long Jump, High Jump, Triple Jump)']
            ];
        } elseif ($sportId === 'SWI') {
            $eventTypes = [
                ['value' => 'SWIMMING', 'label' => 'Swimming Event']
            ];
        } elseif ($sportId === 'ROW') {
            $eventTypes = [
                ['value' => 'ROWING', 'label' => 'Rowing Event']
            ];
        } elseif ($sportId === 'RR') {
            $eventTypes = [
                ['value' => 'ROAD_RACE', 'label' => 'Road Race']
            ];
        } else {
            $eventTypes = [
                ['value' => 'SPRINT', 'label' => 'Sprint'],
                ['value' => 'MIDDLE_DISTANCE', 'label' => 'Middle Distance'],
                ['value' => 'LONG_DISTANCE', 'label' => 'Long Distance'],
                ['value' => 'RELAY', 'label' => 'Relay'],
                ['value' => 'SWIMMING', 'label' => 'Swimming'],
                ['value' => 'ROWING', 'label' => 'Rowing'],
                ['value' => 'FIELD_THROW', 'label' => 'Field Throw'],
                ['value' => 'FIELD_JUMP', 'label' => 'Field Jump'],
                ['value' => 'ROAD_RACE', 'label' => 'Road Race']
            ];
        }
        
        return [
            'category' => 'TRACK_FIELD',
            'title' => 'Timed/Measured Event',
            'sections' => [
                [
                    'title' => 'Event Details',
                    'fields' => [
                        ['name' => 'event_type', 'label' => 'Event Type', 'type' => 'select', 'options' => $eventTypes],
                        ['name' => 'event_name', 'label' => 'Event Name', 'type' => 'text', 'placeholder' => 'e.g., 100m Finals, 50m Freestyle']
                    ]
                ],
                [
                    'title' => 'Results',
                    'fields' => [
                        ['name' => 'results', 'label' => 'Participant Results', 'type' => 'participant_results', 
                         'description' => 'Add participants with their time/distance and position'],
                        ['name' => 'winning_time', 'label' => 'Winning Time (seconds)', 'type' => 'number', 'step' => '0.001', 'min' => 0],
                        ['name' => 'winning_distance', 'label' => 'Winning Distance (meters)', 'type' => 'number', 'step' => '0.01', 'min' => 0],
                        ['name' => 'winner_user_id', 'label' => 'Winner', 'type' => 'player_select']
                    ]
                ],
                [
                    'title' => 'Records',
                    'collapsible' => true,
                    'fields' => [
                        ['name' => 'is_record', 'label' => 'New Record?', 'type' => 'checkbox'],
                        ['name' => 'record_type', 'label' => 'Record Type', 'type' => 'select', 'conditional' => 'is_record', 'options' => [
                            ['value' => 'PERSONAL_BEST', 'label' => 'Personal Best'],
                            ['value' => 'UNIVERSITY_RECORD', 'label' => 'University Record'],
                            ['value' => 'NATIONAL_RECORD', 'label' => 'National Record']
                        ]]
                    ]
                ],
                [
                    'title' => 'Conditions',
                    'collapsible' => true,
                    'fields' => [
                        ['name' => 'weather_conditions', 'label' => 'Weather', 'type' => 'text', 'placeholder' => 'e.g., Sunny, 25°C'],
                        ['name' => 'wind_speed', 'label' => 'Wind Speed (m/s)', 'type' => 'number', 'step' => '0.1']
                    ]
                ],
                [
                    'title' => 'Notes',
                    'fields' => [
                        ['name' => 'notes', 'label' => 'Event Notes', 'type' => 'textarea']
                    ]
                ]
            ]
        ];
    }
}
