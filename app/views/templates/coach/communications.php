<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Communicate with Captain and Sports Manager</h1>
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
            <option value="" disabled selected>-- Select Recipient --</option>
            <option value="Coach Kasun">Coach Kasun</option>
            <option value="Coach Ratnayaka">Coach Ratnayaka</option>
            <option value="Sports Manager">Sports Manager</option>
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
      </div>
    </div>
  </div>
</div>

<script>
  // Sample messages data
  const messagesData = [
    {
      id: 1,
      sender: 'Coach Kasun',
      title: 'Schedule Update',
      text: 'Check the schedule..',
      date: '27 Jul'
    },
    {
      id: 2,
      sender: 'Coach Ratnayaka',
      title: 'Cancellation',
      text: 'Cancel the schedule..',
      date: '27 Jul'
    },
    {
      id: 3,
      sender: 'Sports Manager',
      title: 'Equipment',
      text: 'Equipment ready for tomorrow\'s training',
      date: '26 Jul'
    }
  ];

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
            <button class="message-delete" title="Delete" onclick="deleteMessage(${msg.id})">×</button>
          `;
          titleGroup.appendChild(item);
        });

        container.appendChild(titleGroup);
      });
    }

    document.getElementById('messageCount').textContent = messagesData.length;
  }

  // Delete message
  function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
      const index = messagesData.findIndex(msg => msg.id === id);
      if (index > -1) {
        messagesData.splice(index, 1);
        renderMessages();
        showStatus('Message deleted successfully', 'success');
      }
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
  document.getElementById('messageForm').addEventListener('submit', (e) => {
    e.preventDefault();

    const recipient = document.getElementById('recipient').value;
    const title = document.getElementById('title').value;
    const message = document.getElementById('message').value;

    if (!recipient || !title || !message) {
      showStatus('Please fill all fields', 'error');
      return;
    }

    // Add new message
    const newMessage = {
      id: Date.now(),
      sender: recipient,
      title: title,
      text: message.substring(0, 50) + (message.length > 50 ? '...' : ''),
      date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    };

    messagesData.unshift(newMessage);
    renderMessages();
    e.target.reset();
    showStatus('Message sent successfully!', 'success');
  });

  // Initial render
  renderMessages();

  // Highlight active page
  const currentPage = document.getElementById('sub-messages');
  if (currentPage) currentPage.classList.add('active');
</script>
