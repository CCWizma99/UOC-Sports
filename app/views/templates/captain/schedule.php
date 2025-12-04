<div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Schedule Practice</h1>
            <p>Create new practice sessions for your team</p>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <h2>New Practice Session</h2>
            <form action="/uoc-sports/public/captain/schedule-practice" method="post" id="scheduleForm">
                <!-- Facility Dropdown -->
                <div class="form-group">
                    <label for="facility">Select Facility</label>
                    <select id="facility" name="facility" required>
                        <option value="">-- Select Facility --</option>
                    </select>
                </div>

                <!-- Date & Time -->
                <div class="datetime">
                    <div class="form-group">
                        <label for="date">Select Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Select Time</label>
                        <input type="time" id="time" name="time" required>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter practice details..."></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="create" id="createBtn" class="btn-primary">Schedule Practice</button>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h2>Scheduled Practices</h2>
            </div>
            <div class="table-wrapper">
                <table class="practice-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Facility</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="no-records">No schedules found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/uoc-sports/public/api/get-facility-rates.php';
        let timeout = null;

        // Load facilities on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFacilities();
        });

        async function loadFacilities() {
            try {
                const response = await fetch(`${API_BASE}?all=true`);
                const facilities = await response.json();
                
                const selectBox = document.getElementById('facility');
                const defaultOption = selectBox.querySelector('option[value=""]');
                
                // Clear existing options except the default
                while (selectBox.options.length > 1) {
                    selectBox.remove(1);
                }
                
                if (Array.isArray(facilities) && facilities.length > 0) {
                    facilities.forEach(f => {
                        const option = document.createElement('option');
                        option.value = f.facility_name;
                        option.textContent = f.facility_name;
                        selectBox.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading facilities:', error);
            }
        }

        async function searchFacilities() {
            const name = document.getElementById('search_facility_name').value.trim();
            const suggestionBox = document.getElementById('suggestions');
            const detailsBox = document.getElementById('facilityDetails');

            if (!name) {
                suggestionBox.innerHTML = '';
                detailsBox.innerHTML = '';
                detailsBox.classList.add('hidden');
                return;
            }

            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`${API_BASE}?facility_name=${encodeURIComponent(name)}`);
                    const results = await response.json();

                    if (!Array.isArray(results) || results.length === 0) {
                        suggestionBox.innerHTML = '<div class="no-results">No facilities found.</div>';
                        return;
                    }

                    suggestionBox.innerHTML = results.map(r => `
                        <div class="facility-card" onclick='showDetails(${JSON.stringify(r)})'>
                            <h4>${r.facility_name}</h4>
                            <p class="type">${r.facility_type.replace('_', ' ')}</p>
                        </div>
                    `).join('');

                } catch (error) {
                    suggestionBox.innerHTML = `<div class="error">Error fetching facilities.</div>`;
                }
            }, 400);
        }

        function showDetails(data) {
            const detailsBox = document.getElementById('facilityDetails');
            detailsBox.classList.remove('hidden');
            document.getElementById('suggestions').innerHTML = '';

            detailsBox.innerHTML = `
                <div class="facility-info">
                    <h3>${data.facility_name}</h3>
                    <p><strong>Type:</strong> ${data.facility_type.replace('_', ' ')}</p>
                    ${data.capacity ? `<p><strong>Capacity:</strong> ${data.capacity}</p>` : ''}
                    <div class="rate-grid">
                        <div><strong>Practice (Working Days):</strong><span>${formatRate(data.practice_working_hours)}</span></div>
                        <div><strong>Practice (Other Days):</strong><span>${formatRate(data.practice_other_hours)}</span></div>
                        <div><strong>Tournament Full Day (Working Days):</strong><span>${formatRate(data.tournament_full_day_working)}</span></div>
                        <div><strong>Tournament Half Day (Working Days):</strong><span>${formatRate(data.tournament_half_day_working)}</span></div>
                        <div><strong>Tournament Full Day (Other Days):</strong><span>${formatRate(data.tournament_full_day_other)}</span></div>
                        <div><strong>Tournament Half Day (Other Days):</strong><span>${formatRate(data.tournament_half_day_other)}</span></div>
                    </div>
                </div>
            `;
        }

        function formatRate(val) {
            return val ? `Rs. ${parseFloat(val).toFixed(2)}` : '-';
        }
    </script>
