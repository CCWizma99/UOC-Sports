<div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Welcome Back, Suresh Kumara</h1>
            <p>Manage your team schedules and members</p>
            <br>
            <h3>Sport: Volleyball</h3>
        </div>
        

        <!-- Summary Section -->
        <div class="summary-section">
            <h2 class="section-title">Summary</h2>
            <div class="summary-cards">
                <div class="card">
                    <h3>Total Schedules</h3>
                    <p><?= isset($schedules) ? count($schedules) : 0 ?></p>
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
            <div class="table-header">
                <h2>Team Schedules</h2>
            </div>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search schedule..." id="scheduleSearch">
            </div>
            <div class="table-wrapper">
                <!-- DEBUG: SportID: <?= $debug_sport_id ?? 'NULL' ?>, UserID: <?= $debug_user_id ?? 'NULL' ?>, ScheduleCount: <?= count($schedules ?? []) ?> -->
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
                        <?php if (isset($schedules) && !empty($schedules)): ?>
                            <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="facility-cell"><?= htmlspecialchars($schedule['facility']) ?></td>
                                <td class="date-cell"><?= htmlspecialchars($schedule['session_date']) ?></td>
                                <td class="time-cell"><?= htmlspecialchars($schedule['session_time']) ?></td>
                                <td class="description-cell"><?= htmlspecialchars($schedule['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No upcoming schedules found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Team Members Table -->
        <!-- DEBUG: Members Count: <?= isset($members) ? count($members) : 'Not Set' ?> -->
        <div class="table-section">
            <div class="table-header">
                <h2>Team Members</h2>
            </div>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search member..." id="memberSearch">
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Player ID</th>
                            <th>Name</th>
                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        <?php if (isset($members) && !empty($members)): ?>
                            <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= htmlspecialchars($member['student_id'] ?? $member['user_id']) ?></td>
                                <td><?= htmlspecialchars($member['fname'] . ' ' . $member['lname']) ?></td>
                                
                                <td><button class="remove-btn" onclick="confirmRemove('<?= htmlspecialchars(addslashes($member['fname'] . ' ' . $member['lname'])) ?>')">Remove</button></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No team members found</td></tr>
                        <?php endif; ?>
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