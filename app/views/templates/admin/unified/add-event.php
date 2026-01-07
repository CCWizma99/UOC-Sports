<div id="add-event">
    <!-- Event Creation -->
    <section>
      <h2>Create a Sport Event</h2>
      <form id="eventForm">
        <input type="text" id="eventName" name="name" placeholder="Event Name" required>
        
        <select id="eventSport" name="sport_id" required>
          <option value="">Select Sport</option>
          <!-- Will be populated dynamically -->
        </select>
        
        <input type="date" id="eventStartDate" name="start_date" required>
        <input type="date" id="eventEndDate" name="end_date">
        
        <button type="submit">Create Event</button>
      </form>
    </section>

    <section id="invitationSection" style="display: none;">
      <h2>Send Invitations for Teams to Participate in the Event</h2>
      
      <!-- Saved Recipients List -->
      <div id="savedRecipientsList"></div>
      
      <!-- Manual Email Entry -->
      <div class="custom-recipient">
        <h4>Invite a Custom Recipient</h4>
        <input type="text" id="custom-institution" placeholder="Enter Institution Name" required>
        <input type="email" id="custom-email" placeholder="Enter Email Address" required>
        
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
    end_date: document.getElementById('eventEndDate').value
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
    showInvitationMessage('Please enter both institution name and email.', 'error');
    return;
  }
  
  if (!currentTournamentId) {
    showInvitationMessage('Please create a tournament first.', 'error');
    return;
  }
  
  await sendInvitation(email, recipientName, saveRecipient);
});

// Send invitation to saved recipient
document.getElementById('sendSavedInvitation').addEventListener('click', async () => {
  const select = document.getElementById('savedRecipientsSelect');
  const selectedValue = select.value;
  
  if (!selectedValue) {
    showInvitationMessage('Please select a recipient.', 'error');
    return;
  }
  
  if (!currentTournamentId) {
    showInvitationMessage('Please create a tournament first.', 'error');
    return;
  }
  
  const recipient = JSON.parse(selectedValue);
  await sendInvitation(recipient.email, recipient.recepient_name, false);
});

// Send invitation to saved recipient (from list)
async function sendToSaved(email, name) {
  if (!currentTournamentId) {
    showInvitationMessage('Please create a tournament first.', 'error');
    return;
  }
  await sendInvitation(email, name, false);
}

// Generic send invitation function
async function sendInvitation(email, recipientName, saveRecipient) {
  const btn = event.target;
  const originalText = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Sending...';
  
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
      showInvitationMessage(data.message, 'success');
      
      // Clear custom fields
      document.getElementById('custom-email').value = '';
      document.getElementById('custom-institution').value = '';
      document.getElementById('saveRecipient').checked = false;
      
      // Reload saved recipients if we saved a new one
      if (saveRecipient) {
        await loadSavedRecipients();
      }
    } else {
      showInvitationMessage(data.message, 'error');
    }
  } catch (error) {
    showInvitationMessage('Error sending invitation: ' + error.message, 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = originalText;
  }
}

// Show invitation message
function showInvitationMessage(message, type) {
  showNotification(message, type);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  loadSports();
  loadSavedRecipients();
});
</script>