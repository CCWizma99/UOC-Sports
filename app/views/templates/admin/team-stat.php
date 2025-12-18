<?php
// Fetch team statistics
$pdo = Database::getConnection();

try {
    // Get total number of sports
    $stmt = $pdo->query("SELECT COUNT(*) as total_sports FROM sport");
    $total_sports = $stmt->fetch(PDO::FETCH_ASSOC)['total_sports'];
    
    // Get total team members across all sports
    $stmt = $pdo->query("
        SELECT COUNT(st.student_id) as total_members
        FROM `sports-team` st
        INNER JOIN user u ON st.student_id = u.user_id
        WHERE u.status = 'ACTIVE'
    ");
    $total_members = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];
    
    // Calculate average team size
    $avg_team_size = $total_sports > 0 ? round($total_members / $total_sports, 1) : 0;
    
} catch(PDOException $e) {
    $total_sports = 0;
    $total_members = 0;
    $avg_team_size = 0;
}
?>

<div class="team-stat-container">
    <div class="stat-header">
        <h2><i class="fas fa-users"></i> Team Overview</h2>
        <p>Manage and monitor sports teams</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <strong><?php echo $total_sports; ?></strong>
                <p>Total Sports</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-content">
                <strong><?php echo $total_members; ?></strong>
                <p>Total Members</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <strong><?php echo $avg_team_size; ?></strong>
                <p>Avg Team Size</p>
            </div>
        </div>
    </div>
</div>
