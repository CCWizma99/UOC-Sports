<div id="event-table-container">
  <h2>Tournament Events</h2>
  <div id="loadingMessage" class="loading-message">Loading tournaments...</div>
  <div id="errorMessage" class="error-message" style="display: none;"></div>
  
  <table class="event-table" id="eventTable" style="display: none;">
    <thead>
      <tr>
        <th>No.</th>
        <th>Event Name</th>
        <th>Sport</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="eventTableBody">
      <!-- Will be populated dynamically -->
    </tbody>
  </table>
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

<script>
let currentModalTournamentId = null;

// Load tournaments on page load
async function loadTournaments() {
  try {
    const response = await fetch('/uoc-sports/public/admin-tournament/list');
    const data = await response.json();
    
    const loadingMsg = document.getElementById('loadingMessage');
    const errorMsg = document.getElementById('errorMessage');
    const table = document.getElementById('eventTable');
    const tbody = document.getElementById('eventTableBody');
    
    loadingMsg.style.display = 'none';
    
    if (data.status === 'success' && data.data && data.data.length > 0) {
      tbody.innerHTML = '';
      
      data.data.forEach((tournament, index) => {
        const row = document.createElement('tr');
        
        const startDate = tournament.start_date ? new Date(tournament.start_date).toLocaleDateString() : '-';
        const endDate = tournament.end_date ? new Date(tournament.end_date).toLocaleDateString() : '-';
        const status = tournament.status || 'INCOMPLETE';
        
        row.innerHTML = `
          <td>${index + 1}</td>
          <td>${tournament.tournament_name}</td>
          <td>${tournament.sport_name || '-'}</td>
          <td>${startDate}</td>
          <td>${endDate}</td>
          <td><span class="status-badge status-${status.toLowerCase()}">${status}</span></td>
          <td>
            <button class="btn-invite" onclick="openInvitationModal('${tournament.tournament_id}', '${tournament.tournament_name}')">
              Send Invitation
            </button>
          </td>
        `;
        
        tbody.appendChild(row);
      });
      
      table.style.display = 'table';
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
  document.getElementById('invitationModal').style.display = 'block';
  
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

// Show message in modal
function showModalMessage(message, type) {
  const messageDiv = document.getElementById('modalMessage');
  messageDiv.className = `message ${type}`;
  messageDiv.textContent = message;
  messageDiv.style.display = 'block';
}

// Close modal when clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('invitationModal');
  if (event.target === modal) {
    closeInvitationModal();
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  loadTournaments();
});
</script>
