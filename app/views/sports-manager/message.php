<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Communicate | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/captain/communication.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    
    /* Modal Styles */
    .modal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
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
        animation: modalFadeIn 0.3s ease;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-header {
        background: linear-gradient(135deg, #2b0c4d 0%, #2b0c4d 70%, #1f1722 100%);
        padding: 20px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-body { padding: 25px; }
    
    .modal-meta { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .modal-meta span { padding: 6px 12px; background: #f7fafc; border-radius: 20px; font-size: 13px; color: #4a5568; }
    .modal-sender { font-weight: 600; color: #2b0c4d !important; }
    
    .modal-message { font-size: 15px; line-height: 1.7; color: #2d3748; white-space: pre-wrap; margin-bottom: 20px; }
    
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

    /* Message Item States */
    .message-item.unread {
        background: #f0ebf7;
        border-left: 3px solid #6b3fa0;
    }
    .message-item.unread .message-sender { font-weight: 700; }
  </style>
</head>
<body>

<?php require "../app/views/templates/general/header.php"; ?>

<div class="container">
  <!-- Header -->
  <div class="page-header">
    <h1>Communicate</h1>
    <p>Send and view messages with Coaches, Captains, and Admins.</p>
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
        <div class="loading-state" style="text-align: center; padding: 40px; color: #718096;">
           <p><i class="fas fa-spinner fa-spin"></i> Loading messages...</p>
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

<?php require "../app/views/templates/general/footer.php"; ?>

<script>
  let recipientsData = [];
  let messagesData = [];
  let currentMessageSender = null;

  // Load recipients on page load - Sport Manager Specific Endpoint
  async function loadRecipients() {
    try {
      const response = await fetch('/uoc-sports/public/api/manager/message/recipients');
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
      } else {
        select.innerHTML = '<option value="" disabled selected>-- No Recipients Available --</option>';
      }
    } catch (error) {
      console.error('Error loading recipients:', error);
      const select = document.getElementById('recipient');
      select.innerHTML = '<option value="" disabled selected>-- Error Loading --</option>';
    }
  }

  // Load messages
  async function loadMessages() {
    try {
      const [inboxRes, sentRes] = await Promise.all([
        fetch('/uoc-sports/public/api/message/inbox'),
        fetch('/uoc-sports/public/api/message/list')
      ]);

      const inboxResult = await inboxRes.json();
      const sentResult = await sentRes.json();
      
      let allMessages = [];
      if (inboxResult.status === 'success') allMessages = allMessages.concat(inboxResult.data.map(m => ({...m, type: 'received'})));
      if (sentResult.status === 'success') allMessages = allMessages.concat(sentResult.data.map(m => ({...m, type: 'sent'})));

      allMessages.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
      messagesData = allMessages;
      renderMessages();

    } catch (error) {
      console.error('Error loading messages:', error);
      renderMessages();
    }
  }

  // Render messages in standardized list
  function renderMessages() {
    const container = document.getElementById('messagesContainer');
    container.innerHTML = '';

    if (messagesData.length === 0) {
      container.innerHTML = '<div class="empty-state"><h3>No Messages</h3><p>Start a conversation today.</p></div>';
    } else {
      const grouped = {};
      messagesData.forEach(msg => {
        const title = msg.title || 'General';
        if (!grouped[title]) grouped[title] = [];
        grouped[title].push(msg);
      });

      Object.keys(grouped).sort().forEach(title => {
        const group = document.createElement('div');
        group.className = 'title-group';
        group.innerHTML = `<div class="title-group-header">${title}</div>`;

        grouped[title].forEach(msg => {
          const item = document.createElement('div');
          item.className = 'message-item' + (msg.is_read ? '' : ' unread');
          item.onclick = () => openMessage(msg);
          
          let senderDisplay = msg.type === 'sent' ? 
            `<span style="color: #6b3fa0;">↗ To: ${msg.recipient_name}</span>` : 
            `<span style="color: #2d3748;">↙ From: ${msg.sender} (${msg.sender_role})</span>`;

          const actions = msg.type === 'sent' ? `<button class="message-delete" title="Delete" onclick="event.stopPropagation(); deleteMessage('${msg.id}')">×</button>` : '';

          item.innerHTML = `
            <div class="message-sender">${senderDisplay}</div>
            <div class="message-text">${msg.text}</div>
            <div class="message-date">${msg.date}</div>
            ${actions}
          `;
          group.appendChild(item);
        });
        container.appendChild(group);
      });
    }
    document.getElementById('messageCount').textContent = messagesData.length;
  }

  function openMessage(msg) {
    currentMessageSender = msg;
    document.getElementById('modalTitle').textContent = msg.title;
    document.getElementById('modalSender').textContent = msg.type === 'sent' ? `To: ${msg.recipient_name}` : `From: ${msg.sender}`;
    document.getElementById('modalSport').textContent = msg.sport ? `🏅 ${msg.sport}` : '';
    document.getElementById('modalDate').textContent = `📅 ${msg.date}`;
    document.getElementById('modalMessage').textContent = msg.full_message;
    document.getElementById('messageModal').style.display = 'flex';
    document.getElementById('replyBtn').style.display = msg.type === 'sent' ? 'none' : 'inline-block';
    
    if (msg.type === 'received' && !msg.is_read) {
      const fd = new FormData();
      fd.append('message_id', msg.id);
      fetch('/uoc-sports/public/api/message/mark-read', { method: 'POST', body: fd });
      msg.is_read = true;
      // No immediate re-render needed for UX snappiness unless we have unread indicators
    }
  }

  function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
    currentMessageSender = null;
  }

  function replyToMessage() {
    if (!currentMessageSender) return;
    document.getElementById('title').value = 'Re: ' + currentMessageSender.title;
    
    const select = document.getElementById('recipient');
    const targetId = currentMessageSender.sender_id;
    
    for (let i = 0; i < select.options.length; i++) {
        try {
            const val = JSON.parse(select.options[i].value);
            if (val.id === targetId) {
                select.selectedIndex = i;
                break;
            }
        } catch(e) {}
    }
    
    closeModal();
    document.getElementById('message').focus();
    document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
  }

  async function deleteMessage(id) {
    if (!confirm('Are you sure you want to delete this message?')) return;
    try {
      const fd = new FormData();
      fd.append('message_id', id);
      const res = await fetch('/uoc-sports/public/api/message/delete', { method: 'POST', body: fd });
      const result = await res.json();
      if (result.status === 'success') {
        messagesData = messagesData.filter(m => m.id !== id);
        renderMessages();
        showStatus('Message deleted', 'success');
      }
    } catch (e) { showStatus('Error deleting', 'error'); }
  }

  function showStatus(message, type) {
    const statusEl = document.getElementById('statusMsg');
    statusEl.textContent = message;
    statusEl.className = `status ${type}`;
    setTimeout(() => { statusEl.className = 'status'; }, 3000);
  }

  document.getElementById('messageForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
      const recipientData = JSON.parse(document.getElementById('recipient').value);
      const fd = new FormData();
      fd.append('recipient_id', recipientData.id);
      fd.append('recipient_type', recipientData.type);
      fd.append('title', document.getElementById('title').value);
      fd.append('message', document.getElementById('message').value);

      const res = await fetch('/uoc-sports/public/api/message/send', { method: 'POST', body: fd });
      const result = await res.json();
      
      if (result.status === 'success') {
        showStatus('Message sent!', 'success');
        e.target.reset();
        await loadMessages();
      } else {
        showStatus(result.message || 'Failed to send', 'error');
      }
    } catch (error) { showStatus('Error sending', 'error'); }
  });

  document.addEventListener('DOMContentLoaded', () => {
    loadRecipients();
    loadMessages();
  });
</script>

</body>
</html>