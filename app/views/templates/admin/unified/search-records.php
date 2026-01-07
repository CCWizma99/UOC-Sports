<section id="search-user">
    <h2>Search Player Records</h2>
    <div class="filter-bar">
        <h3>Filter <i class="fa-solid fa-filter"></i></h3>

        <div class="btn" id="faculty-btn">
            Faculty
            <div class="dropdown" data-filter="faculty">
                <div data-value="">All</div>
                <div data-value="Science">Science</div>
                <div data-value="Arts">Arts</div>
                <div data-value="Medicine">Medicine</div>
            </div>
        </div>

        <div class="btn" id="year-btn">
            Year
            <div class="dropdown" data-filter="year">
                <div data-value="">All</div>
                <div data-value="1">1</div>
                <div data-value="2">2</div>
                <div data-value="3">3</div>
                <div data-value="4">4</div>
            </div>
        </div>

        <div class="btn" id="sport-btn">
            Sport
            <div class="dropdown" data-filter="sport">
                <div data-value="">All</div>
                <div data-value="Cricket">Cricket</div>
                <div data-value="Football">Football</div>
                <div data-value="Rowing">Rowing</div>
            </div>
        </div>

        <div class="btn" id="public-btn">
            Type
            <div class="dropdown" data-filter="type">
                <div data-value="">All</div>
                <div data-value="Student">Student</div>
                <div data-value="Staff">Staff</div>
            </div>
        </div>
    </div>

    <input type="text" name="search-user-inp" id="search-user-inp" 
        title="Enter user ID No. or Name" placeholder="Enter User ID or Name">

    <div class="search-output"></div>
</section>

<script>
  const filters = { faculty: '', year: '', sport: '', type: '' };

// Store original button labels for reset
document.querySelectorAll('.btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

// Toggle dropdown visibility
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(dd => {
            if (dd.parentElement !== btn) dd.classList.remove('show');
        });
        btn.querySelector('.dropdown').classList.toggle('show');
    });
});

// Select filter value
document.querySelectorAll('.dropdown div').forEach(option => {
    option.addEventListener('click', e => {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;

        const labelNode = btn.childNodes[0]; 
        const originalLabel = btn.getAttribute('data-original');

        if (value === '') {
            labelNode.textContent = originalLabel;
        } else {
            labelNode.textContent = value;
        }

        e.target.closest('.dropdown').classList.remove('show');
        performSearch();
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Search on typing
document.getElementById('search-user-inp').addEventListener('input', performSearch);

function performSearch() {
    const query = document.getElementById('search-user-inp').value.trim();
    if (query.length === 0 && Object.values(filters).every(f => f === '')) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    const params = new URLSearchParams({ q: query, ...filters });

    fetch(`/uoc-sports/public/api/search-user.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.fname} ${user.lname}</td>
                            <td>${user.type}</td>
                            <td>
                                <a href="./user.php?id=${user.user_id}" class="action-link" title="View User">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No users found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
}
</script>
