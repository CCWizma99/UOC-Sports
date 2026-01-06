<?php
/**
 * MatchResultFactory - Factory class for sport-specific match models
 * 
 * Returns the appropriate model class based on sport category.
 */
class MatchResultFactory {
    
    /**
     * Get the appropriate match model for a sport category
     * 
     * @param string $sportCategory The sport category (TEAM_GOAL, RACKET, CRICKET, etc.)
     * @return object The sport-specific match model
     * @throws Exception If sport category is unknown
     */
    public static function getModel(string $sportCategory) {
        return match($sportCategory) {
            'TEAM_GOAL' => new TeamGoalMatch(),
            'RACKET' => new RacketMatch(),
            'CRICKET' => new CricketMatch(),
            'COMBAT' => new CombatMatch(),
            'TRACK_FIELD' => new TimedMatch(),
            'BOARD_GAME' => new BoardGameMatch(),
            'BALL_COURT' => new BallCourtMatch(),
            'WEIGHT' => new WeightLiftingMatch(),
            default => throw new Exception("Unknown sport category: $sportCategory")
        };
    }
    
    /**
     * Get sport category from sport ID
     * 
     * @param string $sportId The sport ID
     * @return string|null The sport category or null if not found
     */
    public static function getSportCategory(string $sportId): ?string {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT sport_category FROM sport WHERE sport_id = :sport_id");
        $stmt->execute(['sport_id' => $sportId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['sport_category'] : null;
    }
    
    /**
     * Get all sports grouped by category
     * 
     * @return array Sports grouped by category
     */
    public static function getSportsGroupedByCategory(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT sport_id, sport_name, sport_category FROM sport ORDER BY sport_category, sport_name");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $grouped = [];
        foreach ($results as $sport) {
            $grouped[$sport['sport_category']][] = $sport;
        }
        return $grouped;
    }
    
    /**
     * Get table name for a sport category
     * 
     * @param string $sportCategory The sport category
     * @return string The table name
     */
    public static function getTableName(string $sportCategory): string {
        return match($sportCategory) {
            'TEAM_GOAL' => 'match_team_goal',
            'RACKET' => 'match_racket',
            'CRICKET' => 'match_cricket',
            'COMBAT' => 'match_combat',
            'TRACK_FIELD' => 'match_timed',
            'BOARD_GAME' => 'match_board_game',
            'BALL_COURT' => 'match_ball_court',
            'WEIGHT' => 'match_weight_lifting',
            default => throw new Exception("Unknown sport category: $sportCategory")
        };
    }
}
