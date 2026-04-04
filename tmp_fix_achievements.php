<?php
$file = 'c:/wamp64/www/uoc-sports/app/controllers/api/DashboardApiController.php';
$content = file_get_contents($file);

$replacement = '    private function getAchievementStats() {
        $db = Database::getConnection();
        
        // Build faculty filter based on USER\'S faculty (since students generate achievements)
        $facultyFilter = $this->currentFacultyId ? " AND u.faculty_id = " . $this->quoteFacultyId($this->currentFacultyId) : "";
        
        // Total achievements
        $totalSQL = "SELECT COUNT(*) as total FROM sport_achievements sa
                     JOIN user u ON sa.user_id = u.user_id
                     WHERE 1=1" . $facultyFilter;
        try {
            $stmt = $db->query($totalSQL);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)[\'total\'] ?? 0;
        } catch (Exception $e) {
            error_log("Achievement stats error: " . $e->getMessage());
            $total = 0;
        }
        
        // Recent achievements (last 5)
        $recentSQL = "SELECT sa.id, sa.achievement_type, sa.points, sa.date_achieved,
                             CONCAT(u.fname, \' \', u.lname) as student_name,
                             s.sport_name
                      FROM sport_achievements sa
                      JOIN user u ON sa.user_id = u.user_id
                      LEFT JOIN sport s ON sa.sport_id = s.sport_id
                      WHERE 1=1" . $facultyFilter . "
                      ORDER BY sa.date_achieved DESC
                      LIMIT 5";
        try {
            $stmt = $db->query($recentSQL);
            $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recent = [];
        }
        
        // Top performers
        $topSQL = "SELECT CONCAT(u.fname, \' \', u.lname) as student_name,
                          SUM(sa.points) as total_points,
                          COUNT(*) as achievement_count
                   FROM sport_achievements sa
                   JOIN user u ON sa.user_id = u.user_id
                   WHERE 1=1" . $facultyFilter . "
                   GROUP BY sa.user_id
                   ORDER BY total_points DESC
                   LIMIT 5";
        try {
            $stmt = $db->query($topSQL);
            $topPerformers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $topPerformers = [];
        }
        
        // Achievements by sport
        $bySportSQL = "SELECT s.sport_name, COUNT(*) as count, SUM(sa.points) as total_points
                       FROM sport_achievements sa
                       JOIN sport s ON sa.sport_id = s.sport_id
                       JOIN user u ON sa.user_id = u.user_id
                       WHERE 1=1" . $facultyFilter . "
                       GROUP BY sa.sport_id
                       ORDER BY count DESC
                       LIMIT 8";
        try {
            $stmt = $db->query($bySportSQL);
            $bySport = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $bySport = [];
        }
        
        return [
            \'total\' => $total,
            \'recent\' => $recent,
            \'top_performers\' => $topPerformers,
            \'by_sport\' => $bySport
        ];
    }';

// Regex to find getAchievementStats method
$regex = '/private function getAchievementStats\(\) \{.*?return \[.*?\];\s+\}/s';

$newContent = preg_replace($regex, $replacement, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Success: getAchievementStats refactored.\n";
} else {
    echo "Error: Target method not found.\n";
}
