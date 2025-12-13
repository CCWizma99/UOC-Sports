<div class="container">
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
    const injuryType = document.getElementById("injuryType").value;
    const injuryDate = document.getElementById("injuryDate").value;
    const injurySeverity = document.getElementById("injurySeverity").value;
    const substituteId = document.getElementById("subPlayer").value;

    const player = players.find(p => p.id === playerId);
    const subPlayer = players.find(p => p.id === substituteId);

    const tableBody = document.getElementById("injuryTableBody");

    const row = `
        <tr>
            <td>${player.name}</td>
            <td>${injuryType}</td>
            <td>${injuryDate}</td>
            <td>${injurySeverity}</td>
            <td>${subPlayer ? subPlayer.name : "-"}</td>
        </tr>
    `;

    tableBody.innerHTML += row;

    this.reset();
    document.getElementById("substitutionSection").style.display = "none";
});

</script>
