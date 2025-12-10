<div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="title">Mark Team Attendance</h1>
            <h3 class="info-title">2025/10/15 | Weekly Volleyball Practice</h3>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-secondary" onclick="openAttendanceRecords()">View Previous Records</button>
            <button class="btn btn-secondary" onclick="openLastDayAttendance()">Last Day Attendance</button>
        </div>

        <!-- Attendance Summary -->
        <div class="attendance-summary">
            <div class="summary-title">Overall Attendance</div>
            <div class="summary-count" id="attendanceSummary">12/15 Players Present</div>
        </div>

        <!-- Attendance Table -->
        <div class="table-section">
            <div class="table-header">
                <div>Student Name</div>
                <div>ID Number</div>
                <div>Present</div>
                <div>Attendance Percentage</div>
            </div>

            <div class="table-row">
                <div class="student-name">J. Balakrishnan</div>
                <div class="student-id">2023/IS/012</div>
                <div>
                    <button class="attendance-toggle" data-student="balakrishnan" onclick="toggleAttendance(this)">Present</button>
                </div>
                <div class="percentage">80%</div>
            </div>

            <div class="table-row">
                <div class="student-name">Jayaweera M. A. J. C. S.</div>
                <div class="student-id">2023/IS/043</div>
                <div>
                    <button class="attendance-toggle" data-student="jayaweera" onclick="toggleAttendance(this)">Present</button>
                </div>
                <div class="percentage">90%</div>
            </div>

            <div class="table-row">
                <div class="student-name">Rajapaksha K. A. G. S. M.</div>
                <div class="student-id">2023/IS/079</div>
                <div>
                    <button class="attendance-toggle" data-student="rajapaksha" onclick="toggleAttendance(this)">Present</button>
                </div>
                <div class="percentage">70%</div>
            </div>

            <div class="table-row">
                <div class="student-name">Hettiarachchi H. H. K. C. C.</div>
                <div class="student-id">2023/IS/034</div>
                <div>
                    <button class="attendance-toggle" data-student="chamal" onclick="toggleAttendance(this)">Present</button>
                </div>
                <div class="percentage">100%</div>
            </div>
        </div>

        <button class="submit-btn" onclick="submitAttendance()">Submit Attendance</button>
    </div>

    <!-- Previous Attendance Records Modal -->
    <div id="recordsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Attendance Records</h2>
                <button class="close-btn" onclick="closeAttendanceRecords()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-date">📅 2025/10/08 | Weekly Volleyball Practice</div>
                <div class="attendance-item">
                    <span class="attendance-item-name">J. Balakrishnan</span>
                    <span class="attendance-item-status">Present</span>
                </div>
                <div class="attendance-item">
                    <span class="attendance-item-name">Jayaweera M. A. J. C. S.</span>
                    <span class="attendance-item-status">Present</span>
                </div>
                <div class="attendance-item absent">
                    <span class="attendance-item-name">Rajapaksha K. A. G. S. M.</span>
                    <span class="attendance-item-status">Absent</span>
                </div>
                <div class="attendance-item">
                    <span class="attendance-item-name">Hettiarachchi H. H. K. C. C.</span>
                    <span class="attendance-item-status">Present</span>
                </div>

                <div style="margin-top: 30px; border-top: 2px solid #e2e8f0; padding-top: 20px;">
                    <div class="modal-date">📅 2025/10/01 | Weekly Volleyball Practice</div>
                    <div class="attendance-item">
                        <span class="attendance-item-name">J. Balakrishnan</span>
                        <span class="attendance-item-status">Present</span>
                    </div>
                    <div class="attendance-item absent">
                        <span class="attendance-item-name">Jayaweera M. A. J. C. S.</span>
                        <span class="attendance-item-status">Absent</span>
                    </div>
                    <div class="attendance-item">
                        <span class="attendance-item-name">Rajapaksha K. A. G. S. M.</span>
                        <span class="attendance-item-status">Present</span>
                    </div>
                    <div class="attendance-item">
                        <span class="attendance-item-name">Hettiarachchi H. H. K. C. C.</span>
                        <span class="attendance-item-status">Present</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Day Attendance Modal -->
    <div id="lastDayModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Last Day Attendance</h2>
                <button class="close-btn" onclick="closeLastDayAttendance()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-date">📅 2025/10/14 | Weekly Volleyball Practice</div>
                <div class="attendance-item">
                    <span class="attendance-item-name">J. Balakrishnan</span>
                    <span class="attendance-item-status">Present</span>
                </div>
                <div class="attendance-item">
                    <span class="attendance-item-name">Jayaweera M. A. J. C. S.</span>
                    <span class="attendance-item-status">Present</span>
                </div>
                <div class="attendance-item">
                    <span class="attendance-item-name">Rajapaksha K. A. G. S. M.</span>
                    <span class="attendance-item-status">Present</span>
                </div>
                <div class="attendance-item absent">
                    <span class="attendance-item-name">Hettiarachchi H. H. K. C. C.</span>
                    <span class="attendance-item-status">Absent</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle attendance status
        function toggleAttendance(button) {
            if (button.classList.contains('present')) {
                button.classList.remove('present');
                button.classList.add('absent');
                button.textContent = 'Absent';
            } else if (button.classList.contains('absent')) {
                button.classList.remove('absent');
                button.textContent = 'Present';
            } else {
                button.classList.add('present');
                button.textContent = 'Present';
            }
            updateAttendanceCount();
        }

        // Update attendance count
        function updateAttendanceCount() {
            const presentButtons = document.querySelectorAll('.attendance-toggle.present');
            const totalButtons = document.querySelectorAll('.attendance-toggle');
            const count = presentButtons.length;
            const total = totalButtons.length;
            document.getElementById('attendanceSummary').textContent = `${count}/${total} Players Present`;
        }

        // Submit attendance
        function submitAttendance() {
            const presentButtons = document.querySelectorAll('.attendance-toggle.present');
            const totalButtons = document.querySelectorAll('.attendance-toggle');
            alert(`Attendance submitted: ${presentButtons.length}/${totalButtons.length} players marked present`);
        }

        // Open previous records modal
        function openAttendanceRecords() {
            document.getElementById('recordsModal').classList.add('show');
        }

        // Close previous records modal
        function closeAttendanceRecords() {
            document.getElementById('recordsModal').classList.remove('show');
        }

        // Open last day attendance modal
        function openLastDayAttendance() {
            document.getElementById('lastDayModal').classList.add('show');
        }

        // Close last day attendance modal
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateAttendanceCount();
        });
    </script>