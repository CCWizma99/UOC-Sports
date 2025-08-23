<div id="search-team">
    <h2>Search for a Team</h2>
    <input type="text" id="team-search" placeholder="Search by Sport or Team name">
    <div id="search-results"></div>
</div>

<script>
document.getElementById('team-search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('search-results');

    if (!query) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch('./admin-teams/search-team?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<ul>';
                data.data.forEach(team => {
                    html += `<li>${team.sport_name} 
                             - <a href="teams.php?sport_id=${team.sport_id}">View</a></li>`;
                });
                html += '</ul>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p>No results found</p>';
            }
        });
});
</script>
