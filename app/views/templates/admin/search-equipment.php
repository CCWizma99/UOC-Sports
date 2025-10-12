<div id="Search-equipment">
    <h2>Search Equipment</h2>
    <input type="text" id="equipment-search" placeholder="Type equipment name, ID, or category">
    <div id="search-results"></div>
</div>

<script>
document.getElementById('equipment-search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('search-results');

    if (!query) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch('admin-equipments/search-equipment?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<ul>';
                data.data.forEach(eq => {
                    html += `
                    <li>
                        <img src="./images/equipment/${eq.image_name}" alt="${eq.equipment_name}">
                        <div class="equipment-info">
                            <strong>${eq.equipment_name} (${eq.category})</strong>
                            <span>Condition: ${eq.equipment_condition}</span>
                            <span>Quantity: ${eq.quantity}</span>
                        </div>
                    </li>
                    `;
                });
                html += '</ul>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p>No results found</p>';
            }
        })
        .catch(err => console.error('Error fetching equipment:', err));
});
</script>