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
                    
                    <div class="form-group">
                        <label for="edit-facility">Facility</label>
                        <select id="edit-facility" name="facility" required>
                            <option value="">-- Select Facility --</option>
                        </select>
                    </div>

                    <div class="datetime">
                        <div class="form-group">
                            <label for="edit-date">Date</label>
                            <input type="date" id="edit-date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-time">Time</label>
                            <input type="time" id="edit-time" name="time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea id="edit-description" name="description" placeholder="Enter practice details..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-cancel" onclick="closeEditModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn-modal btn-confirm" onclick="saveEdit()">
                    <i class="fas fa-save"></i> Save Changes
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

    <script>
    const API_BASE = '/uoc-sports/public/api/get-facility-rates.php';
        const ATTENDANCE_API_BASE = '/uoc-sports/public/api/attendance';
        
        const SPORT_ID = '<?php echo $_SESSION['captain_sport_id'] ?? 'VOL'; ?>';
        
        let timeout = null;
        let currentEditId = null;
        let currentDeleteId = null;

        // Load facilities and schedules on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFacilities();
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
                            <td>${formatTime(session.session_time)}</td>
                            <td>${session.description || '-'}</td>
                            <td>
                                <div class="actions-cell">
                                    <button class="btn-action btn-edit" onclick="editSession(${session.id})" title="Edit Session">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteSession(${session.id})" title="Delete Session">
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

        async function loadFacilities() {
            try {
                const response = await fetch(`${API_BASE}?all=true`);
                const facilities = await response.json();
                
                const selectBoxes = [document.getElementById('facility'), document.getElementById('edit-facility')];
                
                selectBoxes.forEach(selectBox => {
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
                });
            } catch (error) {
                console.error('Error loading facilities:', error);
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
                        document.getElementById('edit-facility').value = session.facility;
                        document.getElementById('edit-date').value = session.session_date;
                        document.getElementById('edit-time').value = session.session_time;
                        document.getElementById('edit-description').value = session.description || '';
                        
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
                                <span class="detail-value">${formatTime(session.session_time)}</span>
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
    </script>
