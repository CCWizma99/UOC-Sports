<?php

class SportAchievements extends Model {
    
    /**
     * Get all achievements with user and competition details
     */
    public function getAll($filters = []) {
        $query = "SELECT 
                    a.achievement_id,
                    a.user_id,
                    a.sport_id,
                    a.competition_id,
                    a.tournament_year,
                    a.achievement,
                    a.points,
                    u.fname,
                    u.lname,
                    u.email,
                    s.sport_name,
                    c.competition_name,
                    c.date as competition_date
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  LEFT JOIN sport s ON a.sport_id = s.sport_id
                  LEFT JOIN competition c ON a.competition_id = c.competition_id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['sport_id'])) {
            $query .= " AND a.sport_id = ?";
            $params[] = $filters['sport_id'];
        }
        
        if (!empty($filters['user_id'])) {
            $query .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['competition_id'])) {
            $query .= " AND a.competition_id = ?";
            $params[] = $filters['competition_id'];
        }
        
        $query .= " ORDER BY a.points DESC, c.date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get student rankings by total points
     */
    public function getStudentRankings($sportId = null, $limit = 10) {
        $query = "SELECT 
                    up.user_id,
                    u.fname,
                    u.lname,
                    u.email,
                    up.user_points,
                    COUNT(DISTINCT a.achievement_id) as total_achievements,
                    COUNT(DISTINCT a.competition_id) as competitions_participated
                  FROM user_points up
                  LEFT JOIN user u ON up.user_id = u.user_id
                  LEFT JOIN achievement a ON up.user_id = a.user_id";
        
        $params = [];
        
        if ($sportId) {
            $query .= " WHERE a.sport_id = ?";
            $params[] = $sportId;
        }
        
        $query .= " GROUP BY up.user_id, u.fname, u.lname, u.email, up.user_points
                    ORDER BY up.user_points DESC";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get achievements for a specific student
     */
    public function getByStudent($userId) {
        $query = "SELECT 
                    a.achievement_id,
                    a.achievement,
                    a.points,
                    a.sport_id,
                    a.tournament_year,
                    s.sport_name,
                    c.competition_name,
                    c.date as competition_date
                  FROM achievement a
                  LEFT JOIN sport s ON a.sport_id = s.sport_id
                  LEFT JOIN competition c ON a.competition_id = c.competition_id
                  WHERE a.user_id = ?
                  ORDER BY a.tournament_year DESC, c.date DESC, a.points DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get student total points
     */
    public function getStudentPoints($userId) {
        $query = "SELECT user_points FROM user_points WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['user_points'] : 0;
    }
    
    /**
     * Get achievements by sport
     */
    public function getBySport($sportId) {
        return $this->getAll(['sport_id' => $sportId]);
    }
    
    /**
     * Get top performers for a specific achievement type
     */
    public function getTopPerformers($achievementType, $sportId = null, $limit = 5) {
        $query = "SELECT 
                    a.user_id,
                    u.fname,
                    u.lname,
                    u.email,
                    COUNT(*) as achievement_count,
                    SUM(a.points) as total_points,
                    s.sport_name
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  LEFT JOIN sport s ON a.sport_id = s.sport_id
                  WHERE a.achievement = ?";
        
        $params = [$achievementType];
        
        if ($sportId) {
            $query .= " AND a.sport_id = ?";
            $params[] = $sportId;
        }
        
        $query .= " GROUP BY a.user_id, u.fname, u.lname, u.email, s.sport_name
                    ORDER BY achievement_count DESC, total_points DESC
                    LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get achievement statistics for a sport
     */
    public function getStatsBySport($sportId) {
        $query = "SELECT 
                    COUNT(DISTINCT a.user_id) as total_students,
                    COUNT(DISTINCT a.competition_id) as total_competitions,
                    COUNT(*) as total_achievements,
                    SUM(a.points) as total_points,
                    AVG(a.points) as avg_points_per_achievement
                  FROM achievement a
                  WHERE a.sport_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sportId]);
        $overall = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get breakdown by achievement type
        $query2 = "SELECT 
                    a.achievement,
                    COUNT(*) as count
                  FROM achievement a
                  WHERE a.sport_id = ?
                  GROUP BY a.achievement
                  ORDER BY count DESC";
        
        $stmt2 = $this->db->prepare($query2);
        $stmt2->execute([$sportId]);
        $breakdown = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'overall' => $overall,
            'breakdown' => $breakdown
        ];
    }
    
    /**
     * Create new achievement
     */
    public function create($data) {
        $query = "INSERT INTO achievement (user_id, sport_id, competition_id, achievement) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['sport_id'],
            $data['competition_id'],
            $data['achievement']
        ]);
    }
    
    /**
     * Update achievement
     */
    public function update($id, $data) {
        $query = "UPDATE achievement 
                  SET achievement = ?
                  WHERE achievement_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['achievement'],
            $id
        ]);
    }
    
    /**
     * Delete achievement
     */
    public function delete($id) {
        $query = "DELETE FROM achievement WHERE achievement_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get achievement by ID
     */
    public function getById($id) {
        $query = "SELECT 
                    a.*,
                    u.fname,
                    u.lname,
                    s.sport_name,
                    c.competition_name
                  FROM achievement a
                  LEFT JOIN user u ON a.user_id = u.user_id
                  LEFT JOIN sport s ON a.sport_id = s.sport_id
                  LEFT JOIN competition c ON a.competition_id = c.competition_id
                  WHERE a.achievement_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
