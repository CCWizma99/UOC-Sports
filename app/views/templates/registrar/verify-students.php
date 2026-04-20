
<div class="container">
  <div class="verify-container">
    <div class="header-bar">
      <h2>Verify Student Accounts</h2>
      <div class="stats-badge">
        <span id="pending-count"><?php echo count($verifications ?? []); ?></span> Pending
      </div>
    </div>

    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search by name, student ID, or sport..." />
    </div>

    <?php if (empty($verifications)): ?>
      <div class="empty-state">
        <i class="fas fa-check-circle"></i>
        <h3>No Pending Verifications</h3>
        <p>All student verifications have been processed.</p>
      </div>
    <?php else: ?>
      <div class="verification-cards" id="verification-cards">
        <?php foreach ($verifications as $v): ?>
          <div class="verification-card" data-request-id="<?php echo htmlspecialchars($v['request_id']); ?>" data-student-id="<?php echo htmlspecialchars($v['student_id']); ?>">
            <div class="card-header">
              <div class="sport-badge"><?php echo htmlspecialchars($v['sport_name'] ?? 'Unknown Sport'); ?></div>
              <div class="request-date"><?php echo date('M d, Y', strtotime($v['request_date'])); ?></div>
            </div>
            
            <div class="student-info">
              <div class="student-avatar">
                <i class="fas fa-user-graduate"></i>
              </div>
              <div class="student-details">
                <h3><?php echo htmlspecialchars($v['fname'] . ' ' . $v['lname']); ?></h3>
                <p class="student-id-text"><i class="fas fa-id-badge"></i> <?php echo htmlspecialchars($v['uni_student_id']); ?></p>
                <p class="student-email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($v['email']); ?></p>
              </div>
            </div>
            
            <div class="requested-by">
              <small>Requested by: <strong><?php echo htmlspecialchars($v['requested_by_name'] ?? 'Sport Manager'); ?></strong></small>
            </div>

            <div class="card-actions">
              <button class="btn-approve" onclick="verifyStudent('<?php echo htmlspecialchars($v['request_id']); ?>', '<?php echo htmlspecialchars($v['student_id']); ?>', 'VERIFIED')">
                <i class="fas fa-check"></i> Approve
              </button>
              <button class="btn-reject" onclick="showRejectModal('<?php echo htmlspecialchars($v['request_id']); ?>', '<?php echo htmlspecialchars($v['student_id']); ?>')">
                <i class="fas fa-times"></i> Reject
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>


<!-- Reject Modal -->
<div id="rejectModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Reject Verification</h3>
      <span class="close-modal">&times;</span>
    </div>
    <div class="modal-body">
      <p>Please provide a reason for rejection:</p>
      <textarea id="rejectionReason" rows="3" placeholder="Enter reason..."></textarea>
      <input type="hidden" id="rejectRequestId" />
      <input type="hidden" id="rejectStudentId" />
      <div class="modal-actions">
        <button class="btn-cancel" onclick="closeModals()">Cancel</button>
        <button class="btn-confirm-reject" onclick="confirmReject()">Confirm Rejection</button>
      </div>
    </div>
  </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    const cards = document.querySelectorAll('.verification-card');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? 'block' : 'none';
    });
});


// Verify Student
async function verifyStudent(requestId, studentId, status, reason = null) {
    try {
        const res = await fetch('/uoc-sports/public/api/registrar/verify-student', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                request_id: requestId,
                student_id: studentId,
                status: status,
                reason: reason
            })
        });
        
        const data = await res.json();
        
        if (data.status === 'success') {
            // Remove card from UI
            const card = document.querySelector(`[data-request-id="${requestId}"][data-student-id="${studentId}"]`);
            if (card) {
                card.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => card.remove(), 300);
                
                // Update count
                const countEl = document.getElementById('pending-count');
                const currentCount = parseInt(countEl.textContent);
                countEl.textContent = Math.max(0, currentCount - 1);
            }
            UI.showToast(data.message || (status === 'VERIFIED' ? 'Student verified' : 'Student rejected'), 'success');
            closeModals();
        } else {
            UI.showToast(data.message || 'Failed to update verification', 'error');
        }
    } catch (err) {
        UI.showToast('Error: ' + err.message, 'error');
    }
}

// Show reject modal
function showRejectModal(requestId, studentId) {
    document.getElementById('rejectRequestId').value = requestId;
    document.getElementById('rejectStudentId').value = studentId;
    document.getElementById('rejectionReason').value = '';
    document.getElementById('rejectModal').style.display = 'flex';
}

// Confirm reject
function confirmReject() {
    const requestId = document.getElementById('rejectRequestId').value;
    const studentId = document.getElementById('rejectStudentId').value;
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        UI.showToast('Please provide a reason for rejection', 'warning');
        return;
    }
    
    verifyStudent(requestId, studentId, 'REJECTED', reason);
}

// Close modals
function closeModals() {
    document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
}

// Close modal on click outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModals();
    });
});

document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', closeModals);
});
</script>