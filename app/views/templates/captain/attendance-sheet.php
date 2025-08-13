    <div class="container">
        <div class="header">
            <h1 class="title">Mark Team Attendance</h1>
            <h3 class="info-title">2025/10/15 | Weekly Volleyball Practice</h3>
        </div>

        <div class="attendance-summary">
            <div class="summary-title">Overall Attendance</div>
            <div class="summary-count" id="attendanceSummary">12/15 Players Present</div>
        </div>

        <div class="attendance-table">
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

        <button class="submit-btn" onclick="submitAttendance()">Submit</button>
        <div style="clear: both;"></div>
    </div>