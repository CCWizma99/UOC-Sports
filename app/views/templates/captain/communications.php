<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Communicate with Coach and Sports Manager</h1>
    <p>Send messages to your coach and sports manager, and view previous communications.</p>
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
          Previous Messages
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

<script>
  const API_BASE = '/uoc-sports/public/api/captain/message';
  let recipientsData = [];
  let messagesData = [];

  // Load recipients on page load
  async function loadRecipients() {
    try {
      const response = await fetch(`${API_BASE}/recipients`);
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
  async function loadMessages() {
    try {
      const response = await fetch(`${API_BASE}/list`);
      const result = await response.json();
      
      if (result.status === 'success') {
        messagesData = result.data;
        renderMessages();
      } else {
        console.error('Error loading messages:', result.message);
        messagesData = [];
        renderMessages();
      }
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
        <p>Send your first message to get started</p>
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
          item.innerHTML = `
            <div class="message-sender">${msg.sender}</div>
            <div class="message-text">${msg.text}</div>
            <div class="message-date">${msg.date}</div>
            <button class="message-delete" title="Delete" onclick="deleteMessage('${msg.id}')">×</button>
          `;
          titleGroup.appendChild(item);
        });

        container.appendChild(titleGroup);
      });
    }

    document.getElementById('messageCount').textContent = messagesData.length;
  }

  // Delete message via API
  async function deleteMessage(id) {
    if (!confirm('Are you sure you want to delete this message?')) {
      return;
    }

    try {
      const formData = new FormData();
      formData.append('message_id', id);

      const response = await fetch(`${API_BASE}/delete`, {
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
  }

  // Show status message
  function showStatus(message, type) {
    const statusEl = document.getElementById('statusMsg');
    statusEl.textContent = message;
    statusEl.className = `status ${type}`;
    
    setTimeout(() => {
      statusEl.className = 'status';
    }, 3000);
  }

  // Handle form submission
  document.getElementById('messageForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const recipientSelect = document.getElementById('recipient');
    const titleInput = document.getElementById('title');
    const messageInput = document.getElementById('message');

    if (!recipientSelect.value || !titleInput.value || !messageInput.value) {
      showStatus('Please fill all fields', 'error');
      return;
    }

    try {
      const recipientData = JSON.parse(recipientSelect.value);
      
      const formData = new FormData();
      formData.append('recipient_id', recipientData.id);
      formData.append('recipient_type', recipientData.type);
      formData.append('title', titleInput.value);
      formData.append('message', messageInput.value);

      const response = await fetch(`${API_BASE}/send`, {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.status === 'success') {
        showStatus('Message sent successfully!', 'success');
        e.target.reset();
        // Reload messages to show the new one
        await loadMessages();
      } else {
        showStatus(result.message || 'Failed to send message', 'error');
      }
    } catch (error) {
      console.error('Error sending message:', error);
      showStatus('Error sending message', 'error');
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
