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

?>

<div class="main-content-wrapper">
    <div class="teams-container">
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
