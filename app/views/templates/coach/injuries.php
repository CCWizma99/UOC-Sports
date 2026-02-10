<div class="container">
<?php
// Get coach sport id from session or DB
$coachSportId = $_SESSION['coach_sport_id'] ?? '';
if (empty($coachSportId) && isset($_SESSION['user_id'])) {
    require_once dirname(__DIR__, 4) . '/core/Database.php';
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT sport_id FROM sport WHERE coach_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res && !empty($res['sport_id'])) {
        $coachSportId = $res['sport_id'];
        $_SESSION['coach_sport_id'] = $coachSportId;
    }
}
?>
    <!-- Injury Management Section -->
    <div class="table-section">
        <h2 class="section-title">Player Injury Management</h2>
    
        <!-- Injury Form -->
        <div class="injury-form-container">
            <form id="injuryForm">
    
                <div class="form-row">
                    <div class="form-group">
                        <label for="injuredPlayer">Injured Player</label>
                        <select id="injuredPlayer" class="form-input">
                            <option value="">Select player...</option>
                            <!-- Filled dynamically via JS -->
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="selectPractice">Select Practice</label>
                        <select id="selectPractice" class="form-input">
                            <option value="">Select practice session...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="injuryType">Injury Type</label>
                        <input type="text" id="injuryType" class="form-input" placeholder="e.g., Muscle strain">
                    </div>
                </div>
    
                <div class="form-row">
                    <div class="form-group">
                        <label for="injuryDate">Injury Date</label>
                        <input type="date" id="injuryDate" class="form-input">
                    </div>
    
                    <div class="form-group">
                        <label for="injurySeverity">Severity</label>
                        <select id="injurySeverity" class="form-input">
                            <option value="Minor">Minor</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Severe">Severe</option>
                        </select>
                    </div>
                </div>
    
                <!-- Substitution Field (Hidden by Default) -->
                <div id="substitutionSection" class="sub-section" style="display:none;">
                    <h4>Substitution Needed</h4>
                    <div class="form-group">
                        <label for="subPlayer">Select Substitute Player</label>
                        <select id="subPlayer" class="form-input">
                            <option value="">Select substitute...</option>
                        </select>
                    </div>
                </div>
    
                <button type="submit" class="main-btn">Record Injury</button>
            </form>
        </div>
    
        
            <br>
            <br>

        <!-- Injury Records Table -->
        <div class="table-header">
            <h2>Recent Injury Reports</h2>
        </div>
        <div class="table-wrapper">
            <table class="practice-table">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>Practice Session</th>
                        <th>Injury</th>
                        <th>Date</th>
                        <th>Severity</th>
                        <th>Substitute</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="injuryTableBody">
                    <!-- JS will append rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Injury Report</h3>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editForm">
                <input type="hidden" id="edit-reportId">
                <input type="hidden" id="edit-userId">

                <div class="form-group">
                    <label>Player</label>
                    <input type="text" id="edit-display-player" class="form-input" disabled>
                </div>

                <div class="form-group">
                    <label for="edit-selectPractice">Practice Session</label>
                    <select id="edit-selectPractice" class="form-input">
                        <!-- Populated via JS -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-injuryType">Injury Type</label>
                    <input type="text" id="edit-injuryType" class="form-input">
                </div>

                <div class="datetime">
                    <div class="form-group">
                        <label for="edit-injuryDate">Date</label>
                        <input type="date" id="edit-injuryDate" class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="edit-injurySeverity">Severity</label>
                        <select id="edit-injurySeverity" class="form-input">
                            <option value="Minor">Minor</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Severe">Severe</option>
                        </select>
                    </div>
                </div>

                 <div class="form-group">
                    <label for="edit-subPlayer">Substitute</label>
                    <select id="edit-subPlayer" class="form-input">
                        <option value="">Select substitute...</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-cancel" onclick="closeEditModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" form="editForm" class="btn-modal btn-confirm">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Delete Modal -->
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
                <h4>Delete Injury Report?</h4>
                <p>Are you sure you want to delete this injury report?</p>
                <p>This action cannot be undone.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn-modal btn-confirm-delete" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Delete Report
            </button>
        </div>
    </div>
</div>


<script>
    // Get coach sport id and members from backend
    const SPORT_ID = <?php echo json_encode($coachSportId ?? ''); ?>;
    const membersData = <?php echo json_encode($members ?? []); ?>;
    
    let allPlayers = [];
    let globalPracticeSessions = [];
    let currentDeleteId = null;

    // Populate players from backend data
    function loadTeamPlayers() {
        if (!Array.isArray(membersData) || membersData.length === 0) {
            console.warn('No team members available');
            return;
        }
        
        allPlayers = membersData.map(m => ({
            user_id: m.user_id || m.student_id,
            fname: m.fname || '',
            lname: m.lname || ''
        }));
        
        console.log('Loaded players:', allPlayers);
        populatePlayerDropdowns();
    }

    // Fill injured player + substitute selects
    function populatePlayerDropdowns() {
        const injuredSelect = document.getElementById("injuredPlayer");
        const subSelect = document.getElementById("subPlayer");
        const editSubSelect = document.getElementById("edit-subPlayer");

        // Clear existing options (keep the default one)
        injuredSelect.innerHTML = '<option value="">Select player...</option>';
        subSelect.innerHTML = '<option value="">Select substitute...</option>';
        editSubSelect.innerHTML = '<option value="">Select substitute...</option>';

        allPlayers.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.user_id;
            opt.textContent = p.fname + ' ' + p.lname;
            injuredSelect.appendChild(opt);

            const subOpt = document.createElement("option");
            subOpt.value = p.user_id;
            subOpt.textContent = p.fname + ' ' + p.lname;
            subSelect.appendChild(subOpt);
            
            const editSubOpt = subOpt.cloneNode(true);
            editSubSelect.appendChild(editSubOpt);
        });
    }

    // Load on page ready
    loadTeamPlayers();

// Fetch upcoming practice sessions for coach's sport and populate dropdown
async function loadPracticeSessions() {
    try {
        const url = '/uoc-sports/public/api/injury/past-sessions' + (SPORT_ID ? ('?sport_id=' + encodeURIComponent(SPORT_ID)) : '');
        const res = await fetch(url);
        const data = await res.json();
        if (data.status === 'success') {
            globalPracticeSessions = data.sessions;
            const select = document.getElementById('selectPractice');
            const editSelect = document.getElementById('edit-selectPractice');
            
            data.sessions.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = `${s.facility} - ${s.session_date} ${s.session_time}`;
                select.appendChild(opt);
                
                const editOpt = opt.cloneNode(true);
                editSelect.appendChild(editOpt);
            });
        }
    } catch (err) {
        console.error('Failed to load practice sessions', err);
    }
}

loadPracticeSessions();

// Load existing injury reports for this sport
async function loadInjuryReports() {
    if (!SPORT_ID) return;
    try {
        const res = await fetch('/uoc-sports/public/api/injury/reports/' + encodeURIComponent(SPORT_ID));
        const data = await res.json();
        if (data.status === 'success') {
            const tbody = document.getElementById('injuryTableBody');
            tbody.innerHTML = '';
            data.data.forEach(r => {
                const playerName = r.fname ? (r.fname + ' ' + (r.lname || '')) : r.user_id;
                const practiceText = `${r.facility || ''} - ${r.session_date || ''} ${r.session_time || ''}`.trim();
                
                // Extract severity from description (assuming format "Type (Severity)")
                let severity = '-';
                if(r.description.includes('Minor')) severity = 'Minor';
                else if(r.description.includes('Moderate')) severity = 'Moderate';
                else if(r.description.includes('Severe')) severity = 'Severe';

                // Get substitute player name from allPlayers array
                let substituteName = '-';
                if (r.substitude_id) {
                    const subPlayer = allPlayers.find(p => p.user_id === r.substitude_id);
                    if (subPlayer) {
                        substituteName = subPlayer.fname + ' ' + subPlayer.lname;
                    }
                }

                const row = `
                    <tr>
                        <td>${playerName}</td>
                        <td>${practiceText}</td>
                        <td>${r.description.split('(')[0].trim()}</td>
                        <td>${r.date}</td>
                        <td>${severity}</td>
                        <td>${substituteName}</td>
                         <td>
                            <div class="actions-cell" style="display: flex; gap: 10px;">
                                <button class="btn-action btn-edit" onclick='openEditModal(${JSON.stringify(r).replace(/'/g, "&#39;")})' title="Edit" style="background: none; border: none; cursor: pointer; color: #007bff;"><i class="fas fa-edit"></i></button>
                                <button class="btn-action btn-delete" onclick="openDeleteModal('${r.report_id}')" title="Delete" style="background: none; border: none; cursor: pointer; color: #dc3545;"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    } catch (err) {
        console.error('Failed to load injury reports', err);
    }
}

loadInjuryReports();

// Show substitution section when player is in the team
document.getElementById("injuredPlayer").addEventListener("change", function () {
    const selected = this.value;
    const subSection = document.getElementById("substitutionSection");

    if (selected) {
        subSection.style.display = "block";

        // filter out injured player
        const subSelect = document.getElementById("subPlayer");
        subSelect.innerHTML = '<option value="">Select substitute...</option>';

        allPlayers
            .filter(p => p.user_id !== selected)
            .forEach(p => {
                const opt = document.createElement("option");
                opt.value = p.user_id;
                opt.textContent = p.fname + ' ' + p.lname;
                subSelect.appendChild(opt);
            });

    } else {
        subSection.style.display = "none";
    }
});

// Injury submit handler
document.getElementById("injuryForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const playerId = document.getElementById("injuredPlayer").value;
    const practiceId = document.getElementById("selectPractice").value;
    const injuryType = document.getElementById("injuryType").value;
    const injuryDate = document.getElementById("injuryDate").value;
    const injurySeverity = document.getElementById("injurySeverity").value;
    const substituteId = document.getElementById("subPlayer").value;

    // POST to API
    (async () => {
        try {
            const payload = {
                user_id: playerId,
                practice_id: practiceId,
                date: injuryDate,
                description: injuryType + ' (' + injurySeverity + ')',
                need_substitude: substituteId ? 'YES' : 'NO',
                substitude_id: substituteId || ''
            };

            const resp = await fetch('/uoc-sports/public/api/injury/report', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await resp.json();
            if (result.status === 'success') {
                document.getElementById('injuryForm').reset();
                document.getElementById('substitutionSection').style.display = 'none';
                loadInjuryReports();
            } else {
                alert('Failed to save injury: ' + (result.message || 'Unknown error'));
            }
        } catch (err) {
            console.error('Error saving injury', err);
            alert('Server error saving injury');
        }
    })();
});

// Edit & Delete Logic
function openEditModal(report) {
    document.getElementById('edit-reportId').value = report.report_id;
    document.getElementById('edit-userId').value = report.user_id; // Keep original user
    
    // Display player name
    const playerName = report.fname ? (report.fname + ' ' + (report.lname || '')) : report.user_id;
    document.getElementById('edit-display-player').value = playerName;

    document.getElementById('edit-selectPractice').value = report.practice_id;
    
    // Parse description for type and severity
    const descParts = report.description.split('(');
    const type = descParts[0].trim();
    let severity = 'Minor';
    if(report.description.includes('Moderate')) severity = 'Moderate';
    if(report.description.includes('Severe')) severity = 'Severe';

    document.getElementById('edit-injuryType').value = type;
    document.getElementById('edit-injurySeverity').value = severity;
    document.getElementById('edit-injuryDate').value = report.date;
    document.getElementById('edit-subPlayer').value = report.substitude_id || '';

    // Filter substitutes in edit modal
    const editSubSelect = document.getElementById("edit-subPlayer");
     // Reset options
    editSubSelect.innerHTML = '<option value="">Select substitute...</option>';
    allPlayers
        .filter(p => p.user_id !== report.user_id)
        .forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.user_id;
            opt.textContent = p.fname + ' ' + p.lname;
            editSubSelect.appendChild(opt);
        });
     // Set value again after repopulating
    editSubSelect.value = report.substitude_id || '';

    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function openDeleteModal(id) {
    currentDeleteId = id;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    currentDeleteId = null;
    document.getElementById('deleteModal').classList.remove('active');
}

// Handle Update
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const reportId = document.getElementById('edit-reportId').value;
    const userId = document.getElementById('edit-userId').value;
    const practiceId = document.getElementById('edit-selectPractice').value;
    const type = document.getElementById('edit-injuryType').value;
    const severity = document.getElementById('edit-injurySeverity').value;
    const date = document.getElementById('edit-injuryDate').value;
    const subId = document.getElementById('edit-subPlayer').value;

    const payload = {
        report_id: reportId,
        user_id: userId,
        practice_id: practiceId,
        date: date,
        description: `${type} (${severity})`,
        need_substitude: subId ? 'YES' : 'NO',
        substitude_id: subId
    };

    try {
        const resp = await fetch('/uoc-sports/public/api/injury/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await resp.json();
        if(result.status === 'success') {
            closeEditModal();
            loadInjuryReports();
        } else {
            alert('Failed to update: ' + result.message);
        }
    } catch(err) {
        console.error(err);
        alert('Error updating report');
    }
});

// Handle Delete
async function confirmDelete() {
    if(!currentDeleteId) return;
    
    try {
        const resp = await fetch('/uoc-sports/public/api/injury/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ report_id: currentDeleteId })
        });
        const result = await resp.json();
        if(result.status === 'success') {
            closeDeleteModal();
            loadInjuryReports();
        } else {
            alert('Failed to delete: ' + result.message);
        }
    } catch(err) {
        console.error(err);
        alert('Error deleting report');
    }
}
</script>
