<style>
.card.card-link { text-decoration: none; color: inherit; cursor: pointer; }
#team-schedules, #team-members, #upcoming-matches { scroll-margin-top: 120px; }

.summary-section {
  margin-bottom: 40px;
}

.summary-header {
  background: linear-gradient(135deg, #2b0c4d 0%, #2b0c4d 70%, #1f1722 100%);
  padding: 25px 30px;
  color: white;
  border-radius: 16px 16px 0 0;
}

.summary-header h2 {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
  padding: 24px 0;
}
</style>

<div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Welcome Back, <?= isset($coach_name) ? $coach_name : 'Coach' ?></h1>
            <p>Manage your team schedules and members</p>
            <br>
            <h3>Sport: <?= isset($sport_name) ? $sport_name : 'Not Assigned' ?></h3>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-header">
                <h2>Summary</h2>
            </div>
            <div class="summary-cards">
                <a class="card card-link" href="#team-schedules">
                    <h3>Total Schedules</h3>
                    <p><?= isset($schedules) ? count($schedules) : 0 ?></p>
                </a>
                <a class="card card-link" href="#upcoming-matches">
                    <h3>Upcoming Matches</h3>
                    <p><?= isset($upcoming_matches) ? count($upcoming_matches) : 0 ?></p>
                </a>
                <a class="card card-link" href="#team-members">
                    <h3>Players</h3>
                    <p><?= isset($members) ? count($members) : 0 ?></p>
                </a>
            </div>
        </div>

        <!-- Team Schedule Table -->
        <div class="table-section" id="team-schedules">
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
                                <td class="time-cell"><?= htmlspecialchars($schedule['start_time']) ?></td>
                                <td class="description-cell"><?= htmlspecialchars($schedule['notes']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No upcoming schedules found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Two-column: Team Members + Upcoming Matches -->
        <div class="two-column" style="display:flex; gap:20px; align-items:flex-start; margin-top:20px;">
            <!-- Team Members Table -->
            <div class="table-section" id="team-members" style="flex:1;">
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

            <!-- Upcoming Matches Table (sidebar-like, parallel to members) -->
            <div class="table-section" id="upcoming-matches" style="flex:1;">
                <div class="table-header">
                    <h2>Upcoming Matches</h2>
                </div>
                <div class="table-wrapper">
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Event</th>
                            </tr>
                        </thead>
                        <tbody id="upcomingMatchesBody">
                            <?php if (isset($upcoming_matches) && !empty($upcoming_matches)): ?>
                                <?php foreach ($upcoming_matches as $m): ?>
                                <tr>
                                    <td class="date-cell"><?= htmlspecialchars($m['date'] ?? $m['session_date'] ?? 'N/A') ?></td>
                                    <td class="venue-cell"><?= htmlspecialchars($m['time'] ?? $m['session_time'] ?? 'N/A') ?></td>
                                    <td><span><?= htmlspecialchars($m['title'] ?? $m['description'] ?? 'Match') ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="date-cell">05 Jan 2026</td>
                                    <td class="venue-cell">09:00 AM</td>
                                    <td><span>Inter-Faculty Friendly</span></td>
                                </tr>
                                <tr>
                                    <td class="date-cell">18 Jan 2026</td>
                                    <td class="venue-cell">02:30 PM</td>
                                    <td><span>Training Camp</span></td>
                                </tr>
                                <tr>
                                    <td class="date-cell">28 Jan 2026</td>
                                    <td class="venue-cell">06:00 PM</td>
                                    <td><span>Friendly Match vs Alumni</span></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

        // Smooth scroll when clicking the summary cards (account for fixed header)
        document.querySelectorAll('.card-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (e.metaKey || e.ctrlKey) return;
                e.preventDefault();
                const targetId = this.getAttribute('href').slice(1);
                const target = document.getElementById(targetId);
                if (target) {
                    const header = document.querySelector('.page-header');
                    const headerOffset = header ? header.offsetHeight + 20 : 120;
                    const targetRect = target.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const targetY = targetRect.top + scrollTop - headerOffset;
                    window.scrollTo({ top: targetY, behavior: 'smooth' });
                    // focus relevant search input when available
                    if (targetId === 'team-schedules') {
                        const s = document.getElementById('scheduleSearch'); if (s) s.focus();
                    } else if (targetId === 'team-members') {
                        const m = document.getElementById('memberSearch'); if (m) m.focus();
                    }
                }
            });
        });
    </script>