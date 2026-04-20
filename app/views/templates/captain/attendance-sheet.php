<?php
// Get captain's sport from session
$captainSportId = $_SESSION['captain_sport_id'] ?? '';

// If not in session, try to get from database
if (empty($captainSportId) && isset($_SESSION['user_id'])) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE captain_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && !empty($result['sport_id'])) {
        $captainSportId = $result['sport_id'];
        $_SESSION['captain_sport_id'] = $captainSportId;
    }
}
?>

<div class="container">
    <!-- Header -->
    <div class="header">
        <h1 class="title">Mark Team Attendance</h1>
        <p class="subtitle">Select a practice session to mark attendance</p>
    </div>

    <!-- Practice Session Selector -->
    <div class="session-selector-section">
        <div class="form-group">
            <label for="practiceSession">Practice Session <span class="required">*</span></label>
            <select id="practiceSession" class="form-select" required>
                <option value="">-- Select a practice session --</option>
            </select>
        </div>
        
        <!-- Session Details (Hidden until session selected) -->
        <div id="sessionDetails" class="session-info" style="display: none;">
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <span><strong>Date:</strong> <span id="sessionDate"></span></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span><strong>Time:</strong> <span id="startTime"></span></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-location-dot"></i>
                    <span><strong>Location:</strong> <span id="sessionLocation"></span></span>
                </div>
            </div>
            <div class="session-description" id="sessionDescription"></div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="loading-state" style="display: none;">
        <div class="spinner"></div>
        <p>Loading team members...</p>
    </div>

    <!-- Attendance Section (Hidden until session selected) -->
    <div id="attendanceSection" style="display: none;">
        <!-- Action Buttons (always visible) -->
        <div class="action-buttons">
            <button class="btn btn-secondary" onclick="openAttendanceRecords()">
                <i class="fas fa-history"></i> View Previous Records
            </button>
            <button class="btn btn-secondary" onclick="openLastDayAttendance()">
                <i class="fas fa-calendar-day"></i> Last Day Attendance
            </button>
        </div>

        <!-- Attendance Summary -->
        <div class="attendance-summary">
            <div class="summary-title">Overall Attendance</div>
            <div class="summary-count" id="attendanceSummary">0/0 Players Present</div>
        </div>

        <!-- Attendance Table and Submit Button (conditionally shown) -->
        <div id="attendanceMarkingSection">
            <div class="table-section">
                <div class="table-header">
                    <div>Student Name</div>
                    <div>ID Number</div>
                    <div>Present</div>
                    <div>Attendance %</div>
                </div>
                <div id="teamMembersContainer">
                    <!-- Team members will be loaded here dynamically -->
                </div>
            </div>
            
            <!-- Pagination Controls -->
            <div id="paginationControls" class="pagination-controls"></div>

            <button class="submit-btn" id="submitBtn" onclick="submitAttendance()">
                <i class="fas fa-check"></i> Submit Attendance
            </button>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-state">
        <i class="fas fa-calendar-xmark"></i>
        <h3>No Practice Session Selected</h3>
        <p>Please select a practice session from the dropdown above to mark attendance.</p>
    </div>
</div>

<!-- Previous Attendance Records Modal -->
<div id="recordsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Attendance Records</h2>
<button onclick="closeAttendanceRecords()">×</button>
        </div>
        <div class="modal-body" id="recordsModalBody">
        <div class="filter-section">
    <label>Select Date</label>
<div class="date-picker-wrapper">
    <input type="text" id="historyDate" class="form-select" placeholder="Select Date" />
    <i class="fas fa-calendar calendar-icon"></i>
</div>


</div>

<div id="historySessions"></div>
<div id="historyDetails"></div>

</div>

<div id="recordsResult"></div>

    </div>
</div>

<!-- Last Day Attendance Modal -->
<div id="lastDayModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Last Day Attendance</h2>
            <button class="close-btn" onclick="closeLastDayAttendance()">&times;</button>
        </div>
        <div class="modal-body" id="lastDayModalBody">
            <div class="loading-state">
                <div class="spinner"></div>
                <p>Loading last session...</p>
            </div>
        </div>
    </div>
</div>

<!-- Submission Warning Modal -->
<div id="cautionModal" class="modal">
    <div class="modal-content warning-theme">
        <div class="modal-header warning">
            <h2><i class="fas fa-exclamation-triangle"></i> Incomplete Review</h2>
            <button class="close-btn" onclick="closeCautionModal()">&times;</button>
        </div>
        <div class="modal-body text-center">
            <div class="warning-icon">
                <i class="fas fa-file-circle-exclamation"></i>
            </div>
            <p class="warning-text">
                You have only viewed <strong id="visitedCount">0</strong> out of <strong id="totalCount">0</strong> pages of students.
            </p>
            <p class="secondary-text">
                Submitting now might lead to missing attendance records for some students. Are you sure you want to proceed?
            </p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeCautionModal()">Go Back</button>
                <button class="btn btn-warning" onclick="executeFinalSubmit()">Continue to Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.session-selector-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2d3748;
}

.required {
    color: #e53e3e;
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
    background: white;
}

.form-select:focus {
    outline: none;
    border-color: #5e2d91;
    box-shadow: 0 0 0 3px rgba(94, 45, 145, 0.1);
}

.session-info {
    margin-top: 20px;
    padding: 20px;
    background: #f7fafc;
    border-radius: 8px;
    border-left: 4px solid #5e2d91;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item i {
    color: #5e2d91;
    font-size: 18px;
}

.session-description {
    padding-top: 15px;
    border-top: 1px solid #e2e8f0;
    color: #4a5568;
}

.loading-state {
    text-align: center;
    padding: 40px;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #5e2d91;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.empty-state i {
    font-size: 64px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2d3748;
    margin-bottom: 10px;
}

.empty-state p {
    color: #718096;
}

.subtitle {
    color: #718096;
    margin-top: 5px;
}

.submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ===== Modal Improvements ===== */

.modal-content {
    border-radius: 16px;
    padding: 0; /* remove big white spacing */
    overflow: visible;
    background: #f8fafc; /* soft background instead of white block */
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 22px;
    background: linear-gradient(135deg, #2b0c4d 0%, #2b0c4d 70%, #1f1722 100%);
    color: white;
    border-bottom: none;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
}

.modal-header h2 {
    margin: 0;
    font-size: 18px;
}

.modal-header button {
    background: transparent;   /* remove white box */
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: 0.2s;
}

.modal-header button:hover {
    opacity: 0.7;
}

.modal-body {
    padding: 20px;
    border-radius: 12px;
    position: relative; /* Base for absolute positioned calendar */
}





/* Session Card */
.history-session {
    background: #ffffff;
    padding: 18px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border-left: 5px solid #5e2d91;
    transition: 0.3s ease;
}

#historySessions button {
    width: 50%;
    text-align: left;
    background: white;
    border: 1px solid #e2e8f0;
    padding: 12px 14px;
    border-radius: 8px;
    transition: 0.2s;
    font-weight: 500;
}

#historySessions button:hover {
    background:linear-gradient(135deg, #2b0c4d 0%, #2b0c4d 70%, #1f1722 100%);
    color: white;
    border-color: #5e2d91;
}

.history-session:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

/* Session Header */
.history-session h4 {
    margin: 0;
    font-size: 16px;
    color: #2d3748;
}

.history-session p {
    margin: 6px 0 12px;
    color: #718096;
    font-size: 14px;
}

/* Attendance List */
.attendance-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    background: white;
    border: 1px solid #edf2f7;
    transition: 0.2s;
}

.attendance-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.05);
}

/* Present Badge */
.attendance-item.present {
    border-left: 4px solid #48bb78;
}

/* Absent Badge */
.attendance-item.absent {
    border-left: 4px solid #f56565;
}

/* Status Text Styling */
.attendance-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-present {
    background: #e6fffa;
    color: #2f855a;
}

.status-absent {
    background: #fff5f5;
    color: #c53030;
}

/* Date Picker Styling */
.filter-section {
    margin-bottom: 20px;
}

.filter-section label {
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
}

.flatpickr-calendar {
    z-index: 99999 !important; /* ensure it stays above modal */
}

.history-session {
    background: white;
    padding: 20px;
    margin-top: 15px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border-left: 4px solid #5e2d91;
}

.history-session h4 {
    margin-bottom: 10px;
}

#historyDate {
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    padding: 10px;
    transition: 0.3s;
}

#historyDate:focus {
    border-color: #5e2d91;
    box-shadow: 0 0 0 3px rgba(94, 45, 145, 0.1);
}

.flatpickr-day.has-session {
    background: #5e2d91;
    color: white;
    border-radius: 50%;
}

.flatpickr-day.has-session:hover {
    background: #4c2375;
}

.date-picker-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

/* Icon removed */

#historyDate::placeholder {
    color: #718096;
    opacity: 1;
}

.filter-section .form-select {
    width: 100%;
}

/* Removed forced absolute positioning to allow calendar to float correctly */


/* Pagination Controls */
.pagination-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-bottom: 25px;
    padding: 10px;
}

.pagination-btn {
    padding: 8px 14px;
    border: 1.5px solid #e2e8f0;
    background: white;
    color: #4a5568;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
    border-color: #6b3fa0;
    color: #6b3fa0;
    background: #f8fafc;
}

.pagination-btn.active {
    background: #6b3fa0;
    color: white;
    border-color: #6b3fa0;
    box-shadow: 0 4px 10px rgba(107, 63, 160, 0.2);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f1f5f9;
}

.pagination-info {
    font-size: 14px;
    color: #718096;
    margin: 0 10px;
    font-weight: 500;
}

.pagination-btn.visited:not(.active) {
    background: #f0e6ff;
    border-color: #9f7aea;
    color: #6b3fa0;
}

/* Warning Modal Specifics */
.warning-theme {
    max-width: 450px !important;
}

.modal-header.warning {
    background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%);
}

.warning-icon {
    font-size: 48px;
    color: #f57c00;
    margin-bottom: 20px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.warning-text {
    font-size: 18px;
    color: #2d3748;
    margin-bottom: 12px;
}

.secondary-text {
    font-size: 14px;
    color: #718096;
    margin-bottom: 30px;
}

.text-center { text-align: center; }

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-warning {
    background: #f57c00;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.btn-warning:hover {
    background: #e65100;
    box-shadow: 0 4px 12px rgba(245, 124, 0, 0.3);
}

</style>

<script>
    const SPORT_ID = '<?php echo $captainSportId; ?>';
    let currentSessionId = null;
    let teamMembers = [];
    let attendanceData = {};
    
    // Pagination state
    let currentPage = 1;
    let visitedPages = new Set();
    const itemsPerPage = 10;

    // Load available sessions on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAvailableSessions();
    });

    // Load available practice sessions (recent past and today)
    async function loadAvailableSessions() {
        try {
            const response = await fetch(`/uoc-sports/public/api/attendance/previous-sessions/${SPORT_ID}`);
            const data = await response.json();

            const select = document.getElementById('practiceSession');
            select.innerHTML = '<option value="">-- Select a practice session --</option>';

            if (data.status === 'success' && data.sessions.length > 0) {
                data.sessions.forEach(session => {
                    const option = document.createElement('option');
                    option.value = session.id;
                    option.textContent = `${session.session_date} at ${session.start_time} - ${session.location}`;
                    option.dataset.session = JSON.stringify(session);
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No upcoming sessions found</option>';
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            showNotification('Failed to load practice sessions', 'error');
        }
    }

    // Handle session selection
    document.getElementById('practiceSession').addEventListener('change', async function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption.value) {
            hideAttendanceSection();
            return;
        }

        const session = JSON.parse(selectedOption.dataset.session);
        currentSessionId = session.id;
        
        // IMPORTANT: Clear previous attendance data and visited pages when switching sessions
        attendanceData = {};
        visitedPages = new Set();
        document.getElementById('sessionDate').textContent = session.session_date;
        document.getElementById('startTime').textContent = session.start_time;
        document.getElementById('sessionLocation').textContent = session.location;
        document.getElementById('sessionDescription').textContent = session.description || 'Regular practice session';
        document.getElementById('sessionDetails').style.display = 'block';

       const now = new Date();
const sessionDateTime = new Date(session.session_date + " " + session.start_time);

const attendanceSection = document.getElementById('attendanceSection');
const markingSection = document.getElementById('attendanceMarkingSection');

attendanceSection.style.display = 'block';
document.getElementById('emptyState').style.display = 'none';

if (isNaN(sessionDateTime)) {
    console.error("Invalid session date/time format");
    markingSection.style.display = 'none';
    return;
}

if (sessionDateTime > now) {
    markingSection.style.display = 'none';
    showNotification("You cannot mark attendance before session time.", 'info');
} else {
    markingSection.style.display = 'block';
    await loadTeamMembers();
}


    });

    // Load team members with attendance percentages
    async function loadTeamMembers() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('attendanceSection').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';

        try {
            const response = await fetch(`/uoc-sports/public/api/attendance/team-members/${SPORT_ID}`);
            const data = await response.json();

            if (data.status === 'success') {
                teamMembers = data.members;
                renderTeamMembers();
                
                // Check if attendance already exists for this session
                await checkExistingAttendance();
                
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('attendanceSection').style.display = 'block';
            } else {
                throw new Error('Failed to load team members');
            }
        } catch (error) {
            console.error('Error loading team members:', error);
            document.getElementById('loadingState').style.display = 'none';
            showNotification('Failed to load team members', 'error');
        }
    }

    // Check if attendance already exists
    async function checkExistingAttendance() {
        try {
            const response = await fetch(`/uoc-sports/public/api/attendance/session/${currentSessionId}`);
            const data = await response.json();

            if (data.status === 'success' && data.data.length > 0) {
                // Pre-fill existing attendance
                data.data.forEach(record => {
                    attendanceData[record.user_id] = record.status;
                });
                updateAttendanceButtons();
                showNotification('Loaded existing attendance for this session', 'info');
            }
        } catch (error) {
            console.error('Error checking existing attendance:', error);
        }
    }

    // Render team members (Wraps pagination)
    function renderTeamMembers() {
        currentPage = 1;
        visitedPages = new Set();
        
        // Initial setup for attendanceData if not already set by existing attendance
        teamMembers.forEach(member => {
            if (!attendanceData[member.user_id]) {
                attendanceData[member.user_id] = 'PRESENT';
            }
        });

        renderTablePage(currentPage);
        updateAttendanceCount();
    }

    // Render a specific page of the table
    function renderTablePage(page) {
        currentPage = page;
        visitedPages.add(page);
        const container = document.getElementById('teamMembersContainer');
        container.innerHTML = '';

        if (teamMembers.length === 0) {
            container.innerHTML = '<div class="empty-message">No team members found</div>';
            document.getElementById('paginationControls').innerHTML = '';
            return;
        }

        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, teamMembers.length);
        const pageMembers = teamMembers.slice(startIndex, endIndex);

        pageMembers.forEach(member => {
            const status = attendanceData[member.user_id] || 'PRESENT';
            const statusClass = status === 'PRESENT' ? 'present' : (status === 'ABSENT' ? 'absent' : '');
            const buttonText = status === 'PRESENT' ? 'Present' : 'Absent';

            const row = document.createElement('div');
            row.className = 'table-row';
            row.innerHTML = `
                <div class="student-name">${member.fname} ${member.lname}</div>
                <div class="student-id">${member.student_id}</div>
                <div>
                    <button class="attendance-toggle ${statusClass}" 
                            data-user-id="${member.user_id}" 
                            onclick="toggleAttendance(this)">
                        ${buttonText}
                    </button>
                </div>
                <div class="percentage">${member.attendance_percentage}%</div>
            `;
            container.appendChild(row);
        });

        renderPaginationControls();
    }

    // Render pagination buttons
    function renderPaginationControls() {
        const controls = document.getElementById('paginationControls');
        const totalPages = Math.ceil(teamMembers.length / itemsPerPage);

        if (totalPages <= 1) {
            controls.innerHTML = '';
            return;
        }

        let html = `
            <button class="pagination-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            const isActive = i === currentPage;
            const isVisited = visitedPages.has(i);
            const classes = `pagination-btn ${isActive ? 'active' : ''} ${isVisited ? 'visited' : ''}`;
            
            // Logic to show fewer pages if many exist
            if (totalPages > 7) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<button class="${classes}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="pagination-info">...</span>`;
                }
            } else {
                html += `<button class="${classes}" onclick="changePage(${i})">${i}</button>`;
            }
        }

        html += `
            <button class="pagination-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        controls.innerHTML = html;
    }

    // Change page
    function changePage(page) {
        const totalPages = Math.ceil(teamMembers.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        renderTablePage(page);
        
        // Scroll to top of table section for better UX
        document.getElementById('attendanceMarkingSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Update attendance buttons based on data
    function updateAttendanceButtons() {
        Object.keys(attendanceData).forEach(userId => {
            const button = document.querySelector(`[data-user-id="${userId}"]`);
            if (button) {
                if (attendanceData[userId] === 'PRESENT') {
                    button.classList.add('present');
                    button.classList.remove('absent');
                    button.textContent = 'Present';
                } else {
                    button.classList.add('absent');
                    button.classList.remove('present');
                    button.textContent = 'Absent';
                }
            }
        });
        updateAttendanceCount();
    }

    // Toggle attendance status
    function toggleAttendance(button) {
        const userId = button.dataset.userId;
        
        if (button.classList.contains('present')) {
            button.classList.remove('present');
            button.classList.add('absent');
            button.textContent = 'Absent';
            attendanceData[userId] = 'ABSENT';
        } else if (button.classList.contains('absent')) {
            button.classList.remove('absent');
            button.textContent = 'Present';
            attendanceData[userId] = 'PRESENT';
        } else {
            button.classList.add('present');
            button.textContent = 'Present';
            attendanceData[userId] = 'PRESENT';
        }
        
        updateAttendanceCount();
    }

    // Update attendance count
    function updateAttendanceCount() {
        const presentCount = Object.values(attendanceData).filter(status => status === 'PRESENT').length;
        const totalCount = Object.keys(attendanceData).length;
        document.getElementById('attendanceSummary').textContent = `${presentCount}/${totalCount} Players Present`;
    }

    // Submit attendance
    async function submitAttendance() {
        if (!currentSessionId) {
            showNotification('Please select a practice session', 'error');
            return;
        }

        if (Object.keys(attendanceData).length === 0) {
            showNotification('No attendance data to submit', 'error');
            return;
        }

        // Check if all pages have been visited
        const totalPages = Math.ceil(teamMembers.length / itemsPerPage);
        if (visitedPages.size < totalPages) {
            document.getElementById('visitedCount').textContent = visitedPages.size;
            document.getElementById('totalCount').textContent = totalPages;
            document.getElementById('cautionModal').classList.add('show');
            return;
        }

        executeFinalSubmit();
    }

    // Modal helpers
    function closeCautionModal() {
        document.getElementById('cautionModal').classList.remove('show');
    }

    // Final submission execution
    async function executeFinalSubmit() {
        closeCautionModal();
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const response = await fetch('/uoc-sports/public/api/attendance/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    practice_id: currentSessionId,
                    attendance: attendanceData
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                showNotification(`Attendance saved! ${result.data.present} present, ${result.data.absent} absent`, 'success');
                // Reload team members to update percentages
                await loadTeamMembers();
            } else {
                throw new Error(result.message || 'Failed to save attendance');
            }
        } catch (error) {
            console.error('Error submitting attendance:', error);
            showNotification('Failed to save attendance: ' + error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Attendance';
        }
    }

    // Open last day attendance modal
    async function openLastDayAttendance() {
        document.getElementById('lastDayModal').classList.add('show');
        const modalBody = document.getElementById('lastDayModalBody');
        modalBody.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Loading last session...</p></div>';

        try {
            // Fetch last session attendance from backend
            const response = await fetch(`/uoc-sports/public/api/attendance/last-session/${SPORT_ID}`);
            const data = await response.json();

            if (data.status === 'success' && data.data) {
                const session = data.data;
                let html = `<div class="modal-date">📅 ${session.session_date} | ${session.location}</div>`;
                if (session.attendance && session.attendance.length > 0) {
                    html += session.attendance.map(att => `
                        <div class="attendance-item ${att.status === 'ABSENT' ? 'absent' : ''}">
                            <span class="attendance-item-name">${att.fname} ${att.lname}</span>
                            <span class="attendance-item-status">${att.status === 'PRESENT' ? 'Present' : 'Absent'}</span>
                        </div>
                    `).join('');
                } else {
                    html += '<div class="empty-message">No attendance records for last session</div>';
                }
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = '<div class="empty-message">No previous session found</div>';
            }
        } catch (error) {
            modalBody.innerHTML = '<div class="error-message">Failed to load last session</div>';
        }
    }

    // Close modals
    function closeAttendanceRecords() {
        document.getElementById('recordsModal').classList.remove('show');
    }

    function closeLastDayAttendance() {
        document.getElementById('lastDayModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const recordsModal = document.getElementById('recordsModal');
        const lastDayModal = document.getElementById('lastDayModal');
        
        if (event.target == recordsModal) {
            recordsModal.classList.remove('show');
        }
        if (event.target == lastDayModal) {
            lastDayModal.classList.remove('show');
        }
    }

    // Hide attendance section
    function hideAttendanceSection() {
        document.getElementById('sessionDetails').style.display = 'none';
        document.getElementById('attendanceSection').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
        currentSessionId = null;
        attendanceData = {};
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Hide and remove after 4 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }



let historySessions = [];

async function openAttendanceRecords() {
    document.getElementById('recordsModal').classList.add('show');

    const modalBody = document.getElementById('recordsModalBody');
    modalBody.innerHTML = `
        <div class="filter-section">
            <label>Select Date</label>
            <input type="text" id="historyDate" class="form-select" placeholder="Select Date" autocomplete="off" />
        </div>
        <div id="historySessions"></div>
        <div id="historyDetails"></div>
    `;

    try {
        const response = await fetch(`/uoc-sports/public/api/attendance/history/${SPORT_ID}?limit=50`);
        const data = await response.json();

        if (data.status === 'success') {
            historySessions = data.data;
            initializeCalendar();
        }
    } catch (error) {
        console.error(error);
    }
}
function initializeCalendar() {

    // Extract unique dates from sessions
    const availableDates = [...new Set(
        historySessions.map(s => s.session_date.split(' ')[0])
    )];

    flatpickr("#historyDate", {
    dateFormat: "Y-m-d",
    maxDate: "today",
    appendTo: document.body,   // Let it float over everything
    static: false,             // Prevents layout shift and "hiding inside box"
    allowInput: true,
    disableMobile: "true",
    onOpen: function(selectedDates, dateStr, instance) {
        // Re-calculate position in case modal shifted
        setTimeout(() => instance.positionCalendar(), 10);
    },

    onDayCreate: function(dObj, dStr, fp, dayElem) {
        const year = dayElem.dateObj.getFullYear();
        const month = String(dayElem.dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dayElem.dateObj.getDate()).padStart(2, '0');

        const date = `${year}-${month}-${day}`;

        if (availableDates.includes(date)) {
            dayElem.classList.add("has-session");
        }
    },

    onChange: function(selectedDates, dateStr) {
        filterSessionsByDate(dateStr);
    }
});

       
}



function filterSessionsByDate(selectedDate) {

    const sessionsDiv = document.getElementById('historySessions');
    const detailsDiv = document.getElementById('historyDetails');

    sessionsDiv.innerHTML = '';
    detailsDiv.innerHTML = '';

    if (!selectedDate) return;

    const filtered = historySessions.filter(s =>
        s.session_date.split(' ')[0] === selectedDate
    );

    if (filtered.length === 0) {
        sessionsDiv.innerHTML = '<div class="empty-message">No sessions on this date</div>';
        return;
    }

    filtered.forEach(session => {
        const btn = document.createElement('button');
        btn.className = 'btn btn-secondary';
        btn.style.margin = '5px 0';
        btn.textContent = `${session.start_time} | ${session.location}`;
        btn.onclick = () => showSessionDetails(session);
        sessionsDiv.appendChild(btn);
    });
}

function showSessionDetails(session) {

    const detailsDiv = document.getElementById('historyDetails');

    let html = `
        <div class="history-session">
            <h4>📅 ${session.session_date} | ${session.start_time}</h4>
            <p><strong>Location:</strong> ${session.location}</p>
            <hr>
    `;

    if (session.attendance.length > 0) {
        html += session.attendance.map(att => `
           <div class="attendance-item ${att.status === 'ABSENT' ? 'absent' : 'present'}">
    <span>${att.fname} ${att.lname} (${att.student_id})</span>
    <span class="attendance-status ${att.status === 'ABSENT' ? 'status-absent' : 'status-present'}">
        ${att.status === 'ABSENT' ? 'Absent' : 'Present'}
    </span>
</div>

        `).join('');
    } else {
        html += `<div class="empty-message">No attendance data</div>`;
    }

    html += `</div>`;

    detailsDiv.innerHTML = html;
}






</script>

<style>
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10000;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification-success {
    background: #48bb78;
    color: white;
}

.notification-error {
    background: #f56565;
    color: white;
}

.notification-info {
    background: #4299e1;
    color: white;
}

.empty-message, .error-message {
    text-align: center;
    padding: 40px;
    color: #718096;
}
</style>