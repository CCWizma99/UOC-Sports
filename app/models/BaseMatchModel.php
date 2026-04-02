<?php
/**
 * BaseMatchModel - Abstract base class for all sport-specific match models
 */
abstract class BaseMatchModel {
    protected $db;
    protected string $tableName;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    /**
     * Create the central match record
     * 
     * @param array $data Match data
     * @return string The match_id
     */
    public function createMatch(array $data): string {
        $matchId = uniqid("match_", true);
        
        $stmt = $this->db->prepare("
            INSERT INTO tournament_match 
            (match_id, tournament_id, sport_id, sport_category, match_name, match_date, winner_id, winner_name, winner_type, winner_invitational_id, result_status, is_published, submitted_by)
            VALUES (:match_id, :tournament_id, :sport_id, :sport_category, :match_name, :match_date, :winner_id, :winner_name, :winner_type, :winner_invitational_id, :result_status, :is_published, :submitted_by)
        ");
        
        $stmt->execute([
            'match_id' => $matchId,
            'tournament_id' => $data['tournament_id'],
            'sport_id' => $data['sport_id'],
            'sport_category' => $data['sport_category'],
            'match_name' => $data['match_name'],
            'match_date' => $data['match_date'],
            'winner_id' => $data['winner_id'] ?? null,
            'winner_name' => $data['winner_name'] ?? null,
            'winner_type' => $data['winner_type'] ?? null,
            'winner_invitational_id' => $data['winner_invitational_id'] ?? null,
            'result_status' => $data['result_status'] ?? 'PENDING',
            'is_published' => $data['is_published'] ?? 0,
            'submitted_by' => $data['submitted_by'] ?? null
        ]);

        
        return $matchId;
    }
    
    /**
     * Get match by ID with sport-specific details
     * 
     * @param string $matchId
     * @return array|null
     */
    public function getMatch(string $matchId): ?array {
        // Get base match data
        $stmt = $this->db->prepare("
            SELECT 
                tm.*, 
                t.tournament_name, 
                s.sport_name,
                CASE 
                    WHEN tm.winner_type = 'INTERNAL' THEN CONCAT(u.fname, ' ', u.lname)
                    WHEN tm.winner_type = 'INVITATIONAL' THEN CONCAT(ip.fname, ' ', ip.lname)
                    WHEN tm.winner_type = 'TEAM' THEN tm.winner_name
                    WHEN tm.winner_type = 'DRAW' THEN 'DRAW'
                    ELSE COALESCE(tm.winner_name, CONCAT(u.fname, ' ', u.lname))
                END as winner_display_name
            FROM tournament_match tm
            JOIN tournament t ON tm.tournament_id = t.tournament_id
            JOIN sport s ON tm.sport_id = s.sport_id
            LEFT JOIN user u ON tm.winner_id = u.user_id
            LEFT JOIN invitational_players ip ON tm.winner_invitational_id = ip.inv_player_id
            WHERE tm.match_id = :match_id
        ");
        $stmt->execute(['match_id' => $matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$match) return null;
        
        // Get sport-specific details
        $match['details'] = $this->getDetails($matchId);
        
        return $match;
    }
    
    /**
     * Add sport-specific details - to be implemented by child classes
     * 
     * @param string $matchId
     * @param array $details
     * @return bool
     */
    abstract public function addDetails(string $matchId, array $details): bool;
    
    /**
     * Get sport-specific details - to be implemented by child classes
     * 
     * @param string $matchId
     * @return array|null
     */
    abstract public function getDetails(string $matchId): ?array;
    
    /**
     * Update sport-specific details
     * 
     * @param string $matchId
     * @param array $details
     * @return bool
     */
    abstract public function updateDetails(string $matchId, array $details): bool;
    
    /**
     * Get form fields configuration for this sport category
     * Used by the frontend to dynamically render the form
     * 
     * @param string|null $sportId Optional sport ID for sport-specific field variations
     * @return array
     */
    abstract public function getFormConfig(?string $sportId = null): array;
}
