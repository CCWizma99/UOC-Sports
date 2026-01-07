<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Records | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/players-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="players-grid-container">
        <div class="players-grid-left">
            <section id="search-user">
                <h2>Search Player Records</h2>
                <div class="filter-bar">
                    <h3>Filter <i class="fa-solid fa-filter"></i></h3>

                    <div class="btn" id="faculty-btn">
                        Faculty
                        <div class="dropdown" data-filter="faculty">
                            <div data-value="">All</div>
                            <div data-value="Science">Science</div>
                            <div data-value="Arts">Arts</div>
                            <div data-value="Medicine">Medicine</div>
                        </div>
                    </div>

                    <div class="btn" id="year-btn">
                        Year
                        <div class="dropdown" data-filter="year">
                            <div data-value="">All</div>
                            <div data-value="1">1</div>
                            <div data-value="2">2</div>
                            <div data-value="3">3</div>
                            <div data-value="4">4</div>
                        </div>
                    </div>

                    <div class="btn" id="sport-btn">
                        Sport
                        <div class="dropdown" data-filter="sport">
                            <div data-value="">All</div>
                            <div data-value="Cricket">Cricket</div>
                            <div data-value="Football">Football</div>
                            <div data-value="Rowing">Rowing</div>
                        </div>
                    </div>

                    <div class="btn" id="public-btn">
                        Type
                        <div class="dropdown" data-filter="type">
                            <div data-value="">All</div>
                            <div data-value="Student">Student</div>
                            <div data-value="Staff">Staff</div>
                        </div>
                    </div>
                </div>

                <input type="text" name="search-user-inp" id="search-user-inp" 
                    title="Enter user ID No. or Name" placeholder="Enter User ID or Name">

                <div class="search-output">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Search Player Records</h3>
                        <p>Use the filters or search to find players</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="players-grid-right">
            <section id="add-match-result">
                <h2>Add Match Result</h2>
                <form id="add-match-form" novalidate>
                    <p class="required-note"><span>*</span> Required fields</p>
                    
                    <!-- Tournament Select -->
                    <div class="input-div">
                        <label for="tournament">Tournament <span class="required">*</span></label>
                        <select id="tournament" name="tournament_id" required>
                            <option value="">Loading tournaments...</option>
                        </select>
                    </div>

                    <!-- Sport (Auto-populated from tournament) -->
                    <div class="input-div">
                        <label for="sport-display">Sport</label>
                        <input type="text" id="sport-display" readonly placeholder="(Selected from tournament)">
                        <input type="hidden" id="sport" name="sport_id" value="">
                        <input type="hidden" id="sport-category" name="sport_category" value="">
                    </div>

                    <!-- Match Name -->
                    <div class="input-div">
                        <label for="match-name">Match Name <span class="required">*</span></label>
                        <input type="text" id="match-name" name="match_name" 
                               placeholder="Quarter Final / Semi Final..." required>
                    </div>

                    <!-- Match Date -->
                    <div class="input-div">
                        <label for="match-date">Match Date <span class="required">*</span></label>
                        <input type="date" id="match-date" name="match_date" required>
                    </div>

                    <!-- Result Status -->
                    <div class="input-div">
                        <label for="result-status">Result Status</label>
                        <select id="result-status" name="result_status">
                            <option value="COMPLETED">Completed</option>
                            <option value="DRAW">Draw</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="PENDING">Pending</option>
                            <option value="NO_RESULT">No Result</option>
                        </select>
                    </div>

                    <!-- Winner (Player) - Optional -->
                    <div class="input-div">
                        <label for="winner">Winner (Player)</label>
                        <select id="winner" name="winner_id">
                            <option value="">Select winner (optional)</option>
                        </select>
                    </div>

                    <!-- Sport Category Info Badge -->
                    <div id="sport-category-badge" class="category-badge" style="display:none;">
                        <span id="category-label"></span>
                    </div>

                    <!-- Dynamic Sport-Specific Fields Container -->
                    <div id="sport-specific-fields" class="sport-fields-container">
                        <p class="hint-text"><i class="fas fa-info-circle"></i> Select a sport to see scoring fields</p>
                    </div>

                    <button type="submit" class="btn" id="submit-btn">
                        <i class="fas fa-plus-circle"></i> Add Result
                    </button>
                    <div id="form-message"></div>
                </form>
            </section>
        </div>
    </div>
</div>

<!-- Player Match History Modal -->
<div id="match-history-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-trophy"></i> Match History</h2>
            <button class="modal-close" onclick="closeMatchHistoryModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="player-info-header"></div>
            <div id="match-history-content">
                <div class="loading-spinner">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- Search Match Modal -->
<div id="search-match-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2><i class="fas fa-search"></i> Search Matches</h2>
            <button class="modal-close" onclick="closeSearchMatchModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="search-match-filters">
                <input type="text" id="search-match-query" placeholder="Type to search matches..." oninput="debounceMatchSearch()">
                <select id="search-match-sport" onchange="performMatchSearch()">
                    <option value="">All Sports</option>
                </select>
            </div>
            <div class="search-match-container">
                <div id="search-match-results">
                    <p class="hint-text">Start typing to search matches...</p>
                </div>
                <div id="match-details-panel" class="match-details-panel" style="display:none;">
                    <div class="details-header">
                        <button class="btn-back" onclick="hideMatchDetails()"><i class="fas fa-arrow-left"></i> Back</button>
                        <h3 id="details-match-name"></h3>
                    </div>
                    <div id="match-details-content">
                        <!-- Match details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button for Search Match -->
<button class="fab-search-match" onclick="openSearchMatchModal()" title="Search Matches">
    <i class="fas fa-search"></i>
</button>


<script>
// Search Player Records Script
const filters = { faculty: '', year: '', sport: '', type: '' };

// Store original button labels for reset
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

// Toggle dropdown visibility
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(dd => {
            if (dd.parentElement !== btn) dd.classList.remove('show');
        });
        btn.querySelector('.dropdown').classList.toggle('show');
    });
});

// Select filter value
document.querySelectorAll('.dropdown div').forEach(option => {
    option.addEventListener('click', e => {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;

        const labelNode = btn.childNodes[0]; 
        const originalLabel = btn.getAttribute('data-original');

        if (value === '') {
            labelNode.textContent = originalLabel;
        } else {
            labelNode.textContent = value;
        }

        e.target.closest('.dropdown').classList.remove('show');
        performSearch();
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Search on typing
document.getElementById('search-user-inp').addEventListener('input', performSearch);

function performSearch() {
    const query = document.getElementById('search-user-inp').value.trim();
    if (query.length === 0 && Object.values(filters).every(f => f === '')) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    const params = new URLSearchParams({ q: query, ...filters });

    fetch(`/uoc-sports/public/api/search-user.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.fname} ${user.lname}</td>
                            <td>${user.type}</td>
                            <td class="action-buttons">
                                <button class="btn-icon btn-history" onclick="openMatchHistoryModal('${user.user_id}', '${user.fname} ${user.lname}')" title="View Match History">
                                    <i class="fas fa-trophy"></i>
                                </button>
                                <a href="./admin-user-profile?id=${user.user_id}" class="action-link" title="View Profile">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No users found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
}
</script>

<script>
// ========== Add Match Result Script - Dynamic Sport-Specific Forms ==========
document.addEventListener("DOMContentLoaded", async () => {
    const tournamentSelect = document.getElementById("tournament");
    const sportInput = document.getElementById("sport");
    const sportDisplay = document.getElementById("sport-display");
    const sportCategoryInput = document.getElementById("sport-category");
    const categoryBadge = document.getElementById("sport-category-badge");
    const categoryLabel = document.getElementById("category-label");
    const sportFieldsContainer = document.getElementById("sport-specific-fields");
    const winnerSelect = document.getElementById("winner");
    const form = document.getElementById("add-match-form");

    // Store data
    let studentsData = [];
    let currentFormConfig = null;
    let tournamentsData = [];

    // Category display names
    const categoryNames = {
        'TEAM_GOAL': 'Team Sport (Goals)',
        'RACKET': 'Racket Sport (Sets)',
        'CRICKET': 'Cricket',
        'COMBAT': 'Combat Sport',
        'TRACK_FIELD': 'Track & Field',
        'BOARD_GAME': 'Board Game',
        'BALL_COURT': 'Ball Court Sport',
        'WEIGHT': 'Weight Lifting'
    };

    // ===== Load Tournaments (with sport info) =====
    try {
        const res = await fetch("admin-sport/get-tournaments");
        const data = await res.json();
        tournamentSelect.innerHTML = '<option value="">Select tournament</option>';
        if(data.status === "success"){
            tournamentsData = data.data;
            data.data.forEach(t => {
                const opt = document.createElement("option");
                opt.value = t.tournament_id;
                opt.textContent = t.tournament_name;
                opt.dataset.sportId = t.sport_id;
                opt.dataset.sportName = t.sport_name;
                opt.dataset.sportCategory = t.sport_category;
                tournamentSelect.appendChild(opt);
            });
        } else if (data.status === "empty"){
            tournamentSelect.innerHTML = '<option>' + data.data + '</option>';
        }
    } catch(err){ tournamentSelect.innerHTML = '<option>Error loading tournaments</option>'; }

    // ===== Tournament Change - Auto-populate Sport =====
    tournamentSelect.addEventListener("change", async () => {
        const selectedOption = tournamentSelect.options[tournamentSelect.selectedIndex];
        
        if (!selectedOption.value) {
            // No tournament selected
            sportInput.value = '';
            sportDisplay.value = '';
            sportCategoryInput.value = '';
            sportFieldsContainer.innerHTML = '<p class="hint-text"><i class="fas fa-info-circle"></i> Select a tournament to see scoring fields</p>';
            categoryBadge.style.display = 'none';
            currentFormConfig = null;
            return;
        }
        
        // Get sport info from tournament
        const sportId = selectedOption.dataset.sportId;
        const sportName = selectedOption.dataset.sportName;
        const category = selectedOption.dataset.sportCategory;
        
        // Set sport values
        sportInput.value = sportId;
        sportDisplay.value = sportName;
        sportCategoryInput.value = category;
        
        // Show category badge
        categoryBadge.style.display = 'block';
        categoryLabel.textContent = categoryNames[category] || category;
        categoryBadge.className = `category-badge category-${category.toLowerCase().replace('_', '-')}`;
        
        // Load sport-specific form fields
        sportFieldsContainer.innerHTML = '<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading form fields...</div>';
        
        try {
            const res = await fetch(`admin-sport/get-sport-fields?sport_id=${sportId}`);
            const data = await res.json();
            
            if (data.status === "success" && data.data) {
                currentFormConfig = data.data;
                renderSportFields(data.data);
            } else {
                sportFieldsContainer.innerHTML = '<p class="error-text">Could not load form configuration</p>';
            }
        } catch(err) {
            sportFieldsContainer.innerHTML = '<p class="error-text">Error loading form fields</p>';
            console.error(err);
        }
    });

    // ===== Load Students for Winner Select =====
    try {
        const res = await fetch("admin-sport/get-students");
        const data = await res.json();
        winnerSelect.innerHTML = '<option value="">Select winner (optional)</option>';
        if(data.status === "success"){
            studentsData = data.data;
            data.data.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.user_id;
                opt.textContent = s.name;
                winnerSelect.appendChild(opt);
            });
        }
    } catch(err){ winnerSelect.innerHTML = '<option>Error loading students</option>'; }

    window.studentsData = studentsData;

    // ===== Render Sport-Specific Fields =====
    function renderSportFields(config) {
        let html = '';
        
        config.sections.forEach(section => {
            const isCollapsible = section.collapsible;
            const sectionId = section.title.toLowerCase().replace(/\s+/g, '-');
            
            html += `<div class="form-section ${isCollapsible ? 'collapsible' : ''}" id="section-${sectionId}">`;
            html += `<h3 class="section-title ${isCollapsible ? 'collapsible-toggle' : ''}" 
                        ${isCollapsible ? `onclick="toggleSection('${sectionId}')"` : ''}>
                        ${section.title}
                        ${isCollapsible ? '<i class="fas fa-chevron-down"></i>' : ''}
                    </h3>`;
            html += `<div class="section-content ${isCollapsible ? 'collapsed' : ''}">`;
            
            if (section.description) {
                html += `<p class="section-description">${section.description}</p>`;
            }
            
            // Render fields
            const fieldsPerRow = section.fields.length <= 2 ? section.fields.length : 2;
            html += `<div class="fields-grid cols-${fieldsPerRow}">`;
            
            section.fields.forEach(field => {
                html += renderField(field);
            });
            
            html += '</div></div></div>';
        });
        
        sportFieldsContainer.innerHTML = html;
        
        // Add event listeners for special field types
        setupSpecialFields();
    }

    // ===== Render Individual Field =====
    function renderField(field) {
        if (field.type === 'hidden') {
            return `<input type="hidden" name="details[${field.name}]" value="${field.value || ''}">`;
        }
        
        let html = `<div class="input-div field-${field.name}" ${field.conditional ? `data-conditional="${field.conditional}"` : ''}>`;
        
        if (field.label && field.type !== 'checkbox') {
            html += `<label for="${field.name}">${field.label}${field.required ? ' <span class="required">*</span>' : ''}</label>`;
        }
        
        switch(field.type) {
            case 'text':
                html += `<input type="text" id="${field.name}" name="details[${field.name}]" 
                         placeholder="${field.placeholder || ''}" ${field.required ? 'required' : ''} ${field.readonly ? 'readonly' : ''}>`;
                break;
                
            case 'number':
                html += `<input type="number" id="${field.name}" name="details[${field.name}]" 
                         min="${field.min ?? ''}" max="${field.max ?? ''}" step="${field.step || '1'}"
                         placeholder="${field.placeholder || '0'}" ${field.readonly ? 'readonly' : ''}>`;
                break;
                
            case 'select':
                html += `<select id="${field.name}" name="details[${field.name}]" ${field.required ? 'required' : ''}>`;
                html += `<option value="">Select...</option>`;
                field.options?.forEach(opt => {
                    html += `<option value="${opt.value}">${opt.label}</option>`;
                });
                html += '</select>';
                break;
                
            case 'checkbox':
                html += `<label class="checkbox-label">
                    <input type="checkbox" id="${field.name}" name="details[${field.name}]" value="1">
                    ${field.label}
                </label>`;
                break;
                
            case 'textarea':
                html += `<textarea id="${field.name}" name="details[${field.name}]" 
                         placeholder="${field.placeholder || ''}" rows="3"></textarea>`;
                break;
                
            case 'player_select':
                html += `<select id="${field.name}" name="details[${field.name}]">`;
                html += '<option value="">Select player...</option>';
                if (window.studentsData) {
                    window.studentsData.forEach(s => {
                        html += `<option value="${s.user_id}">${s.name}</option>`;
                    });
                }
                html += '</select>';
                break;
                
            case 'set_scores':
                html += renderSetScoresField(field);
                break;
                
            case 'round_scores':
                html += renderRoundScoresField(field);
                break;
                
            case 'period_scores':
                html += renderPeriodScoresField(field);
                break;
                
            case 'participant_results':
                html += renderParticipantResultsField(field);
                break;
                
            default:
                html += `<input type="text" id="${field.name}" name="details[${field.name}]">`;
        }
        
        html += '</div>';
        return html;
    }

    // ===== Render Set Scores (for racket sports) =====
    function renderSetScoresField(field) {
        return `
            <div class="set-scores-container" id="set-scores-container">
                <div class="set-scores-list" id="set-scores-list">
                    <div class="set-row" data-set="1">
                        <span class="set-label">Set 1:</span>
                        <input type="number" class="set-score-a" placeholder="A" min="0">
                        <span class="vs">-</span>
                        <input type="number" class="set-score-b" placeholder="B" min="0">
                    </div>
                </div>
                <button type="button" class="btn-small btn-add-set" onclick="addSetRow()">+ Add Set</button>
            </div>
            <input type="hidden" id="set_scores" name="details[set_scores]" value="[]">
        `;
    }

    // ===== Render Round Scores (for combat sports) =====
    function renderRoundScoresField(field) {
        return `
            <div class="round-scores-container" id="round-scores-container">
                <div class="round-scores-list" id="round-scores-list">
                    <div class="round-row" data-round="1">
                        <span class="round-label">Round 1:</span>
                        <input type="number" class="round-score-a" placeholder="A" min="0">
                        <span class="vs">-</span>
                        <input type="number" class="round-score-b" placeholder="B" min="0">
                    </div>
                </div>
                <button type="button" class="btn-small btn-add-round" onclick="addRoundRow()">+ Add Round</button>
            </div>
            <input type="hidden" id="round_scores" name="details[round_scores]" value="[]">
        `;
    }

    // ===== Render Period Scores (for basketball/volleyball/baseball) =====
    function renderPeriodScoresField(field) {
        const label = field.periodLabel || 'Period';
        return `
            <div class="period-scores-container" id="period-scores-container">
                <div class="period-scores-list" id="period-scores-list">
                    <div class="period-row" data-period="1">
                        <span class="period-label">${label} 1:</span>
                        <input type="number" class="period-score-a" placeholder="A" min="0">
                        <span class="vs">-</span>
                        <input type="number" class="period-score-b" placeholder="B" min="0">
                    </div>
                </div>
                <button type="button" class="btn-small btn-add-period" onclick="addPeriodRow('${label}')">+ Add ${label}</button>
            </div>
            <input type="hidden" id="period_scores" name="details[period_scores]" value="[]">
        `;
    }

    // ===== Render Participant Results (for timed events) =====
    function renderParticipantResultsField(field) {
        return `
            <div class="participant-results-container" id="participant-results-container">
                <div class="participant-results-list" id="participant-results-list">
                    <!-- Will be populated dynamically -->
                </div>
                <button type="button" class="btn-small btn-add-participant" onclick="addParticipantResultRow()">+ Add Participant</button>
            </div>
            <input type="hidden" id="results" name="details[results]" value="[]">
        `;
    }

    // ===== Setup Special Field Listeners =====
    function setupSpecialFields() {
        // Collect set/round/period scores before submit
        form.addEventListener('submit', collectDynamicScores, { once: false });
    }

    // ===== Form Submission =====
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        
        // Collect dynamic scores first
        collectDynamicScores();
        
        const formData = new FormData(form);

        try {
            const res = await fetch("admin-tournament/add-match-result", {
                method: "POST",
                body: formData
            });
            const data = await res.json();
            
            if(data.status === "success"){
                showNotification("Match result added successfully!", "success");
                form.reset();
                sportFieldsContainer.innerHTML = '<p class="hint-text"><i class="fas fa-info-circle"></i> Select a sport to see scoring fields</p>';
                categoryBadge.style.display = 'none';
                currentFormConfig = null;
            } else {
                showNotification(data.message || "Failed to add result", "error");
            }
        } catch(err){
            showNotification("Error submitting form!", "error");
            console.error(err);
        }
    });
});

// ===== Global Functions for Dynamic Rows =====

// Toggle collapsible section
function toggleSection(sectionId) {
    const content = document.querySelector(`#section-${sectionId} .section-content`);
    const icon = document.querySelector(`#section-${sectionId} .fa-chevron-down, #section-${sectionId} .fa-chevron-up`);
    content.classList.toggle('collapsed');
    if (icon) {
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }
}

// Add set row for racket sports
let setCounter = 1;
function addSetRow() {
    setCounter++;
    const list = document.getElementById('set-scores-list');
    const row = document.createElement('div');
    row.className = 'set-row';
    row.dataset.set = setCounter;
    row.innerHTML = `
        <span class="set-label">Set ${setCounter}:</span>
        <input type="number" class="set-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="set-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(row);
}

// Add round row for combat sports
let roundCounter = 1;
function addRoundRow() {
    roundCounter++;
    const list = document.getElementById('round-scores-list');
    const row = document.createElement('div');
    row.className = 'round-row';
    row.dataset.round = roundCounter;
    row.innerHTML = `
        <span class="round-label">Round ${roundCounter}:</span>
        <input type="number" class="round-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="round-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(row);
}

// Add period row for ball court sports
let periodCounter = 1;
function addPeriodRow(label) {
    periodCounter++;
    const list = document.getElementById('period-scores-list');
    const row = document.createElement('div');
    row.className = 'period-row';
    row.dataset.period = periodCounter;
    row.innerHTML = `
        <span class="period-label">${label} ${periodCounter}:</span>
        <input type="number" class="period-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="period-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(row);
}

// Add participant result row for timed events
let participantResultCounter = 0;
function addParticipantResultRow() {
    participantResultCounter++;
    const list = document.getElementById('participant-results-list');
    const row = document.createElement('div');
    row.className = 'participant-result-row';
    row.id = `result-row-${participantResultCounter}`;
    
    let playerOptions = '<option value="">Select player...</option>';
    if (window.studentsData) {
        window.studentsData.forEach(s => {
            playerOptions += `<option value="${s.user_id}">${s.name}</option>`;
        });
    }
    
    row.innerHTML = `
        <select class="result-player">${playerOptions}</select>
        <input type="number" class="result-time" placeholder="Time (s)" step="0.001" min="0">
        <input type="number" class="result-distance" placeholder="Distance (m)" step="0.01" min="0">
        <input type="number" class="result-position" placeholder="Pos" min="1" max="100">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>
    `;
    list.appendChild(row);
}

// Collect dynamic scores into hidden fields before submission
function collectDynamicScores() {
    // Collect set scores
    const setRows = document.querySelectorAll('.set-row');
    if (setRows.length > 0) {
        const setScores = [];
        setRows.forEach(row => {
            const a = row.querySelector('.set-score-a')?.value || 0;
            const b = row.querySelector('.set-score-b')?.value || 0;
            setScores.push({ a: parseInt(a), b: parseInt(b) });
        });
        const setScoresInput = document.getElementById('set_scores');
        if (setScoresInput) setScoresInput.value = JSON.stringify(setScores);
    }
    
    // Collect round scores
    const roundRows = document.querySelectorAll('.round-row');
    if (roundRows.length > 0) {
        const roundScores = [];
        roundRows.forEach(row => {
            const a = row.querySelector('.round-score-a')?.value || 0;
            const b = row.querySelector('.round-score-b')?.value || 0;
            roundScores.push({ a: parseInt(a), b: parseInt(b) });
        });
        const roundScoresInput = document.getElementById('round_scores');
        if (roundScoresInput) roundScoresInput.value = JSON.stringify(roundScores);
    }
    
    // Collect period scores
    const periodRows = document.querySelectorAll('.period-row');
    if (periodRows.length > 0) {
        const periodScores = [];
        periodRows.forEach(row => {
            const a = row.querySelector('.period-score-a')?.value || 0;
            const b = row.querySelector('.period-score-b')?.value || 0;
            periodScores.push({ a: parseInt(a), b: parseInt(b) });
        });
        const periodScoresInput = document.getElementById('period_scores');
        if (periodScoresInput) periodScoresInput.value = JSON.stringify(periodScores);
    }
    
    // Collect participant results
    const resultRows = document.querySelectorAll('.participant-result-row');
    if (resultRows.length > 0) {
        const results = [];
        resultRows.forEach(row => {
            const userId = row.querySelector('.result-player')?.value;
            if (userId) {
                results.push({
                    user_id: userId,
                    time: parseFloat(row.querySelector('.result-time')?.value) || null,
                    distance: parseFloat(row.querySelector('.result-distance')?.value) || null,
                    position: parseInt(row.querySelector('.result-position')?.value) || null
                });
            }
        });
        const resultsInput = document.getElementById('results');
        if (resultsInput) resultsInput.value = JSON.stringify(results);
    }
}
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-players");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/search-keyboard-nav.js"></script>
<script>
    SearchKeyboardNav.init({
        inputSelector: '#search-user-inp',
        resultsSelector: '.search-output',
        itemSelector: 'tbody tr',
        actionSelector: '.action-link'
    });
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>

<script>
// ========== Match History Modal Functions ==========
function openMatchHistoryModal(userId, playerName) {
    const modal = document.getElementById('match-history-modal');
    const playerHeader = document.getElementById('player-info-header');
    const content = document.getElementById('match-history-content');
    
    playerHeader.innerHTML = `<h3>${playerName}</h3>`;
    content.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading match history...</div>';
    modal.style.display = 'flex';
    
    fetch(`admin-sport/player-match-history?user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<div class="match-history-list">';
                data.data.forEach(match => {
                    const outcomeClass = match.outcome === 'WON' ? 'outcome-won' : 'outcome-participated';
                    const date = new Date(match.match_date).toLocaleDateString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric' 
                    });
                    html += `
                        <div class="match-card">
                            <div class="match-card-header">
                                <span class="sport-badge">${match.sport_name}</span>
                                <span class="outcome-badge ${outcomeClass}">${match.outcome}</span>
                            </div>
                            <h4>${match.match_name}</h4>
                            <p class="tournament-name">${match.tournament_name}</p>
                            <div class="match-meta">
                                <span><i class="fas fa-calendar"></i> ${date}</span>
                                ${match.player_score ? `<span><i class="fas fa-star"></i> Score: ${match.player_score}</span>` : ''}
                            </div>
                            ${match.winner_score && match.loser_score ? 
                                `<div class="match-score">Final: ${match.winner_score} - ${match.loser_score}</div>` : ''}
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<p class="no-data">No match history found for this player.</p>';
            }
        })
        .catch(err => {
            console.error('Error fetching match history:', err);
            content.innerHTML = '<p class="error-text">Failed to load match history.</p>';
        });
}

function closeMatchHistoryModal() {
    document.getElementById('match-history-modal').style.display = 'none';
}

// ========== Search Match Modal Functions ==========
async function openSearchMatchModal() {
    const modal = document.getElementById('search-match-modal');
    const sportSelect = document.getElementById('search-match-sport');
    
    modal.style.display = 'flex';
    
    // Load sports if not already loaded
    if (sportSelect.options.length <= 1) {
        try {
            const res = await fetch('admin-sport/get-sports');
            const data = await res.json();
            if (data.status === 'success') {
                data.data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.sport_id;
                    opt.textContent = s.sport_name;
                    sportSelect.appendChild(opt);
                });
            }
        } catch(err) { console.error('Error loading sports:', err); }
    }
}

function closeSearchMatchModal() {
    document.getElementById('search-match-modal').style.display = 'none';
    hideMatchDetails();
}

// Debounce timer
let matchSearchTimer = null;

function debounceMatchSearch() {
    clearTimeout(matchSearchTimer);
    matchSearchTimer = setTimeout(performMatchSearch, 300);
}

function performMatchSearch() {
    const query = document.getElementById('search-match-query').value.trim();
    const sportId = document.getElementById('search-match-sport').value;
    const resultsDiv = document.getElementById('search-match-results');
    
    // Hide details panel when searching
    hideMatchDetails();
    
    if (query.length < 2 && !sportId) {
        resultsDiv.innerHTML = '<p class="hint-text">Start typing to search matches...</p>';
        return;
    }
    
    resultsDiv.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
    
    const params = new URLSearchParams();
    if (query) params.append('q', query);
    if (sportId) params.append('sport_id', sportId);
    
    fetch(`admin-sport/search-matches?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<div class="match-cards-grid">';
                data.data.forEach(m => {
                    const date = new Date(m.match_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    html += `<div class="match-card" onclick="viewMatchDetails('${m.match_id}', '${m.match_name.replace(/'/g, "\\'")}')">
                        <div class="match-card-header">
                            <span class="sport-tag">${m.sport_name}</span>
                            <span class="status-badge status-${m.result_status.toLowerCase()}">${m.result_status}</span>
                        </div>
                        <h4 class="match-card-title">${m.match_name}</h4>
                        <div class="match-card-meta">
                            <span class="tournament"><i class="fas fa-trophy"></i> ${m.tournament_name}</span>
                            <span class="date"><i class="fas fa-calendar"></i> ${date}</span>
                        </div>
                    </div>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p class="no-data">No matches found.</p>';
            }
        })
        .catch(err => {
            console.error('Error searching matches:', err);
            resultsDiv.innerHTML = '<p class="error-text">Failed to search matches.</p>';
        });
}

function viewMatchDetails(matchId, matchName) {
    const detailsPanel = document.getElementById('match-details-panel');
    const resultsDiv = document.getElementById('search-match-results');
    const detailsContent = document.getElementById('match-details-content');
    const matchTitle = document.getElementById('details-match-name');
    
    matchTitle.textContent = matchName;
    detailsContent.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading details...</div>';
    
    resultsDiv.style.display = 'none';
    detailsPanel.style.display = 'block';
    
    fetch(`admin-sport/match-details?match_id=${matchId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const match = data.data;
                const date = new Date(match.match_date).toLocaleDateString('en-US', { 
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                });
                
                let html = `
                    <div class="match-info-grid">
                        <div class="info-item">
                            <label>Tournament</label>
                            <span>${match.tournament_name}</span>
                        </div>
                        <div class="info-item">
                            <label>Sport</label>
                            <span>${match.sport_name}</span>
                        </div>
                        <div class="info-item">
                            <label>Date</label>
                            <span>${date}</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="status-badge status-${match.result_status.toLowerCase()}">${match.result_status}</span>
                        </div>
                        ${match.winner_name ? `<div class="info-item"><label>Winner</label><span class="winner-name"><i class="fas fa-trophy"></i> ${match.winner_name}</span></div>` : ''}
                    </div>
                `;
                
                // Sport-specific details
                if (match.details) {
                    html += '<div class="sport-details-section"><h4>Match Details</h4>';
                    html += renderSportDetails(match.details, match.sport_category);
                    html += '</div>';
                }
                
                // Participants
                if (match.participants && match.participants.length > 0) {
                    html += '<div class="participants-section"><h4>Participants</h4><div class="participants-list">';
                    match.participants.forEach(p => {
                        html += `<div class="participant-card">
                            <span class="name">${p.player_name}</span>
                            ${p.team ? `<span class="team">Team ${p.team}</span>` : ''}
                            ${p.score ? `<span class="score">Score: ${p.score}</span>` : ''}
                        </div>`;
                    });
                    html += '</div></div>';
                }
                
                detailsContent.innerHTML = html;
            } else {
                detailsContent.innerHTML = '<p class="error-text">Match details not found.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading match details:', err);
            detailsContent.innerHTML = '<p class="error-text">Failed to load match details.</p>';
        });
}

function hideMatchDetails() {
    document.getElementById('match-details-panel').style.display = 'none';
    document.getElementById('search-match-results').style.display = 'block';
}

function renderSportDetails(details, category) {
    let html = '<div class="details-grid">';
    
    for (const [key, value] of Object.entries(details)) {
        if (value === null || value === undefined || key === 'id' || key === 'match_id') continue;
        
        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        // Handle JSON fields
        if (typeof value === 'object') {
            html += `<div class="detail-item full-width"><label>${label}</label><span>${JSON.stringify(value)}</span></div>`;
        } else {
            html += `<div class="detail-item"><label>${label}</label><span>${value}</span></div>`;
        }
    }
    
    html += '</div>';
    return html;
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});
</script>
</html>
