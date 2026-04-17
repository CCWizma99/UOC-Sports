<?php
require_once __DIR__ . '/../../core/Database.php';

class Achievements {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all players for a sport and competition (students in the sport)
     */
    public function getPlayersByCompetition($sportId, $competitionId) {
        $query = "SELECT u.user_id, u.fname, u.lname, u.email
                  FROM user u
                  WHERE u.sport_id = ? AND u.type = 'STUDENT'
                  ORDER BY u.fname, u.lname";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get individual achievements for a competition
     */
    public function getIndividualAchievementsByCompetition($competitionId) {
        $query = "SELECT a.*, u.fname, u.lname
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  WHERE a.tournament_id = ? AND a.user_id != 'TEAM'
                  ORDER BY a.points DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$competitionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all achievements for players in a specific sport
     * @param string $sportId
     * @return array
     */
    public function getBySport($sportId) {
        $query = "SELECT 
                    a.achievement_id,
                    a.user_id,
                    a.sport_id,
                    a.competition_id,
                    a.achievement,
                    a.points,
                    u.fname,
                    u.lname,
                    u.email,
                    t.tournament_name,
                    t.start_date as tournament_date
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  LEFT JOIN tournament t ON a.tournament_id = t.tournament_id
                  WHERE a.sport_id = ?
                  ORDER BY a.points DESC, t.start_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get team achievements for a sport (no user_id)
     * @param string $sportId
     * @return array
     */
    public function getTeamAchievements($sportId) {
        $query = "SELECT a.*, t.tournament_name, t.start_date as tournament_date
                  FROM achievement a
                  LEFT JOIN tournament t ON a.tournament_id = t.tournament_id
                  WHERE a.sport_id = ? AND (a.user_id IS NULL OR a.user_id = '')
                  ORDER BY t.start_date DESC, a.achievement_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get individual achievements for a sport (has user_id)
     * @param string $sportId
     * @return array
     */
    public function getIndividualAchievements($sportId) {
        $query = "SELECT a.*, u.fname, u.lname, t.tournament_name, t.start_date as tournament_date
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  LEFT JOIN tournament t ON a.tournament_id = t.tournament_id
                  WHERE a.sport_id = ? AND a.user_id IS NOT NULL AND a.user_id != ''
                  ORDER BY t.start_date DESC, a.points DESC, a.achievement_id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
