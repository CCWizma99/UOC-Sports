<div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Coach Dashboard</h1>
            <p>Manage your team schedules and members</p>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <h2 class="section-title">Summary</h2>
            <div class="summary-cards">
                <div class="card">
                    <h3>Total Schedules</h3>
                    <p>12</p>
                </div>
                <div class="card">
                    <h3>Upcoming Matches</h3>
                    <p>3</p>
                </div>
                <div class="card">
                    <h3>Players</h3>
                    <p>18</p>
                </div>
            </div>
        </div>

        <!-- Team Schedule Table -->
        <div class="table-section">
            <h2 class="section-title">Team Schedules</h2>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search schedule..." id="scheduleSearch">
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        <tr>
                            <td class="facility-cell">Sports Complex</td>
                            <td class="date-cell">2025-08-18</td>
                            <td class="time-cell">10:00 AM</td>
                            <td class="description-cell">Football Practice</td>
                        </tr>
                        <tr>
                            <td class="facility-cell">Main Ground</td>
                            <td class="date-cell">2025-08-20</td>
                            <td class="time-cell">02:00 PM</td>
                            <td class="description-cell">Cricket Match</td>
                        </tr>
                        <tr>
                            <td class="facility-cell">Indoor Court</td>
                            <td class="date-cell">2025-08-22</td>
                            <td class="time-cell">09:00 AM</td>
                            <td class="description-cell">Badminton Training</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Team Members Table -->
        <div class="table-section">
            <h2 class="section-title">Team Members</h2>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search member..." id="memberSearch">
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Player ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        <tr>
                            <td>P001</td>
                            <td>John Silva</td>
                            <td><span class="position-cell">Forward</span></td>
                            <td><button class="remove-btn" onclick="confirmRemove('John Silva')">Remove</button></td>
                        </tr>
                        <tr>
                            <td>P002</td>
                            <td>Akila Perera</td>
                            <td><span class="position-cell">Goalkeeper</span></td>
                            <td><button class="remove-btn" onclick="confirmRemove('Akila Perera')">Remove</button></td>
                        </tr>
                        <tr>
                            <td>P003</td>
                            <td>Kavindu Fernando</td>
                            <td><span class="position-cell">Defender</span></td>
                            <td><button class="remove-btn" onclick="confirmRemove('Kavindu Fernando')">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Schedule table search
        const scheduleSearch = document.getElementById('scheduleSearch');
        const scheduleTableBody = document.getElementById('scheduleTableBody');
        const scheduleRows = scheduleTableBody.querySelectorAll('tr');

        scheduleSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            scheduleRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Member table search
        const memberSearch = document.getElementById('memberSearch');
        const memberTableBody = document.getElementById('memberTableBody');
        const memberRows = memberTableBody.querySelectorAll('tr');

        memberSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            memberRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Remove member
        function confirmRemove(playerName) {
            if (confirm(`Are you sure you want to remove ${playerName} from the team?`)) {
                alert(`${playerName} has been removed from the team.`);
                // Here you would typically make an API call to remove the player
            }
        }
    </script>