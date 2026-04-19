<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Communicate | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/sports-manager/messages.css");
    
    .page-container {
        padding: 20px 40px;
        min-height: calc(100vh - 160px);
    }

    .container-header {
        margin-bottom: 30px;
    }

    .container-header h2 {
        color: #2b0c4d;
        font-size: 28px;
        margin-bottom: 8px;
    }

    .container-header p {
        color: #666;
    }

    /* Standardized Communication UI Styles */
    .content-wrapper {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 30px;
    }

    .form-section {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        height: fit-content;
    }

    .form-section h2 {
        font-size: 20px;
        color: #2b0c4d;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
    }

    .form-group select,
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: inherit;
    }

    .form-group textarea {
        height: 120px;
        resize: vertical;
    }

    .send-btn {
        background: linear-gradient(135deg, #6b3fa0 0%, #8e5fb8 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }

    .send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(107, 63, 160, 0.4);
    }

    .messages-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages-header {
        padding: 20px 25px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .messages-header h2 {
        font-size: 18px;
        color: #2b0c4d;
        margin: 0;
    }

    .message-count {
        background: #6b3fa0;
        color: white;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-left: 8px;
    }

    .messages-container {
        padding: 15px;
        min-height: 400px;
    }

    .title-group {
        margin-bottom: 25px;
    }

    .title-group-header {
        font-weight: 700;
        color: #6b3fa0;
        padding: 0 10px 10px;
        border-bottom: 1px dashed #ddd;
        margin-bottom: 10px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .message-item {
        padding: 15px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #f0f0f0;
        margin-bottom: 10px;
        cursor: pointer;
        transition: 0.2s;
        position: relative;
    }

    .message-item:hover {
        background: #f9f6ff;
        border-color: #dcd0ff;
    }

    .message-item.unread {
        background: #f0ebf7;
        border-left: 4px solid #6b3fa0;
    }

    .message-sender {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .message-text {
        font-size: 13px;
        color: #666;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .message-date {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 11px;
        color: #999;
    }

    .status {
        margin-top: 10px;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        display: none;
    }

    .status.success { display: block; background: #d1fae5; color: #065f46; }
    .status.error { display: block; background: #fee2e2; color: #991b1b; }

    /* Modal */
    .modal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 600px;
        overflow: hidden;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        background: #2b0c4d;
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body { padding: 25px; }

    .modal-meta {
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
        display: flex;
        gap: 15px;
    }

    .modal-message {
        font-size: 15px;
        line-height: 1.6;
        color: #333;
        white-space: pre-wrap;
        margin-bottom: 25px;
    }

    .reply-btn {
        background: #6b3fa0;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .loading-state {
        text-align: center;
        padding: 50px;
        color: #999;
    }

    .empty-state {
        text-align: center;
        padding: 50px;
        color: #666;
    }
  </style>
</head>
<body>

<?php require "../app/views/templates/general/header.php"; ?>

<div class="page-container">
    <div class="container-header">
        <h2>Messages & Communication</h2>
        <p>Coordinate with your sport's Coach, Captain, and University Admins.</p>
    </div>

    <div class="content-wrapper">
        <!-- Send Message Form -->
        <div class="form-section">
            <h2>Compose Message</h2>
            <form id="messageForm">
                <div class="form-group">
                    <label for="recipient">To</label>
                    <select id="recipient" name="recipient" required>
                        <option value="" disabled selected>Loading contacts...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">Subject</label>
                    <input type="text" id="title" name="title" placeholder="What is this about?" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" class="send-btn">Send Message</button>
                <div id="statusMsg" class="status"></div>
            </form>
        </div>

        <!-- Inbox Section -->
        <div class="messages-section">
            <div class="messages-header">
                <h2>Message History <span class="message-count" id="messageCount">0</span></h2>
            </div>
            <div class="messages-container" id="messagesContainer">
                <div class="loading-state">
                    <p><i class="fas fa-spinner fa-spin"></i> Fetching messages...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Message Title</h3>
            <button onclick="closeModal()" style="background:none; border:none; color:white; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-meta">
                <span id="modalSender"></span>
                <span id="modalDate"></span>
            </div>
            <div class="modal-message" id="modalMessage"></div>
            <button class="reply-btn" onclick="replyToMessage()">Reply</button>
        </div>
    </div>
</div>

<?php require "../app/views/templates/general/footer.php"; ?>

<script>
    let messagesData = [];
    let currentMessage = null;

    async function loadRecipients() {
        try {
            const res = await fetch('/uoc-sports/public/api/manager/message/recipients');
            const result = await res.json();
            const select = document.getElementById('recipient');
            
            if (result.status === 'success') {
                select.innerHTML = '<option value="" disabled selected>-- Select Recipient --</option>';
                result.data.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = JSON.stringify({id: r.user_id, type: r.type});
                    opt.textContent = r.label;
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="" disabled selected>Error loading contacts</option>';
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function loadMessages() {
        try {
            const [inboxRes, sentRes] = await Promise.all([
                fetch('/uoc-sports/public/api/message/inbox'),
                fetch('/uoc-sports/public/api/message/list')
            ]);

            const inbox = await inboxRes.json();
            const sent = await sentRes.json();
            
            let all = [];
            if (inbox.status === 'success') all = all.concat(inbox.data.map(m => ({...m, flow: 'received'})));
            if (sent.status === 'success') all = all.concat(sent.data.map(m => ({...m, flow: 'sent', is_read: true})));

            all.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
            messagesData = all;
            renderMessages();
        } catch (e) {
            console.error(e);
        }
    }

    function renderMessages() {
        const container = document.getElementById('messagesContainer');
        container.innerHTML = '';
        
        if (messagesData.length === 0) {
            container.innerHTML = '<div class="empty-state"><p>No messages yet.</p></div>';
            return;
        }

        const grouped = {};
        messagesData.forEach(m => {
            const t = m.title || 'No Subject';
            if (!grouped[t]) grouped[t] = [];
            grouped[t].push(m);
        });

        Object.keys(grouped).forEach(title => {
            const group = document.createElement('div');
            group.className = 'title-group';
            group.innerHTML = `<div class="title-group-header">${title}</div>`;

            grouped[title].forEach(msg => {
                const item = document.createElement('div');
                item.className = 'message-item' + (msg.is_read ? '' : ' unread');
                item.onclick = () => openMessage(msg);
                
                const senderLabel = msg.flow === 'sent' ? `To: ${msg.recipient_name}` : `From: ${msg.sender} (${msg.sender_role})`;
                
                item.innerHTML = `
                    <div class="message-sender">${senderLabel}</div>
                    <div class="message-text">${msg.text}</div>
                    <span class="message-date">${msg.date}</span>
                `;
                group.appendChild(item);
            });
            container.appendChild(group);
        });

        document.getElementById('messageCount').textContent = messagesData.length;
    }

    function openMessage(msg) {
        currentMessage = msg;
        document.getElementById('modalTitle').textContent = msg.title;
        document.getElementById('modalSender').textContent = msg.flow === 'sent' ? `To: ${msg.recipient_name}` : `From: ${msg.sender}`;
        document.getElementById('modalDate').textContent = msg.date;
        document.getElementById('modalMessage').textContent = msg.full_message;
        document.getElementById('messageModal').style.display = 'flex';

        if (!msg.is_read) {
            const fd = new FormData();
            fd.append('message_id', msg.id);
            fetch('/uoc-sports/public/api/message/mark-read', { method: 'POST', body: fd });
            msg.is_read = true;
            renderMessages();
        }
    }

    function closeModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    function replyToMessage() {
        if (!currentMessage) return;
        document.getElementById('title').value = 'Re: ' + currentMessage.title;
        
        const select = document.getElementById('recipient');
        const targetId = currentMessage.sender_id;
        
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
    }

    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const status = document.getElementById('statusMsg');
        
        try {
            const recipientData = JSON.parse(document.getElementById('recipient').value);
            const formData = new FormData();
            formData.append('recipient_id', recipientData.id);
            formData.append('recipient_type', recipientData.type);
            formData.append('title', document.getElementById('title').value);
            formData.append('message', document.getElementById('message').value);

            const res = await fetch('/uoc-sports/public/api/message/send', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();

            if (result.status === 'success') {
                status.textContent = 'Message sent!';
                status.className = 'status success';
                e.target.reset();
                loadMessages();
            } else {
                status.textContent = result.message || 'Error sending';
                status.className = 'status error';
            }
        } catch (err) {
            status.textContent = 'Error sending message';
            status.className = 'status error';
        }

        setTimeout(() => status.style.display = 'none', 3000);
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadRecipients();
        loadMessages();
    });
</script>

</body>
</html>