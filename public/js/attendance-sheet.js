function toggleAttendance(button) {
    if (button.classList.contains('present')) {
        button.classList.remove('present');
        button.classList.add('absent');
        button.textContent = 'Absent';
    } else {
        button.classList.remove('absent');
        button.classList.add('present');
        button.textContent = 'Present';
    }
    updateAttendanceSummary();
}

function updateAttendanceSummary() {
    const presentButtons = document.querySelectorAll('.attendance-toggle.present');
    const totalStudents = document.querySelectorAll('.attendance-toggle').length;
    const presentCount = presentButtons.length;
    
    document.getElementById('attendanceSummary').textContent = 
        `${presentCount}/${totalStudents} Players Present`;
}

function submitAttendance() {
    const date = document.getElementById('attendanceDate').value;
    const attendanceData = {};
    
    document.querySelectorAll('.attendance-toggle').forEach(button => {
        const student = button.dataset.student;
        const isPresent = button.classList.contains('present');
        attendanceData[student] = isPresent;
    });
    
    // Simulate submission
    alert(`Attendance submitted for ${date}!\n\nData: ${JSON.stringify(attendanceData, null, 2)}`);
}

// Initialize the summary on page load
updateAttendanceSummary();