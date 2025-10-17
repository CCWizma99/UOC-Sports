<div class="container">
  <div class="header">
    <h1 class="title">Verify Staff Accounts</h1>
    <h3 class="info-title">Review and Approve Pending Registrations</h3>
  </div>

  <div class="search-filter">
    <input type="text" id="searchInput" placeholder="Search by name or department" onkeyup="filterStaff()">
  </div>

  <div class="staff-table">
    <div class="table-header">
      <div>Name</div>
      <div>Department</div>
      <div>Email</div>
      <div>Staff ID</div>
      <div>Registration Date</div>
      <div>Verification Status</div>
      <div>Actions</div>
    </div>

    <!-- Rows -->
    <div class="table-row">
      <div>Dr. Eleanor Harper</div>
      <div>Computer Science</div>
      <div>eleanor.harper@email.com</div>
      <div>STAFF001</div>
      <div>2025/09/15</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, true)">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, false)">Reject</button>
      </div>
    </div>

    <div class="table-row">
      <div>Prof. Samuel Bennett</div>
      <div>Mathematics</div>
      <div>samuel.bennett@email.com</div>
      <div>STAFF002</div>
      <div>2025/09/20</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, true)">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, false)">Reject</button>
      </div>
    </div>

    <div class="table-row">
      <div>Dr. Olivia Carter</div>
      <div>Physics</div>
      <div>olivia.carter@email.com</div>
      <div>STAFF003</div>
      <div>2025/09/22</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, true)">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, false)">Reject</button>
      </div>
    </div>

    <div class="table-row">
      <div>Prof. Ethan Davis</div>
      <div>Chemistry</div>
      <div>ethan.davis@email.com</div>
      <div>STAFF004</div>
      <div>2025/09/25</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, true)">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, false)">Reject</button>
      </div>
    </div>

    <div class="table-row">
      <div>Dr. Sophia Evans</div>
      <div>Biology</div>
      <div>sophia.evans@email.com</div>
      <div>STAFF005</div>
      <div>2025/09/28</div>
      <div class="status pending">Pending</div>
      <div class="actions">
        <button class="approve-btn" onclick="updateStatus(this, true)">Approve</button>
        <button class="reject-btn" onclick="updateStatus(this, false)">Reject</button>
      </div>
    </div>
  </div>
</div>
