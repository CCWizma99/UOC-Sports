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

// Sample chat data
const chatsData = [
  {
    id: 1,
    user: 'K S Silva',
    role: 'Equipment Manager',
    title: 'Equipment Ready',
    preview: 'Yes, I\'ll be there by 10!',
    timestamp: '2 hours ago',
    messages: [
      { type: 'sent', text: 'Hey Saman!' },
      { type: 'received', text: 'Hey, are you coming tomorrow?' },
      { type: 'sent', text: 'Yes, I\'ll be there by 10!' }
    ]
  },
  {
    id: 2,
    user: 'N S Perera',
    role: 'Coach',
    title: 'Practice Session Time',
    preview: 'Hey Nimal, What is the time of the new practice session?',
    timestamp: '5 hours ago',
    messages: [
      { type: 'received', text: 'Hi! I scheduled a new practice session.' },
      { type: 'sent', text: 'Hey Nimal, What is the time of the new practice session?' }
    ]
  },
  {
    id: 3,
    user: 'Nadith Nemal',
    role: 'Captain',
    title: 'Team Meeting',
    preview: 'Don\'t forget about tomorrow\'s meeting',
    timestamp: '1 day ago',
    messages: [
      { type: 'received', text: 'Team meeting scheduled for tomorrow at 3 PM' },
      { type: 'sent', text: 'Got it, I\'ll inform the others' }
    ]
  }
];

// Get initials from name
function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

// Render chat card
function createChatCard(chat) {
  const div = document.createElement('div');
  div.className = 'chat-card';
  div.dataset.chatId = chat.id;

  const header = document.createElement('div');
  header.className = 'chat-header';

  const headerInfo = document.createElement('div');
  headerInfo.className = 'chat-header-info';

  const avatar = document.createElement('div');
  avatar.className = 'chat-avatar';
  avatar.textContent = getInitials(chat.user);

  const userInfo = document.createElement('div');
  userInfo.className = 'chat-user-info';

  const username = document.createElement('div');
  username.className = 'chat-user';
  username.textContent = chat.user;

  const chatTitle = document.createElement('div');
  chatTitle.className = 'chat-title';
  chatTitle.textContent = chat.title;

  userInfo.appendChild(username);
  userInfo.appendChild(chatTitle);
  headerInfo.appendChild(avatar);
  headerInfo.appendChild(userInfo);

  const timestamp = document.createElement('div');
  timestamp.className = 'chat-timestamp';
  timestamp.textContent = chat.timestamp;

  const toggleIcon = document.createElement('div');
  toggleIcon.className = 'toggle-icon';
  toggleIcon.innerHTML = '▼';

  header.appendChild(headerInfo);
  header.appendChild(timestamp);
  header.appendChild(toggleIcon);

  const preview = document.createElement('div');
  preview.className = 'chat-preview';
  preview.textContent = chat.preview;

  const messagesContainer = document.createElement('div');
  messagesContainer.className = 'chat-messages';

  if (chat.messages.length === 0) {
    const noMsg = document.createElement('div');
    noMsg.className = 'no-messages';
    noMsg.textContent = 'No messages yet';
    messagesContainer.appendChild(noMsg);
  } else {
    chat.messages.forEach(msg => {
      const msgDiv = document.createElement('div');
      msgDiv.className = `message ${msg.type}`;
      msgDiv.textContent = msg.text;
      messagesContainer.appendChild(msgDiv);
    });
  }

  div.appendChild(header);
  div.appendChild(preview);
  div.appendChild(messagesContainer);

  // Toggle chat expansion
  header.addEventListener('click', () => {
    div.classList.toggle('active');
  });

  return div;
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

// Initialize message form handler
function initializeMessageForm() {
  const messageForm = document.getElementById('messageForm');
  if (!messageForm) return;

  messageForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const to = formData.get('To');
    const title = formData.get('Title');
    const message = formData.get('Message');

    // Find or create chat
    const recipientMap = {
      'captain_1': { user: 'Nadith Nemal', role: 'Captain' },
      'coach_1': { user: 'N S Perera', role: 'Coach' },
      'equipment_1': { user: 'K S Silva', role: 'Equipment Manager' }
    };

    const recipient = recipientMap[to];
    let chatExists = chatsData.find(c => c.user === recipient.user);

    if (!chatExists) {
      chatExists = {
        id: chatsData.length + 1,
        user: recipient.user,
        role: recipient.role,
        title: title,
        preview: message.substring(0, 50) + (message.length > 50 ? '...' : ''),
        timestamp: 'just now',
        messages: []
      };
      chatsData.unshift(chatExists);
    }

    // Add message to chat
    chatExists.messages.push({ type: 'sent', text: message });
    chatExists.preview = message.substring(0, 50) + (message.length > 50 ? '...' : '');
    chatExists.timestamp = 'just now';
    chatExists.title = title;

    // Move chat to top
    chatsData.splice(chatsData.indexOf(chatExists), 1);
    chatsData.unshift(chatExists);

    renderChats();
    e.target.reset();

    // Hide the form after sending
    toggleMessageForm();

    // Show success feedback
    alert('Message sent successfully!');
  });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  renderChats();
  initializeMessageForm();

  // Highlight active page
  const currentPage = document.getElementById("sub-messages");
  if (currentPage) currentPage.classList.add("active");
});
