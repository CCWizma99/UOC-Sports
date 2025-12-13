<div class="reservations-container">
    <!-- Week Navigation -->
    <div class="week-navigation">
      <button class="week-nav-btn">
        ← Previous Week
      </button>
      <div class="week-display">
        <div class="week-range">October 21 - October 27</div>
        <div class="current-week">Current Week</div>
      </div>
      <button class="week-nav-btn">
        Next Week →
      </button>
    </div>

    <!-- Filters -->
    <div class="filters">
    <button class="filter-btn">All</button>
    <button class="filter-btn active">Pending</button>
    <button class="filter-btn">Approved</button>
    <button class="filter-btn">Rejected</button>
    </div>

    <!-- Reservations Grid -->
    <div class="reservations-grid" id="reservationsList">
      <!-- Reservations will be populated by JavaScript -->
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="reservationModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Reservation Details</h2>
        <button class="close-btn" id="closeModal">&times;</button>
      </div>
      <div class="modal-details" id="modalDetails">
        <!-- Details will be populated by JavaScript -->
      </div>
      <div class="modal-actions" id="modalActions">
        <!-- Actions will be populated by JavaScript -->
      </div>
    </div>
  </div>

  <script>
    // Dummy data for equipment reservations
    const reservations = [
      {
        id: 1,
        title: "Badminton Team Practice",
        equipment: "4 Badminton Rackets, 2 Nets, Shuttles",
        sport: "Badminton",
        date: "2024-11-25",
        time: "14:00 - 16:00",
        requester: "K S Silva",
        status: "approved",
        manager: "N S Perera",
        location: "Main Court A",
        notes: "Regular team practice session"
      },
      {
        id: 2,
        title: "Football Training Session",
        equipment: "15 Footballs, Cones, Goal Posts, Bibs",
        sport: "Football",
        date: "2024-11-26",
        time: "16:00 - 18:00",
        requester: "M D Fernando",
        status: "pending",
        manager: "A R Khan",
        location: "Football Field 1",
        notes: "Youth team training"
      },
      {
        id: 3,
        title: "Cricket Nets Practice",
        equipment: "6 Cricket Bats, Bowling Machine, Helmets",
        sport: "Cricket",
        date: "2024-11-27",
        time: "09:00 - 11:00",
        requester: "P T Wijesinghe",
        status: "approved",
        manager: "S L Jayakody",
        location: "Practice Nets",
        notes: "Batting practice session"
      },
      {
        id: 4,
        title: "Badminton Beginners Class",
        equipment: "8 Badminton Rackets, Shuttles, Nets",
        sport: "Badminton",
        date: "2024-11-28",
        time: "10:00 - 12:00",
        requester: "R M Dissanayake",
        status: "pending",
        manager: "K S Silva",
        location: "Court B",
        notes: "Beginner coaching session"
      },
      {
        id: 5,
        title: "Football League Match",
        equipment: "Match Balls, Referee Equipment, Flags",
        sport: "Football",
        date: "2024-11-29",
        time: "15:00 - 17:00",
        requester: "T A Rathnayake",
        status: "approved",
        manager: "M D Fernando",
        location: "Main Stadium",
        notes: "Inter-department league match"
      },
      {
        id: 6,
        title: "Cricket Team Practice",
        equipment: "Full Kit, Practice Balls, Stumps",
        sport: "Cricket",
        date: "2024-11-30",
        time: "08:00 - 11:00",
        requester: "S L Jayakody",
        status: "rejected",
        manager: "P T Wijesinghe",
        location: "Main Ground",
        notes: "Equipment conflict with another event"
      },
      {
        id: 7,
        title: "Badminton Tournament",
        equipment: "10 Rackets, Tournament Nets, Scoreboards",
        sport: "Badminton",
        date: "2024-12-01",
        time: "13:00 - 18:00",
        requester: "A R Khan",
        status: "pending",
        manager: "R M Dissanayake",
        location: "All Courts",
        notes: "Annual inter-college tournament"
      },
      {
        id: 8,
        title: "Football Youth Training",
        equipment: "20 Footballs, Training Bibs, Cones",
        sport: "Football",
        date: "2024-11-25",
        time: "17:00 - 19:00",
        requester: "N S Perera",
        status: "approved",
        manager: "T A Rathnayake",
        location: "Training Field",
        notes: "Under-16 team practice"
      },
      {
        id: 9,
        title: "Cricket Bowling Practice",
        equipment: "Bowling Machine, Protective Gear, Balls",
        sport: "Cricket",
        date: "2024-11-26",
        time: "14:00 - 16:00",
        requester: "K S Silva",
        status: "approved",
        manager: "S L Jayakody",
        location: "Practice Area",
        notes: "Fast bowling practice"
      },
      {
        id: 10,
        title: "Badminton Mixed Doubles",
        equipment: "6 Rackets, Competition Shuttles, Nets",
        sport: "Badminton",
        date: "2024-11-27",
        time: "19:00 - 21:00",
        requester: "M D Fernando",
        status: "pending",
        manager: "A R Khan",
        location: "Court A & B",
        notes: "Mixed doubles practice matches"
      }
    ];

    // Modal elements
    const modal = document.getElementById('reservationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDetails = document.getElementById('modalDetails');
    const modalActions = document.getElementById('modalActions');
    const closeModal = document.getElementById('closeModal');

    // Function to render reservations
    function renderReservations(filter = 'all') {
      const container = document.getElementById('reservationsList');
      
      const filteredReservations = filter === 'all' 
        ? reservations 
        : reservations.filter(res => res.status === filter);

      if (filteredReservations.length === 0) {
        container.innerHTML = `
          <div class="empty-state">
            <h3>No reservations found</h3>
            <p>There are no ${filter === 'all' ? '' : filter + ' '}reservations for this week.</p>
            <button class="view-btn action-btn" style="margin: 0 auto;" onclick="renderReservations('all')">View All Reservations</button>
          </div>
        `;
        return;
      }

      container.innerHTML = filteredReservations.map(reservation => `
        <div class="reservation-card ${reservation.status}">
          <div class="card-header">
            <div class="reservation-title">${reservation.title}</div>
            <span class="sport-badge ${reservation.sport.toLowerCase()}">${reservation.sport}</span>
          </div>
          
          <div class="reservation-meta">
            <div class="meta-item">
              <span class="meta-label">Date & Time</span>
              <span class="meta-value">${new Date(reservation.date).toLocaleDateString()} • ${reservation.time}</span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Requester</span>
              <span class="meta-value">${reservation.requester}</span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Equipment</span>
              <span class="meta-value">${reservation.equipment.split(',')[0]}...</span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Location</span>
              <span class="meta-value">${reservation.location}</span>
            </div>
          </div>

          <div class="card-footer">
            <span class="reservation-status status-${reservation.status}">
              ${reservation.status}
            </span>
            <button class="view-btn action-btn" onclick="viewDetails(${reservation.id})">
              View Details
            </button>
          </div>
        </div>
      `).join('');
    }

    // Function to view reservation details in modal
    function viewDetails(id) {
      const reservation = reservations.find(res => res.id === id);
      if (reservation) {
        modalTitle.textContent = reservation.title;
        
        modalDetails.innerHTML = `
          <div class="detail-group">
            <span class="detail-label">Sport</span>
            <span class="detail-value">${reservation.sport}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Date & Time</span>
            <span class="detail-value">${new Date(reservation.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })} • ${reservation.time}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Location</span>
            <span class="detail-value">${reservation.location}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Requester</span>
            <span class="detail-value">${reservation.requester}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Manager</span>
            <span class="detail-value">${reservation.manager}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Equipment</span>
            <span class="detail-value">${reservation.equipment}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Notes</span>
            <span class="detail-value">${reservation.notes}</span>
          </div>
          <div class="detail-group">
            <span class="detail-label">Status</span>
            <span class="detail-value">
              <span class="reservation-status status-${reservation.status}">
                ${reservation.status}
              </span>
            </span>
          </div>
        `;

        // Set up actions based on status
        if (reservation.status === 'pending') {
          modalActions.innerHTML = `
            <button class="approve-btn action-btn" onclick="updateStatus(${reservation.id}, 'approved')">Approve</button>
            <button class="reject-btn action-btn" onclick="updateStatus(${reservation.id}, 'rejected')">Reject</button>
            <button class="view-btn action-btn" onclick="closeModalFunc()">Close</button>
          `;
        } else {
          modalActions.innerHTML = `
            <button class="view-btn action-btn" onclick="closeModalFunc()" style="flex: 1;">Close</button>
          `;
        }

        modal.classList.add('active');
      }
    }

    // Function to update reservation status
    function updateStatus(id, status) {
      const reservation = reservations.find(res => res.id === id);
      if (reservation) {
        reservation.status = status;
        renderReservations(getCurrentFilter());
        closeModalFunc();
      }
    }

    // Function to get current active filter
    function getCurrentFilter() {
      const activeFilter = document.querySelector('.filter-btn.active');
      return activeFilter ? activeFilter.textContent.toLowerCase().replace(' reservations', '') : 'all';
    }

    // Function to close modal
    function closeModalFunc() {
      modal.classList.remove('active');
    }

    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', () => {
  renderReservations('pending'); // Changed from renderReservations()

  // Filter button event listeners
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      e.target.classList.add('active');
      const filter = e.target.textContent.toLowerCase();
      renderReservations(filter);
    });
  });

      // Week navigation event listeners
      document.querySelectorAll('.week-nav-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          alert('Week navigation would load different reservations in a real application');
        });
      });

      // Modal close events
      closeModal.addEventListener('click', closeModalFunc);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          closeModalFunc();
        }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
          closeModalFunc();
        }
      });
    });
  </script>