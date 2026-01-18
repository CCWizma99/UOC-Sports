<?php
$pageTitle = 'Team Management';
$userName = $_SESSION['user_name'] ?? 'Sport Manager';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Team | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/sports-manager/team.css");
    @import url("/uoc-sports/public/css/general/footer.css");
</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?>

<div class="main-wrapper">
    <div class="team-container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> Team Management</h1>
            <div class="sport-switcher">
                <?php if (count($managedSports ?? []) > 1): ?>
                    <label><i class="fas fa-trophy"></i> Select Sport:</label>
                    <select id="sportSwitcher" onchange="switchSport(this.value)">
                        <?php foreach ($managedSports as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['sport_id']); ?>" 
                                <?php echo ($s['sport_id'] === $sportId) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['sport_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div class="sport-name-badge">
                        <i class="fas fa-trophy"></i> <?php echo htmlspecialchars($sportName ?? 'No Sports Assigned'); ?>
                    </div>
                <?php endif; ?>
            </div>
            <p>Manage your team members and request student verification</p>
        </div>

        <!-- Team Members Section -->
        <div class="section-card">
            <div class="section-header">
                <h2>Team Members</h2>
                <div class="header-actions">
                    <button class="btn-primary" id="selectAllBtn" onclick="toggleSelectAll()">
                        <i class="fas fa-check-double"></i> Select All Unverified
                    </button>
                    <button class="btn-secondary" id="requestVerifyBtn" onclick="requestVerification()" disabled>
                        <i class="fas fa-user-check"></i> Request Verification (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>

            <div class="filters">
                <select id="statusFilter" onchange="filterStudents()">
                    <option value="all">All Students</option>
                    <option value="unverified">Unverified</option>
                    <option value="pending">Pending Verification</option>
                    <option value="verified">Verified</option>
                </select>
                <input type="text" id="searchInput" placeholder="Search by name..." oninput="filterStudents()">
            </div>

            <div class="students-grid" id="studentsGrid">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading team members...
                </div>
            </div>
        </div>

        <!-- Verification Requests Section -->
        <div class="section-card">
            <div class="section-header">
                <h2>My Verification Requests</h2>
            </div>
            <div class="requests-list" id="requestsList">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading requests...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const sportId = '<?php echo htmlspecialchars($sportId ?? ''); ?>';
let allStudents = [];
let selectedStudents = new Set();

// Switch sport function
function switchSport(newSportId) {
    window.location.href = '/uoc-sports/public/sport-manager/team?sport=' + newSportId;
}

// Load data on page load
document.addEventListener('DOMContentLoaded', () => {
    loadTeamMembers();
    loadMyRequests();
});

// Load team members
async function loadTeamMembers() {
    const grid = document.getElementById('studentsGrid');
    
    try {
        const res = await fetch(`/uoc-sports/public/api/verification/unverified-students?sport_id=${sportId}`);
        const data = await res.json();
        
        if (data.status === 'success') {
            allStudents = data.students;
            renderStudents(allStudents);
        } else {
            grid.innerHTML = '<div class="empty-state">No team members found</div>';
        }
    } catch (err) {
        grid.innerHTML = '<div class="error-state">Failed to load team members</div>';
    }
}

// Render students
function renderStudents(students) {
    const grid = document.getElementById('studentsGrid');
    
    if (students.length === 0) {
        grid.innerHTML = '<div class="empty-state"><i class="fas fa-users-slash"></i><p>No students found</p></div>';
        return;
    }
    
    grid.innerHTML = students.map(s => `
        <div class="student-card ${s.verification_status.toLowerCase()}" data-student-id="${s.student_id}">
            <div class="card-checkbox">
                <input type="checkbox" id="chk-${s.student_id}" 
                    ${s.verification_status !== 'UNVERIFIED' ? 'disabled' : ''}
                    ${selectedStudents.has(s.student_id) ? 'checked' : ''}
                    onchange="toggleStudent('${s.student_id}')">
            </div>
            <div class="student-avatar">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="student-info">
                <h4>${s.fname} ${s.lname}</h4>
                <p class="student-id"><i class="fas fa-id-badge"></i> ${s.student_id}</p>
                <p class="faculty"><i class="fas fa-university"></i> ${s.faculty_name || 'Unknown Faculty'}</p>
            </div>
            <div class="status-badge ${s.verification_status.toLowerCase()}">
                ${getStatusIcon(s.verification_status)} ${s.verification_status}
            </div>
        </div>
    `).join('');
}

function getStatusIcon(status) {
    switch(status) {
        case 'VERIFIED': return '<i class="fas fa-check-circle"></i>';
        case 'PENDING': return '<i class="fas fa-clock"></i>';
        default: return '<i class="fas fa-exclamation-circle"></i>';
    }
}

// Filter students
function filterStudents() {
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = allStudents;
    
    if (status !== 'all') {
        filtered = filtered.filter(s => s.verification_status.toLowerCase() === status);
    }
    
    if (search) {
        filtered = filtered.filter(s => 
            (s.fname + ' ' + s.lname).toLowerCase().includes(search) ||
            s.student_id.toLowerCase().includes(search)
        );
    }
    
    renderStudents(filtered);
}

// Toggle student selection
function toggleStudent(studentId) {
    if (selectedStudents.has(studentId)) {
        selectedStudents.delete(studentId);
    } else {
        selectedStudents.add(studentId);
    }
    updateSelectedCount();
}

// Toggle select all
function toggleSelectAll() {
    const unverified = allStudents.filter(s => s.verification_status === 'UNVERIFIED');
    
    if (selectedStudents.size === unverified.length) {
        selectedStudents.clear();
    } else {
        unverified.forEach(s => selectedStudents.add(s.student_id));
    }
    
    renderStudents(allStudents);
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const count = selectedStudents.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('requestVerifyBtn').disabled = count === 0;
}

// Request verification
async function requestVerification() {
    if (selectedStudents.size === 0) {
        alert('Please select students to verify');
        return;
    }
    
    try {
        const res = await fetch('/uoc-sports/public/api/verification/create-request', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                sport_id: sportId,
                student_ids: Array.from(selectedStudents)
            })
        });
        
        const data = await res.json();
        
        if (data.status === 'success') {
            alert('Verification request sent successfully! Registrars will be notified.');
            selectedStudents.clear();
            loadTeamMembers();
            loadMyRequests();
        } else {
            alert(data.message || 'Failed to create request');
        }
    } catch (err) {
        alert('Error: ' + err.message);
    }
}

// Load my requests
async function loadMyRequests() {
    const list = document.getElementById('requestsList');
    
    try {
        const res = await fetch('/uoc-sports/public/api/verification/my-requests');
        const data = await res.json();
        
        if (data.status === 'success' && data.requests.length > 0) {
            list.innerHTML = data.requests.map(r => `
                <div class="request-item ${r.status.toLowerCase()}">
                    <div class="request-info">
                        <h4>${r.sport_name || 'Unknown Sport'}</h4>
                        <p>${new Date(r.created_at).toLocaleDateString()}</p>
                    </div>
                    <div class="request-stats">
                        <span class="verified"><i class="fas fa-check"></i> ${r.verified_count}</span>
                        <span class="rejected"><i class="fas fa-times"></i> ${r.rejected_count}</span>
                        <span class="pending"><i class="fas fa-clock"></i> ${r.total_students - r.verified_count - r.rejected_count}</span>
                    </div>
                    <div class="request-status ${r.status.toLowerCase()}">${r.status}</div>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No verification requests yet</p></div>';
        }
    } catch (err) {
        list.innerHTML = '<div class="error-state">Failed to load requests</div>';
    }
}
</script>

<?php
    require "../app/views/templates/general/footer.php";      
?>
</body>
</html>
