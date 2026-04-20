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
                    <div class="card-icon">📅</div>
                    <div class="card-info">
                        <h3>UPCOMING SCHEDULES</h3>
                        <p class="card-subtitle">Practices this month</p>
                    </div>
                    <p class="card-value"><?= isset($schedules) ? count($schedules) : 0 ?></p>
                </a>
                <a class="card card-link" href="#upcoming-matches">
                    <div class="card-icon">🏆</div>
                    <div class="card-info">
                        <h3>UPCOMING TOURNAMENTS</h3>
                        <p class="card-subtitle">Next 30 days</p>
                    </div>
                    <p class="card-value"><?= isset($upcoming_matches) ? count($upcoming_matches) : 0 ?></p>
                </a>
                <a class="card card-link" href="#team-members">
                    <div class="card-icon">👥</div>
                    <div class="card-info">
                        <h3>PLAYERS</h3>
                        <p class="card-subtitle">Active team members</p>
                    </div>
                    <p class="card-value"><?= isset($members) ? count($members) : 0 ?></p>
                </a>
            </div>
        </div>

        <!-- Team Schedule Table -->
        <div class="table-section" id="team-schedules">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Team Schedules</h2>
                <button class="btn-secondary" onclick="openPreviousSessions()">
                    <i class="fas fa-calendar-alt"></i> Previous Sessions
                </button>
            </div>
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search schedule..." id="scheduleSearch">
            </div>
            <div class="table-wrapper">
                <!-- DEBUG: SportID: <?= $debug_sport_id ?? 'NULL' ?>, UserID: <?= $debug_user_id ?? 'NULL' ?>, ScheduleCount: <?= count($schedules ?? []) ?> -->
                <table>
                    <thead>
                        <tr>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        <?php if (isset($schedules) && !empty($schedules)): ?>
                            <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="facility-cell"><?= htmlspecialchars($schedule['location']) ?></td>
                                <td class="date-cell"><?= htmlspecialchars($schedule['session_date']) ?></td>
                                <td class="time-cell"><?= date("g:i A", strtotime($schedule['start_time'])) ?></td>
                                <td class="time-cell"><?= date("g:i A", strtotime($schedule['end_time'])) ?></td>
                                <td class="description-cell"><?= htmlspecialchars($schedule['notes']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">No upcoming schedules found</td></tr>
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
                    <h2>Upcoming Tournaments</h2>
                </div>
                <div class="table-wrapper">
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Tournament</th>
                            </tr>
                        </thead>
                        <tbody id="upcomingMatchesBody">
                            <?php if (isset($upcoming_matches) && !empty($upcoming_matches)): ?>
                                <?php foreach ($upcoming_matches as $m): ?>
                                <tr>
                                    <td class="date-cell"><?= htmlspecialchars($m['start_date']) ?></td>
                                    <td class="venue-cell"><?= htmlspecialchars($m['end_date']) ?></td>
                                    <td><span><?= htmlspecialchars($m['name']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="no-records" style="text-align: center;">No upcoming tournaments found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Detail Modal -->
    <div class="calendar-modal-overlay" id="calendarModal">
        <div class="calendar-modal">
            <div class="calendar-modal-header">
                <h3>Session Details</h3>
                <button class="calendar-modal-close" onclick="closeCalendarModal()">&times;</button>
            </div>
            <div class="calendar-modal-body" id="calendarSessionDetails">
                <!-- Session details injected here -->
            </div>
        </div>
    </div>

    <!-- Previous Sessions Calendar Modal -->
    <div class="modal-overlay" id="previousModal">
        <div class="modal" style="width: 80%; max-width: 800px;">
            <div class="modal-header">
                <h3>Previous Practice Sessions</h3>
                <button class="modal-close" onclick="closePreviousModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="calendar" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>

    <script>
        const ATTENDANCE_API_BASE = '/uoc-sports/public/api/attendance';
        const SPORT_ID = '<?= $debug_sport_id ?? '' ?>';

        function openPreviousSessions() {
            document.getElementById('previousModal').classList.add('active');
            loadPreviousCalendar();
        }

        function closePreviousModal() {
            document.getElementById('previousModal').classList.remove('active');
        }

        function closeCalendarModal() {
            document.getElementById('calendarModal').classList.remove('active');
        }

        async function loadPreviousCalendar() {
            try {
                const response = await fetch(`${ATTENDANCE_API_BASE}/previous-sessions/${SPORT_ID}`);
                const data = await response.json();

                const calendarEl = document.getElementById('calendar');
                calendarEl.innerHTML = "";

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 500,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek'
                    },
                    events: data.sessions.map(session => ({
                        title: "Practice",
                        start: session.session_date,
                        className: "previous-session",
                        extendedProps: session
                    })),
                    eventClick: function(info) {
                        const session = info.event.extendedProps;
                        document.getElementById('calendarSessionDetails').innerHTML = `
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${session.session_date}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${session.start_time} - ${session.end_time}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Venue:</span>
                                <span class="detail-value">${session.location}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Equipment:</span>
                                <span class="detail-value">${session.need_equipment}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Notes:</span>
                                <span class="detail-value">${session.notes || '-'}</span>
                            </div>
                        `;
                        document.getElementById('calendarModal').classList.add('active');
                    }
                });

                calendar.render();
            } catch (error) {
                console.error('Error loading calendar:', error);
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closePreviousModal();
            }
            if (event.target.classList.contains('calendar-modal-overlay')) {
                closeCalendarModal();
            }
        };

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
            UI.confirm(`Are you sure you want to remove ${playerName} from the team?`, () => {
                UI.showToast(`${playerName} has been removed from the team.`, 'success');
                // Here you would typically make an API call to remove the player
            });
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