<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-portal.css);
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .page-container {
            flex: 1;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px 40px;
        }

        .portal-card {
            min-height: 75vh;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            height: 100%;
        }

        #reserve-equipment-form {
            gap: 0.5rem;
        }

        @media (max-width: 1024px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
            .page-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

    <div class="page-container">
        <div class="grid-layout">
            <!-- Reserved Items Section (Now on Left) -->
            <div class="portal-card">
                <section id="reserved-section" class="reserved-section">
                    <h2><i class="fas fa-box-open"></i> My Reserved Items</h2>
                    <div class="reserved-container" id="reserved-container">
                        <p>Loading reserved items...</p>
                    </div>
                </section>
            </div>

            <!-- Reserve Equipment Form (Now on Right) -->
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
                                <thead><tr><th>Date</th><th>Start</th><th>End</th><th>By</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="input-div">
                            <label for="student-id"><i class="fas fa-id-card"></i> Student ID</label>
                            <input type="text" id="student-id" name="student_id" value="<?= htmlspecialchars($data['student_id'] ?? '')?>" readonly>
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

                        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle"></i> Reserve</button>
                        <div id="reserve-message"></div>
                    </form>
                </section>
            </div>
        </div>
    </div>

    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

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
                    if (qty > 10) availability.textContent = `${qty} available`;
                    else if (qty > 0) availability.textContent = `${qty} left`;
                    else availability.textContent = "Unavailable";
                    
                    li.appendChild(img); li.appendChild(details); li.appendChild(availability);
                    li.addEventListener("click", () => selectEquipment(eq));
                    suggestions.appendChild(li);
                });
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

        // Load Reserved Items
        function loadMyReservedItems() {
            fetch("/uoc-sports/public/reserve-equipments/view")
                .then(res => res.json())
                .then(response => {
                    const container = document.getElementById("reserved-container");
                    if(!container) return;
                    container.innerHTML = "";
                    const data = response.data;
    
                    if (!data || data.length === 0) {
                        container.innerHTML = "<p class='no-reservations'><i class='fas fa-inbox'></i> No active reservations.</p>";
                        return;
                    }
    
                    // Show max 3 active
                    data.slice(0, 3).forEach(item => {
                        const statusClass = item.status.toLowerCase();
                        container.innerHTML += `
                            <div class="reserved-item">
                                <img src="/uoc-sports/public/images/equipment-types/${item.image_name}" 
                                     alt="${item.equipment_name}"
                                     onerror="this.src='https://via.placeholder.com/50?text=${item.equipment_name.charAt(0)}';">
                                <div class="reserved-details">
                                    <h3>${item.equipment_name}</h3>
                                    <p><i class="fas fa-clock"></i> ${item.start_time} - ${item.end_time}</p>
                                    <span class="status-badge ${statusClass}">${item.status}</span>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(() => {
                    const container = document.getElementById("reserved-container");
                    if(container) container.innerHTML = "<p class='no-reservations'>Error loading items.</p>";
                });
        }
        
        loadMyReservedItems();

        document.getElementById("reserve-equipment-form").addEventListener("submit", async e => {
            e.preventDefault();
            msg.textContent = ""; msg.className = "";
            if (!selectedEquipmentId) {
                msg.textContent = "Please select equipment from suggestions.";
                msg.classList.add("error");
                return;
            }
            const formData = new FormData(e.target);
            formData.append("equipment_id", selectedEquipmentId);

            const res = await fetch("/uoc-sports/public/reserve-equipments/add", { method: "POST", body: formData });
            const result = await res.json();
            msg.textContent = result.message;
            msg.classList.add(result.status === "success" ? "success" : "error");

            if (result.status === "success") {
                e.target.reset();
                loadReservedTimes(selectedEquipmentId);
                loadMyReservedItems(); // Refresh the reserved items list
            }
        });
    });
    </script>
</body>
</html>
