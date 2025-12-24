<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Records | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/players-page.css);
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
    <div class="players-grid-container">
        <div class="players-grid-left">
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

                <div class="search-output">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Search Player Records</h3>
                        <p>Use the filters or search to find players</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="players-grid-right">
            <section id="add-match-result">
                <h2>Add Match Result</h2>
                <form id="add-match-form" novalidate>
                    <p class="required-note"><span>*</span> Required fields</p>
                    
                    <!-- Tournament Select -->
                    <div class="input-div">
                        <label for="tournament">Tournament <span class="required">*</span></label>
                        <select id="tournament" name="tournament_id" 
                                aria-required="true" required>
                            <option value="">Loading tournaments...</option>
                        </select>
                    </div>

                    <!-- Sport Select -->
                    <div class="input-div">
                        <label for="sport">Sport <span class="required">*</span></label>
                        <select id="sport" name="sport_id" 
                                aria-required="true" required>
                            <option value="">Select sport</option>
                        </select>
                    </div>

                    <!-- Match Name -->
                    <div class="input-div">
                        <label for="match-name">Match Name <span class="required">*</span></label>
                        <input type="text" id="match-name" name="match_name" 
                               placeholder="Quarter Final / Semi Final..." 
                               aria-required="true" required>
                    </div>

                    <!-- Match Date -->
                    <div class="input-div">
                        <label for="match-date">Match Date <span class="required">*</span></label>
                        <input type="datetime-local" id="match-date" name="match_date" 
                               aria-required="true" required>
                    </div>

                    <!-- Winner (Student) -->
                    <div class="input-div">
                        <label for="winner">Winner (Student)</label>
                        <select id="winner" name="winner_id">
                            <option value="">Select winner (optional)</option>
                        </select>
                    </div>

                    <!-- Dynamic fields container -->
                    <div id="dynamic-fields"></div>

                    <button type="submit" class="btn">Add Result</button>
                    <div id="form-message"></div>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
// Search Player Records Script
const filters = { faculty: '', year: '', sport: '', type: '' };

// Store original button labels for reset
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
    btn.setAttribute('data-original', btn.childNodes[0].textContent.trim());
});

// Toggle dropdown visibility
document.querySelectorAll('.filter-bar .btn').forEach(btn => {
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

<script>
// Add Match Result Script
document.addEventListener("DOMContentLoaded", async () => {
    const tournamentSelect = document.getElementById("tournament");
    const sportSelect = document.getElementById("sport");
    const winnerSelect = document.getElementById("winner");
    const dynamicFields = document.getElementById("dynamic-fields");
    const form = document.getElementById("add-match-form");
    const msg = document.getElementById("form-message");

    // Load tournaments
    try {
        const res = await fetch("admin-sport/get-tournaments");
        const data = await res.json();
        tournamentSelect.innerHTML = '<option value="">Select tournament</option>';
        if(data.status === "success"){
            data.data.forEach(t => {
                const opt = document.createElement("option");
                opt.value = t.tournament_id;
                opt.textContent = t.tournament_name;
                tournamentSelect.appendChild(opt);
            });
        }
        else if (data.status === "empty"){
            tournamentSelect.innerHTML = '<option>' + data.data + '</option>';
        }
    } catch(err){ tournamentSelect.innerHTML = '<option>Error loading tournaments</option>'; }

    // Load sports
    try {
        const res = await fetch("admin-sport/get-sports");
        const data = await res.json();
        sportSelect.innerHTML = '<option value="">Select sport</option>';
        if(data.status === "success"){
            data.data.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.sport_id;
                opt.textContent = s.sport_name;
                sportSelect.appendChild(opt);
            });
        }
    } catch(err){ sportSelect.innerHTML = '<option>Error loading sports</option>'; }

    // Load students for winner select
    try {
        const res = await fetch("admin-sport/get-students");
        const data = await res.json();
        winnerSelect.innerHTML = '<option value="">Select winner (optional)</option>';
        if(data.status === "success"){
            data.data.forEach(s => {
                const opt = document.createElement("option");
                opt.value = s.user_id;
                opt.textContent = s.name;
                winnerSelect.appendChild(opt);
            });
        }
    } catch(err){ winnerSelect.innerHTML = '<option>Error loading students</option>'; }

    // Dynamic fields based on sport
    sportSelect.addEventListener("change", async () => {
        const sportId = sportSelect.value;
        dynamicFields.innerHTML = "<p>Loading fields...</p>";
        if(!sportId) {
            dynamicFields.innerHTML = "";
            return;
        }
        try {
            const res = await fetch(`admin-sport/get-sport-fields?sport_id=${sportId}`);
            const data = await res.json();
            dynamicFields.innerHTML = '';
            if(data.status === "success"){
                data.data.forEach(f => {
                    const div = document.createElement("div");
                    div.classList.add("input-div");
                    div.innerHTML = `<label for="${f.field_name}">${f.field_label} ${f.unit ? '('+f.unit+')' : ''}</label>
                                     <input type="${f.data_type === 'INT' || f.data_type === 'FLOAT' ? 'number' : 'text'}" 
                                            step="${f.data_type==='FLOAT' ? '0.01' : '1'}" 
                                            id="${f.field_name}" 
                                            name="fields[${f.field_name}]">`;
                    dynamicFields.appendChild(div);
                });
            } else dynamicFields.innerHTML = '<p>No fields defined for this sport.</p>';
        } catch(err){ dynamicFields.innerHTML = '<p>Error loading fields.</p>'; }
    });

    // Form submission
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const res = await fetch("admin-tournament/add-result", {
                method: "POST",
                body: formData
            });
            const data = await res.json();
            if(data.status === "success"){
                showNotification("Result added successfully!", "success");
                form.reset();
                dynamicFields.innerHTML = '';
            } else {
                showNotification(data.message || "Failed to add result", "error");
            }
        } catch(err){
            showNotification("Error submitting form!", "error");
        }
    });
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-players");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
