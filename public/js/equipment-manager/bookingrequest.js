function updateStatus(requestId, newStatus, dropdownElement) {
    console.log('updateStatus called with:', requestId, newStatus);
    
    const originalStatus = dropdownElement.getAttribute('data-original-status');
    
    if (!confirm('Are you sure you want to update the status to ' + newStatus + '?')) {
        console.log('User cancelled status update');
        // Reset dropdown to original value
        dropdownElement.value = originalStatus;
        return;
    }
    
    console.log('Sending request to:', '/uoc-sports/public/equipment-manager/update-booking-status');
    
    fetch('/uoc-sports/public/equipment-manager/update-booking-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            request_id: requestId, 
            status: newStatus 
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Status updated successfully!');
            // Update the original status data attribute
            dropdownElement.setAttribute('data-original-status', newStatus);
            // Update dropdown color class
            dropdownElement.className = 'status-dropdown status-' + newStatus.toLowerCase();
        } else {
            alert('Error: ' + data.message);
            // Reset dropdown to original value
            dropdownElement.value = originalStatus;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error updating status: ' + error.message);
        // Reset dropdown to original value
        dropdownElement.value = originalStatus;
    });
}

function editRequest(requestId) {
    window.location.href = '/uoc-sports/public/equipment-manager/edit-booking-request?id=' + requestId;
}

function openNotificationModal(requestId, studentId, requesterName) {
    document.getElementById('notificationRequestId').value = requestId || '';
    document.getElementById('notificationStudentId').value = studentId || '';
    document.getElementById('notificationRequesterName').value = requesterName || '';
    document.getElementById('notificationMessage').value = '';
    loadNotificationHistory();
    document.getElementById('notificationModal').style.display = 'flex';
}

function closeNotificationModal() {
    document.getElementById('notificationModal').style.display = 'none';
}

function loadNotificationHistory() {
    const requestId = document.getElementById('notificationRequestId').value.trim();
    const studentId = document.getElementById('notificationStudentId').value.trim();
    const requesterName = document.getElementById('notificationRequesterName').value.trim();
    const historyEl = document.getElementById('notificationHistory');

    if (!requestId || !studentId || !requesterName) {
        historyEl.innerHTML = '<p style="margin:0; color:#6b7280; font-size:0.85rem;">No request details available.</p>';
        return;
    }

    historyEl.innerHTML = '<p style="margin:0; color:#6b7280; font-size:0.85rem;">Loading notifications...</p>';

    const url = new URL('/uoc-sports/public/equipment-manager/request-notifications', window.location.origin);
    url.searchParams.set('request_id', requestId);
    url.searchParams.set('student_id', studentId);
    url.searchParams.set('requester_name', requesterName);

    fetch(url.toString())
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                historyEl.innerHTML = '<p style="margin:0; color:#ef4444; font-size:0.85rem;">' + (data.message || 'Failed to fetch notifications') + '</p>';
                return;
            }

            if (!Array.isArray(data.notifications) || data.notifications.length === 0) {
                historyEl.innerHTML = '<p style="margin:0; color:#6b7280; font-size:0.85rem;">No notifications sent yet.</p>';
                return;
            }

            historyEl.innerHTML = data.notifications.map((item, index) => {
                const safeMsg = String(item.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                const safeId = String(item.notification_id || '').replace(/"/g, '&quot;');
                return '<div style="padding:0.45rem 0.5rem; border-bottom:' + (index === data.notifications.length - 1 ? 'none' : '1px solid #e5e7eb') + ';">'
                    + '<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.6rem;">'
                    + '<p style="margin:0; font-size:0.83rem; color:#1f2937; flex:1;">' + safeMsg + '</p>'
                    + '<button type="button" title="Delete notification" onclick="deleteNotificationHistoryItem(\'' + safeId + '\')" style="border:none; background:transparent; color:#dc2626; font-size:1rem; line-height:1; cursor:pointer; padding:0;">&times;</button>'
                    + '</div>'
                    + '</div>';
            }).join('');
        })
        .catch(error => {
            historyEl.innerHTML = '<p style="margin:0; color:#ef4444; font-size:0.85rem;">Error fetching notifications: ' + error.message + '</p>';
        });
}

function deleteNotificationHistoryItem(notificationId) {
    if (!notificationId) {
        return;
    }

    if (!confirm('Delete this notification?')) {
        return;
    }

    const requestId = document.getElementById('notificationRequestId').value.trim();
    const studentId = document.getElementById('notificationStudentId').value.trim();
    const requesterName = document.getElementById('notificationRequesterName').value.trim();

    fetch('/uoc-sports/public/equipment-manager/delete-request-notification', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            notification_id: notificationId,
            request_id: requestId,
            student_id: studentId,
            requester_name: requesterName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotificationHistory();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete notification'));
        }
    })
    .catch(error => {
        alert('Error deleting notification: ' + error.message);
    });
}

function sendSpecialNotification() {
    const requestId = document.getElementById('notificationRequestId').value.trim();
    const studentId = document.getElementById('notificationStudentId').value.trim();
    const requesterName = document.getElementById('notificationRequesterName').value.trim();
    const message = document.getElementById('notificationMessage').value.trim();

    if (!requestId || !studentId || !requesterName) {
        alert('Missing request details. Please try again.');
        return;
    }

    if (!message) {
        alert('Please type a notification message.');
        return;
    }

    fetch('/uoc-sports/public/equipment-manager/send-request-notification', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            request_id: requestId,
            student_id: studentId,
            requester_name: requesterName,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notification sent successfully.');
            document.getElementById('notificationMessage').value = '';
            loadNotificationHistory();
        } else {
            alert('Error: ' + (data.message || 'Failed to send notification'));
        }
    })
    .catch(error => {
        alert('Error sending notification: ' + error.message);
    });
}

function deleteRequest(requestId) {
    if (!confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
        return;
    }
    
    fetch('/uoc-sports/public/equipment-manager/delete-booking-request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ request_id: requestId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Request deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting request');
        console.error('Error:', error);
    });
}

function filterRequests() {
    const status = document.getElementById('statusFilter').value;
    const sport = document.getElementById('sportFilter').value;
    const url = new URL(window.location.href);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    if (sport) {
        url.searchParams.set('sport_id', sport);
    } else {
        url.searchParams.delete('sport_id');
    }
    
    window.location.href = url.toString();
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Sort table function
let sortDirection = {};
function sortTable(columnIndex) {
    const table = document.querySelector('.data-table table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    if (!sortDirection[columnIndex]) {
        sortDirection[columnIndex] = 'asc';
    } else {
        sortDirection[columnIndex] = sortDirection[columnIndex] === 'asc' ? 'desc' : 'asc';
    }
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex]?.textContent.trim() || '';
        const bValue = b.cells[columnIndex]?.textContent.trim() || '';
        
        if (sortDirection[columnIndex] === 'asc') {
            return aValue.localeCompare(bValue, undefined, { numeric: true });
        } else {
            return bValue.localeCompare(aValue, undefined, { numeric: true });
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}
