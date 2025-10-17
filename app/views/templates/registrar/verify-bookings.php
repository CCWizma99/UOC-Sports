<div class="container">
  <div class="header">
    <h1 class="title">Verify Bookings</h1>
  </div>

  <div class="booking-table">
    <div class="table-header table-grid">
      <div>Requester</div>
      <div>Facility</div>
      <div>Date/Time</div>
      <div>Purpose</div>
      <div>Booking ID</div>
      <div>Booking Date</div>
      <div>Status</div>
      <div>Actions</div>
    </div>

    <div class="table-row table-grid">
      <div>Dr. Evelyn Hayes</div>
      <div>Main Auditorium</div>
      <div>2024-07-20 | 14:00–16:00</div>
      <div>Guest Lecture</div>
      <div>BK2024001</div>
      <div>2024-07-15</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, 'Approved')">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, 'Rejected')">Reject</button>
      </div>
    </div>

    <div class="table-row table-grid">
      <div>Ms. Sophia Turner</div>
      <div>Gymnasium</div>
      <div>2024-07-25 | 18:00–20:00</div>
      <div>Sports Event</div>
      <div>BK2024003</div>
      <div>2024-07-18</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, 'Approved')">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, 'Rejected')">Reject</button>
      </div>
    </div>

    <div class="table-row table-grid">
      <div>Dr. Amelia Foster</div>
      <div>Library Seminar Room</div>
      <div>2024-07-30 | 13:00–15:00</div>
      <div>Research Presentation</div>
      <div>BK2024005</div>
      <div>2024-07-22</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, 'Approved')">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, 'Rejected')">Reject</button>
      </div>
    </div>
  </div>
</div>
