<?php
require_once __DIR__ . '/BaseMatchModel.php';

/**
 * BoardGameMatch - Model for board games
 * Sports: Chess, Scrabble, Carrom
 */
class BoardGameMatch extends BaseMatchModel {
    protected string $tableName = 'match_board_game';
    
    public function addDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            INSERT INTO match_board_game 
            (match_id, player_a_name, player_b_name, game_type, chess_result, chess_opening,
             moves_count, time_control, scrabble_score_a, scrabble_score_b, highest_word_score,
             highest_word, carrom_score_a, carrom_score_b, boards_played, notes)
            VALUES 
            (:match_id, :player_a_name, :player_b_name, :game_type, :chess_result, :chess_opening,
             :moves_count, :time_control, :scrabble_score_a, :scrabble_score_b, :highest_word_score,
             :highest_word, :carrom_score_a, :carrom_score_b, :boards_played, :notes)
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'player_a_name' => $details['player_a_name'] ?? null,
            'player_b_name' => $details['player_b_name'] ?? null,
            'game_type' => $details['game_type'] ?? 'CHESS',
            'chess_result' => $details['chess_result'] ?? null,
            'chess_opening' => $details['chess_opening'] ?? null,
            'moves_count' => $details['moves_count'] ?? null,
            'time_control' => $details['time_control'] ?? null,
            'scrabble_score_a' => $details['scrabble_score_a'] ?? null,
            'scrabble_score_b' => $details['scrabble_score_b'] ?? null,
            'highest_word_score' => $details['highest_word_score'] ?? null,
            'highest_word' => $details['highest_word'] ?? null,
            'carrom_score_a' => $details['carrom_score_a'] ?? null,
            'carrom_score_b' => $details['carrom_score_b'] ?? null,
            'boards_played' => $details['boards_played'] ?? 1,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getDetails(string $matchId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM match_board_game WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function updateDetails(string $matchId, array $details): bool {
        $stmt = $this->db->prepare("
            UPDATE match_board_game SET
                player_a_name = :player_a_name, player_b_name = :player_b_name,
                game_type = :game_type, chess_result = :chess_result,
                chess_opening = :chess_opening, moves_count = :moves_count,
                time_control = :time_control, scrabble_score_a = :scrabble_score_a,
                scrabble_score_b = :scrabble_score_b, highest_word_score = :highest_word_score,
                highest_word = :highest_word, carrom_score_a = :carrom_score_a,
                carrom_score_b = :carrom_score_b, boards_played = :boards_played, notes = :notes
            WHERE match_id = :match_id
        ");
        
        return $stmt->execute([
            'match_id' => $matchId,
            'player_a_name' => $details['player_a_name'] ?? null,
            'player_b_name' => $details['player_b_name'] ?? null,
            'game_type' => $details['game_type'] ?? 'CHESS',
            'chess_result' => $details['chess_result'] ?? null,
            'chess_opening' => $details['chess_opening'] ?? null,
            'moves_count' => $details['moves_count'] ?? null,
            'time_control' => $details['time_control'] ?? null,
            'scrabble_score_a' => $details['scrabble_score_a'] ?? null,
            'scrabble_score_b' => $details['scrabble_score_b'] ?? null,
            'highest_word_score' => $details['highest_word_score'] ?? null,
            'highest_word' => $details['highest_word'] ?? null,
            'carrom_score_a' => $details['carrom_score_a'] ?? null,
            'carrom_score_b' => $details['carrom_score_b'] ?? null,
            'boards_played' => $details['boards_played'] ?? 1,
            'notes' => $details['notes'] ?? null
        ]);
    }
    
    public function getFormConfig(?string $sportId = null): array {
        $gameType = match($sportId) {
            'CHE' => 'CHESS',
            'SCR' => 'SCRABBLE',
            'CRM' => 'CARROM',
            default => 'CHESS'
        };
        
        $config = [
            'category' => 'BOARD_GAME',
            'title' => 'Board Game Match',
            'gameType' => $gameType,
            'sections' => [
                [
                    'title' => 'Players',
                    'fields' => [
                        ['name' => 'player_a_name', 'label' => 'Player A', 'type' => 'text', 'required' => true],
                        ['name' => 'player_b_name', 'label' => 'Player B', 'type' => 'text', 'required' => true],
                        ['name' => 'game_type', 'label' => 'Game Type', 'type' => 'hidden', 'value' => $gameType]
                    ]
                ]
            ]
        ];
        
        // Chess-specific fields
        if ($sportId === 'CHE') {
            $config['sections'][] = [
                'title' => 'Chess Details',
                'fields' => [
                    ['name' => 'chess_result', 'label' => 'Result', 'type' => 'select', 'options' => [
                        ['value' => 'WHITE_WIN', 'label' => 'White Wins'],
                        ['value' => 'BLACK_WIN', 'label' => 'Black Wins'],
                        ['value' => 'DRAW', 'label' => 'Draw'],
                        ['value' => 'STALEMATE', 'label' => 'Stalemate']
                    ]],
                    ['name' => 'chess_opening', 'label' => 'Opening', 'type' => 'text', 'placeholder' => 'e.g., Sicilian Defense'],
                    ['name' => 'moves_count', 'label' => 'Number of Moves', 'type' => 'number', 'min' => 1],
                    ['name' => 'time_control', 'label' => 'Time Control', 'type' => 'text', 'placeholder' => 'e.g., 10+5, Classical']
                ]
            ];
        }
        
        // Scrabble-specific fields
        if ($sportId === 'SCR') {
            $config['sections'][] = [
                'title' => 'Scrabble Score',
                'fields' => [
                    ['name' => 'scrabble_score_a', 'label' => 'Player A Score', 'type' => 'number', 'min' => 0],
                    ['name' => 'scrabble_score_b', 'label' => 'Player B Score', 'type' => 'number', 'min' => 0],
                    ['name' => 'highest_word', 'label' => 'Highest Scoring Word', 'type' => 'text'],
                    ['name' => 'highest_word_score', 'label' => 'Highest Word Score', 'type' => 'number', 'min' => 0]
                ]
            ];
        }
        
        // Carrom-specific fields
        if ($sportId === 'CRM') {
            $config['sections'][] = [
                'title' => 'Carrom Score',
                'fields' => [
                    ['name' => 'carrom_score_a', 'label' => 'Player A Score', 'type' => 'number', 'min' => 0],
                    ['name' => 'carrom_score_b', 'label' => 'Player B Score', 'type' => 'number', 'min' => 0],
                    ['name' => 'boards_played', 'label' => 'Boards Played', 'type' => 'number', 'min' => 1, 'default' => 1]
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
