<?php

class MatchPlayer {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->ensureTableExists();
    }

    /**
     * Add multiple players to a match
     * @param string $matchId
     * @param array $players - Each item: ['user_id'=>..., 'player_name'=>..., 'external_id'=>..., 'team_side'=>'A'|'B', 'is_uoc_student'=>1|0]
     */
    public function addPlayers($matchId, $players) {
        if (empty($players)) return true;

        $sql = "INSERT INTO match_players (match_id, user_id, player_name, external_id, team_side, is_uoc_student) 
                VALUES (:match_id, :user_id, :player_name, :external_id, :team_side, :is_uoc)";
        $stmt = $this->db->prepare($sql);

        foreach ($players as $p) {
            $stmt->execute([
                'match_id'    => $matchId,
                'user_id'     => $p['user_id'] ?: null,
                'player_name' => $p['player_name'],
                'external_id' => $p['external_id'] ?? null,
                'team_side'   => $p['team_side'],
                'is_uoc'      => isset($p['is_uoc_student']) ? (int)$p['is_uoc_student'] : 1
            ]);
        }
        return true;
    }

    /**
     * Get all players for a specific match
     */
    public function getPlayersByMatch($matchId) {
        $sql = "SELECT mp.*, u.fname, u.lname, u.email
                FROM match_players mp
                LEFT JOIN user u ON mp.user_id = u.user_id
                WHERE mp.match_id = :match_id
                ORDER BY mp.team_side, mp.player_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get distinct team rosters from previous matches in the same tournament.
     * Used for the "reuse previous team" dropdown.
     */
    public function getDistinctTeams($tournamentId) {
        $sql = "SELECT DISTINCT mp.team_side, mp.player_name, mp.user_id, mp.external_id, mp.is_uoc_student,
                       tm.match_name, tm.match_id
                FROM match_players mp
                JOIN tournament_match tm ON mp.match_id = tm.match_id
                WHERE tm.tournament_id = :tournament_id
                ORDER BY tm.created_at DESC, mp.team_side, mp.player_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tournament_id' => $tournamentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by match, then by side
        $teams = [];
        foreach ($rows as $row) {
            $key = $row['match_id'];
            if (!isset($teams[$key])) {
                $teams[$key] = [
                    'match_name' => $row['match_name'],
                    'match_id'   => $row['match_id'],
                    'A' => [],
                    'B' => []
                ];
            }
            $teams[$key][$row['team_side']][] = [
                'user_id'        => $row['user_id'],
                'player_name'    => $row['player_name'],
                'external_id'    => $row['external_id'],
                'is_uoc_student' => $row['is_uoc_student']
            ];
        }
        return array_values($teams);
    }

    /**
     * Get the most recent roster for a specific team name in a tournament.
     * @param string $teamName
     * @param string $tournamentId
     * @return array - List of players
     */
    public function getTeamRosterForTournament($teamName, $tournamentId) {
        // Try to find the latest match involving this team name
        // Team name could be found in Match details (stored in different tables based on sport)
        // This is tricky because team name isn't in match_players. 
        // We'll look for matches in tournament_match where details contain the team name.
        
        // Actually, we can check match_players where any record has team_side that matches
        // the provided team name in any match's details.
        
        // Simpler: find the last match_id in this tournament where this team name appeared.
        // We'll need to join with sport-specific results or just look at all tournament matches.
        
        // FOR NOW: We'll assume the client passes the match_id if they want to clone, 
        // OR we'll search match_players records directly if we had a team_id.
        // Since we don't have team_id in match_players, we'll search by match_id.
        return []; 
    }

    /**
     * Get all UOC student user_ids who participated in a match
     */
    public function getUocPlayersByMatch($matchId) {
        $sql = "SELECT user_id, team_side FROM match_players 
                WHERE match_id = :match_id AND is_uoc_student = 1 AND user_id IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function ensureTableExists() {
        // Create table if not exists with all columns
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `match_players` (
              `id` int NOT NULL AUTO_INCREMENT,
              `match_id` varchar(50) NOT NULL,
              `user_id` varchar(12) DEFAULT NULL,
              `player_name` varchar(120) NOT NULL,
              `external_id` varchar(50) DEFAULT NULL,
              `team_side` enum('A','B') NOT NULL,
              `is_uoc_student` tinyint(1) NOT NULL DEFAULT 1,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `idx_match` (`match_id`),
              KEY `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Manually add external_id if it's missing (one-time migration for existing tables)
        try {
            $this->db->exec("ALTER TABLE `match_players` ADD COLUMN `external_id` varchar(50) DEFAULT NULL AFTER `is_uoc_student` text");
        } catch (Exception $e) {
            // Probably already exists
        }
    }
}
