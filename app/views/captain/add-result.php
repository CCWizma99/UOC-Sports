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
    <!-- Tab Navigation -->
    <div class="tab-nav" style="display:flex;gap:0;margin-bottom:0;border-bottom:3px solid #e9e4f5;">
        <button class="tab-btn active" id="tab-match-results" onclick="switchResultTab('match-results')" style="padding:12px 28px;font-size:14px;font-weight:700;border:none;background:none;cursor:pointer;color:#5e2d91;border-bottom:3px solid #5e2d91;margin-bottom:-3px;transition:all 0.2s;">
            <i class="fas fa-trophy"></i> Match Results
        </button>
        <button class="tab-btn" id="tab-overall-awards" onclick="switchResultTab('overall-awards')" style="padding:12px 28px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;color:#94a3b8;border-bottom:3px solid transparent;margin-bottom:-3px;transition:all 0.2s;">
            <i class="fas fa-medal"></i> Overall Awards
        </button>
    </div>

    <!-- Match Results Tab Content -->
    <div id="panel-match-results">

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

                    <!-- Team Roster Sections -->
                    <div id="team-roster-section" style="display:none;">
                        <h3 style="font-size:14px;font-weight:700;color:#5e2d91;margin:18px 0 10px;display:flex;align-items:center;gap:8px;">
                            <i class="fas fa-users"></i> Match Team Rosters
                        </h3>

                        <!-- Load Previous Teams Dropdown -->
                        <div id="previous-teams-container" style="margin-bottom:12px;padding:12px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;display:none;">
                            <label style="font-size:12px;font-weight:700;color:#15803d;"><i class="fas fa-history"></i> Load team from previous match:</label>
                            <select id="previous-team-select" style="width:100%;margin-top:6px;padding:8px 12px;border-radius:8px;border:1px solid #86efac;font-size:13px;" onchange="loadPreviousTeam(this.value)">
                                <option value="">Select a previous match...</option>
                            </select>
                        </div>

                        <!-- Team A Players -->
                        <div style="padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:10px;">
                            <h4 style="font-size:13px;font-weight:700;color:#1e40af;margin:0 0 8px;display:flex;align-items:center;gap:6px;">
                                <span style="background:#3b82f6;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;">A</span>
                                Team A Players (UOC)
                            </h4>
                            <div id="team-a-players-list"></div>
                            <button type="button" class="btn-small" onclick="addTeamPlayerRow('A')" style="margin-top:6px;">
                                <i class="fas fa-plus"></i> Add Player
                            </button>
                        </div>

                        <!-- Team B Players -->
                        <div style="padding:14px;background:#fffbeb;border-radius:10px;border:1px solid #fef3c7;margin-bottom:10px;">
                            <h4 style="font-size:13px;font-weight:700;color:#92400e;margin:0 0 8px;display:flex;align-items:center;gap:6px;">
                                <span style="background:#f59e0b;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;">B</span>
                                Team B Players (Opponents)
                            </h4>
                            <div id="team-b-players-list"></div>
                            <button type="button" class="btn-small" onclick="addTeamPlayerRow('B')" style="margin-top:6px;">
                                <i class="fas fa-plus"></i> Add Player
                            </button>
                        </div>
                    </div>
                    <!-- Hidden fields for team player JSON -->
                    <input type="hidden" id="team_a_players_json" name="team_a_players">
                    <input type="hidden" id="team_b_players_json" name="team_b_players">

                    <button type="submit" class="submit-result-btn" id="captain-submit-btn">
                        <i class="fas fa-check-circle"></i> Submit Match Result
                    </button>
                    <div id="form-message-captain"></div>
                </form>

            </div>
        </div>
    </div> <!-- end result-grid -->
    </div> <!-- end panel-match-results -->

    <!-- Overall Awards Tab Content (hidden by default) -->
    <div id="panel-overall-awards" style="display:none;">
        <div class="result-grid">
            <!-- LEFT: Tournament Selector for Awards -->
            <div class="result-card">
                <div class="result-card-header">
                    <i class="fas fa-list-check"></i>
                    <h2>Select Tournament</h2>
                </div>
                <div class="result-card-body">
                    <p style="font-size:13px;color:#6b7280;margin:0 0 16px;">Click a tournament to add overall awards for it.</p>
                    <div class="tournament-list" id="awards-tournament-list">
                        <?php foreach ($permitted as $t): ?>
                        <div class="tournament-item"
                             data-tournament-id="<?= htmlspecialchars($t['tournament_id']) ?>"
                             data-sport-id="<?= htmlspecialchars($t['sport_id']) ?>"
                             data-sport-name="<?= htmlspecialchars($t['sport_name']) ?>"
                             data-sport-category="<?= htmlspecialchars($t['sport_category']) ?>"
                             data-tournament-name="<?= htmlspecialchars($t['tournament_name']) ?>"
                             onclick="selectAwardsTournament(this)">
                            <div class="tournament-name"><?= htmlspecialchars($t['tournament_name']) ?></div>
                            <div class="tournament-meta">
                                <span class="meta-tag sport-tag">
                                    <i class="fas fa-futbol" style="font-size:10px;"></i>
                                    <?= htmlspecialchars($t['sport_name']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Awards Form -->
            <div class="result-card">
                <div class="result-card-header">
                    <i class="fas fa-medal"></i>
                    <h2 id="awards-form-title">Overall Awards</h2>
                </div>
                <div class="result-card-body">
                    <div id="awards-placeholder" class="result-form-placeholder">
                        <i class="fas fa-hand-pointer"></i>
                        <p>Select a tournament from the left to add overall awards</p>
                    </div>

                    <div id="awards-form-container" style="display:none;">
                        <p style="font-size:13px;color:#6b7280;margin:0 0 12px;"><i class="fas fa-info-circle" style="color:#a855f7;"></i> Assign sport-specific titles to outstanding players. Points are automatically calculated based on the tournament level.</p>
                        
                        <input type="hidden" id="awards-tournament-id">
                        <input type="hidden" id="awards-sport-id">
                        <input type="hidden" id="awards-sport-category">

                        <div id="award-rows-container"></div>

                        <button type="button" class="submit-result-btn" id="submit-awards-btn" onclick="submitOverallAwards()">
                            <i class="fas fa-medal"></i> Submit Awards
                        </button>
                        <div id="awards-message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end panel-overall-awards -->

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
        const res  = await fetch(`/uoc-sports/public/add-result/get-sport-fields?sport_id=${sportId}`);
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
    
    // Collect and validate team rosters
    const teamA = collectTeamPlayers('A');
    const teamB = collectTeamPlayers('B');
    
    // Validate mandatory NIC/RegNo for external players
    const allPlayers = [...teamA, ...teamB];
    const missingId = allPlayers.find(p => !p.is_uoc_student && !p.external_id);
    if (missingId) {
        alert(`Mandatory field missing: Please provide NIC or Registration No for player "${missingId.player_name}".`);
        return;
    }

    // Set hidden fields
    document.getElementById('team_a_players_json').value = JSON.stringify(teamA);
    document.getElementById('team_b_players_json').value = JSON.stringify(teamB);

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
                
                if (data.status === 'success') {
                    let html = data.data.map(team => `
                        <div class="autocomplete-item" onclick="selectSuggestion('${id}', '${team.team_name.replace(/'/g, "\\'")}')">
                            ${team.team_name}
                            <span class="meta">Registered Team</span>
                        </div>
                    `).join('');
                    
                    // Add "NEW TEAM" option
                    html += `
                        <div class="autocomplete-item" style="border-top: 1px dashed #cbd5e1; color: #5e2d91; font-weight: 700;" 
                             onclick="handleNewTeamOption('${id}')">
                            <i class="fas fa-plus-circle"></i> NEW TEAM...
                        </div>
                    `;
                    
                    list.innerHTML = html;
                    list.style.display = 'block';
                } else {
                    list.innerHTML = `
                        <div class="autocomplete-item" style="cursor:default; color:#94a3b8;">No matches found</div>
                        <div class="autocomplete-item" style="border-top: 1px dashed #cbd5e1; color: #5e2d91; font-weight: 700;" 
                             onclick="handleNewTeamOption('${id}')">
                            <i class="fas fa-plus-circle"></i> ADD NEW TEAM...
                        </div>
                    `;
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
    
    // Trigger roster autoloading
    const side = inputId === 'team_a_name' ? 'A' : 'B';
    autoloadTeamRoster(side, value);
}

function handleNewTeamOption(inputId) {
    const name = prompt("Enter the name of the new team/university:");
    if (name && name.trim()) {
        const input = document.getElementById(inputId);
        input.value = name.trim();
        input.parentElement.querySelector('.autocomplete-list').style.display = 'none';
        
        if (['TEAM_GOAL', 'BALL_COURT', 'CRICKET'].includes(currentSportCategory)) {
            updateTeamWinnerOptions();
        }
    }
}

async function autoloadTeamRoster(side, teamName) {
    const tournamentId = document.getElementById('captain-tournament-id').value;
    if (!tournamentId || !teamName) return;

    try {
        const container = document.getElementById(side === 'A' ? 'team-a-players-list' : 'team-b-players-list');
        const originalContent = container.innerHTML;
        
        // Don't overwrite if there's already data unless confirmed
        if (container.querySelectorAll('.player-row').length > 0) {
            if (!confirm(`Autoload roster for ${teamName}? This will clear current players on this side.`)) return;
        }

        container.innerHTML = '<div style="padding:10px; font-size:12px; color:#64748b;"><i class="fas fa-spinner fa-spin"></i> Checking for team card...</div>';

        const res = await fetch(`/uoc-sports/public/captain/get-team-roster?tournament_id=${tournamentId}&team_name=${encodeURIComponent(teamName)}`);
        const data = await res.json();

        if (data.status === 'success' && data.data.length > 0) {
            container.innerHTML = '';
            teamPlayerCounters[side] = 0;
            data.data.forEach(p => addTeamPlayerRow(side, p));
            
            // Show toast/message
            showFormMessage(document.getElementById('form-message-captain'), 
                `✓ Autoloaded ${data.data.length} players for ${teamName} (${data.source})`, 'success');
        } else if (data.status === 'empty') {
            container.innerHTML = originalContent; // Restore
            showFormMessage(document.getElementById('form-message-captain'), data.message, 'error');
        } else {
            container.innerHTML = originalContent;
        }
    } catch (e) {
        console.error('Autoload error:', e);
    }
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
});

// ======================================================
// TAB SWITCHING
// ======================================================
function switchResultTab(tab) {
    const matchPanel = document.getElementById('panel-match-results');
    const awardsPanel = document.getElementById('panel-overall-awards');
    const tabMatch = document.getElementById('tab-match-results');
    const tabAwards = document.getElementById('tab-overall-awards');
    
    if (tab === 'match-results') {
        matchPanel.style.display = 'block';
        awardsPanel.style.display = 'none';
        tabMatch.style.color = '#5e2d91';
        tabMatch.style.fontWeight = '700';
        tabMatch.style.borderBottomColor = '#5e2d91';
        tabAwards.style.color = '#94a3b8';
        tabAwards.style.fontWeight = '600';
        tabAwards.style.borderBottomColor = 'transparent';
    } else {
        matchPanel.style.display = 'none';
        awardsPanel.style.display = 'block';
        tabAwards.style.color = '#5e2d91';
        tabAwards.style.fontWeight = '700';
        tabAwards.style.borderBottomColor = '#5e2d91';
        tabMatch.style.color = '#94a3b8';
        tabMatch.style.fontWeight = '600';
        tabMatch.style.borderBottomColor = 'transparent';
    }
}

// ======================================================
// TEAM ROSTER MANAGEMENT
// ======================================================
let teamPlayerCounters = { A: 0, B: 0 };

function showTeamRosterSection(tournamentId) {
    const section = document.getElementById('team-roster-section');
    if (section) {
        section.style.display = 'block';
        loadPreviousTeams(tournamentId);
    }
}

function addTeamPlayerRow(side, prefill = {}) {
    const containerId = side === 'A' ? 'team-a-players-list' : 'team-b-players-list';
    const container = document.getElementById(containerId);
    if (!container) return;

    teamPlayerCounters[side]++;
    const idx = teamPlayerCounters[side];
    const isUoc = side === 'A' ? true : (prefill.is_uoc_student ?? false);
    
    const row = document.createElement('div');
    row.className = 'player-row';
    row.style.cssText = 'display:flex;gap:6px;align-items:center;margin-bottom:6px;';
    row.id = `player-row-${side}-${idx}`;
    
    let studentOptions = '<option value="">Select student</option>';
    studentsData.forEach(s => {
        const selected = (prefill.user_id && prefill.user_id === s.user_id) ? 'selected' : '';
        studentOptions += `<option value="${s.user_id}" ${selected}>${s.name}</option>`;
    });

    row.innerHTML = `
        <div style="display:flex; flex-direction:column; flex:1; gap:4px;">
            ${isUoc ? `
            <select class="player-select" data-side="${side}" style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;">
                ${studentOptions}
            </select>` : `
            <input type="text" class="player-name-input" data-side="${side}" placeholder="Player Name" 
                   value="${prefill.player_name || ''}"
                   style="width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;">`}
            
            ${!isUoc ? `
            <input type="text" class="player-external-id" data-side="${side}" placeholder="NIC / Registration No" 
                   value="${prefill.external_id || ''}"
                   style="width:100%;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:11px;background:#fcfcfc;">` : ''}
        </div>
        <label style="display:flex;align-items:center;gap:3px;font-size:11px;white-space:nowrap; cursor:pointer;" title="UOC Student?">
            <input type="checkbox" class="uoc-checkbox" ${isUoc ? 'checked' : ''} data-side="${side}" data-idx="${idx}" 
                   onchange="toggleUocStatus('${side}', ${idx}, this.checked)">
            UOC
        </label>
        <button type="button" onclick="removePlayerRow('${side}', ${idx})" 
                style="background:#fee2e2;color:#dc2626;border:none;padding:8px 10px;border-radius:6px;cursor:pointer;font-size:12px;align-self:flex-start;">
            <i class="fas fa-times"></i>
        </button>`;
    
    container.appendChild(row);
}

function toggleUocStatus(side, idx, isChecked) {
    const row = document.getElementById(`player-row-${side}-${idx}`);
    if (!row) return;
    
    const playerName = collectSinglePlayerData(row).player_name;
    const externalId = row.querySelector('.player-external-id')?.value || '';
    
    // Re-render the row with current values prefilled
    addTeamPlayerRow(side, {
        player_name: playerName,
        external_id: externalId,
        is_uoc_student: isChecked
    });
    
    // Remove the old one (addTeamPlayerRow adds a new one)
    row.remove();
}

function collectSinglePlayerData(row) {
    const select = row.querySelector('select.player-select');
    const input = row.querySelector('input.player-name-input');
    const extId = row.querySelector('input.player-external-id');
    const uocCheck = row.querySelector('input.uoc-checkbox');
    
    let userId = null, playerName = '';
    
    if (select && select.value) {
        userId = select.value;
        playerName = select.options[select.selectedIndex].text;
    } else if (input && input.value.trim()) {
        playerName = input.value.trim();
    }
    
    return {
        user_id: userId,
        player_name: playerName,
        external_id: extId ? extId.value.trim() : null,
        is_uoc_student: uocCheck && uocCheck.checked ? 1 : 0
    };
}

function removePlayerRow(side, idx) {
    const row = document.getElementById(`player-row-${side}-${idx}`);
    if (row) row.remove();
}

function collectTeamPlayers(side) {
    const containerId = side === 'A' ? 'team-a-players-list' : 'team-b-players-list';
    const container = document.getElementById(containerId);
    if (!container) return [];

    const players = [];
    container.querySelectorAll('.player-row').forEach(row => {
        const p = collectSinglePlayerData(row);
        if (p.player_name) {
            players.push(p);
        }
    });
    return players;
}

async function loadPreviousTeams(tournamentId) {
    try {
        const res = await fetch(`/uoc-sports/public/captain/get-match-teams?tournament_id=${tournamentId}`);
        const data = await res.json();
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            const container = document.getElementById('previous-teams-container');
            const select = document.getElementById('previous-team-select');
            
            select.innerHTML = '<option value="">Select a previous match...</option>';
            window._previousTeamsData = data.data;
            
            data.data.forEach((team, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = `${team.match_name} (Team A: ${team.A.length} players, Team B: ${team.B.length} players)`;
                select.appendChild(opt);
            });
            
            container.style.display = 'block';
        }
    } catch (e) {
        console.error('Failed to load previous teams:', e);
    }
}

function loadPreviousTeam(index) {
    if (index === '' || !window._previousTeamsData) return;
    
    const team = window._previousTeamsData[parseInt(index)];
    
    // Clear existing
    document.getElementById('team-a-players-list').innerHTML = '';
    document.getElementById('team-b-players-list').innerHTML = '';
    teamPlayerCounters = { A: 0, B: 0 };
    
    // Add Team A players
    team.A.forEach(p => addTeamPlayerRow('A', p));
    // Add Team B players
    team.B.forEach(p => addTeamPlayerRow('B', p));
}

// Override the selectTournament function to also show team roster
const originalSelectTournament = window.selectTournament;
if (originalSelectTournament) {
    window.selectTournament = async function(el) {
        await originalSelectTournament.call(this, el);
        const tournamentId = el.dataset.tournamentId;
        showTeamRosterSection(tournamentId);
    };
}

// ======================================================
// OVERALL AWARDS TAB
// ======================================================
let awardTitles = [];
let selectedAwardsTournament = null;

function selectAwardsTournament(el) {
    // Mark selected (within awards panel only)
    document.querySelectorAll('#awards-tournament-list .tournament-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');

    const tournamentId = el.dataset.tournamentId;
    const sportId = el.dataset.sportId;
    const sportCategory = el.dataset.sportCategory;
    const tournamentName = el.dataset.tournamentName;

    selectedAwardsTournament = { tournamentId, sportId, sportCategory, tournamentName };

    document.getElementById('awards-tournament-id').value = tournamentId;
    document.getElementById('awards-sport-id').value = sportId;
    document.getElementById('awards-sport-category').value = sportCategory;
    document.getElementById('awards-form-title').textContent = 'Awards — ' + tournamentName;

    // Show form
    document.getElementById('awards-placeholder').style.display = 'none';
    document.getElementById('awards-form-container').style.display = 'block';

    // Load award titles for this sport category
    loadAwardTitles(sportCategory);
}

async function loadAwardTitles(sportCategory) {
    try {
        const res = await fetch(`/uoc-sports/public/captain/get-award-titles?sport_category=${sportCategory}`);
        const data = await res.json();
        
        if (data.status === 'success') {
            awardTitles = data.data;
            renderAwardRows();
        }
    } catch (e) {
        console.error('Failed to load award titles:', e);
    }
}

function renderAwardRows() {
    const container = document.getElementById('award-rows-container');
    container.innerHTML = '';
    
    let studentOptions = '<option value="">Select student</option>';
    studentsData.forEach(s => {
        studentOptions += `<option value="${s.user_id}">${s.name}</option>`;
    });

    awardTitles.forEach((title, i) => {
        const row = document.createElement('div');
        row.style.cssText = 'padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:10px;';
        row.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                <span style="background:#a855f7;color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">${i + 1}</span>
                <label style="font-weight:700;font-size:13px;color:#374151;flex:1;">${title}</label>
                <span style="background:#f3e8ff;color:#7c3aed;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">🏅 Award</span>
            </div>
            <select class="award-student-select" data-title="${title}" data-index="${i}"
                    style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
                ${studentOptions}
            </select>
        `;
        container.appendChild(row);
    });
}

async function submitOverallAwards() {
    const btn = document.getElementById('submit-awards-btn');
    const msgDiv = document.getElementById('awards-message');
    
    const tournamentId = document.getElementById('awards-tournament-id').value;
    const sportId = document.getElementById('awards-sport-id').value;
    
    if (!tournamentId || !sportId) {
        showFormMessage(msgDiv, 'Please select a tournament first.', 'error');
        return;
    }
    
    const awards = [];
    document.querySelectorAll('.award-student-select').forEach(sel => {
        if (sel.value) {
            awards.push({
                user_id: sel.value,
                award_title: sel.dataset.title
            });
        }
    });
    
    if (awards.length === 0) {
        showFormMessage(msgDiv, 'Please select at least one student for an award.', 'error');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    try {
        const res = await fetch('/uoc-sports/public/captain/submit-overall-awards', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tournament_id: tournamentId,
                sport_id: sportId,
                awards: awards
            })
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            showFormMessage(msgDiv, data.message, 'success');
        } else {
            showFormMessage(msgDiv, data.message, 'error');
        }
    } catch (e) {
        showFormMessage(msgDiv, 'Error submitting awards: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-medal"></i> Submit Awards';
    }
}

function showFormMessage(el, msg, type) {
    if (!el) return;
    el.innerHTML = `<div style="padding:10px 14px;border-radius:8px;margin-top:10px;font-size:13px;
        background:${type === 'success' ? '#f0fdf4' : '#fef2f2'};
        color:${type === 'success' ? '#15803d' : '#dc2626'};
        border:1px solid ${type === 'success' ? '#bbf7d0' : '#fecaca'};">
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}
    </div>`;
    setTimeout(() => { el.innerHTML = ''; }, 6000);
}

// ======================================================
// FORM SUBMIT OVERRIDE — Include team players
// ======================================================
// Roster data is now handled in the main submit listener above

// Add CSS for player management buttons
const styleEl = document.createElement('style');
styleEl.textContent = `
    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        background: #5e2d91;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-small:hover { background: #4a2374; }
    .player-row select, .player-row input[type="text"] {
        transition: border-color 0.2s;
    }
    .player-row select:focus, .player-row input[type="text"]:focus {
        border-color: #a855f7;
        outline: none;
        box-shadow: 0 0 0 3px rgba(168,85,247,0.1);
    }
`;
document.head.appendChild(styleEl);
</script>

</body>
</html>
