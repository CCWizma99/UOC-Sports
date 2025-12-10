<div class="container" style="margin-top: 120px;">
  <section id="enroll-sport">
    <h2><i class="fas fa-trophy"></i> Enroll in Sports</h2>
    
    <!-- Enrolled Sports Section -->
    <div id="enrolled-sports-section">
      <h3><i class="fas fa-check-circle"></i> My Enrolled Sports</h3>
      <div id="enrolled-sports-list" class="sports-grid">
        <!-- Enrolled sports will be loaded here -->
      </div>
    </div>

    <!-- Available Sports Section -->
    <div id="available-sports-section">
      <h3><i class="fas fa-plus-circle"></i> Available Sports</h3>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="sport-search" placeholder="Search for sports to enroll..." autocomplete="off">
      </div>
      <div id="available-sports-list" class="sports-grid">
        <!-- Available sports will be loaded here -->
      </div>
    </div>

    <div id="enroll-message"></div>
  </section>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <i class="fas fa-question-circle"></i>
      <h3 id="confirmModalTitle">Confirm Action</h3>
    </div>
    <div class="modal-body">
      <p id="confirmModalMessage"></p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-cancel" onclick="closeConfirmModal()">
        <i class="fas fa-times"></i> Cancel
      </button>
      <button class="btn btn-primary" id="confirmModalBtn">
        <i class="fas fa-check"></i> Confirm
      </button>
    </div>
  </div>
</div>

<!-- Encouragement Modal -->
<div id="encouragementModal" class="modal">
  <div class="modal-content encouragement">
    <div class="modal-header success">
      <i class="fas fa-trophy"></i>
      <h3>Welcome to the Team!</h3>
    </div>
    <div class="modal-body">
      <p class="encouragement-text">
        <strong>Congratulations on enrolling in <span id="enrolledSportName"></span>!</strong>
      </p>
      <p class="encouragement-message">
        The University of Colombo has invested significant resources in developing world-class sports facilities and programs for your growth and excellence. We believe in your potential and are committed to supporting your athletic journey.
      </p>
      <p class="encouragement-message">
        <strong>Remember:</strong> Success in sports requires dedication, consistency, and commitment. Show up to every practice, give your best effort, and represent UOC with pride. Your hard work will not only develop your athletic skills but also build character, discipline, and teamwork that will serve you throughout life.
      </p>
      <p class="encouragement-footer">
        <i class="fas fa-heart"></i> Stay committed. Stay consistent. Make UOC proud!
      </p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="closeEncouragementModal()">
        <i class="fas fa-check-circle"></i> I'm Ready to Commit!
      </button>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const enrolledList = document.getElementById("enrolled-sports-list");
  const availableList = document.getElementById("available-sports-list");
  const searchInput = document.getElementById("sport-search");
  const msg = document.getElementById("enroll-message");
  const confirmModal = document.getElementById("confirmModal");
  const encouragementModal = document.getElementById("encouragementModal");
  
  let availableSports = [];
  let enrolledSports = [];
  let sportsLoaded = false;
  let pendingAction = null;

  // Modal functions
  window.closeConfirmModal = () => {
    confirmModal.style.display = 'none';
    pendingAction = null;
  };

  window.closeEncouragementModal = () => {
    encouragementModal.style.display = 'none';
  };

  function showConfirmModal(title, message, onConfirm) {
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalMessage').textContent = message;
    confirmModal.style.display = 'flex';
    
    const confirmBtn = document.getElementById('confirmModalBtn');
    confirmBtn.onclick = () => {
      closeConfirmModal();
      onConfirm();
    };
  }

  function showEncouragementModal(sportName) {
    document.getElementById('enrolledSportName').textContent = sportName;
    encouragementModal.style.display = 'flex';
  }

  // Close modal when clicking outside
  window.onclick = (event) => {
    if (event.target === confirmModal) {
      closeConfirmModal();
    }
    if (event.target === encouragementModal) {
      closeEncouragementModal();
    }
  };

  // Load enrolled sports
  async function loadEnrolledSports() {
    try {
      const res = await fetch('/uoc-sports/public/student/enrolled-sports');
      const data = await res.json();
      
      if (data.status === 'success') {
        enrolledSports = data.data;
        renderEnrolledSports();
      }
    } catch (error) {
      console.error('Error loading enrolled sports:', error);
    }
  }

  // Load available sports (only when needed)
  async function loadAvailableSports() {
    if (sportsLoaded) return;
    
    try {
      const res = await fetch('/uoc-sports/public/student/available-sports');
      const data = await res.json();
      
      if (data.status === 'success') {
        availableSports = data.data;
        sportsLoaded = true;
      }
    } catch (error) {
      console.error('Error loading available sports:', error);
    }
  }

  // Render enrolled sports
  function renderEnrolledSports() {
    enrolledList.innerHTML = '';
    
    if (enrolledSports.length === 0) {
      enrolledList.innerHTML = '<p class="no-sports">You are not enrolled in any sports yet.</p>';
      return;
    }

    enrolledSports.forEach(sport => {
      const card = document.createElement('div');
      card.className = 'sport-card enrolled';
      card.innerHTML = `
        <div class="sport-icon">
          <i class="fas fa-medal"></i>
        </div>
        <div class="sport-info">
          <h4>${sport.sport_name}</h4>
          <p class="joined-date">Joined: ${formatDate(sport.joined_date)}</p>
        </div>
        <button class="btn btn-danger btn-sm" onclick="unenrollSport('${sport.sport_id}', '${sport.sport_name}')">
          <i class="fas fa-times"></i> Unenroll
        </button>
      `;
      enrolledList.appendChild(card);
    });
  }

  // Render available sports
  function renderAvailableSports(sports) {
    availableList.innerHTML = '';
    
    if (sports.length === 0) {
      availableList.innerHTML = '<p class="no-sports">No sports found matching your search.</p>';
      return;
    }

    sports.forEach(sport => {
      const card = document.createElement('div');
      card.className = 'sport-card available';
      card.innerHTML = `
        <div class="sport-icon">
          <i class="fas fa-running"></i>
        </div>
        <div class="sport-info">
          <h4>${sport.sport_name}</h4>
          <p class="sport-id">ID: ${sport.sport_id}</p>
        </div>
        <button class="btn btn-primary btn-sm" onclick="enrollInSport('${sport.sport_id}', '${sport.sport_name}')">
          <i class="fas fa-plus"></i> Enroll
        </button>
      `;
      availableList.appendChild(card);
    });
  }

  // Search functionality
  searchInput.addEventListener('input', async () => {
    const query = searchInput.value.toLowerCase().trim();
    
    if (query === '') {
      availableList.innerHTML = '<p class="no-sports">Start typing to search for sports...</p>';
      return;
    }

    // Load sports if not already loaded
    if (!sportsLoaded) {
      availableList.innerHTML = '<p class="no-sports">Loading sports...</p>';
      await loadAvailableSports();
    }

    const filtered = availableSports.filter(sport => 
      sport.sport_name.toLowerCase().includes(query) ||
      sport.sport_id.toLowerCase().includes(query)
    );
    
    renderAvailableSports(filtered);
  });

  // Enroll in sport
  window.enrollInSport = async (sportId, sportName) => {
    showConfirmModal(
      'Enroll in Sport',
      `Are you sure you want to enroll in ${sportName}?`,
      async () => {
        const formData = new FormData();
        formData.append('sport_id', sportId);

        try {
          const res = await fetch('/uoc-sports/public/student/enroll-sport', {
            method: 'POST',
            body: formData
          });

          const result = await res.json();

          if (result.status === 'success') {
            // Show encouragement modal
            showEncouragementModal(sportName);
            
            // Reload enrolled sports and reset available sports
            await loadEnrolledSports();
            sportsLoaded = false;
            searchInput.value = '';
            availableList.innerHTML = '<p class="no-sports">Start typing to search for sports...</p>';
          } else {
            showMessage(result.message, 'error');
          }
        } catch (error) {
          showMessage('An error occurred while enrolling', 'error');
        }
      }
    );
  };

  // Unenroll from sport
  window.unenrollSport = async (sportId, sportName) => {
    showConfirmModal(
      'Unenroll from Sport',
      `Are you sure you want to unenroll from ${sportName}? You can always re-enroll later.`,
      async () => {
        const formData = new FormData();
        formData.append('sport_id', sportId);

        try {
          const res = await fetch('/uoc-sports/public/student/unenroll-sport', {
            method: 'POST',
            body: formData
          });

          const result = await res.json();
          showMessage(result.message, result.status);

          if (result.status === 'success') {
            // Reload enrolled sports and reset available sports
            await loadEnrolledSports();
            sportsLoaded = false;
            availableList.innerHTML = '<p class="no-sports">Start typing to search for sports...</p>';
          }
        } catch (error) {
          showMessage('An error occurred while unenrolling', 'error');
        }
      }
    );
  };

  // Show message
  function showMessage(message, status) {
    msg.textContent = message;
    msg.className = status === 'success' ? 'success' : 'error';
    msg.style.display = 'block';

    setTimeout(() => {
      msg.style.display = 'none';
    }, 5000);
  }

  // Format date
  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
  }

  // Initial load - only enrolled sports
  loadEnrolledSports();
  availableList.innerHTML = '<p class="no-sports">Start typing to search for sports...</p>';
});
</script>
