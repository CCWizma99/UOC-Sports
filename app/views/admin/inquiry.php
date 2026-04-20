<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/inquiry-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="search-container">
        <div class="header-row">
            <h2>Manage Inquiries</h2>
            <button class="btn-view-all" id="viewAllBtn" onclick="loadAllInquiries()">
                <i class="fas fa-list"></i> View All Inquiries
            </button>
        </div>
        
        <input type="text" id="search" placeholder="Type inquiry ID, email, or subject to search...">

        <table id="resultTable">
            <thead>
                <tr>
                    <th>Inquiry ID</th>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="resultsBody">
                <tr><td colspan="7" style="text-align:center; color:gray;">Start typing to search or click "View All Inquiries"</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Inquiry Details Modal -->
<div id="inquiryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Inquiry Details</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Details will be loaded here -->
        </div>
        <div class="modal-footer" id="modalFooter" style="padding: 20px; border-top: 1px solid #eee; display: none;">
            <div id="replySection">
                <h4>Send a Reply Message</h4>
                <div class="form-group" style="margin-top: 10px;">
                    <textarea id="replyMessage" placeholder="Type your response here..." style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 8px;"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button class="btn-view-all" onclick="sendInquiryReply()" style="padding: 8px 20px; font-size: 14px;">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
                <div id="replyStatus" style="margin-top: 10px; font-size: 13px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
const searchInput = document.getElementById('search');
const resultsBody = document.getElementById('resultsBody');

// Live search functionality
searchInput.addEventListener('keyup', async () => {
    const query = searchInput.value.trim();

    if (!query) {
        resultsBody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:gray;">Start typing to search or click "View All Inquiries"</td></tr>';
        return;
    }

    const res = await fetch(`/uoc-sports/public/admin-inquiry/search?q=${encodeURIComponent(query)}`);
    const data = await res.json();

    displayInquiries(data);
});

// Load all inquiries
async function loadAllInquiries() {
    try {
        resultsBody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:gray;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
        
        const res = await fetch('/uoc-sports/public/admin-inquiry/all');
        const result = await res.json();

        if (result.status === 'success') {
            displayInquiries(result.data);
        } else {
            resultsBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:red;">${result.message}</td></tr>`;
        }
    } catch (error) {
        resultsBody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:red;">Failed to load inquiries</td></tr>';
    }
}

// Display inquiries in table
function displayInquiries(data) {
    if (data.length === 0) {
        resultsBody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:gray;">No inquiries found</td></tr>';
        return;
    }

    resultsBody.innerHTML = data.map(i => `
        <tr>
            <td data-label="Inquiry ID">${i.inquiry_id}</td>
            <td data-label="User ID">${i.user_id}</td>
            <td data-label="Email">${i.email}</td>
            <td data-label="Subject" class="subject-cell" title="${escapeHtml(i.subject)}">${truncate(i.subject, 30)}</td>
            <td data-label="Date">${formatDate(i.date)}</td>
            <td data-label="Status">
                <span class="status-badge ${i.status === 'RESOLVED' ? 'resolved' : 'not-resolved'}">
                    ${i.status}
                </span>
            </td>
            <td data-label="Actions" class="action-buttons">
                <button class="btn-action btn-view" onclick="viewInquiry('${i.inquiry_id}')" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn-action btn-toggle" onclick="toggleStatus('${i.inquiry_id}', '${i.status}')" title="Toggle Status">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn-action btn-delete" onclick="deleteInquiry('${i.inquiry_id}')" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// View inquiry details
async function viewInquiry(inquiryId) {
    try {
        const res = await fetch(`/uoc-sports/public/admin-inquiry/details/${inquiryId}`);
        const result = await res.json();

        if (result.status === 'success') {
            const inquiry = result.data;
            document.getElementById('modalBody').innerHTML = `
                <div class="detail-row">
                    <strong>Inquiry ID:</strong>
                    <span>${inquiry.inquiry_id}</span>
                </div>
                <div class="detail-row">
                    <strong>User ID:</strong>
                    <span>${inquiry.user_id}</span>
                </div>
                <div class="detail-row">
                    <strong>Email:</strong>
                    <span>${inquiry.email}</span>
                </div>
                <div class="detail-row">
                    <strong>Subject:</strong>
                    <span>${escapeHtml(inquiry.subject)}</span>
                </div>
                <div class="detail-row">
                    <strong>Date:</strong>
                    <span>${formatDate(inquiry.date)}</span>
                </div>
                <div class="detail-row">
                    <strong>Status:</strong>
                    <span class="status-badge ${inquiry.status === 'RESOLVED' ? 'resolved' : 'not-resolved'}">
                        ${inquiry.status}
                    </span>
                </div>
                <div class="detail-row message-row">
                    <strong>Message:</strong>
                    <p>${escapeHtml(inquiry.message)}</p>
                </div>
            `;
            
            // Show reply section only if user_id exists
            const footer = document.getElementById('modalFooter');
            if (inquiry.user_id && inquiry.user_id !== 'PUBLIC' && inquiry.user_id !== 'Guest') {
                footer.style.display = 'block';
                footer.setAttribute('data-user-id', inquiry.user_id);
                footer.setAttribute('data-subject', inquiry.subject);
                footer.setAttribute('data-inquiry-id', inquiry.inquiry_id);
            } else {
                footer.style.display = 'none';
            }

            document.getElementById('inquiryModal').style.display = 'flex';
        } else {
            UI.showToast('Failed to load inquiry details', 'error');
        }
    } catch (error) {
        UI.showToast('An error occurred while loading inquiry details', 'error');
    }
}

// Toggle inquiry status
async function toggleStatus(inquiryId, currentStatus) {
    const newStatus = currentStatus === 'RESOLVED' ? 'NOT-RESOLVED' : 'RESOLVED';
    
    UI.confirm(`Change status to ${newStatus}?`, async () => {
        try {
            const formData = new FormData();
            formData.append('inquiry_id', inquiryId);
            formData.append('status', newStatus);

            const res = await fetch('/uoc-sports/public/admin-inquiry/update-status', {
                method: 'POST',
                body: formData
            });

            const result = await res.json();

            if (result.status === 'success') {
                UI.showToast('Status updated successfully', 'success');
                if (searchInput.value.trim()) {
                    searchInput.dispatchEvent(new Event('keyup'));
                } else {
                    loadAllInquiries();
                }
            } else {
                UI.showToast('Failed to update status: ' + result.message, 'error');
            }
        } catch (error) {
            UI.showToast('An error occurred while updating status', 'error');
        }
    });
}

// Delete inquiry
async function deleteInquiry(inquiryId) {
    UI.confirm('Are you sure you want to delete this inquiry? This action cannot be undone.', async () => {
        try {
            const formData = new FormData();
            formData.append('inquiry_id', inquiryId);

            const res = await fetch('/uoc-sports/public/admin-inquiry/delete', {
                method: 'POST',
                body: formData
            });

            const result = await res.json();

            if (result.status === 'success') {
                UI.showToast('Inquiry deleted successfully', 'success');
                if (searchInput.value.trim()) {
                    searchInput.dispatchEvent(new Event('keyup'));
                } else {
                    loadAllInquiries();
                }
            } else {
                UI.showToast('Failed to delete inquiry: ' + result.message, 'error');
            }
        } catch (error) {
            UI.showToast('An error occurred while deleting inquiry', 'error');
        }
    }, null, true); // Danger theme
}

// Close modal
function closeModal() {
    document.getElementById('inquiryModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('inquiryModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Helper functions
function truncate(str, length) {
    return str.length > length ? str.substring(0, length) + '...' : str;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Send reply via messaging system
async function sendInquiryReply() {
    const footer = document.getElementById('modalFooter');
    const userId = footer.getAttribute('data-user-id');
    const subject = footer.getAttribute('data-subject');
    const inquiryId = footer.getAttribute('data-inquiry-id');
    const message = document.getElementById('replyMessage').value.trim();
    const statusEl = document.getElementById('replyStatus');

    if (!message) {
        statusEl.innerHTML = '<span style="color:red">Please enter a message</span>';
        return;
    }

    try {
        const formData = new FormData();
        formData.append('recipient_id', userId);
        formData.append('recipient_type', ''); // Backend will deduce this from userId
        formData.append('title', 'RE: ' + subject);
        formData.append('message', message);
        formData.append('sport_id', 'GEN'); // Generic inquiry sport

        const res = await fetch('/uoc-sports/public/api/message/send', {
            method: 'POST',
            body: formData
        });

        const result = await res.json();

        if (result.status === 'success') {
            statusEl.innerHTML = '<span style="color:green">Reply sent successfully via messaging system!</span>';
            document.getElementById('replyMessage').value = '';
            
            // Optionally auto-resolve the inquiry
            const resolveFormData = new FormData();
            resolveFormData.append('inquiry_id', inquiryId);
            resolveFormData.append('status', 'RESOLVED');
            fetch('/uoc-sports/public/admin-inquiry/update-status', { method: 'POST', body: resolveFormData });
            
        } else {
            // Try as MANAGER if CAPTAIN failed? Or just error.
            statusEl.innerHTML = '<span style="color:red">Error: ' + result.message + '</span>';
        }
    } catch (error) {
        statusEl.innerHTML = '<span style="color:red">Failed to send reply</span>';
    }
}
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-inquiry");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/search-keyboard-nav.js"></script>
<script>
    SearchKeyboardNav.init({
        inputSelector: '#search',
        resultsSelector: '#resultsBody',
        itemSelector: 'tr',
        actionSelector: '.btn-view'
    });
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
