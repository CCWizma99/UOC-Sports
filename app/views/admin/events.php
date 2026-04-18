<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Events | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/events-page.css);
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
    <div class="events-grid-container">
        <div class="events-grid-left">
            <div id="add-event">
                <!-- Event Creation -->
                <section>
                    <h2>Create a Sport Event</h2>
                    <form id="eventForm" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>
                        
                        <div class="input-div">
                            <label for="eventName">Event Name <span class="required">*</span></label>
                            <input type="text" id="eventName" name="name" 
                                   placeholder="Enter event name" 
                                   aria-required="true" required>
                        </div>
                        
                        <div class="input-div">
                            <label for="eventSport">Sport <span class="required">*</span></label>
                            <select id="eventSport" name="sport_id" 
                                    aria-required="true" required>
                                <option value="">Select Sport</option>
                            </select>
                        </div>
                        
                        <div class="input-div">
                            <label for="eventStartDate">Start Date <span class="required">*</span></label>
                            <input type="date" id="eventStartDate" name="start_date" 
                                   aria-required="true" required>
                        </div>
                        
                        <div class="input-div">
                            <label for="eventEndDate">End Date</label>
                            <input type="date" id="eventEndDate" name="end_date">
                        </div>
                        
                        <div class="input-div">
                            <label for="eventMatchLevel">Match Level <span class="required">*</span></label>
                            <select id="eventMatchLevel" name="match_level" required>
                                <option value="UNIVERSITY">University Level</option>
                                <option value="NATIONAL">National Level</option>
                                <option value="INTERNATIONAL">International Level</option>
                            </select>
                        </div>
                        
                        <button type="submit">Create Event</button>
                    </form>
                </section>

                <section id="invitationSection" style="display: none;">
                    <h2>Send Invitations for Teams to Participate</h2>
                    
                    <!-- Saved Recipients List -->
                    <div id="savedRecipientsList"></div>
                    
                    <!-- Manual Email Entry -->
                    <div class="custom-recipient">
                        <h4>Invite a Custom Recipient</h4>
                        <div class="input-div">
                            <label for="custom-institution">Institution Name <span class="required">*</span></label>
                            <input type="text" id="custom-institution" 
                                   placeholder="Enter Institution Name" 
                                   aria-required="true" required>
                        </div>
                        <div class="input-div">
                            <label for="custom-email">Email Address <span class="required">*</span></label>
                            <input type="email" id="custom-email" 
                                   placeholder="Enter Email Address" 
                                   autocomplete="email"
                                   aria-required="true" required>
                        </div>
                        
                        <label class="save-checkbox">
                            <input type="checkbox" id="saveRecipient">
                            <span>Save this recipient for future use</span>
                        </label>
                        
                        <button type="button" id="sendCustomInvitation">Send Invitation</button>
                    </div>
                    
                    <!-- Choose from Saved Recipients -->
                    <div class="saved-recipients-dropdown">
                        <h4>Or Choose from Saved Recipients</h4>
                        <select id="savedRecipientsSelect">
                            <option value="">-- Select a saved recipient --</option>
                        </select>
                        <button type="button" id="sendSavedInvitation">Send Invitation</button>
                    </div>
                </section>
            </div>
        </div>

        <div class="events-grid-right">
            <div id="event-table-container">
                <h2>Tournament Events</h2>
                <div id="loadingMessage" class="loading-message">Loading tournaments...</div>
                <div id="errorMessage" class="error-message" style="display: none;"></div>
                
                <div id="eventCardsContainer" class="event-cards-container" style="display: none;">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Invitation Modal -->
<div id="invitationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeInvitationModal()">&times;</span>
        <h3>Send Tournament Invitation</h3>
        <p id="modalTournamentName"></p>
        
        <div class="modal-body">
            <!-- Manual Email Entry -->
            <div class="email-input-section">
                <h4>Enter Recipient Details</h4>
                <input type="text" id="modalInstitution" placeholder="Institution Name" required>
                <input type="email" id="modalEmail" placeholder="Email Address" required>
                
                <label class="save-checkbox">
                    <input type="checkbox" id="modalSaveRecipient">
                    <span>Save this recipient for future use</span>
                </label>
            </div>
            
            <!-- Saved Recipients -->
            <div class="saved-recipients-section">
                <h4>Or Choose from Saved Recipients</h4>
                <select id="modalSavedRecipients">
                    <option value="">-- Select a saved recipient --</option>
                </select>
            </div>
            
            <div id="modalMessage" class="message"></div>
            
            <div class="modal-actions">
                <button type="button" class="btn-primary" onclick="sendModalInvitation()">Send Invitation</button>
                <button type="button" class="btn-secondary" onclick="closeInvitationModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Completion Modal -->
<div id="completionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeCompletionModal()">&times;</span>
        <div class="modal-header-icon xy-center">
            <i class="fas fa-check-double"></i>
        </div>
        <h3>Finalize Tournament</h3>
        <p id="completionModalTournamentName" class="modal-subtitle"></p>
        
        <div class="modal-body">
            <div class="warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <p>This will mark the event as <strong>COMPLETE</strong>. This action will finalize all results and potentially restrict further modifications by captains.</p>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn-complete" id="confirmCompletionBtn">
                    <i class="fas fa-check-circle"></i> Confirm Completion
                </button>
                <button type="button" class="btn-secondary" onclick="closeCompletionModal()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTournamentId = null;

// Load sports for dropdown
async function loadSports() {
    try {
        const response = await fetch('/uoc-sports/public/admin-sport/get-sports');
        const data = await response.json();
        
        const select = document.getElementById('eventSport');
        select.innerHTML = '<option value="">Select Sport</option>';
        
        if (data.status === 'success' && data.data) {
            data.data.forEach(sport => {
                const option = document.createElement('option');
                option.value = sport.sport_id;
                option.textContent = sport.sport_name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading sports:', error);
    }
}

// Load saved recipients
async function loadSavedRecipients() {
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/saved-recipients');
        const data = await response.json();
        
        if (data.status === 'success' && data.recipients) {
            const select = document.getElementById('savedRecipientsSelect');
            const listDiv = document.getElementById('savedRecipientsList');
            
            select.innerHTML = '<option value="">-- Select a saved recipient --</option>';
            listDiv.innerHTML = '';
            
            data.recipients.forEach((recipient, index) => {
                // Add to dropdown
                const option = document.createElement('option');
                option.value = JSON.stringify(recipient);
                option.textContent = `${recipient.recepient_name} (${recipient.email})`;
                select.appendChild(option);
                
                // Add to list view
                const row = document.createElement('div');
                row.className = 'recipient-row flex';
                row.innerHTML = `
                    <span class="number xy-center">${String(index + 1).padStart(2, '0')}</span>
                    <div class="recipient">
                        <span class="name">${recipient.recepient_name}</span>
                        <span class="email">${recipient.email}</span>
                    </div>
                    <button class="send-mail-btn" onclick="sendToSaved('${recipient.email}', '${recipient.recepient_name}')">
                        Send Invitation
                    </button>
                `;
                listDiv.appendChild(row);
            });
        }
    } catch (error) {
        console.error('Error loading saved recipients:', error);
    }
}

// Handle tournament creation
document.getElementById('eventForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('eventName').value,
        sport_id: document.getElementById('eventSport').value,
        start_date: document.getElementById('eventStartDate').value,
        end_date: document.getElementById('eventEndDate').value,
        match_level: document.getElementById('eventMatchLevel').value
    };
    
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            currentTournamentId = data.tournament_id;
            
            // Show invitation section
            document.getElementById('invitationSection').style.display = 'block';
            
            // Reset form
            document.getElementById('eventForm').reset();
            
            // Reload tournaments table
            loadTournaments();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error creating tournament: ' + error.message, 'error');
    }
});

// Send custom invitation
document.getElementById('sendCustomInvitation').addEventListener('click', async () => {
    const email = document.getElementById('custom-email').value.trim();
    const recipientName = document.getElementById('custom-institution').value.trim();
    const saveRecipient = document.getElementById('saveRecipient').checked;
    
    if (!email || !recipientName) {
        showNotification('Please enter both institution name and email.', 'error');
        return;
    }
    
    if (!currentTournamentId) {
        showNotification('Please create a tournament first.', 'error');
        return;
    }
    
    await sendInvitation(email, recipientName, saveRecipient);
});

// Send invitation to saved recipient
document.getElementById('sendSavedInvitation').addEventListener('click', async () => {
    const select = document.getElementById('savedRecipientsSelect');
    const selectedValue = select.value;
    
    if (!selectedValue) {
        showNotification('Please select a recipient.', 'error');
        return;
    }
    
    if (!currentTournamentId) {
        showNotification('Please create a tournament first.', 'error');
        return;
    }
    
    const recipient = JSON.parse(selectedValue);
    await sendInvitation(recipient.email, recipient.recepient_name, false);
});

// Send invitation to saved recipient (from list)
async function sendToSaved(email, name) {
    if (!currentTournamentId) {
        showNotification('Please create a tournament first.', 'error');
        return;
    }
    await sendInvitation(email, name, false);
}

// Generic send invitation function
async function sendInvitation(email, recipientName, saveRecipient) {
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/send-invitation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                recipient_name: recipientName,
                tournament_id: currentTournamentId,
                save_recipient: saveRecipient
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            
            // Clear custom fields
            document.getElementById('custom-email').value = '';
            document.getElementById('custom-institution').value = '';
            document.getElementById('saveRecipient').checked = false;
            
            // Reload saved recipients if we saved a new one
            if (saveRecipient) {
                await loadSavedRecipients();
            }
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Error sending invitation: ' + error.message, 'error');
    }
}
</script>

<script>
let currentModalTournamentId = null;

// Load tournaments on page load
async function loadTournaments() {
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/list');
        const data = await response.json();
        
        const loadingMsg = document.getElementById('loadingMessage');
        const errorMsg = document.getElementById('errorMessage');
        const cardsContainer = document.getElementById('eventCardsContainer');
        
        loadingMsg.style.display = 'none';
        
        if (data.status === 'success' && data.data && data.data.length > 0) {
            cardsContainer.innerHTML = '';
            
            data.data.forEach((tournament, index) => {
                const startDate = tournament.start_date ? new Date(tournament.start_date).toLocaleDateString() : '-';
                const endDate = tournament.end_date ? new Date(tournament.end_date).toLocaleDateString() : '-';
                const status = tournament.status || 'INCOMPLETE';
                const isComplete = status === 'COMPLETE';
                
                const card = document.createElement('div');
                card.className = 'event-card';
                card.innerHTML = `
                    <div class="event-card-header">
                        <span class="event-number">#${index + 1}</span>
                        <span class="status-badge status-${status.toLowerCase()}">${status}</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-name">${tournament.tournament_name}</h3>
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fas fa-futbol"></i>
                                <span>${tournament.sport_name || 'N/A'}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>${startDate} - ${endDate}</span>
                            </div>
                        </div>
                    </div>
                    <div class="event-card-footer" style="display: flex; gap: 10px;">
                        <button class="btn-invite" onclick="openInvitationModal('${tournament.tournament_id}', '${tournament.tournament_name}')">
                            <i class="fas fa-envelope"></i> Invite
                        </button>
                        ${!isComplete ? `
                        <button class="btn-complete" onclick="markAsComplete('${tournament.tournament_id}', '${tournament.tournament_name.replace(/'/g, "\\'")}')">
                            <i class="fas fa-check-circle"></i> Complete
                        </button>
                        ` : ''}
                    </div>
                `;
                
                cardsContainer.appendChild(card);
            });
            
            cardsContainer.style.display = 'flex';
        } else {
            errorMsg.textContent = 'No tournaments found.';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading tournaments:', error);
        document.getElementById('loadingMessage').style.display = 'none';
        const errorMsg = document.getElementById('errorMessage');
        errorMsg.textContent = 'Error loading tournaments: ' + error.message;
        errorMsg.style.display = 'block';
    }
}

// Load saved recipients for modal
async function loadModalSavedRecipients() {
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/saved-recipients');
        const data = await response.json();
        
        if (data.status === 'success' && data.recipients) {
            const select = document.getElementById('modalSavedRecipients');
            select.innerHTML = '<option value="">-- Select a saved recipient --</option>';
            
            data.recipients.forEach(recipient => {
                const option = document.createElement('option');
                option.value = JSON.stringify(recipient);
                option.textContent = `${recipient.recepient_name} (${recipient.email})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading saved recipients:', error);
    }
}

// Open invitation modal
function openInvitationModal(tournamentId, tournamentName) {
    currentModalTournamentId = tournamentId;
    document.getElementById('modalTournamentName').textContent = `Tournament: ${tournamentName}`;
    document.getElementById('invitationModal').style.display = 'flex';
    
    // Clear previous inputs
    document.getElementById('modalInstitution').value = '';
    document.getElementById('modalEmail').value = '';
    document.getElementById('modalSaveRecipient').checked = false;
    document.getElementById('modalSavedRecipients').value = '';
    document.getElementById('modalMessage').style.display = 'none';
    
    // Load saved recipients
    loadModalSavedRecipients();
}

// Close invitation modal
function closeInvitationModal() {
    document.getElementById('invitationModal').style.display = 'none';
    currentModalTournamentId = null;
}

// Send invitation from modal
async function sendModalInvitation() {
    const institution = document.getElementById('modalInstitution').value.trim();
    const email = document.getElementById('modalEmail').value.trim();
    const saveRecipient = document.getElementById('modalSaveRecipient').checked;
    const savedSelect = document.getElementById('modalSavedRecipients').value;
    
    let recipientEmail, recipientName;
    
    // Check if using saved recipient or manual entry
    if (savedSelect) {
        const savedRecipient = JSON.parse(savedSelect);
        recipientEmail = savedRecipient.email;
        recipientName = savedRecipient.recepient_name;
    } else if (institution && email) {
        recipientEmail = email;
        recipientName = institution;
    } else {
        showModalMessage('Please enter recipient details or select a saved recipient.', 'error');
        return;
    }
    
    if (!currentModalTournamentId) {
        showModalMessage('Tournament ID is missing.', 'error');
        return;
    }
    
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/send-invitation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: recipientEmail,
                recipient_name: recipientName,
                tournament_id: currentModalTournamentId,
                save_recipient: saveRecipient && !savedSelect
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showModalMessage(data.message, 'success');
            
            // Clear form after success
            setTimeout(() => {
                closeInvitationModal();
            }, 2000);
        } else {
            showModalMessage(data.message, 'error');
        }
    } catch (error) {
        showModalMessage('Error sending invitation: ' + error.message, 'error');
    }
}

// Mark tournament as complete
let tournamentIdToComplete = null;

function markAsComplete(tournamentId, tournamentName) {
    tournamentIdToComplete = tournamentId;
    document.getElementById('completionModalTournamentName').textContent = tournamentName;
    document.getElementById('completionModal').style.display = 'flex';
}

function closeCompletionModal() {
    document.getElementById('completionModal').style.display = 'none';
    tournamentIdToComplete = null;
}

document.getElementById('confirmCompletionBtn').addEventListener('click', async () => {
    if (!tournamentIdToComplete) return;
    
    const btn = document.getElementById('confirmCompletionBtn');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    try {
        const response = await fetch('/uoc-sports/public/admin-tournament/complete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tournament_id: tournamentIdToComplete })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            closeCompletionModal();
            loadTournaments(); // Refresh list
        } else {
            showNotification(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    } catch (error) {
        showNotification('Error completing tournament: ' + error.message, 'error');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
});

// Show message in modal
function showModalMessage(message, type) {
    const messageDiv = document.getElementById('modalMessage');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const invModal = document.getElementById('invitationModal');
    const compModal = document.getElementById('completionModal');
    if (event.target === invModal) {
        closeInvitationModal();
    }
    if (event.target === compModal) {
        closeCompletionModal();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadSports();
    loadSavedRecipients();
    loadTournaments();
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-events");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
