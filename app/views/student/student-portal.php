<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-portal.css);
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        
    </style>
</head>
<body class="">
    <?php
        $userModel = new User();
        $student_id = $userModel->getStudentId($_SESSION['user_id']);
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/student/sub_header.php';
    ?>

    <div class="student-portal-wrapper">
        <!-- Card 1: Enrolled Sports (Top Left) -->
        <div class="portal-card">
            <section id="enrolled-sports-section">
                <h2><i class="fas fa-medal"></i> My Enrolled Sports</h2>
                <div id="enrolled-sports-list" class="sports-grid"></div>
                <div id="enroll-message"></div>
            </section>
        </div>

        <!-- Card 2: Available Sports (Top Right) -->
        <div class="portal-card">
            <section id="available-sports-section">
                <h2><i class="fas fa-plus-circle"></i> Available Sports</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="sport-search" placeholder="Search for sports to enroll..." autocomplete="off">
                </div>
                <div id="available-sports-list" class="sports-grid"></div>
            </section>
        </div>

        <!-- Card 3: Reserved Items (Bottom Left) -->
        <div class="portal-card">
            <section id="reserved-section" class="reserved-section">
                <h2><i class="fas fa-box-open"></i> Reserved Items</h2>
                <div class="reserved-container" id="reserved-container">
                    <p>Loading reserved items...</p>
                </div>
            </section>
        </div>

        <!-- Card 4: Reserve Equipment (Bottom Right) -->
        <div class="portal-card">
            <section id="reserve-equipment">
                <h2><i class="fas fa-clipboard-list"></i> Reserve Equipment</h2>
                <form id="reserve-equipment-form">
                    <div class="input-div" style="position: relative;">
                        <label for="equipment-search"><i class="fas fa-search"></i> Search Equipment</label>
                        <input type="text" id="equipment-search" name="equipment_name" placeholder="Start typing..." autocomplete="off" required>
                        <ul id="suggestions"></ul>
                    </div>

                    <div id="reserved-times-div" class="reserved-times-box">
                        <h3><i class="fas fa-calendar-alt"></i> Reserved Times</h3>
                        <table id="reserved-times" class="styled-table">
                            <thead>
                                <tr><th>Date</th><th>Start</th><th>End</th><th>By</th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="input-div">
                        <label for="student-id"><i class="fas fa-id-card"></i> Student ID</label>
                        <input type="text" id="student-id" name="student_id" value="<?= htmlspecialchars($student_id['student_id'])?>" readonly>
                    </div>

                    <div class="input-row">
                        <div class="input-div">
                            <label for="date"><i class="fas fa-calendar"></i> Date</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        <div class="input-div">
                            <label for="start-time"><i class="fas fa-clock"></i> Start</label>
                            <input type="time" id="start-time" name="start_time" required>
                        </div>
                        <div class="input-div">
                            <label for="end-time"><i class="fas fa-clock"></i> End</label>
                            <input type="time" id="end-time" name="end_time" required>
                        </div>
                    </div>

                    <div class="input-div">
                        <label for="purpose"><i class="fas fa-bullseye"></i> Purpose</label>
                        <textarea id="purpose" name="purpose" rows="2" placeholder="Purpose..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Reserve
                    </button>
                    <div id="reserve-message"></div>
                </form>
            </section>
        </div>
    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>

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
                    Stay committed. Stay consistent. Make UOC proud!
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeEncouragementModal()">
                    <i class="fas fa-check-circle"></i> I'm Ready!
                </button>
            </div>
        </div>
    </div>
</body>

<script>
    var currentPage = document.getElementById("user_type");
    currentPage.classList.add("active");
</script>

<!-- Enroll Sports Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const enrolledList = document.getElementById("enrolled-sports-list");
    const availableList = document.getElementById("available-sports-list");
    const searchInput = document.getElementById("sport-search");
    const encouragementModal = document.getElementById("encouragementModal");
    
    let availableSports = [];
    let enrolledSports = [];
    let sportsLoaded = false;

    window.closeEncouragementModal = () => {
        encouragementModal.style.display = 'none';
    };

    function showEncouragementModal(sportName) {
        document.getElementById('enrolledSportName').textContent = sportName;
        encouragementModal.style.display = 'flex';
    }

    window.onclick = (event) => {
        if (event.target === encouragementModal) closeEncouragementModal();
    };

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

    function renderEnrolledSports() {
        enrolledList.innerHTML = '';
        
        if (enrolledSports.length === 0) {
            enrolledList.innerHTML = '<p class="no-sports">No enrolled sports yet.</p>';
            return;
        }

        enrolledSports.forEach(sport => {
            const card = document.createElement('div');
            card.className = 'sport-card enrolled';
            card.innerHTML = `
                <div class="sport-icon"><i class="fas fa-medal"></i></div>
                <div class="sport-info">
                    <h4>${sport.sport_name}</h4>
                    <p>Joined: ${formatDate(sport.joined_date)}</p>
                </div>
                <button class="btn btn-danger btn-sm" onclick="unenrollSport('${sport.sport_id}', '${sport.sport_name}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            enrolledList.appendChild(card);
        });
    }

    function renderAvailableSports(sports) {
        availableList.innerHTML = '';
        
        if (sports.length === 0) {
            availableList.innerHTML = '<p class="no-sports">No sports found.</p>';
            return;
        }

        sports.forEach(sport => {
            const card = document.createElement('div');
            card.className = 'sport-card available';
            card.innerHTML = `
                <div class="sport-icon"><i class="fas fa-running"></i></div>
                <div class="sport-info">
                    <h4>${sport.sport_name}</h4>
                </div>
                <button class="btn btn-primary btn-sm" onclick="enrollInSport('${sport.sport_id}', '${sport.sport_name}')">
                    <i class="fas fa-plus"></i>
                </button>
            `;
            availableList.appendChild(card);
        });
    }

    searchInput.addEventListener('input', async () => {
        const query = searchInput.value.toLowerCase().trim();
        
        if (query === '') {
            availableList.innerHTML = '<p class="no-sports">Start typing to search...</p>';
            return;
        }

        if (!sportsLoaded) {
            availableList.innerHTML = '<p class="no-sports">Loading...</p>';
            await loadAvailableSports();
        }

        const filtered = availableSports.filter(sport => 
            sport.sport_name.toLowerCase().includes(query)
        );
        
        renderAvailableSports(filtered);
    });

    window.enrollInSport = async (sportId, sportName) => {
        UI.confirm(`Enroll in ${sportName}?`, async () => {
            const formData = new FormData();
            formData.append('sport_id', sportId);

            try {
                const res = await fetch('/uoc-sports/public/student/enroll-sport', {
                    method: 'POST',
                    body: formData
                });

                const result = await res.json();

                if (result.status === 'success') {
                    showEncouragementModal(sportName);
                    await loadEnrolledSports();
                    sportsLoaded = false;
                    searchInput.value = '';
                    availableList.innerHTML = '<p class="no-sports">Start typing to search...</p>';
                } else {
                    UI.showToast(result.message, 'error');
                }
            } catch (error) {
                UI.showToast('An error occurred', 'error');
            }
        });
    };

    window.unenrollSport = async (sportId, sportName) => {
        UI.confirm(`Unenroll from ${sportName}?`, async () => {
            const formData = new FormData();
            formData.append('sport_id', sportId);

            try {
                const res = await fetch('/uoc-sports/public/student/unenroll-sport', {
                    method: 'POST',
                    body: formData
                });

                const result = await res.json();
                UI.showToast(result.message, result.status);

                if (result.status === 'success') {
                    await loadEnrolledSports();
                    sportsLoaded = false;
                    availableList.innerHTML = '<p class="no-sports">Start typing to search...</p>';
                }
            } catch (error) {
                UI.showToast('An error occurred', 'error');
            }
        });
    };



    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    loadEnrolledSports();
    availableList.innerHTML = '<p class="no-sports">Start typing to search...</p>';
});
</script>

<!-- Reserved Items Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    fetchReservedItems();
});

function fetchReservedItems() {
    fetch("/uoc-sports/public/reserve-equipments/view")
        .then(res => res.json())
        .then(response => {
            const container = document.getElementById("reserved-container");
            container.innerHTML = "";
            const data = response.data;

            if (!data || data.length === 0) {
                container.innerHTML = "<p class='no-reservations'><i class='fas fa-inbox'></i> No reserved items.</p>";
                return;
            }

            data.forEach(item => {
                const statusClass = item.status.toLowerCase();
                container.innerHTML += `
                    <div class="reserved-item">
                        <img src="/uoc-sports/public/images/equipment-types/${item.image_name}" 
                             alt="${item.equipment_name}"
                             onerror="this.src='https://via.placeholder.com/50?text=${item.equipment_name.charAt(0)}';">
                        <div class="reserved-details">
                            <h3>${item.equipment_name}</h3>
                            <p><i class="fas fa-calendar"></i> ${new Date(item.request_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</p>
                            <p><i class="fas fa-clock"></i> ${item.start_time} - ${item.end_time}</p>
                            <span class="status-badge ${statusClass}">${item.status}</span>
                        </div>
                        ${item.status === 'ACTIVE' ? `
                            <button class="cancel-reservation" onclick="cancelReservation('${item.request_id}')">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                `;
            });
        })
        .catch(() => {
            document.getElementById("reserved-container").innerHTML = "<p class='no-reservations'>Error loading items.</p>";
        });
}

function cancelReservation(reservationId) {
    UI.confirm('Cancel this reservation?', () => {
        fetch("/uoc-sports/public/reserve-equipments/cancel", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "reservation_id=" + encodeURIComponent(reservationId)
        })
        .then(res => res.text())
        .then(msg => {
            UI.showToast(msg, 'success');
            fetchReservedItems();
        })
        .catch(() => UI.showToast("Error cancelling reservation.", "error"));
    });
}
</script>

<!-- Reserve Equipment Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("equipment-search");
    const suggestions = document.getElementById("suggestions");
    const msg = document.getElementById("reserve-message");
    const timesDiv = document.getElementById("reserved-times-div");
    const timesTable = document.getElementById("reserved-times").querySelector("tbody");
    let selectedEquipmentId = null;

    suggestions.style.display = "none";

    searchInput.addEventListener("input", async () => {
        const q = searchInput.value.trim();
        if (q.length < 1) {
            suggestions.innerHTML = "";
            suggestions.style.display = "none";
            return;
        }

        const res = await fetch(`/uoc-sports/public/reserve-equipments/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        suggestions.innerHTML = "";
        suggestions.style.display = "none";

        if (data.status === "success" && data.data.length > 0) {
            suggestions.style.display = "block";
            data.data.forEach(eq => {
                const li = document.createElement("li");
                
                const img = document.createElement("img");
                img.className = "suggestion-image";
                img.src = eq.image_name ? `/uoc-sports/public/images/equipment-types/${eq.image_name}` : `https://via.placeholder.com/35?text=${eq.equipment_name.charAt(0)}`;
                img.alt = eq.equipment_name;
                img.onerror = function() { this.src = `https://via.placeholder.com/35?text=${eq.equipment_name.charAt(0)}`; };
                
                const details = document.createElement("div");
                details.className = "suggestion-details";
                details.innerHTML = `<div class="suggestion-name">${eq.equipment_name}</div><div class="suggestion-sport">${eq.sport_name}</div>`;
                
                const availability = document.createElement("div");
                availability.className = "suggestion-availability";
                const qty = parseInt(eq.available_quantity);
                if (qty > 10) {
                    availability.classList.add("availability-high");
                    availability.textContent = `${qty} available`;
                } else if (qty > 0) {
                    availability.classList.add("availability-low");
                    availability.textContent = `${qty} left`;
                } else {
                    availability.classList.add("availability-none");
                    availability.textContent = "Unavailable";
                }
                
                li.appendChild(img);
                li.appendChild(details);
                li.appendChild(availability);
                li.dataset.id = eq.equipment_id;
                li.addEventListener("click", () => selectEquipment(eq));
                suggestions.appendChild(li);
            });
        } else if (data.status === "success" && data.data.length === 0) {
            suggestions.style.display = "block";
            const li = document.createElement("li");
            li.textContent = "No equipment found";
            li.style.color = "#999";
            li.style.cursor = "default";
            suggestions.appendChild(li);
        }
    });

    function selectEquipment(eq) {
        searchInput.value = eq.equipment_name;
        selectedEquipmentId = eq.equipment_id;
        suggestions.innerHTML = "";
        suggestions.style.display = "none";
        loadReservedTimes(eq.equipment_id);
    }

    async function loadReservedTimes(equipmentId) {
        const res = await fetch(`/uoc-sports/public/reserve-equipments/get-times?equipment_id=${equipmentId}`);
        const data = await res.json();
        timesTable.innerHTML = "";

        if (data.status === "success" && data.data.length > 0) {
            timesDiv.style.display = "block";
            data.data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `<td>${row.request_date}</td><td>${row.start_time}</td><td>${row.end_time}</td><td>${row.student_id}</td>`;
                timesTable.appendChild(tr);
            });
        } else {
            timesDiv.style.display = "none";
        }
    }

    document.getElementById("reserve-equipment-form").addEventListener("submit", async e => {
        e.preventDefault();
        msg.textContent = "";
        msg.className = "";

        if (!selectedEquipmentId) {
            msg.textContent = "Please select equipment from suggestions.";
            msg.classList.add("error");
            return;
        }

        const formData = new FormData(e.target);
        formData.append("equipment_id", selectedEquipmentId);

        const res = await fetch("/uoc-sports/public/reserve-equipments/add", {
            method: "POST",
            body: formData
        });

        const result = await res.json();
        UI.showToast(result.message, result.status === "success" ? "success" : "error");

        if (result.status === "success") {
            e.target.reset();
            fetchReservedItems();
            loadReservedTimes(selectedEquipmentId);
        }
    });
});
</script>
</html>