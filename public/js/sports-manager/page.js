function toggleAddParticipantsForm() {
    const form = document.getElementById('addParticipantsForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Scroll to the form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
    }
}

// Toggle message form
function toggleMessageForm() {
    const form = document.getElementById('messageFormSection');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Scroll to the form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
    }
}

// Chat data from backend
let chatsData = [];

// Initialize chats from backend
function initializeChatsFromBackend() {
    if (typeof backendConversations !== 'undefined' && backendConversations.length > 0) {
        chatsData = backendConversations;
    }
}

// Get initials from name
function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

// Format timestamp
function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor(diff / (1000 * 60));
    
    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes} min ago`;
    if (hours < 24) return `${hours} hours ago`;
    if (days === 1) return 'yesterday';
    if (days < 7) return `${days} days ago`;
    
    return date.toLocaleDateString();
}

// Render chat card
function createChatCard(chat) {
  const div = document.createElement('div');
  div.className = 'chat-card';
  div.dataset.chatId = chat.partner_id;

  const header = document.createElement('div');
  header.className = 'chat-header';

  const headerInfo = document.createElement('div');
  headerInfo.className = 'chat-header-info';

  const avatar = document.createElement('div');
  avatar.className = 'chat-avatar';
  avatar.textContent = getInitials(chat.partner_name);

  const userInfo = document.createElement('div');
  userInfo.className = 'chat-user-info';

  const username = document.createElement('div');
  username.className = 'chat-user';
  username.textContent = chat.partner_name;

  const chatRole = document.createElement('div');
  chatRole.className = 'chat-role';
  chatRole.textContent = chat.partner_role;
  chatRole.style.fontSize = '0.85rem';
  chatRole.style.color = '#6b7280';

  userInfo.appendChild(username);
  userInfo.appendChild(chatRole);
  headerInfo.appendChild(avatar);
  headerInfo.appendChild(userInfo);

  const timestamp = document.createElement('div');
  timestamp.className = 'chat-timestamp';
  timestamp.textContent = formatTimestamp(chat.last_message_time);

  const toggleIcon = document.createElement('div');
  toggleIcon.className = 'toggle-icon';
  toggleIcon.innerHTML = '▼';

  header.appendChild(headerInfo);
  header.appendChild(timestamp);
  header.appendChild(toggleIcon);

  const preview = document.createElement('div');
  preview.className = 'chat-preview';
  preview.textContent = chat.last_message || 'No messages yet';

  const messagesContainer = document.createElement('div');
  messagesContainer.className = 'chat-messages';
  messagesContainer.innerHTML = '<div style="padding: 1rem; text-align: center; color: #6b7280;">Click to view conversation</div>';

  div.appendChild(header);
  div.appendChild(preview);
  div.appendChild(messagesContainer);

  // Toggle chat expansion and load messages
  header.addEventListener('click', () => {
    if (!div.classList.contains('active')) {
        loadConversation(chat.partner_id, messagesContainer);
    }
    div.classList.toggle('active');
  });

  return div;
}

// Load conversation messages
function loadConversation(partnerId, container) {
    container.innerHTML = '<div style="padding: 1rem; text-align: center;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    fetch(`/uoc-sports/public/sport-manager/messages/conversation?partner_id=${partnerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages) {
                container.innerHTML = '';
                
                if (data.messages.length === 0) {
                    container.innerHTML = '<div style="padding: 1rem; text-align: center; color: #6b7280;">No messages yet</div>';
                } else {
                    const currentUserId = parseInt(document.body.dataset.userId) || 0;
                    data.messages.forEach(msg => {
                        const msgDiv = document.createElement('div');
                        msgDiv.className = `message ${parseInt(msg.sender_id) === currentUserId ? 'sent' : 'received'}`;
                        
                        const msgHeader = document.createElement('div');
                        msgHeader.style.fontSize = '0.75rem';
                        msgHeader.style.color = '#6b7280';
                        msgHeader.style.marginBottom = '0.25rem';
                        msgHeader.textContent = `${msg.sender_name} - ${new Date(msg.sent_at).toLocaleString()}`;
                        
                        const msgTitle = document.createElement('div');
                        msgTitle.style.fontWeight = '600';
                        msgTitle.style.marginBottom = '0.25rem';
                        msgTitle.textContent = msg.title;
                        
                        const msgText = document.createElement('div');
                        msgText.textContent = msg.message;
                        
                        msgDiv.appendChild(msgHeader);
                        msgDiv.appendChild(msgTitle);
                        msgDiv.appendChild(msgText);
                        container.appendChild(msgDiv);
                    });
                }
            } else {
                container.innerHTML = '<div style="padding: 1rem; text-align: center; color: #dc2626;">Failed to load messages</div>';
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
            container.innerHTML = '<div style="padding: 1rem; text-align: center; color: #dc2626;">Error loading messages</div>';
        });
}

// Render all chats
function renderChats() {
  const container = document.getElementById('messagesContainer');
  if (!container) return;

  container.innerHTML = '';

  if (chatsData.length === 0) {
    const empty = document.createElement('div');
    empty.className = 'empty-state';
    empty.innerHTML = `
      <div class="empty-state-icon">💬</div>
      <h3>No Conversations</h3>
      <p>Start a new conversation by sending a message</p>
    `;
    container.appendChild(empty);
  } else {
    chatsData.forEach(chat => {
      container.appendChild(createChatCard(chat));
    });
  }

  // Update message count
  const messageCount = document.getElementById('messageCount');
  if (messageCount) {
    messageCount.textContent = chatsData.length;
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeChatsFromBackend();
  renderChats();

  // Highlight active page
  const currentPage = document.getElementById("sub-messages");
  if (currentPage) currentPage.classList.add("active");
});
