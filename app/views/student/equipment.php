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

        .equipment-selection-box {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px;
            max-height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .equipment-item-card {
            display: grid;
            grid-template-columns: 20px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #fff;
            border-radius: 6px;
            border: 1.5px solid #e9ecef;
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }

        .equipment-item-card:hover {
            border-color: #b39ddb;
            background: #f8f4ff;
        }

        .equipment-item-card.selected {
            border-color: #6a0dad;
            background: #f3eaff;
        }

        .equipment-item-card.unavailable {
            opacity: 0.45;
            pointer-events: none;
        }

        .equipment-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #6a0dad;
            flex-shrink: 0;
        }

        .equipment-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .equipment-name-text {
            font-size: 0.88rem;
            font-weight: 600;
            color: #222;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .equipment-avail-text {
            font-size: 0.72rem;
            color: #888;
            margin-top: 1px;
        }

        .equipment-qty-wrapper {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .equipment-qty-wrapper span {
            font-size: 0.75rem;
            color: #666;
        }

        .equipment-quantity-input {
            width: 52px !important;
            padding: 4px 6px !important;
            border: 1.5px solid #ccc !important;
            border-radius: 5px !important;
            font-size: 0.82rem !important;
            background: #fff !important;
            color: #333 !important;
            text-align: center;
            margin: 0 !important;
        }

        .equipment-quantity-input:disabled {
            background: #f0f0f0 !important;
            color: #aaa !important;
            border-color: #ddd !important;
        }

        .cancel-reservation {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .cancel-reservation:hover {
            background: #ff6b81;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.3);
        }

        .cancel-reservation i {
            font-size: 0.9rem;
        }

        .empty-msg {
            text-align: center;
            padding: 20px;
            opacity: 0.6;
            font-style: italic;
        }
    </style>
</head>
<body class="">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

    <div class="page-container">
        <div class="grid-layout">
            <!-- Reserve Equipment Form (Now on Left) -->
            <div class="portal-card">
                <section id="reserve-equipment">
                    <h2><i class="fas fa-clipboard-list"></i> Reserve Equipment</h2>
                    <form id="reserve-equipment-form">
                        <div class="input-row">
                            <div class="input-div">
                                <label for="sport"><i class="fas fa-running"></i> Sport *</label>
                                <select id="sport" name="sport" required>
                                    <option value="">Select Sport</option>
                                    <?php foreach ($data['sports'] as $sport): ?>
                                        <option value="<?= $sport['sport_id'] ?>"><?= $sport['sport_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-div">
                                <label for="location"><i class="fas fa-map-marker-alt"></i> Reserved Location *</label>
                                <select id="location" name="location" required>
                                    <option value="">Loading locations...</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-row">
                            <div class="input-div">
                                <label for="date"><i class="fas fa-calendar"></i> Date *</label>
                                <input type="date" id="date" name="date" required>
                            </div>
                            <div class="input-div">
                                <label for="start-time"><i class="fas fa-clock"></i> Start *</label>
                                <input type="time" id="start-time" name="start_time" step="1800" required>
                            </div>
                            <div class="input-div">
                                <label for="end-time"><i class="fas fa-clock"></i> End *</label>
                                <input type="time" id="end-time" name="end_time" step="1800" required>
                            </div>
                        </div>

                        <div class="input-div">
                            <label><i class="fas fa-box"></i> Equipment Selection *</label>
                            <div id="equipment-selection-container" class="equipment-selection-box">
                                <p class="empty-msg">Please select a sport and date first</p>
                            </div>
                        </div>

                        <div class="input-div">
                            <label for="notes"><i class="fas fa-sticky-note"></i> Notes</label>
                            <textarea id="notes" name="notes" rows="2" placeholder="Additional notes..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle" style="margin-right: 6px;"></i>Reserve</button>
                        <div id="reserve-message"></div>
                    </form>
                </section>
            </div>

            <!-- Reserved Items Section (Now on Right) -->
            <div class="portal-card">
                <section id="reserved-section" class="reserved-section">
                    <h2><i class="fas fa-box-open"></i> My Reserved Items</h2>
                    <div class="reserved-container" id="reserved-container">
                        <p>Loading reserved items...</p>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-question-circle"></i>
                <h3 id="confirmModalTitle">Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-primary" id="confirmModalBtn">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const sportSelect = document.getElementById("sport");
        const equipmentContainer = document.getElementById("equipment-selection-container");
        const reserveForm = document.getElementById("reserve-equipment-form");
        const msg = document.getElementById("reserve-message");
        const confirmModal = document.getElementById("confirmModal");

        window.closeConfirmModal = () => {
            confirmModal.style.display = 'none';
        };

        function showConfirmModal(title, message, onConfirm) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = message;
            confirmModal.style.display = 'flex';
            
            const confirmBtn = document.getElementById('confirmModalBtn');
            confirmBtn.onclick = () => {
                closeConfirmModal();
                onConfirm();
            };
        }

        window.onclick = (event) => {
            if (event.target === confirmModal) closeConfirmModal();
        };

        // Load equipment only when BOTH sport and date are selected
        function tryLoadEquipment() {
            const sportId = sportSelect.value;
            const date = document.getElementById('date').value;
            if (sportId && date) {
                loadEquipmentBySport(sportId);
            } else if (!sportId && !date) {
                equipmentContainer.innerHTML = '<p class="empty-msg">Please select a sport and date first</p>';
            } else if (!sportId) {
                equipmentContainer.innerHTML = '<p class="empty-msg">Please select a sport first</p>';
            } else {
                equipmentContainer.innerHTML = '<p class="empty-msg">Please select a date first</p>';
            }
        }

        sportSelect.addEventListener("change", tryLoadEquipment);

        async function loadLocations() {
            const locationSelect = document.getElementById('location');
            try {
                const res = await fetch("/uoc-sports/public/api/reservation/locations");
                const locations = await res.json();
                
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                if (Array.isArray(locations)) {
                    locations.forEach(loc => {
                        const option = document.createElement('option');
                        option.value = loc.facility_name;
                        option.textContent = loc.facility_name;
                        option.dataset.id = loc.facility_id;
                        locationSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error("Error loading locations:", error);
                locationSelect.innerHTML = '<option value="">Error loading locations</option>';
            }
        }

        loadLocations();

        async function loadEquipmentBySport(sportId) {
            equipmentContainer.innerHTML = '<p class="empty-msg">Loading equipment...</p>';

            try {
                const date = document.getElementById('date').value;
                const startTime = document.getElementById('start-time').value;
                const endTime = document.getElementById('end-time').value;

                let apiUrl = `/uoc-sports/public/api/get-equipment-with-requests.php?sport_id=${sportId}`;
                if (date) apiUrl += `&request_date=${date}`;
                if (startTime) apiUrl += `&start_time=${startTime}`;
                if (endTime) apiUrl += `&end_time=${endTime}`;

                const res = await fetch(apiUrl);
                const data = await res.json();

                equipmentContainer.innerHTML = "";

                if (data.success && data.equipment && data.equipment.length > 0) {
                    data.equipment.forEach(item => {
                        const available = parseInt(item.slot_available_count || item.available_count || 0);
                        
                        const itemCard = document.createElement("div");
                        itemCard.className = "equipment-item-card";
                        if (available <= 0) itemCard.style.opacity = "0.5";

                        itemCard.innerHTML = `
                            <input type="checkbox" name="equipment[]" value="${item.equipment_name}" 
                                   class="equipment-checkbox" id="eq_${item.equipment_id}"
                                   ${available <= 0 ? 'disabled' : ''}>
                            <div class="equipment-info">
                                <span class="equipment-name-text">${item.equipment_name}</span>
                                <span class="equipment-avail-text">${available > 0 ? available + ' available' : 'Out of stock'}</span>
                            </div>
                            <div class="equipment-qty-wrapper">
                                <span>Qty</span>
                                <input type="number" name="quantity[${item.equipment_name}]" 
                                       class="equipment-quantity-input" value="1" min="1" max="${available}" 
                                       id="qty_${item.equipment_id}" disabled>
                            </div>
                        `;

                        // Click whole card to toggle checkbox
                        itemCard.addEventListener("click", (e) => {
                            if (e.target.tagName === 'INPUT') return;
                            const cb = itemCard.querySelector('input[type="checkbox"]');
                            if (!cb.disabled) cb.click();
                        });

                        const checkbox = itemCard.querySelector('input[type="checkbox"]');
                        const qtyInput = itemCard.querySelector('input[type="number"]');

                        checkbox.addEventListener("change", () => {
                            qtyInput.disabled = !checkbox.checked;
                            if (checkbox.checked) {
                                itemCard.classList.add("selected");
                            } else {
                                itemCard.classList.remove("selected");
                            }
                        });

                        equipmentContainer.appendChild(itemCard);
                    });
                } else {
                    equipmentContainer.innerHTML = '<p class="empty-msg">No equipment available for this sport</p>';
                }
            } catch (error) {
                console.error("Error loading equipment:", error);
                equipmentContainer.innerHTML = '<p class="empty-msg" style="color: var(--error-color);">Error loading equipment.</p>';
            }
        }

        // Reload equipment when date/time changes to update availability
        document.getElementById('date').addEventListener('change', tryLoadEquipment);

        const roundTo30 = (timeStr) => {
            if (!timeStr) return "";
            const [hours, minutes] = timeStr.split(':').map(Number);
            const roundedMinutes = minutes < 15 ? 0 : (minutes < 45 ? 30 : 0);
            let roundedHours = (minutes >= 45) ? (hours + 1) % 24 : hours;
            return `${String(roundedHours).padStart(2, '0')}:${String(roundedMinutes).padStart(2, '0')}`;
        };

        ['start-time', 'end-time'].forEach(id => {
            const el = document.getElementById(id);
            el.addEventListener('change', () => {
                const rounded = roundTo30(el.value);
                if (rounded !== el.value) {
                    el.value = rounded;
                }
                
                const sportId = sportSelect.value;
                const date = document.getElementById('date').value;
                if (sportId && date) loadEquipmentBySport(sportId);
            });
        });

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
    
                    function getIconForEquipment(name) {
                        const n = name.toLowerCase();
                        if (n.includes('basketball')) return 'fa-basketball-ball';
                        if (n.includes('football') || n.includes('soccer') || n.includes('futsal')) return 'fa-futbol';
                        if (n.includes('volleyball')) return 'fa-volleyball-ball';
                        if (n.includes('badminton') || n.includes('shuttle') || n.includes('racquet') || n.includes('tennis')) return 'fa-table-tennis';
                        if (n.includes('cricket') || n.includes('baseball') || n.includes('bat')) return 'fa-baseball-ball';
                        if (n.includes('swimming') || n.includes('pool')) return 'fa-swimmer';
                        if (n.includes('gym') || n.includes('weight') || n.includes('dumbbell')) return 'fa-dumbbell';
                        return 'fa-box-open'; // Default icon
                    }

                    data.forEach(item => {
                        const statusClass = item.status.toLowerCase();
                        const iconClass = getIconForEquipment(item.equipment_name);
                        container.innerHTML += `
                            <div class="reserved-item">
                                <div style="width: 50px; height: 50px; border-radius: 8px; background: #f3eaff; color: #6a0dad; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                                    <i class="fas ${iconClass}"></i>
                                </div>
                                <div class="reserved-details">
                                    <h3>${item.equipment_name}</h3>
                                    <p><i class="fas fa-calendar"></i> ${item.request_date}</p>
                                    <p><i class="fas fa-clock"></i> ${item.start_time.substring(0,5)} - ${item.end_time.substring(0,5)}</p>
                                    <div style="display: flex; gap: 15px; font-size: 0.85rem; color: #666; margin-top: 4px;">
                                        <span><i class="fas fa-running"></i> ${item.sport_name || 'N/A'}</span>
                                        <span><i class="fas fa-map-marker-alt"></i> ${item.reserved_location || 'Not specified'}</span>
                                    </div>
                                    <span class="status-badge ${statusClass}">${item.status}</span>
                                </div>
                                <button class="cancel-reservation" data-id="${item.request_id}">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </button>
                            </div>
                        `;
                    });

                    // Add click listeners to cancel buttons
                    document.querySelectorAll(".cancel-reservation").forEach(btn => {
                        btn.addEventListener("click", () => {
                            const requestId = btn.getAttribute("data-id");
                            
                            showConfirmModal(
                                "Cancel Reservation", 
                                "Are you sure you want to cancel this reservation?", 
                                async () => {
                                    const formData = new FormData();
                                    formData.append("request_id", requestId);

                                    try {
                                        const res = await fetch("/uoc-sports/public/reserve-equipments/cancel", {
                                            method: "POST",
                                            body: formData
                                        });
                                        const result = await res.json();
                                        if (result.status === "success") {
                                            loadMyReservedItems();
                                        } else {
                                            alert(result.message);
                                        }
                                    } catch (e) {
                                        alert("Failed to cancel reservation.");
                                    }
                                }
                            );
                        });
                    });
                })
                .catch(() => {
                    const container = document.getElementById("reserved-container");
                    if(container) container.innerHTML = "<p class='no-reservations'>Error loading items.</p>";
                });
        }
        
        loadMyReservedItems();

        // Form Submission
        reserveForm.addEventListener("submit", async e => {
            e.preventDefault();
            msg.textContent = "Processing..."; msg.className = "";
            
            const selectedEq = document.querySelectorAll('input[name="equipment[]"]:checked');
            if (selectedEq.length === 0) {
                msg.textContent = "Please select at least one equipment item.";
                msg.classList.add("error");
                return;
            }

            const formData = new FormData(reserveForm);
            
            try {
                const res = await fetch("/uoc-sports/public/reserve-equipments/add", { 
                    method: "POST", 
                    body: formData 
                });
                const result = await res.json();
                
                msg.textContent = result.message;
                msg.classList.add(result.status === "success" ? "success" : "error");

                if (result.status === "success") {
                    reserveForm.reset();
                    equipmentContainer.innerHTML = '<p class="empty-msg">Please select a sport and date first</p>';
                    loadMyReservedItems();

                    // Auto-clear the success message after 4 seconds
                    setTimeout(() => {
                        msg.style.transition = "opacity 0.5s ease";
                        msg.style.opacity = "0";
                        setTimeout(() => {
                            msg.textContent = "";
                            msg.className = "";
                            msg.style.opacity = "1";
                            msg.style.transition = "";
                        }, 500);
                    }, 4000);
                } else {
                    // Clear error messages after 5 seconds too
                    setTimeout(() => {
                        msg.style.transition = "opacity 0.5s ease";
                        msg.style.opacity = "0";
                        setTimeout(() => {
                            msg.textContent = "";
                            msg.className = "";
                            msg.style.opacity = "1";
                            msg.style.transition = "";
                        }, 500);
                    }, 5000);
                }
            } catch (error) {
                msg.textContent = "An error occurred. Please try again.";
                msg.classList.add("error");
            }
        });
    });
    </script>
</body>
</html>
