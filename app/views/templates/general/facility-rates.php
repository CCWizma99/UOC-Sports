<div class="container">
        <div class="form-section">
            <h3>Search Facility Rates</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="search_facility_type">Facility Type</label>
                    <select id="search_facility_type" name="search_facility_type">
                        <option value="">All Types</option>
                        <option value="INDOOR_GYM">Indoor Gym</option>
                        <option value="GROUND">Ground</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search_facility_name">Facility Name</label>
                    <input type="text" id="search_facility_name" name="search_facility_name" placeholder="Search by name...">
            </div>
            <div style="text-align: center;">
                <button onclick="searchRates()" class="btn btn-info">Search</button>
                <button onclick="clearSearch()" class="btn btn-success">Clear</button>
            </div>
        </div>

        <div id="searchResults"></div>
    </div>
</div>

<script>
    const API_BASE = '/uoc-sports/public/api/get-facility-rates.php';

    // Search rates
    async function searchRates() {
        const facilityType = document.getElementById('search_facility_type').value;
        const facilityName = document.getElementById('search_facility_name').value;

        try {
            showLoading('searchResults');
            
            // Build query parameters
            const params = new URLSearchParams();
            if (facilityType) params.append('facility_type', facilityType);
            if (facilityName) params.append('facility_name', facilityName);

            const response = await fetch(`${API_BASE}?${params.toString()}`);
            const result = await response.json();

            // Since backend returns raw array
            if (Array.isArray(result)) {
                displayResults('searchResults', result);
            } else {
                showMessage('searchResults', 'Error: ' + (result.message || 'Unknown error'), 'error');
            }

        } catch (error) {
            showMessage('searchResults', 'Error: ' + error.message, 'error');
        }
    }

    // Clear search
    function clearSearch() {
        document.getElementById('search_facility_type').value = '';
        document.getElementById('search_facility_name').value = '';
        document.getElementById('searchResults').innerHTML = '';
    }

    // Display results in table format
    function displayResults(containerId, data) {
        const container = document.getElementById(containerId);
        
        if (!data || data.length === 0) {
            container.innerHTML = '<div class="message">No results found.</div>';
            return;
        }
        
        let html = `
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Facility Type</th>
                        <th>Facility Name</th>
                        <th>Capacity</th>
                        <th>Practice Working (Rs.)</th>
                        <th>Practice Other (Rs.)</th>
                        <th>Tournament Full Day Working (Rs.)</th>
                        <th>Tournament Half Day Working (Rs.)</th>
                        <th>Tournament Full Day Other (Rs.)</th>
                        <th>Tournament Half Day Other (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        data.forEach(row => {
            html += `
                <tr>
                    <td>${row.facility_type}</td>
                    <td>${row.facility_name}</td>
                    <td>${row.capacity || '-'}</td>
                    <td>${row.practice_working_hours ? 'Rs. ' + parseFloat(row.practice_working_hours).toFixed(2) : '-'}</td>
                    <td>${row.practice_other_hours ? 'Rs. ' + parseFloat(row.practice_other_hours).toFixed(2) : '-'}</td>
                    <td>${row.tournament_full_day_working ? 'Rs. ' + parseFloat(row.tournament_full_day_working).toFixed(2) : '-'}</td>
                    <td>${row.tournament_half_day_working ? 'Rs. ' + parseFloat(row.tournament_half_day_working).toFixed(2) : '-'}</td>
                    <td>${row.tournament_full_day_other ? 'Rs. ' + parseFloat(row.tournament_full_day_other).toFixed(2) : '-'}</td>
                    <td>${row.tournament_half_day_other ? 'Rs. ' + parseFloat(row.tournament_half_day_other).toFixed(2) : '-'}</td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
            <div style="padding: 15px; text-align: center; color: #6c757d;">
                Found ${data.length} result(s)
            </div>
        `;
        
        container.innerHTML = html;
    }

    // Show message
    function showMessage(containerId, message, type) {
        const container = document.getElementById(containerId);
        container.innerHTML = `<div class="message ${type}">${message}</div>`;
    }

    // Show loading
    function showLoading(containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '<div class="loading">Loading...</div>';
    }
</script>
