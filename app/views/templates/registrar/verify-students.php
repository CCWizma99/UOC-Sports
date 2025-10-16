
<div class="container">
  <div class="verify-container">
    <div class="header-bar">
      <h2>Verify Student Accounts</h2>
      <div class="filters">
        <select id="statusFilter">
          <option value="all">Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
        <select id="dateFilter">
          <option value="all">Date Range</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
        </select>
      </div>
    </div>

    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search" />
    </div>

    <table class="verify-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Student ID</th>
          <th>Department</th>
          <th>Email</th>
          <th>Registration ID</th>
          <th>Verification Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="studentTableBody">
        <tr>
          <td>Ethan Harper</td>
          <td>STU12345</td>
          <td>Computer Science</td>
          <td>ethan.harper@email.com</td>
          <td>REG67890</td>
          <td><span class="status pending">Pending</span></td>
          <td>
            <button class="approve-btn">Approve</button>
            <button class="reject-btn">Reject</button>
          </td>
        </tr>
        <tr>
          <td>Olivia Bennett</td>
          <td>STU67890</td>
          <td>Biology</td>
          <td>olivia.bennett@email.com</td>
          <td>REG12345</td>
          <td><span class="status approved">Approved</span></td>
          <td>
            <button class="view-btn">View</button>
          </td>
        </tr>
        <tr>
          <td>Noah Carter</td>
          <td>STU24680</td>
          <td>Business Administration</td>
          <td>noah.carter@email.com</td>
          <td>REG36912</td>
          <td><span class="status rejected">Rejected</span></td>
          <td>
            <button class="view-btn">View</button>
          </td>
        </tr>
        <tr>
          <td>Ava Thompson</td>
          <td>STU13579</td>
          <td>Psychology</td>
          <td>ava.thompson@email.com</td>
          <td>REG80246</td>
          <td><span class="status pending">Pending</span></td>
          <td>
            <button class="approve-btn">Approve</button>
            <button class="reject-btn">Reject</button>
          </td>
        </tr>
        <tr>
          <td>Liam Foster</td>
          <td>STU98765</td>
          <td>Engineering</td>
          <td>liam.foster@email.com</td>
          <td>REG54321</td>
          <td><span class="status approved">Approved</span></td>
          <td>
            <button class="approve-btn">Approve</button>
            <button class="reject-btn">Reject</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
 
