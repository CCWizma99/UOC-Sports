<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Match Result | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/captain/add-result.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .mesh-sporty {
            background:
                linear-gradient(rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
        }
        .active { border: none !important; font-weight: 300 !important; }

        /* Autocomplete styles */
        .autocomplete-container { position: relative; }
        .autocomplete-list {
            position: absolute; top: 100%; left: 0; right: 0; background: white;
            border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 12px 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); z-index: 1000;
            max-height: 200px; overflow-y: auto; display: none;
        }
        .autocomplete-item {
            padding: 10px 16px; cursor: pointer; font-size: 14px;
            transition: background 0.2s; border-bottom: 1px solid #f8fafc;
        }
        .autocomplete-item:hover { background: #f1f5f9; }
        .autocomplete-item:last-child { border-bottom: none; }
        .autocomplete-item .meta { font-size: 11px; color: #64748b; display: block; }

        .badge-new {
            background: #ecfdf5; color: #059669; font-size: 10px; font-weight: 700;
            padding: 2px 8px; border-radius: 4px; vertical-align: middle;
            margin-left: 8px; border: 1px solid #d1fae5; text-transform: uppercase;
        }
        
        .invitational-toggle-container {
            margin: 15px 0; padding: 15px; background: #f8fafc; border-radius: 12px;
            border: 1px dashed #e2e8f0;
        }
        
        .period-row { 
            display: flex; gap: 10px; align-items: center; margin-bottom: 8px; 
            background: white; padding: 10px; border-radius: 8px; border: 1px solid #f1f5f9;
        }
        .period-label { font-size: 12px; font-weight: 700; color: #4b0082; min-width: 60px; }
        .period-row input { width: 60px !important; text-align: center; padding: 8px !important; }
    </style>
</head>
<body class="mesh-sporty">

<?php
    require '../app/views/templates/general/header.php';
?>

<div class="add-result-wrapper">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-icon">
            <i class="fas fa-trophy"></i>
        </div>
        <div>
            <h1>Add Match Result</h1>
            <p>Submit official match results for tournaments you are authorised to manage</p>
        </div>
    </div>

    <?php
    $permitted = $permitted_tournaments ?? [];
    if (empty($permitted)):
    ?>
    <!-- No Permissions State -->
    <div class="result-card">
        <div class="result-card-body">
            <div class="no-permission-state">
                <div class="icon"><i class="fas fa-lock"></i></div>
                <h3>No Tournaments Available</h3>
                <p>You have not been granted permission to add results for any tournament yet.<br>
                   Please wait for the admin to grant access after your event starts.</p>
            </div>
        </div>
    </div>
    <?php else: ?>

    <!-- Two-column layout -->
    <div class="result-grid">

        <!-- LEFT: Tournament Selector -->
        <div class="result-card">
            <div class="result-card-header">
                <i class="fas fa-list-check"></i>
                <h2>Select Tournament</h2>
            </div>
            <div class="result-card-body">
                <p style="font-size:13px;color:#6b7280;margin:0 0 16px;">Click a tournament to load its result form.</p>
                <div class="tournament-list" id="tournament-list">
                    <?php foreach ($permitted as $t): ?>
                    <div class="tournament-item"
                         data-tournament-id="<?= htmlspecialchars($t['tournament_id']) ?>"
                         data-sport-id="<?= htmlspecialchars($t['sport_id']) ?>"
                         data-sport-name="<?= htmlspecialchars($t['sport_name']) ?>"
                         data-sport-category="<?= htmlspecialchars($t['sport_category']) ?>"
                         data-tournament-name="<?= htmlspecialchars($t['tournament_name']) ?>"
                         onclick="selectTournament(this)">
                        <div class="tournament-name"><?= htmlspecialchars($t['tournament_name']) ?></div>
                        <div class="tournament-meta">
                            <span class="meta-tag sport-tag">
                                <i class="fas fa-futbol" style="font-size:10px;"></i>
                                <?= htmlspecialchars($t['sport_name']) ?>
                            </span>
                            <?php if (!empty($t['start_date'])): ?>
                            <span class="meta-tag date-tag">
                                <i class="fas fa-calendar-alt" style="font-size:10px;"></i>
                                Started: <?= date('d M Y', strtotime($t['start_date'])) ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($t['end_date'])): ?>
                            <span class="meta-tag">
                                <i class="fas fa-flag-checkered" style="font-size:10px;"></i>
                                Ends: <?= date('d M Y', strtotime($t['end_date'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: Match Result Form -->
        <div class="result-card">
            <div class="result-card-header">
                <i class="fas fa-pen-to-square"></i>
                <h2 id="form-card-title">Match Result Form</h2>
            </div>
            <div class="result-card-body">

                <!-- Placeholder before tournament selected -->
                <div id="form-placeholder" class="result-form-placeholder">
                    <i class="fas fa-hand-pointer"></i>
                    <p>Select a tournament from the left to load the match result form</p>
                </div>

                <!-- Actual form (hidden until tournament is selected) -->
                <form id="add-result-form" novalidate style="display:none;">
                    <p class="required-note"><span>*</span> Required fields</p>

                    <!-- Hidden fields -->
                    <input type="hidden" id="captain-tournament-id" name="tournament_id">
                    <input type="hidden" id="captain-sport-id" name="sport_id">

                    <!-- Sport (auto-filled, readonly) -->
                    <div class="input-div">
                        <label>Tournament</label>
                        <input type="text" id="captain-tournament-display" readonly>
                    </div>

                    <div class="input-div">
                        <label>Sport</label>
                        <input type="text" id="captain-sport-display" readonly>
                    </div>

                    <!-- Match Name -->
                    <div class="input-div">
                        <label for="captain-match-name">Match Name <span class="required">*</span></label>
                        <input type="text" id="captain-match-name" name="match_name"
                               placeholder="e.g. Quarter Final, Semi Final, Final" required>
                    </div>

                    <!-- Match Date -->
                    <div class="input-div">
                        <label for="captain-match-date">Match Date <span class="required">*</span></label>
                        <input type="date" id="captain-match-date" name="match_date" required>
                    </div>

                    <!-- Result Status -->
                    <div class="input-div">
                        <label for="captain-result-status">Result Status</label>
                        <select id="captain-result-status" name="result_status">
                            <option value="COMPLETED">Completed</option>
                            <option value="DRAW">Draw</option>
                            <option value="CANCELLED">Cancelled</option>
                            <option value="NO_RESULT">No Result</option>
                        </select>
                    </div>

                    <!-- Winner Type Selector -->
                    <input type="hidden" id="captain-winner-type" name="winner_type" value="INTERNAL">

                    <!-- Visiting Player Toggle (Hidden by default, shown for individual sports) -->
                    <div id="invitational-toggle-wrapper" class="invitational-toggle-container" style="display:none;">
                        <label class="checkbox-label" style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin:0;">
                            <input type="checkbox" id="toggle-visiting-player" onchange="toggleWinnerSource(this.checked)">
                            <span>Winner is a visiting/external player?</span>
                        </label>
                    </div>

                    <!-- UOC Student Winner (Default) -->
                    <div class="input-div" id="winner-internal-container">
                        <label for="captain-winner">Winner (UOC Student)</label>
                        <select id="captain-winner" name="winner_id">
                            <option value="">Select winner (optional)</option>
                        </select>
                    </div>

                    <!-- Team Winner Selection (Populated via JS) -->
                    <div class="input-div" id="winner-team-container" style="display:none;">
                        <label for="winner-team-selection">Winner Team</label>
                        <select id="winner-team-selection" name="winner_team_selection" onchange="document.getElementById('captain-winner-name').value = this.value">
                            <option value="">Select winning team...</option>
                        </select>
                        <input type="hidden" id="captain-winner-name" name="winner_name">
                    </div>

                    <!-- Invitational Player Info -->
                    <div id="invitational-fields" style="display:none; padding: 15px; background: #fffbeb; border-radius: 12px; border: 1px solid #fef3c7; margin-bottom: 20px;">
                        <h4 style="font-size:13px; color:#92400e; margin:0 0 12px; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-user-plus"></i> Visiting Player Details
                        </h4>
                        <div class="fields-grid cols-2">
                            <div class="input-div">
                                <label>First Name <span class="required">*</span></label>
                                <input type="text" name="invitational[fname]" id="inv-fname" placeholder="First Name">
                            </div>
                            <div class="input-div">
                                <label>Last Name <span class="required">*</span></label>
                                <input type="text" name="invitational[lname]" id="inv-lname" placeholder="Last Name">
                            </div>
                        </div>
                        <div class="input-div" style="margin-top:10px;">
                            <label>University <span class="required">*</span></label>
                            <input type="text" name="invitational[university]" id="inv-university" placeholder="e.g. University of Ruhuna">
                        </div>
                        <div class="input-div" style="margin-top:10px;">
                            <label>Student ID (Optional)</label>
                            <input type="text" name="invitational[student_id]" id="inv-student-id" placeholder="Official ID">
                        </div>
                    </div>

                    <!-- Sport Category Badge -->
                    <div id="captain-category-badge" class="category-badge" style="display:none;"></div>

                    <!-- Dynamic sport-specific fields -->
                    <div id="captain-sport-fields" class="sport-fields-container">
                        <p class="hint-text"><i class="fas fa-info-circle"></i> Sport-specific fields will appear here</p>
                    </div>

                    <button type="submit" class="submit-result-btn" id="captain-submit-btn">
                        <i class="fas fa-check-circle"></i> Submit Match Result
                    </button>
                    <div id="form-message-captain"></div>
                </form>

            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php require '../app/views/templates/general/footer.php'; ?>

<script>
// ======================================================
// CAPTAIN ADD RESULT PAGE SCRIPT
// Reuses same sport-specific form rendering as admin side
// ======================================================

let studentsData   = [];
let setCounter     = 1;
let roundCounter   = 1;
let periodCounter  = 1;
let participantResultCounter = 0;

const categoryNames = {
    'TEAM_GOAL'  : 'Team Sport (Goals)',
    'RACKET'     : 'Racket Sport (Sets)',
    'CRICKET'    : 'Cricket',
    'COMBAT'     : 'Combat Sport',
    'TRACK_FIELD': 'Track & Field',
    'BOARD_GAME' : 'Board Game',
    'BALL_COURT' : 'Ball Court Sport',
    'WEIGHT'     : 'Weight Lifting'
};

let currentSportCategory = '';

// Load students for winner selector
async function loadStudents() {
    try {
        const res  = await fetch('/uoc-sports/public/admin-sport/get-students');
        const data = await res.json();
        const sel  = document.getElementById('captain-winner');
        if (sel && data.status === 'success') {
            studentsData = data.data;
            sel.innerHTML = '<option value="">Select winner (optional)</option>';
            data.data.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.user_id;
                opt.textContent = s.name;
                sel.appendChild(opt);
            });
        }
    } catch(e) { console.error('Failed to load students:', e); }
}

// Select tournament from the left panel
async function selectTournament(el) {
    // Mark selected
    document.querySelectorAll('.tournament-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    const tournamentId   = el.dataset.tournamentId;
    const sportId        = el.dataset.sportId;
    const sportName      = el.dataset.sportName;
    const sportCategory  = el.dataset.sportCategory;
    const tournamentName = el.dataset.tournamentName;

    currentSportCategory = sportCategory;

    // Populate hidden/display fields
    document.getElementById('captain-tournament-id').value      = tournamentId;
    document.getElementById('captain-sport-id').value           = sportId;
    document.getElementById('captain-tournament-display').value = tournamentName;
    document.getElementById('captain-sport-display').value      = sportName;
    document.getElementById('form-card-title').textContent      = 'Match Result — ' + tournamentName;

    // Show category badge
    const badge = document.getElementById('captain-category-badge');
    badge.textContent = categoryNames[sportCategory] || sportCategory;
    badge.style.display = 'inline-block';

    // Show form, hide placeholder
    document.getElementById('form-placeholder').style.display = 'none';
    document.getElementById('add-result-form').style.display  = 'block';

    // Reset winner selection state
    resetWinnerSelection(sportCategory);

    // Reset dynamic fields
    setCounter = roundCounter = periodCounter = 1;
    participantResultCounter = 0;

    // Load sport-specific fields
    const container = document.getElementById('captain-sport-fields');
    container.innerHTML = '<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading form fields...</div>';

    try {
        const res  = await fetch(`/uoc-sports/public/admin-sport/get-sport-fields?sport_id=${sportId}`);
        const data = await res.json();
        if (data.status === 'success' && data.data) {
            renderSportFields(data.data, container);
            
            // Re-init autocomplete for new fields
            initTeamAutocomplete();
        } else {
            container.innerHTML = '<p class="hint-text"><i class="fas fa-info-circle"></i> No additional fields for this sport.</p>';
        }
    } catch (e) {
        container.innerHTML = '<p style="color:#dc2626;font-size:13px;">Error loading sport fields.</p>';
        console.error(e);
    }
}

// Render sport-specific field sections
function renderSportFields(config, container) {
    let html = '';
    config.sections.forEach(section => {
        const isCollapsible = section.collapsible;
        const sectionId     = section.title.toLowerCase().replace(/\s+/g, '-');

        html += `<div class="form-section ${isCollapsible ? 'collapsible' : ''}" id="section-${sectionId}">`;
        html += `<h3 class="section-title ${isCollapsible ? 'collapsible-toggle' : ''}"
                     ${isCollapsible ? `onclick="toggleSection('${sectionId}')"` : ''}>
                     ${section.title}
                     ${isCollapsible ? '<i class="fas fa-chevron-down" style="font-size:12px;"></i>' : ''}
                 </h3>`;
        html += `<div class="section-content ${isCollapsible ? 'collapsed' : ''}">`;

        if (section.description) {
            html += `<p style="font-size:13px;color:#6b7280;margin:0 0 12px;">${section.description}</p>`;
        }

        const cols = section.fields.length <= 2 ? section.fields.length : 2;
        html += `<div class="fields-grid cols-${cols}">`;
        section.fields.forEach(field => { html += renderField(field); });
        html += '</div></div></div>';
    });
    container.innerHTML = html;
}

function renderField(field) {
    if (field.type === 'hidden') {
        return `<input type="hidden" name="details[${field.name}]" value="${field.value || ''}">`;
    }

    let html = `<div class="input-div field-${field.name}">`;

    if (field.label && field.type !== 'checkbox') {
        html += `<label for="${field.name}">${field.label}${field.required ? ' <span class="required">*</span>' : ''}</label>`;
    }

    switch(field.type) {
        case 'text':
            html += `<input type="text" id="${field.name}" name="details[${field.name}]"
                     placeholder="${field.placeholder || ''}" ${field.required ? 'required' : ''}>`;
            break;
        case 'number':
            html += `<input type="number" id="${field.name}" name="details[${field.name}]"
                     min="${field.min ?? ''}" max="${field.max ?? ''}" step="${field.step || '1'}"
                     placeholder="${field.placeholder || '0'}">`;
            break;
        case 'select':
            html += `<select id="${field.name}" name="details[${field.name}]" ${field.required ? 'required' : ''}>`;
            html += '<option value="">Select...</option>';
            (field.options || []).forEach(opt => {
                html += `<option value="${opt.value}">${opt.label}</option>`;
            });
            html += '</select>';
            break;
        case 'checkbox':
            html += `<label class="checkbox-label" style="display:flex;align-items:center;gap:8px;font-weight:400;cursor:pointer;">
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
            studentsData.forEach(s => {
                html += `<option value="${s.user_id}">${s.name}</option>`;
            });
            html += '</select>';
            break;
        case 'set_scores':
            html += renderSetScoresField();
            break;
        case 'round_scores':
            html += renderRoundScoresField();
            break;
        case 'period_scores':
            html += renderPeriodScoresField(field.periodLabel || 'Period');
            break;
        case 'participant_results':
            html += renderParticipantResultsField();
            break;
        default:
            html += `<input type="text" id="${field.name}" name="details[${field.name}]">`;
    }
    html += '</div>';
    return html;
}

function renderSetScoresField() {
    return `<div class="set-scores-container" id="set-scores-container">
        <div class="set-scores-list" id="set-scores-list">
            <div class="set-row" data-set="1">
                <span class="set-label">Set 1:</span>
                <input type="number" class="set-score-a" placeholder="A" min="0">
                <span class="vs">-</span>
                <input type="number" class="set-score-b" placeholder="B" min="0">
            </div>
        </div>
        <button type="button" class="btn-small" onclick="addSetRow()">+ Add Set</button>
    </div>
    <input type="hidden" id="set_scores" name="details[set_scores]" value="[]">`;
}

function renderRoundScoresField() {
    return `<div class="round-scores-container" id="round-scores-container">
        <div class="round-scores-list" id="round-scores-list">
            <div class="round-row" data-round="1">
                <span class="round-label">Round 1:</span>
                <input type="number" class="round-score-a" placeholder="A" min="0">
                <span class="vs">-</span>
                <input type="number" class="round-score-b" placeholder="B" min="0">
            </div>
        </div>
        <button type="button" class="btn-small" onclick="addRoundRow()">+ Add Round</button>
    </div>
    <input type="hidden" id="round_scores" name="details[round_scores]" value="[]">`;
}

function renderPeriodScoresField(label) {
    return `<div class="period-scores-container" id="period-scores-container">
        <div class="period-scores-list" id="period-scores-list">
            <div class="period-row" data-period="1">
                <span class="period-label">${label} 1:</span>
                <input type="number" class="period-score-a" placeholder="A" min="0">
                <span class="vs">-</span>
                <input type="number" class="period-score-b" placeholder="B" min="0">
            </div>
        </div>
        <button type="button" class="btn-small" onclick="addPeriodRow('${label}')">+ Add ${label}</button>
    </div>
    <input type="hidden" id="period_scores" name="details[period_scores]" value="[]">`;
}

function renderParticipantResultsField() {
    return `<div class="participant-results-container" id="participant-results-container">
        <div class="participant-results-list" id="participant-results-list"></div>
        <button type="button" class="btn-small" onclick="addParticipantResultRow()">+ Add Participant</button>
    </div>
    <input type="hidden" id="results" name="details[results]" value="[]">`;
}

// Toggle collapsible section
function toggleSection(id) {
    const content = document.querySelector(`#section-${id} .section-content`);
    const icon    = document.querySelector(`#section-${id} .fa-chevron-down, #section-${id} .fa-chevron-up`);
    content.classList.toggle('collapsed');
    if (icon) { icon.classList.toggle('fa-chevron-down'); icon.classList.toggle('fa-chevron-up'); }
}

function addSetRow() {
    setCounter++;
    const row = document.createElement('div');
    row.className = 'set-row'; row.dataset.set = setCounter;
    row.innerHTML = `<span class="set-label">Set ${setCounter}:</span>
        <input type="number" class="set-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="set-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>`;
    document.getElementById('set-scores-list').appendChild(row);
}

function addRoundRow() {
    roundCounter++;
    const row = document.createElement('div');
    row.className = 'round-row'; row.dataset.round = roundCounter;
    row.innerHTML = `<span class="round-label">Round ${roundCounter}:</span>
        <input type="number" class="round-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="round-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>`;
    document.getElementById('round-scores-list').appendChild(row);
}

function addPeriodRow(label) {
    periodCounter++;
    const row = document.createElement('div');
    row.className = 'period-row'; row.dataset.period = periodCounter;
    row.innerHTML = `<span class="period-label">${label} ${periodCounter}:</span>
        <input type="number" class="period-score-a" placeholder="A" min="0">
        <span class="vs">-</span>
        <input type="number" class="period-score-b" placeholder="B" min="0">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>`;
    document.getElementById('period-scores-list').appendChild(row);
}

function addParticipantResultRow() {
    participantResultCounter++;
    let opts = '<option value="">Select player...</option>';
    studentsData.forEach(s => { opts += `<option value="${s.user_id}">${s.name}</option>`; });
    const row = document.createElement('div');
    row.className = 'participant-result-row';
    row.innerHTML = `<select class="result-player">${opts}</select>
        <input type="number" class="result-time" placeholder="Time(s)" step="0.001" min="0">
        <input type="number" class="result-distance" placeholder="Dist(m)" step="0.01" min="0">
        <input type="number" class="result-position" placeholder="Pos" min="1" max="100">
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">×</button>`;
    document.getElementById('participant-results-list').appendChild(row);
}

function collectDynamicScores() {
    // Set scores
    const setRows = document.querySelectorAll('.set-row');
    if (setRows.length > 0) {
        const scores = [];
        setRows.forEach(r => scores.push({ a: parseInt(r.querySelector('.set-score-a')?.value || 0), b: parseInt(r.querySelector('.set-score-b')?.value || 0) }));
        const el = document.getElementById('set_scores');
        if (el) el.value = JSON.stringify(scores);
    }
    // Round scores
    const roundRows = document.querySelectorAll('.round-row');
    if (roundRows.length > 0) {
        const scores = [];
        roundRows.forEach(r => scores.push({ a: parseInt(r.querySelector('.round-score-a')?.value || 0), b: parseInt(r.querySelector('.round-score-b')?.value || 0) }));
        const el = document.getElementById('round_scores');
        if (el) el.value = JSON.stringify(scores);
    }
    // Period scores
    const periodRows = document.querySelectorAll('.period-row');
    if (periodRows.length > 0) {
        const scores = [];
        periodRows.forEach(r => scores.push({ a: parseInt(r.querySelector('.period-score-a')?.value || 0), b: parseInt(r.querySelector('.period-score-b')?.value || 0) }));
        const el = document.getElementById('period_scores');
        if (el) el.value = JSON.stringify(scores);
    }
    // Participant results
    const pRows = document.querySelectorAll('.participant-result-row');
    if (pRows.length > 0) {
        const results = [];
        pRows.forEach(r => results.push({
            user_id  : r.querySelector('.result-player')?.value || '',
            time     : parseFloat(r.querySelector('.result-time')?.value || 0),
            distance : parseFloat(r.querySelector('.result-distance')?.value || 0),
            position : parseInt(r.querySelector('.result-position')?.value || 0)
        }));
        const el = document.getElementById('results');
        if (el) el.value = JSON.stringify(results);
    }
}

// Form submission
document.getElementById('add-result-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    collectDynamicScores();

    const btn = document.getElementById('captain-submit-btn');
    const msg = document.getElementById('form-message-captain');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    msg.style.display = 'none';

    try {
        const formData = new FormData(this);
        const res  = await fetch('/uoc-sports/public/captain/submit-result', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.status === 'success') {
            msg.className = 'success';
            msg.textContent = '✓ ' + data.message;
            msg.style.display = 'block';
            this.reset();
            document.getElementById('captain-sport-fields').innerHTML =
                '<p class="hint-text"><i class="fas fa-check-circle" style="color:#16a34a;"></i> Result submitted! Select tournament again to add another match.</p>';
            document.getElementById('captain-category-badge').style.display = 'none';
            document.getElementById('captain-tournament-display').value = '';
            document.getElementById('captain-sport-display').value = '';
            document.getElementById('captain-tournament-id').value = '';
            document.getElementById('captain-sport-id').value = '';
            document.querySelectorAll('.tournament-item').forEach(i => i.classList.remove('selected'));
        } else {
            msg.className = 'error';
            msg.textContent = '✗ ' + (data.message || 'Failed to submit result.');
            msg.style.display = 'block';
        }
    } catch (err) {
        msg.className = 'error';
        msg.textContent = '✗ Network error. Please try again.';
        msg.style.display = 'block';
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Match Result';
    }
});

/**
 * Winner Selection Logic
 */
function resetWinnerSelection(category) {
    const isTeam = ['TEAM_GOAL', 'BALL_COURT', 'CRICKET'].includes(category);
    
    document.getElementById('invitational-toggle-wrapper').style.display = isTeam ? 'none' : 'block';
    document.getElementById('winner-internal-container').style.display = isTeam ? 'none' : 'block';
    document.getElementById('winner-team-container').style.display = isTeam ? 'block' : 'none';
    document.getElementById('invitational-fields').style.display = 'none';
    document.getElementById('toggle-visiting-player').checked = false;
    
    document.getElementById('captain-winner-type').value = isTeam ? 'TEAM' : 'INTERNAL';
    
    if (isTeam) {
        updateTeamWinnerOptions();
    }
}

function toggleWinnerSource(isVisiting) {
    document.getElementById('winner-internal-container').style.display = isVisiting ? 'none' : 'block';
    document.getElementById('invitational-fields').style.display = isVisiting ? 'block' : 'none';
    document.getElementById('captain-winner-type').value = isVisiting ? 'INVITATIONAL' : 'INTERNAL';
}

function updateTeamWinnerOptions() {
    const teamA = document.getElementById('team_a_name')?.value || 'Team A';
    const teamB = document.getElementById('team_b_name')?.value || 'Team B';
    
    const sel = document.getElementById('winner-team-selection');
    const currentVal = sel.value;
    
    sel.innerHTML = `
        <option value="">Select winning team...</option>
        <option value="${teamA}">${teamA}</option>
        <option value="${teamB}">${teamB}</option>
        <option value="DRAW">DRAW</option>
    `;
    
    if (currentVal && (currentVal === teamA || currentVal === teamB || currentVal === 'DRAW')) {
        sel.value = currentVal;
    }
}

/**
 * Autocomplete Logic
 */
function initTeamAutocomplete() {
    ['team_a_name', 'team_b_name'].forEach(id => {
        const input = document.getElementById(id);
        if (!input) return;
        
        // Add container and list
        const parent = input.parentElement;
        parent.classList.add('autocomplete-container');
        
        const list = document.createElement('div');
        list.className = 'autocomplete-list';
        parent.appendChild(list);
        
        input.addEventListener('input', async (e) => {
            const query = e.target.value;
            if (['TEAM_GOAL', 'BALL_COURT', 'CRICKET'].includes(currentSportCategory)) {
                updateTeamWinnerOptions();
            }
            
            if (query.length < 2) {
                list.style.display = 'none';
                return;
            }
            
            try {
                const res = await fetch(`/uoc-sports/public/api/playing-teams/search?q=${encodeURIComponent(query)}`);
                const data = await res.json();
                
                if (data.status === 'success' && data.data.length > 0) {
                    list.innerHTML = data.data.map(team => `
                        <div class="autocomplete-item" onclick="selectSuggestion('${id}', '${team.team_name.replace(/'/g, "\\'")}')">
                            ${team.team_name}
                            <span class="meta">Registered Team</span>
                        </div>
                    `).join('');
                    list.style.display = 'block';
                } else {
                    list.innerHTML = `<div class="autocomplete-item" style="cursor:default; color:#94a3b8;">No matches found <span class="badge-new">NEW</span></div>`;
                    list.style.display = 'block';
                }
            } catch (e) { console.error('Autocomplete error:', e); }
        });
        
        // Hide list on blur
        document.addEventListener('click', (e) => {
            if (!parent.contains(e.target)) list.style.display = 'none';
        });
    });
}

function selectSuggestion(inputId, value) {
    const input = document.getElementById(inputId);
    input.value = value;
    input.parentElement.querySelector('.autocomplete-list').style.display = 'none';
    
    if (['TEAM_GOAL', 'BALL_COURT', 'CRICKET'].includes(currentSportCategory)) {
        updateTeamWinnerOptions();
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
});
</script>

</body>
</html>
