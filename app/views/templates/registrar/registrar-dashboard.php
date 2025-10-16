<div class="container">
    <div class="header">
        <h1 class="title">Registrar Dashboard</h1>
        <h3 class="info-title">Verify Users & Bookings</h3>
    </div>

    <!-- Search Bar -->
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by name or department..." onkeyup="searchTable()" />
    </div>

    <!-- Verify Staff -->
    <div class="section verify-staff">
         <div class="section-header">
        <h2 class="section-title">Verify Staff</h2>
        <a href="verify_staff.php" class="view-all">View All ➜</a>
    </div>
        <div class="table">
            <div class="table-header">
                <div>Name</div>
                <div>Department</div>
                <div>Email</div>
                <div>Registration Date</div>
                <div>Actions</div>
            </div>

            <div class="table-row">
                <div>Dr. Eleanor Bennett</div>
                <div>Physics</div>
                <div>eleanor.bennett@email.com</div>
                <div>2024-07-20</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>

            <div class="table-row">
                <div>Prof. Samuel Carter</div>
                <div>Mathematics</div>
                <div>samuel.carter@email.com</div>
                <div>2024-07-22</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>

            <div class="table-row">
                <div>Ms. Olivia Davis</div>
                <div>Chemistry</div>
                <div>olivia.davis@email.com</div>
                <div>2024-07-25</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify Students -->
    <div class="section verify-students">
        <div class="section-header">
        <h2 class="section-title">Verify Students</h2>
        <a href="verify_students.php" class="view-all">View All ➜</a>
    </div>
        <div class="table">
            <div class="table-header">
                <div>ID</div>
                <div>Name</div>
                <div>Department</div>
                <div>Email</div>
                <div>Registration Date</div>
                <div>Actions</div>
            </div>

            <div class="table-row">
                <div>S01</div>
                <div>Dr. Eleanor Bennett</div>
                <div>Physics</div>
                <div>eleanor.bennett@email.com</div>
                <div>2024-07-20</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>

            <div class="table-row">
                <div>S02</div>
                <div>Prof. Samuel Carter</div>
                <div>Mathematics</div>
                <div>samuel.carter@email.com</div>
                <div>2024-07-22</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify Bookings -->
    <div class="section verify-bookings">
         <div class="section-header">
        <h2 class="section-title">Verify Bookings</h2>
        <a href="verify_bookings.php" class="view-all">View All ➜</a>
    </div>
        <div class="table">
            <div class="table-header">
                <div>Requester</div>
                <div>Facility</div>
                <div>Purpose</div>
                <div>Date</div>
                <div>Actions</div>
            </div>

            <div class="table-row">
                <div>Dr. Eleanor Bennett</div>
                <div>Volleyball Court</div>
                <div>Friendly Tournament</div>
                <div>2024-07-20</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>

            <div class="table-row">
                <div>Ms. Olivia Davis</div>
                <div>Cricket Ground</div>
                <div>Cricket Practice</div>
                <div>2024-07-25</div>
                <div class="actions">
                    <button class="dots" onclick="toggleMenu(this)">⋮</button>
                    <div class="dropdown-menu">
                        <button onclick="approve(this)">Approve</button>
                        <button onclick="reject(this)">Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
