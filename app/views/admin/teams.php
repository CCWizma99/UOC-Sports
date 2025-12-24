<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Teams | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/teams-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';

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

<div class="main-content-wrapper">
    <div class="teams-grid-container">
        <div class="teams-grid-left">
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
        </div>

        <div class="teams-grid-right">
            <div id="search-team">
                <h2>Search for a Team</h2>
                <input type="text" id="team-search" placeholder="Search by Sport or Team name">
                <div id="search-results">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>Find a Team</h3>
                        <p>Search by sport or team name to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('team-search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('search-results');

    if (!query) {
        resultsDiv.innerHTML = `<div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>Find a Team</h3>
            <p>Search by sport or team name to view details</p>
        </div>`;
        return;
    }

    fetch('./admin-teams/search-team?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<ul>';
                data.data.forEach(team => {
                    html += `<li>${team.sport_name} 
                             <a href="admin-team-details?sport_id=${team.sport_id}">View Team</a></li>`;
                });
                html += '</ul>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p>No results found</p>';
            }
        });
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-teams");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
