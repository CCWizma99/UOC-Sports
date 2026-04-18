<?php

class TournamentAward {
    private $db;

    /** Sport-specific award titles (3 per category) */
    private static $awardTitles = [
        'CRICKET'     => ['Best Batsman', 'Best Bowler', 'Man of the Series'],
        'TEAM_GOAL'   => ['Golden Boot', 'Best Defender', 'MVP'],
        'BALL_COURT'  => ['Best Spiker', 'Best Setter', 'MVP'],
        'RACKET'      => ['Best Singles Player', 'Best Doubles Player', 'MVP'],
        'COMBAT'      => ['Best Fighter', 'Most Technical', 'MVP'],
        'TRACK_FIELD' => ['Best Sprinter', 'Best Field Athlete', 'MVP'],
        'BOARD_GAME'  => ['Best Strategist', 'Most Consistent', 'MVP'],
        'WEIGHT'      => ['Best Lifter', 'Most Improved', 'MVP']
    ];

    public function __construct() {
        $this->db = Database::getConnection();
        $this->ensureTableExists();
    }

    /**
     * Get available award titles for a sport category
     */
    public static function getTitlesForCategory($sportCategory) {
        return self::$awardTitles[$sportCategory] ?? ['MVP', 'Best Player', 'Most Improved'];
    }

    /**
     * Add a tournament award and auto-create achievement record
     * Points are determined by tournament match_level
     */
    public function addAward($data) {
        try {
            $this->db->beginTransaction();

            // Determine points from tournament match_level
            $points = $this->getPointsForLevel($data['tournament_id']);

            // Insert award (with UNIQUE constraint preventing duplicates)
            $sql = "INSERT INTO tournament_awards (tournament_id, sport_id, user_id, award_title, points, awarded_by)
                    VALUES (:tournament_id, :sport_id, :user_id, :award_title, :points, :awarded_by)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tournament_id' => $data['tournament_id'],
                'sport_id'      => $data['sport_id'],
                'user_id'       => $data['user_id'],
                'award_title'   => $data['award_title'],
                'points'        => $points,
                'awarded_by'    => $data['awarded_by'] ?? null
            ]);

            // Also insert into achievement table for the points trigger
            $achSql = "INSERT INTO achievement (user_id, sport_id, tournament_id, achievement, points, status)
                       VALUES (:user_id, :sport_id, :tournament_id, :achievement, :points, 'ACTIVE')";
            $achStmt = $this->db->prepare($achSql);
            $achStmt->execute([
                'user_id'       => $data['user_id'],
                'sport_id'      => $data['sport_id'],
                'tournament_id' => $data['tournament_id'],
                'achievement'   => $data['award_title'],
                'points'        => $points
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            // Check for duplicate key (award already exists)
            if ($e->getCode() == 23000) {
                error_log("Duplicate award: " . $data['award_title'] . " for user " . $data['user_id']);
                return 'duplicate';
            }
            error_log("Add award error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get points based on tournament match level
     */
    private function getPointsForLevel($tournamentId) {
        $sql = "SELECT match_level FROM tournament WHERE tournament_id = :tid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tid' => $tournamentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $level = $result['match_level'] ?? 'UNIVERSITY';
        switch ($level) {
            case 'NATIONAL':       return 5;
            case 'INTERNATIONAL':  return 5;
            default:               return 3; // UNIVERSITY
        }
    }

    /**
     * Get all awards for a tournament
     */
    public function getAwardsByTournament($tournamentId) {
        $sql = "SELECT ta.*, u.fname, u.lname, u.email,
                       CONCAT(ab.fname, ' ', ab.lname) as awarded_by_name
                FROM tournament_awards ta
                LEFT JOIN user u ON ta.user_id = u.user_id
                LEFT JOIN user ab ON ta.awarded_by = ab.user_id
                WHERE ta.tournament_id = :tournament_id
                ORDER BY ta.award_title, ta.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tournament_id' => $tournamentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all awards earned by a student
     */
    public function getAwardsByStudent($userId) {
        $sql = "SELECT ta.*, t.tournament_name, t.match_level, s.sport_name
                FROM tournament_awards ta
                LEFT JOIN tournament t ON ta.tournament_id = t.tournament_id
                LEFT JOIN sport s ON ta.sport_id = s.sport_id
                WHERE ta.user_id = :user_id
                ORDER BY ta.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if an award already exists for a tournament/user/title combo
     */
    public function awardExists($tournamentId, $userId, $awardTitle) {
        $sql = "SELECT COUNT(*) FROM tournament_awards 
                WHERE tournament_id = :tid AND user_id = :uid AND award_title = :title";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tid' => $tournamentId, 'uid' => $userId, 'title' => $awardTitle]);
        return $stmt->fetchColumn() > 0;
    }

    private function ensureTableExists() {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `tournament_awards` (
              `id` int NOT NULL AUTO_INCREMENT,
              `tournament_id` varchar(24) NOT NULL,
              `sport_id` varchar(4) NOT NULL,
              `user_id` varchar(12) NOT NULL,
              `award_title` varchar(100) NOT NULL,
              `points` int NOT NULL DEFAULT 3,
              `awarded_by` varchar(12) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `idx_unique_award` (`tournament_id`, `user_id`, `award_title`),
              KEY `idx_tournament` (`tournament_id`),
              KEY `idx_user` (`user_id`),
              KEY `idx_sport` (`sport_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}
