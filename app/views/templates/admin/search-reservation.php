<section id="search-user">
    <h2>Search Reservations</h2>
    <div class="filter-bar">
        <h3>Filter <i class="fa-solid fa-filter"></i></h3>

        <!-- Date -->
        <div class="btn" id="date-btn">
            Date
            <div class="dropdown" data-filter="date">
                <input type="date" id="filter-date">
            </div>
        </div>

        <!-- Location -->
        <div class="btn" id="location-btn">
            Location
            <div class="dropdown" data-filter="location">
                <div data-value="">All</div>
                <div data-value="Indoor Stadium">Indoor Stadium</div>
                <div data-value="Karate/Taekwondo Mats">Karate/Taekwondo Mats</div>
                <div data-value="Volleyball Court">Volleyball Court</div>
                <div data-value="Baseball Court">Baseball Court</div>
            </div>
        </div>

        <!-- User Type -->
        <div class="btn" id="user-type-btn">
            User Type
            <div class="dropdown" data-filter="user_type">
                <div data-value="">All</div>
                <div data-value="Public">Public</div>
                <div data-value="Internal">Internal Users</div>
            </div>
        </div>
    </div>

    <input type="text" name="search-reservation-inp" id="search-reservation-inp" 
        title="Enter Reservation ID or User Name" placeholder="Enter Reservation ID or Name">

    <div class="search-output"></div>
</section>

<script>
const filters = { date: '', location: '', user_type: '' };

// Store original labels
document.querySelectorAll('.btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', e => {
        // If clicked inside a date input, skip toggle
        if (e.target.tagName === 'INPUT' && e.target.type === 'date') {
            return;
        }

        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(dd => {
            if (dd.parentElement !== btn) dd.classList.remove('show');
        });
        btn.querySelector('.dropdown').classList.toggle('show');
    });
});


// Keep dropdown open if clicking inside
document.querySelectorAll('.dropdown').forEach(dd => {
    dd.addEventListener('click', e => e.stopPropagation());
});

// Handle dropdown selections (non-date)
document.querySelectorAll('.dropdown div[data-value]').forEach(option => {
    option.addEventListener('click', e => {
        const value = e.target.getAttribute('data-value');
        const filterType = e.target.parentElement.getAttribute('data-filter');
        const btn = e.target.closest('.btn');

        filters[filterType] = value;
        const originalLabel = btn.getAttribute('data-original');
        btn.childNodes[0].textContent = value === '' ? originalLabel : e.target.textContent;

        e.target.closest('.dropdown').classList.remove('show');
        performSearch();
    });
});

// Handle date selection
const dateInput = document.getElementById('filter-date');
if (dateInput) {
    // Prevent dropdown from closing when clicking the date input
    ['click', 'mousedown', 'focus'].forEach(evt =>
        dateInput.addEventListener(evt, e => e.stopPropagation())
    );

    dateInput.addEventListener('change', e => {
        filters.date = e.target.value;
        const btn = e.target.closest('.btn');
        const originalLabel = btn.getAttribute('data-original');
        btn.childNodes[0].textContent = e.target.value || originalLabel;
        performSearch();
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(dd => dd.classList.remove('show'));
});

// Search when typing in the input
document.getElementById('search-reservation-inp').addEventListener('input', performSearch);

function performSearch() {
    const query = document.getElementById('search-reservation-inp').value.trim();

    if (query.length === 0 && Object.values(filters).every(f => f === '')) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    const params = new URLSearchParams({ q: query, ...filters });

    fetch(`/uoc-sports/public/api/search-reservation.php?${params.toString()}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Reservation ID</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>User Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                var className = null;
                data.forEach(r => {
                    if (r.payment_status == "COMPLETE"){
                        className = "pay-complete"
                    }
                    else{
                        className = "pay-incomplete"
                    }
                    html += `
                        <tr class="${className}">
                            <td>${r.booking_id}</td>
                            <td>${r.user_name}</td>
                            <td>${r.date}</td>
                            <td>${r.facility_id}</td>
                            <td>${r.user_type}</td>
                            <td>
                                <a href="./reservation.php?id=${r.reservation_id}" class="action-link" title="View Reservation">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No reservations found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
}
</script>