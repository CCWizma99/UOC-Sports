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
    
        <!-- Injury Records Table -->
        <div class="table-wrapper" style="margin-top: 30px;">
            <table>
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>Practice Session</th>
                        <th>Injury</th>
                        <th>Date</th>
                        <th>Severity</th>
                        <th>Substitute</th>
                    </tr>
                </thead>
                <tbody id="injuryTableBody">
                    <!-- JS will append rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    // Example team players (you will replace with actual data later)
const players = [
    { id: "P001", name: "John Silva" },
    { id: "P002", name: "Akila Perera" },
    { id: "P003", name: "Kavindu Fernando" }
];

// Fill injured player + substitute selects
function populatePlayerDropdowns() {
    const injuredSelect = document.getElementById("injuredPlayer");
    const subSelect = document.getElementById("subPlayer");

    players.forEach(p => {
        const opt = document.createElement("option");
        opt.value = p.id;
        opt.textContent = p.name;
        injuredSelect.appendChild(opt);

        const subOpt = document.createElement("option");
        subOpt.value = p.id;
        subOpt.textContent = p.name;
        subSelect.appendChild(subOpt);
    });
}

populatePlayerDropdowns();

// Provide sport id to JS
const SPORT_ID = <?php echo json_encode($coachSportId ?? ''); ?>;

// Fetch upcoming practice sessions for coach's sport and populate dropdown
async function loadPracticeSessions() {
    try {
        const url = '/uoc-sports/public/api/injury/upcoming-sessions' + (SPORT_ID ? ('?sport_id=' + encodeURIComponent(SPORT_ID)) : '');
        const res = await fetch(url);
        const data = await res.json();
        if (data.status === 'success') {
            const select = document.getElementById('selectPractice');
            data.sessions.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = `${s.facility} - ${s.session_date} ${s.session_time}`;
                select.appendChild(opt);
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
                const row = `
                    <tr>
                        <td>${playerName}</td>
                        <td>${practiceText}</td>
                        <td>${r.description}</td>
                        <td>${r.date}</td>
                        <td>${r.description.includes('Minor') ? 'Minor' : (r.description.includes('Moderate') ? 'Moderate' : (r.description.includes('Severe') ? 'Severe' : '-'))}</td>
                        <td>${r.substitude_id || '-'}</td>
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

        players
            .filter(p => p.id !== selected)
            .forEach(p => {
                const opt = document.createElement("option");
                opt.value = p.id;
                opt.textContent = p.name;
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

    const player = players.find(p => p.id === playerId);
    const subPlayer = players.find(p => p.id === substituteId);

    const tableBody = document.getElementById("injuryTableBody");

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
                // reload reports table
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

</script>
