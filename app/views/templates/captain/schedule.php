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

    <!-- Facility (Only Volleyball) -->
    <div class="form-group">
        <label for="facility">Sport</label>
        <input type="text" id="facility" name="facility" value="<?= htmlspecialchars($sport_name) ?>" readonly>
    </div>


     <div class="form-group">
                        <label for="practiceSessionDate">Practice Session Date *</label>
                        <input type="date" id="practiceSessionDate" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time *</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time *</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>

                    <div class="form-group">
                        <label for="need_equipment">Need Equipment *</label>
                        <select id="need_equipment" name="need_equipment" required>
                            <option value="No">No</option>
                            <option value="Yes">Yes</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location *</label>
                        <select id="location" name="location" required>
                            <option>Select the Location</option>
                            <option value="Indoor Court">Indoor Tennis Court</option>
                            <option value="Indoor court">Indoor Badminton Court</option>
                            <option value="Outdoor Court">Outdoor Basketball court</option>
                            <option value="Outdoor Field">Outdoor Baseball court</option>
                            <option value="Outdoor Field">Indoor volleyball court</option>
                            <option value="Outdoor Field">Outdoor Cricket Field</option>
                            <option value="Swimming Pool">Elle Field</option>
                            <option value="Carrom room">Carrom Room</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="notes">Special Notes *</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Enter any special notes..."></textarea>
                    </div>
                </div>


    <!-- Submit -->
    <button type="submit" name="create" class="btn-primary">
        Schedule Practice
    </button>

    <button type="button" class="btn-primary" onclick="clearScheduleForm()">
    Clear 
</button>

</form>
        </div>

        <!-- Table Section -->
        <div class="table-section">
           <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Scheduled Practices</h2>
    <button class="btn-secondary" onclick="openPreviousSessions()">
        <i class="fas fa-calendar-alt"></i> Previous Sessions
    </button>
</div>
            <div class="table-wrapper">
                <table class="practice-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Facility</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Equipment</th>
                            <th>Location</th>
                            <th>Special Notes</th>
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

    
    <!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Practice Session</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="edit-id" name="id">

                <!-- Sport -->
               <div class="form-group">
    <label>Sport</label>
    <input type="text" value="<?= htmlspecialchars($sport_name) ?>" readonly>
</div>

                <!-- Date -->
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" id="edit-date" name="date" required>
                </div>

                <!-- Start & End Time Side by Side -->
                <div class="datetime">
                    <div class="form-group">
                        <label>Start Time *</label>
                        <input type="time" id="edit-start-time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label>End Time *</label>
                        <input type="time" id="edit-end-time" name="end_time" required>
                    </div>
                </div>

                <!-- Equipment -->
                <div class="form-group">
                    <label>Need Equipment *</label>
                    <select id="edit-need-equipment" name="need_equipment" required>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label>Location *</label>
                    <select id="edit-location" name="location" required>
                        <option value="Indoor Tennis Court">Indoor Tennis Court</option>
                        <option value="Indoor Badminton Court">Indoor Badminton Court</option>
                        <option value="Outdoor Basketball Court">Outdoor Basketball Court</option>
                        <option value="Outdoor Baseball Court">Outdoor Baseball Court</option>
                        <option value="Indoor Volleyball Court">Indoor Volleyball Court</option>
                        <option value="Outdoor Cricket Field">Outdoor Cricket Field</option>
                        <option value="Elle Field">Elle Field</option>
                        <option value="Carrom Room">Carrom Room</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="form-group full-width">
                    <label>Special Notes</label>
                    <textarea id="edit-notes" name="notes" rows="2"></textarea>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button class="btn-modal btn-cancel" onclick="closeEditModal()">
                Cancel
            </button>
            <button class="btn-modal btn-confirm" onclick="saveEdit()">
                Save Changes
            </button>
        </div>
    </div>
</div>
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay delete-modal" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="delete-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4>Delete Practice Session?</h4>
                    <p>Are you sure you want to delete this practice session?</p>
                    <p>This action cannot be undone.</p>
                    
                    <div class="session-details" id="deleteSessionDetails">
                        <!-- Session details will be populated here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn-modal btn-confirm-delete" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Delete Session
                </button>
            </div>
        </div>
    </div>

<div class="calendar-modal-overlay" id="calendarModal">
    <div class="calendar-modal">
        <div class="calendar-modal-header">
            <h3>Session Details</h3>
            <button class="calendar-modal-close" onclick="closeCalendarModal()">×</button>
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
            <button class="modal-close" onclick="closePreviousModal()">×</button>
        </div>
        <div class="modal-body">
            <div id="calendar" style="min-height: 400px;"></div>
        </div>
    </div>
</div>

    <script>
    const API_BASE = '/uoc-sports/public/api/get-facility-rates.php';
        const ATTENDANCE_API_BASE = '/uoc-sports/public/api/attendance';
        
        const SPORT_ID = '<?php echo $_SESSION['captain_sport_id'] ?? 'VOL'; ?>';
        
        let timeout = null;
        let currentEditId = null;
        let currentDeleteId = null;

        // Load facilities and schedules on page load
        document.addEventListener('DOMContentLoaded', function() {
            
            loadSchedules();
        });

        // Load upcoming schedules
        async function loadSchedules() {
            try {
                const response = await fetch(`${ATTENDANCE_API_BASE}/upcoming-sessions/${SPORT_ID}`);
                const data = await response.json();
                
                const tbody = document.querySelector('.practice-table tbody');
                tbody.innerHTML = '';
                
                if (data.status === 'success' && data.sessions.length > 0) {
                    data.sessions.forEach(session => {
                        const row = document.createElement('tr');
                       row.innerHTML = `
<td>#${session.id}</td>
<td>${session.facility}</td>
<td>${session.session_date}</td>
<td>${formatTime(session.start_time)}</td>
<td>${formatTime(session.end_time)}</td>
<td>${session.need_equipment}</td>
<td>${session.location}</td>
<td>${session.notes || '-'}</td>

<td>
<div class="actions-cell">

<button class="btn-action btn-edit"
onclick="editSession(${session.id})">
<i class="fas fa-edit"></i>
</button>

<button class="btn-action btn-delete"
onclick="deleteSession(${session.id})">
<i class="fas fa-trash-alt"></i>
</button>

</div>
</td>
`;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="no-records">No upcoming practice sessions found.</td></tr>';
                }
            } catch (error) {
                console.error('Error loading schedules:', error);
                const tbody = document.querySelector('.practice-table tbody');
                tbody.innerHTML = '<tr><td colspan="6" class="error-text">Failed to load schedules.</td></tr>';
            }
        }

        

        function formatTime(timeString) {
            if (!timeString) return '-';
            const [hours, minutes] = timeString.split(':');
            const h = parseInt(hours, 10);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const formattedHour = h % 12 || 12;
            return `${formattedHour}:${minutes} ${ampm}`;
        }
        
        // Edit Session
       async function editSession(id) {
    currentEditId = id;

    try {
        const response = await fetch(`${ATTENDANCE_API_BASE}/upcoming-sessions/${SPORT_ID}`);
        const data = await response.json();

        if (data.status === 'success') {
            const session = data.sessions.find(s => s.id == id);

            if (session) {

                document.getElementById('edit-id').value = session.id;
                document.getElementById('edit-date').value = session.session_date;

                // IMPORTANT: Use correct IDs
                document.getElementById('edit-start-time').value = session.start_time;
                document.getElementById('edit-end-time').value = session.end_time;

                document.getElementById('edit-need-equipment').value = session.need_equipment;
                document.getElementById('edit-location').value = session.location;
                document.getElementById('edit-notes').value = session.notes || '';

                document.getElementById('editModal').classList.add('active');
            }
        }
    } catch (error) {
        console.error('Error loading session:', error);
        alert('Failed to load session details');
    }
}
        
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            currentEditId = null;
        }
        
        function saveEdit() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);
            
            // Create a form and submit it
            const submitForm = document.createElement('form');
            submitForm.method = 'POST';
            submitForm.action = '/uoc-sports/public/captain/schedule-practice';
            
            // Add form data
            formData.forEach((value, key) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                submitForm.appendChild(input);
            });
            
            // Add update flag
            const updateInput = document.createElement('input');
            updateInput.type = 'hidden';
            updateInput.name = 'update';
            updateInput.value = '1';
            submitForm.appendChild(updateInput);
            
            document.body.appendChild(submitForm);
            submitForm.submit();
        }
        
        // Delete Session
        async function deleteSession(id) {
            currentDeleteId = id;
            
            try {
                const response = await fetch(`${ATTENDANCE_API_BASE}/upcoming-sessions/${SPORT_ID}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const session = data.sessions.find(s => s.id == id);
                    
                    if (session) {
                        const detailsDiv = document.getElementById('deleteSessionDetails');
                        detailsDiv.innerHTML = `
                            <div class="detail-row">
                                <span class="detail-label">Session ID:</span>
                                <span class="detail-value">#${session.id}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Facility:</span>
                                <span class="detail-value">${session.facility}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${session.session_date}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${formatTime(session.start_time)}</span>
                            </div>
                            ${session.description ? `
                            <div class="detail-row">
                                <span class="detail-label">Description:</span>
                                <span class="detail-value">${session.description}</span>
                            </div>
                            ` : ''}
                        `;
                        
                        document.getElementById('deleteModal').classList.add('active');
                    }
                }
            } catch (error) {
                console.error('Error loading session:', error);
                alert('Failed to load session details');
            }
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            currentDeleteId = null;
        }
        
        function confirmDelete() {
            if (currentDeleteId) {
                window.location.href = `/uoc-sports/public/captain/schedule-practice?delete=${currentDeleteId}`;
            }
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeEditModal();
                closeDeleteModal();
            }
        });

        function openPreviousSessions() {
    document.getElementById('previousModal').classList.add('active');
    loadPreviousCalendar();
}

function closePreviousModal() {
    document.getElementById('previousModal').classList.remove('active');
}

async function loadPreviousCalendar() {
    // Fetch previous sessions
    const response = await fetch(`${ATTENDANCE_API_BASE}/previous-sessions/${SPORT_ID}`);
    const data = await response.json();

    const calendarEl = document.getElementById('calendar');
    calendarEl.innerHTML = "";

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 500,
        events: data.sessions.map(session => ({
            title: "Practice Session",
            start: session.session_date,
            className: "previous-session",
            extendedProps: session
        })),

        eventClick: function(info) {
            const session = info.event.extendedProps;

            // Inject session details into modal body
            document.getElementById('calendarSessionDetails').innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${session.session_date}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Start:</span>
                    <span class="detail-value">${session.start_time}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">End:</span>
                    <span class="detail-value">${session.end_time}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
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

            // Show modal
            document.getElementById('calendarModal').classList.add('active');
        }
    });

    calendar.render();
}

// Function to close modal
function closeCalendarModal() {
    document.getElementById('calendarModal').classList.remove('active');
}

function clearScheduleForm() {
    const form = document.getElementById('scheduleForm');
    form.reset(); // resets all input, select, textarea fields
}

    </script>

