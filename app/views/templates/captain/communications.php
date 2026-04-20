<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Communicate</h1>
    <p>Send and view messages.</p>
  </div>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Send Message Form -->
    <div class="form-section">
      <h2>Send a Message</h2>
      <form class="form" id="messageForm">
        <div class="form-group">
          <label for="recipient">Recipient</label>
          <select id="recipient" name="recipient" required>
            <option value="" disabled selected>-- Loading Recipients --</option>
          </select>
        </div>

        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" placeholder="Enter Subject" required>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" placeholder="Type your message here........" required></textarea>
        </div>

        <div class="buttons">
          <button type="submit" class="send-btn">Send</button>
          <button type="reset" class="clear-btn">Clear</button>
        </div>

        <div id="statusMsg" class="status"></div>
      </form>
    </div>

    <!-- Messages List -->
    <div class="messages-section">
      <div class="messages-header">
        <h2>
          All Messages
          <span class="message-count" id="messageCount">0</span>
        </h2>
      </div>
      <div class="messages-container" id="messagesContainer">
        <!-- Messages will be dynamically inserted here -->
        <div class="loading-state">
          <p>Loading messages...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modalTitle">Message</h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="modal-meta">
        <span class="modal-sender" id="modalSender"></span>
        <span class="modal-sport" id="modalSport"></span>
        <span class="modal-date" id="modalDate"></span>
      </div>
      <div class="modal-message" id="modalMessage"></div>
      <button class="reply-btn" id="replyBtn" onclick="replyToMessage()">Reply</button>
    </div>
  </div>
</div>

<style>
  /* Modal Styles */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }
  
  .modal-content {
    background: white;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }
  
  .modal-header {
    background: linear-gradient(135deg, #2b0c4d 0%, #2b0c4d 70%, #1f1722 100%);
    padding: 20px 25px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .modal-header h3 {
    margin: 0;
    font-size: 20px;
  }
  
  .modal-close {
    background: transparent;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    line-height: 1;
  }
  
  .modal-body {
    padding: 25px;
  }
  
  .modal-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  
  .modal-meta span {
    padding: 6px 12px;
    background: #f7fafc;
    border-radius: 20px;
    font-size: 13px;
    color: #4a5568;
  }
  
  .modal-sender {
    font-weight: 600;
    color: #2b0c4d !important;
  }
  
  .modal-message {
    font-size: 15px;
    line-height: 1.7;
    color: #2d3748;
    white-space: pre-wrap;
    margin-bottom: 20px;
  }
  
  .reply-btn {
    background: linear-gradient(135deg, #6b3fa0 0%, #8e5fb8 100%);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .reply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(107, 63, 160, 0.3);
  }
  
  /* Unread message styling */
  .message-item.unread {
    background: #f0ebf7;
    border-left: 3px solid #6b3fa0;
  }
  
  .message-item.unread .message-sender {
    font-weight: 700;
  }
  
  /* Clickable message */
  .message-item {
    cursor: pointer;
  }
</style>

<script>
  let recipientsData = [];
  let messagesData = [];
  let currentMessageSender = null;

  // Load recipients on page load
  async function loadRecipients() {
    try {
      const response = await fetch('/uoc-sports/public/api/captain/message/recipients');
      const result = await response.json();
      
      const select = document.getElementById('recipient');
      select.innerHTML = '<option value="" disabled selected>-- Select Recipient --</option>';
      
      if (result.status === 'success' && result.data.length > 0) {
        recipientsData = result.data;
        result.data.forEach(recipient => {
          const option = document.createElement('option');
          option.value = JSON.stringify({id: recipient.user_id, type: recipient.type});
          option.textContent = recipient.label;
          select.appendChild(option);
        });
      } else if (result.status === 'empty') {
        select.innerHTML = '<option value="" disabled selected>-- No Recipients Available --</option>';
        showStatus(result.message, 'error');
      } else {
        select.innerHTML = '<option value="" disabled selected>-- Error Loading --</option>';
      }
    } catch (error) {
      console.error('Error loading recipients:', error);
      const select = document.getElementById('recipient');
      select.innerHTML = '<option value="" disabled selected>-- Error Loading --</option>';
    }
  }

  // Load messages from API
  // Load messages (both inbox and sent)
  async function loadMessages() {
    try {
      const [inboxRes, sentRes] = await Promise.all([
        fetch('/uoc-sports/public/api/message/inbox'),
        fetch('/uoc-sports/public/api/message/list')
      ]);

      const inboxResult = await inboxRes.json();
      const sentResult = await sentRes.json();
      
      let allMessages = [];

      if (inboxResult.status === 'success') {
        allMessages = allMessages.concat(inboxResult.data.map(m => ({...m, type: 'received'})));
      }

      if (sentResult.status === 'success') {
        allMessages = allMessages.concat(sentResult.data.map(m => ({
          ...m, 
          type: 'sent',
          sport: 'Sent' 
        })));
      }

      // Sort by timestamp descending
      allMessages.sort((a, b) => {
        return new Date(b.timestamp) - new Date(a.timestamp);
      });

      messagesData = allMessages;
      renderMessages();

    } catch (error) {
      console.error('Error loading messages:', error);
      messagesData = [];
      renderMessages();
    }
  }

  // Group messages by title
  function groupMessagesByTitle() {
    const grouped = {};
    messagesData.forEach(msg => {
      const title = msg.title || 'General';
      if (!grouped[title]) {
        grouped[title] = [];
      }
      grouped[title].push(msg);
    });
    return grouped;
  }

  // Render messages
  function renderMessages() {
    const container = document.getElementById('messagesContainer');
    container.innerHTML = '';

    if (messagesData.length === 0) {
      const empty = document.createElement('div');
      empty.className = 'empty-state';
      empty.innerHTML = `
        <div class="empty-state-icon">📭</div>
        <h3>No Messages</h3>
        <p>You haven't sent or received any messages yet</p>
      `;
      container.appendChild(empty);
    } else {
      const grouped = groupMessagesByTitle();
      const titles = Object.keys(grouped).sort();

      titles.forEach(title => {
        const titleGroup = document.createElement('div');
        titleGroup.className = 'title-group';

        const titleHeader = document.createElement('div');
        titleHeader.className = 'title-group-header';
        titleHeader.textContent = title;

        titleGroup.appendChild(titleHeader);

        grouped[title].forEach(msg => {
          const item = document.createElement('div');
          item.className = 'message-item';
          item.onclick = () => openMessage(msg);
          
          let senderDisplay = '';
          let actions = '';
          
          if (msg.type === 'sent') {
            senderDisplay = `<span style="color: #6b3fa0;">↗ To: ${msg.sender} - ${msg.recipient_name}</span>`;
            actions = `<button class="message-delete" title="Delete" onclick="event.stopPropagation(); deleteMessage('${msg.id}')">×</button>`;
          } else {
            senderDisplay = `<span style="color: #2d3748;">↙ From: ${msg.sender_role} - ${msg.sender}</span>`;
            // No delete action for received messages
          }

          item.innerHTML = `
            <div class="message-sender">${senderDisplay}</div>
            <div class="message-text">${msg.text}</div>
            <div class="message-date">${msg.date}</div>
            ${actions}
          `;
          titleGroup.appendChild(item);
        });

        container.appendChild(titleGroup);
      });
    }

    document.getElementById('messageCount').textContent = messagesData.length;
  }

  // Open message modal
  async function openMessage(msg) {
    currentMessageSender = msg;
    document.getElementById('modalTitle').textContent = msg.title;
    
    if (msg.type === 'sent') {
       document.getElementById('modalSender').innerHTML = '↗ To: ' + (msg.sender && msg.recipient_name ? msg.sender + ' - ' + msg.recipient_name : (msg.recipient_name || 'Recipient'));
       document.getElementById('modalSport').style.display = 'none'; // Hide sport for sent items? Or show my sport
       document.getElementById('replyBtn').style.display = 'none'; // Can't reply to sent message (unless follow up)
    } else {
       document.getElementById('modalSender').textContent = '👤 ' + (msg.sender_role ? msg.sender_role + ' - ' : '') + msg.sender;
       document.getElementById('modalSport').textContent = '🏅 ' + (msg.sport || 'General'); // Sport might not be in captain inbox msg
       document.getElementById('modalSport').style.display = 'block';
       document.getElementById('replyBtn').style.display = 'inline-block';
    }
    
    document.getElementById('modalDate').textContent = '📅 ' + msg.date;
    document.getElementById('modalMessage').textContent = msg.full_message;
    document.getElementById('messageModal').style.display = 'flex';
    
    // Mark as read if it's a received message
    if (msg.type === 'received' && !msg.is_read) {
      try {
        const formData = new FormData();
        formData.append('message_id', msg.id);
        
        await fetch('/uoc-sports/public/api/message/mark-read', {
          method: 'POST',
          body: formData
        });
        
        msg.is_read = true;
        // renderMessages(); // creating a loop or jump? No need to re-render immediately just for read status in list if list doesn't show read status strongly
      } catch (error) {
        console.error('Error marking message as read:', error);
      }
    }
  }

  // Reply to message - pre-fill form
  function replyToMessage() {
    if (currentMessageSender) {
      // Set title as "Re: original title"
      document.getElementById('title').value = 'Re: ' + currentMessageSender.title;
      
      // Try to select the sender in the dropdown
      const select = document.getElementById('recipient');
      // Logic to find recipient by ID or Role
      // Captains send to "COACH" or "MANAGER"
      
      // If received from Coach, set recipient to COACH
      // If received from Manager, set recipient to MANAGER
      let targetRole = '';
      if (currentMessageSender.sender_role === 'Coach' || currentMessageSender.sender === 'Coach') targetRole = 'COACH';
      if (currentMessageSender.sender_role === 'Sports Manager' || currentMessageSender.sender === 'Sports Manager') targetRole = 'SPT';
      if (currentMessageSender.sender_role === 'Admin' || currentMessageSender.sender === 'Admin') targetRole = 'ADMIN';
      
      // Better: use sender_id if available? 
      // API for captain recipients returns list of coach/manager.
      
      for (let i = 0; i < select.options.length; i++) {
        try {
          const optVal = JSON.parse(select.options[i].value);
          // Match by ID if we have it (we added sender_id to message model!)
          if (currentMessageSender.sender_id && optVal.id === currentMessageSender.sender_id) {
             select.selectedIndex = i;
             break;
          }
          // Fallback to role matching
          if (targetRole && optVal.type === targetRole) {
            select.selectedIndex = i;
            break;
          }
        } catch(e) {}
      }
      
      document.getElementById('message').focus();
      closeModal();
      document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
    }
  }

  // Close modal
  function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
    currentMessageSender = null;
  }

  // Close modal on outside click
  document.getElementById('messageModal').addEventListener('click', (e) => {
    if (e.target.id === 'messageModal') {
      closeModal();
    }
  });

  // Delete message via API
  async function deleteMessage(id) {
    UI.confirm('Are you sure you want to delete this message?', async () => {

    try {
      const formData = new FormData();
      formData.append('message_id', id);

      const response = await fetch('/uoc-sports/public/api/message/delete', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.status === 'success') {
        // Remove from local data
        const index = messagesData.findIndex(msg => msg.id === id);
        if (index > -1) {
          messagesData.splice(index, 1);
          renderMessages();
        }
        showStatus('Message deleted successfully', 'success');
      } else {
        showStatus(result.message || 'Failed to delete message', 'error');
      }
    } catch (error) {
      console.error('Error deleting message:', error);
      showStatus('Error deleting message', 'error');
    }
  }, null, true); // Danger theme
}

  // Show status message -- DEPRECATED (use UI.showToast)
  function showStatus(message, type) {
    UI.showToast(message, type === 'error' ? 'error' : (type === 'success' ? 'success' : 'info'));
  }

  // Handle form submission
  document.getElementById('messageForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const recipientSelect = document.getElementById('recipient');
    const titleInput = document.getElementById('title');
    const messageInput = document.getElementById('message');

    if (!recipientSelect.value || !titleInput.value || !messageInput.value) {
      UI.showToast('Please fill all fields', 'warning');
      return;
    }

    try {
      const recipientData = JSON.parse(recipientSelect.value);
      
      const formData = new FormData();
      formData.append('recipient_id', recipientData.id);
      formData.append('recipient_type', recipientData.type);
      formData.append('title', titleInput.value);
      formData.append('message', messageInput.value);

      const response = await fetch('/uoc-sports/public/api/message/send', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.status === 'success') {
        UI.showToast('Message sent successfully!', 'success');
        e.target.reset();
        // Reload messages to show the new one
        await loadMessages();
      } else {
        UI.showToast(result.message || 'Failed to send message', 'error');
      }
    } catch (error) {
      console.error('Error sending message:', error);
      UI.showToast('Error sending message', 'error');
    }
  });

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    loadRecipients();
    loadMessages();
  });

  // Highlight active page
  const currentPage = document.getElementById('sub-messages');
  if (currentPage) currentPage.classList.add('active');
</script>
