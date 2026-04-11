<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Reservation | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/facility-reservation-page.css?v=3.17);
        @import url(/uoc-sports/public/css/general/footer.css);

        .parallel-booking-alert {
            display: none;
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .parallel-booking-alert i {
            font-size: 1.1rem;
        }

        .parallel-booking-alert.active {
            display: flex;
        }
    </style>
</head>
<body>
    <?php require '../app/views/templates/general/header.php'; ?>

    <div class="facility-page-wrapper">
        
        <!-- LEFT PANEL: DATA & SEARCH -->
        <div class="left-panel">
            <!-- Search Facilities Section -->
            <section class="section facility-search">
                <h3><i class="fas fa-search"></i> Search Facility Rates</h3>
                <input 
                    type="text" 
                    id="search_facility_name" 
                    placeholder="Type a facility name (e.g., Cricket Oval, Tennis Court)..."
                    oninput="searchFacilities()"
                />

                <div id="suggestions" class="suggestions"></div>
                <div id="facilityDetails" class="facility-details hidden"></div>
            </section>

            <!-- Monthly Bookings Calendar Section -->
            <section class="section monthly-calendar-section">
                <div class="calendar-header">
                    <h3><i class="fas fa-calendar-alt"></i> Booking Overview</h3>
                    <div class="calendar-nav">
                        <button type="button" onclick="changeCalendarMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                        <span id="calendarMonthLabel"></span>
                        <button type="button" onclick="changeCalendarMonth(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div id="monthlyCalendar" class="monthly-calendar-grid"></div>
                <div class="calendar-legend">
                    <span class="legend-item"><span class="legend-dot booked"></span> Booked</span>
                    <span class="legend-item"><span class="legend-dot today"></span> Today</span>
                </div>
            </section>
        </div>

        <!-- RIGHT PANEL: RESERVATION FORM -->
        <div class="right-panel">
            <section class="section facility-reservation-container">
                <h3><i class="fas fa-calendar-plus"></i> Reserve a Facility</h3>

                <div id="parallelBookingAlert" class="parallel-booking-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Someone else is booking this facility at the same time. Please be careful when selecting a time slot. Refresh before selecting a date and timeslot.</span>
                </div>

                <form id="facilityReservationForm" onsubmit="submitReservation(event)">
                    <div class="form-row split-row">
                        <div class="form-group">
                            <label for="facility_id"><i class="fas fa-building"></i> Select Facility</label>
                            <select id="facility_id" name="facility_id" required>
                                <option value="">-- Choose a facility --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date"><i class="fas fa-calendar"></i> Date</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                    </div>

                    <div class="form-row price-row">
                        <label><i class="fas fa-tag"></i> Base Price</label>
                        <div class="price-display" id="priceDisplay">Rs. 0.00</div>
                    </div>

                    <div id="reservationChartContainer" class="reservation-chart-container hidden">
                        <div class="chart-header">
                            <h4>Current Reservations</h4>
                            <div class="chart-legend">
                                <span class="legend-item"><span class="legend-box available"></span>Available</span>
                                <span class="legend-item"><span class="legend-box interested"></span>Someone Interested</span>
                                <span class="legend-item"><span class="legend-box disabled"></span>Not Available</span>
                                <span class="legend-item"><span class="legend-box reserved"></span>Reserved</span>
                            </div>
                        </div>
                        <div class="reservation-chart" id="reservationChart"></div>
                    </div>

                    <div class="form-row">
                        <label for="slot_id"><i class="fas fa-clock"></i> Select Time Slot</label>
                        <select id="slot_id" name="slot_id" required></select>
                    </div>

                    <div class="form-row">
                        <label for="purpose"><i class="fas fa-pen"></i> Purpose</label>
                        <textarea id="purpose" name="purpose" maxlength="300" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Reserve & Pay Now
                    </button>
                </form>
                
                <div id="reservationMessage"></div>
            </section>
        </div>
    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>
</body>
<script>
    var currentPage = document.getElementById("nav-res");
    currentPage.classList.add("active") 
</script>

<script>
/* -------------------- MAIN SCRIPT ----------------------- */
const dateInput = document.getElementById("date");

const today = new Date();
const maxDate = new Date();
maxDate.setDate(today.getDate() + 10);

const toISO = d => d.toISOString().split('T')[0];

dateInput.min = toISO(today);
dateInput.max = toISO(maxDate);

dateInput.addEventListener("change", () => {
  const min = dateInput.min;
  const max = dateInput.max;
  const value = dateInput.value;

  if (value < min || value > max) {
    alert("Date out of allowed range! Date should be between " + min + " to " + max + ".");
    dateInput.value = ""; // force reset
  }
});

/* -------------------- BASIC CONFIG ----------------------- */
let timeout = null;
let currentPrice = 0;
let facilityPrices = [];

const API_BASE = "/uoc-sports/public/api/get-facility-rates.php";
const BOOKING_API = "/uoc-sports/public/create-facility-booking";
const SLOTS_API = "/uoc-sports/public/get-reserved-slots";
const RESERVATIONS_API = "/uoc-sports/public/reserve-facilities/view-reservations";
const MONTHLY_BOOKINGS_API = "/uoc-sports/public/reserve-facilities/monthly-bookings";

let currentCalendarDate = new Date();

document.addEventListener("DOMContentLoaded", () => {
    loadFacilities();
    renderMonthlyCalendar();
    
    // Add event listeners for both fields to trigger updates
    document.getElementById("date").addEventListener("change", handleFacilityChange);
    document.getElementById("facility_id").addEventListener("change", handleFacilityChange);
});

/* -------------------- HEARTBEAT LOGIC ----------------------- */
let heartbeatInterval = null;
let slotRefreshInterval = null;
let currentSelectedSlot = null; // Track which slot the user clicked on the chart
const HEARTBEAT_API = "/uoc-sports/public/reserve-facilities/heartbeat";

function startHeartbeat(facilityId) {
    stopHeartbeat();
    
    // Function to perform the heartbeat
    const performHeartbeat = async () => {
        try {
            const formData = new FormData();
            formData.append("facility_id", facilityId);
            
            // Send selected date and slot with heartbeat
            const selectedDate = document.getElementById("date").value;
            if (selectedDate) formData.append("date", selectedDate);
            if (currentSelectedSlot) formData.append("slot", currentSelectedSlot);

            const res = await fetch(HEARTBEAT_API, {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            const alertBox = document.getElementById("parallelBookingAlert");
            if (data.parallel_booking) {
                alertBox.classList.add("active");
            } else {
                alertBox.classList.remove("active");
            }
        } catch (e) {
            console.error("Heartbeat error:", e);
        }
    };

    // Function to refresh chart slot colors from heartbeat data
    const refreshSlotColors = async () => {
        try {
            const formData = new FormData();
            formData.append("facility_id", facilityId);
            
            const selectedDate = document.getElementById("date").value;
            if (selectedDate) formData.append("date", selectedDate);
            if (currentSelectedSlot) formData.append("slot", currentSelectedSlot);

            const res = await fetch(HEARTBEAT_API, {
                method: "POST",
                body: formData
            });
            const data = await res.json();

            // Update chart slot colors in real-time
            if (data.slot_interest || data.booked_slots) {
                updateChartSlotColors(data.slot_interest || {}, data.booked_slots || {});
            }
        } catch (e) {
            console.error("Slot refresh error:", e);
        }
    };

    // Fire heartbeat immediately, then every 1s
    performHeartbeat();
    heartbeatInterval = setInterval(performHeartbeat, 1000);
    
    // Refresh slot colors every 5s
    refreshSlotColors();
    slotRefreshInterval = setInterval(refreshSlotColors, 5000);
}

function stopHeartbeat() {
    if (heartbeatInterval) {
        clearInterval(heartbeatInterval);
        heartbeatInterval = null;
    }
    if (slotRefreshInterval) {
        clearInterval(slotRefreshInterval);
        slotRefreshInterval = null;
    }
    const alertBox = document.getElementById("parallelBookingAlert");
    if (alertBox) alertBox.classList.remove("active");
}

/* -------------------- UPDATE CHART SLOT COLORS IN REAL-TIME ----------------------- */
function updateChartSlotColors(slotInterest, bookedSlots) {
    const slotTypes = ['MORNING', 'AFTERNOON', 'FULL'];
    const allDateColumns = document.querySelectorAll('.date-column');
    
    allDateColumns.forEach(col => {
        const dateId = col.id.replace('col-', ''); // e.g. "2026-02-20"
        const slots = col.querySelectorAll('.slot');
        
        slots.forEach((slotEl, index) => {
            const slotType = slotTypes[index];
            if (!slotType) return;
            
            // Skip slots the current user has selected
            if (slotEl.classList.contains('selected')) return;
            // Skip disabled slots
            if (slotEl.classList.contains('disabled')) return;
            
            const isBooked = bookedSlots[dateId] && bookedSlots[dateId].includes(slotType);
            const isInterested = slotInterest[dateId] && slotInterest[dateId].includes(slotType);
            
            // Remove previous real-time classes
            slotEl.classList.remove('interested', 'taken');
            
            if (isBooked) {
                slotEl.classList.add('taken');
                slotEl.title = 'Reserved';
                slotEl.removeAttribute('onclick');
            } else if (isInterested) {
                slotEl.classList.add('interested');
                slotEl.title = 'Someone is considering this slot';
            } else if (!slotEl.classList.contains('taken')) {
                slotEl.classList.add('available');
                slotEl.title = 'Available';
            }
        });
    });
}

/* -------------------- HANDLE FACILITY CHANGE ----------------------- */
async function handleFacilityChange() {
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const chartContainer = document.getElementById("reservationChartContainer");
    const slotSelect = document.getElementById("slot_id");

    // Reset if no facility selected
    if (!facilityId) {
        chartContainer.classList.add("hidden");
        document.getElementById("priceDisplay").textContent = "Rs. 0.00";
        slotSelect.innerHTML = '<option value="">Select facility first</option>';
        stopHeartbeat();
        return;
    }

    const facility = facilityPrices.find(f => f.id == facilityId);
    if (!facility) return;

    // Start heartbeat immediately if facility is selected
    startHeartbeat(facilityId);

    if (!date) {
        chartContainer.classList.add("hidden");
        document.getElementById("priceDisplay").textContent = "Select a date";
        slotSelect.innerHTML = '<option value="">Select date first</option>';
        return;
    }

    // Determine if date is working day
    const dayOfWeek = new Date(date).getDay(); // 0=Sun, 6=Sat
    const isWorkingDay = dayOfWeek >= 1 && dayOfWeek <= 5;

    // Calculate base price for practice slot (this will be updated when slot is selected)
    currentPrice = parseFloat(facility.practice_working_hours || 0);
    if (!isWorkingDay) {
        currentPrice = parseFloat(facility.practice_other_hours || 0);
    }

    // Display base price
    document.getElementById("priceDisplay").textContent = `Rs. ${currentPrice.toFixed(2)}`;

    // Load reservation chart and slots
    await generateReservationChart(facilityId, date);
    await loadSlots();
    chartContainer.classList.remove("hidden");
}

/* -------------------- LOAD SLOTS ----------------------- */
async function loadSlots() {
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const slotSelect = document.getElementById("slot_id");

    if (!facilityId || !date) {
        slotSelect.innerHTML = '<option value="">Select facility and date first</option>';
        return;
    }

    try {
        const res = await fetch(`${SLOTS_API}?facility_id=${facilityId}&date=${date}`);
        const slots = await res.json();

        slotSelect.innerHTML = '<option value="">-- Choose a slot --</option>';

        if (!slots || slots.length === 0) {
            slotSelect.innerHTML += '<option disabled>No available slots</option>';
            return;
        }

        slots.forEach(slot => {
            slotSelect.innerHTML += `
                <option value="${slot.id}" data-price="${slot.price}">
                    ${slot.type} - Rs. ${parseFloat(slot.price).toFixed(2)}
                </option>
            `;
        });

    } catch (e) {
        console.error("Error loading slots:", e);
        slotSelect.innerHTML = '<option disabled>Error loading slots</option>';
    }
}

/* -------------------- UPDATE PRICE WHEN SLOT CHANGES ----------------------- */
document.addEventListener("change", (e) => {
    if (e.target.id === "slot_id") {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const price = selectedOption.getAttribute("data-price") || "0";
        currentPrice = parseFloat(price);
        document.getElementById("priceDisplay").textContent = `Rs. ${currentPrice.toFixed(2)}`;
    }
});

/* -------------------- GENERATE RESERVATION CHART ----------------------- */
/* -------------------- GENERATE RESERVATION CHART ----------------------- */
async function generateReservationChart(facilityId, selectedDate) {
    const chart = document.getElementById("reservationChart");
    
    // Default to today if no date selected
    const dateParam = selectedDate || new Date().toISOString().split('T')[0];

    try {
        const res = await fetch(`/uoc-sports/public/reserve-facilities/chart?facility_id=${facilityId}&date=${dateParam}`);
        const response = await res.json();

        // Handle both old (plain array) and new (object with chart_data) response formats
        const reservations = Array.isArray(response) ? response : (response.chart_data || []);
        const isParallel = !Array.isArray(response) && response.parallel_booking === true;


        if (!reservations || reservations.length === 0) {
            chart.innerHTML = "<p>No reservations data available.</p>";
            return;
        }

        let html = '<div class="chart-wrapper">';
        
        // Fixed Legend Column
        html += `
            <div class="chart-legend-col">
                <div class="legend-header-placeholder"></div>
                <div class="legend-label">Morning</div>
                <div class="legend-label">Afternoon</div>
                <div class="legend-label">Full Day</div>
            </div>
        `;

        // Scrollable Days Container
        html += '<div class="date-columns-container" id="daysContainer">';

        const slotTypes = ['MORNING', 'AFTERNOON', 'FULL'];

        reservations.forEach(day => {
            const isSelected = day.date === selectedDate;
            const highlightClass = isSelected ? 'highlighted' : '';
            
            html += `<div class="date-column ${highlightClass}" id="col-${day.date}">
                <div class="date-label">${formatDateShort(day.date)}</div>`;

            const facility = facilityPrices.find(f => f.id == facilityId);

            slotTypes.forEach(type => {
                const isTaken = day.slots[type] || false;
                let statusClass = isTaken ? 'taken' : 'available';
                let title = isTaken ? 'Reserved' : 'Available';
                let onclick = '';

                // Check if slot is strictly valid for this facility
                if (!isSlotValid(facility, type, day.date)) {
                   statusClass = 'disabled';
                   title = 'Not Available';
                } else if (!isTaken) {
                   onclick = `onclick="selectSlot(this, '${type}', '${day.date}')"`;
                }
                
                html += `<div class="slot ${statusClass}" title="${title}" ${onclick}></div>`;
            });

            html += '</div>';
        });

        html += '</div></div>'; // Close daysContainer and chart-wrapper
        chart.innerHTML = html;

        // Scroll to highlighted column
        if (selectedDate) {
            setTimeout(() => {
                const col = document.getElementById(`col-${selectedDate}`);
                const container = document.getElementById("daysContainer");
                if (col && container) {
                    // Center the selected day
                    const scrollLeft = col.offsetLeft - container.offsetLeft - (container.clientWidth / 2) + (col.clientWidth / 2);
                    container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
                }
            }, 100);
        }

    } catch (e) {
        console.error("Error loading chart:", e);
        chart.innerHTML = "<p>Error loading chart.</p>";
    }
}

/* -------------------- SLOT VALIDATION HELPER ----------------------- */
function isSlotValid(facility, slotType, dateStr) {
    if (!facility) return true; // Fallback

    const d = new Date(dateStr);
    const day = d.getDay();
    const isWorking = (day >= 1 && day <= 5);

    // Logic based on facility rates presence
    // If price is 0 or null, we assume slot is not offered
    
    if (slotType === 'MORNING' || slotType === 'AFTERNOON') {
        const price = isWorking ? facility.practice_working_hours : facility.practice_other_hours;
        return price && parseFloat(price) > 0;
    } 
    
    if (slotType === 'FULL') {
        const price = isWorking ? facility.tournament_full_day_working : facility.tournament_full_day_other;
        return price && parseFloat(price) > 0;
    }

    return true;
}

/* -------------------- HANDLE SLOT SELECTION ----------------------- */
function selectSlot(el, slotId, dateStr) {
    // Toggle Logic: If clicking the same slot, deselect it
    if (el.classList.contains('selected')) {
        deselectSlot();
        return;
    }

    // Track selected slot for heartbeat
    currentSelectedSlot = slotId;

    // 1. Visual Feedback
    document.querySelectorAll('.slot.selected').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');

    // 2. Update Date Input (if different)
    const dateInput = document.getElementById('date');
    const dateChanged = (dateInput.value !== dateStr);
    
    if (dateChanged) {
        dateInput.value = dateStr;
        
        // Reload slots for new date, then select
        loadSlots().then(() => {
             setSlotValue(slotId, el);
        });
    } else {
         setSlotValue(slotId, el);
    }
}

function deselectSlot() {
    document.querySelectorAll('.slot.selected').forEach(s => s.classList.remove('selected'));
    currentSelectedSlot = null; // Clear slot tracking
    const slotSelect = document.getElementById('slot_id');
    if (slotSelect) {
        slotSelect.value = "";
        // Reset price display
        const event = new Event('change');
        slotSelect.dispatchEvent(event);
    }
}

// Click Outside to Deselect
document.addEventListener('click', function(e) {
    const form = document.getElementById('facilityReservationForm');
    const chartContainer = document.getElementById('reservationChartContainer');
    
    // If click is NOT inside the form, deselect
    // This allows clicking "Purpose", "Submit", etc. without deselecting
    // But clicking Left Panel or Background deselects
    if (form && !form.contains(e.target)) {
        deselectSlot();
    }
});

function setSlotValue(slotId, el) {
    const slotSelect = document.getElementById('slot_id');
    if (!slotSelect) return;
    
    slotSelect.value = slotId;
    
    // Trigger change to update price display
    const event = new Event('change');
    slotSelect.dispatchEvent(event);
}

function formatDateShort(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const days = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
    const day = days[date.getDay()];
    const dateNum = date.getDate();
    return `<span class="day-name">${day}</span><span class="day-num">${dateNum}</span>`;
}

/* -------------------- FLOATING MESSAGES ----------------------- */
function showFloatingMessage(msg, type = "success") {
    const div = document.createElement("div");
    div.className = `floating-msg ${type}`;
    div.innerText = msg;

    document.body.appendChild(div);

    setTimeout(() => {
        div.style.opacity = "0";
        setTimeout(() => div.remove(), 500);
    }, 2500);
}


/* -------------------- FACILITY SEARCH ----------------------- */
async function searchFacilities() {
    const name = document.getElementById("search_facility_name").value.trim();
    const suggestionBox = document.getElementById("suggestions");
    const detailsBox = document.getElementById("facilityDetails");

    if (!name) {
        suggestionBox.innerHTML = "";
        detailsBox.classList.add("hidden");
        return;
    }

    clearTimeout(timeout);
    timeout = setTimeout(async () => {
        try {
            const res = await fetch(`${API_BASE}?facility_name=${encodeURIComponent(name)}`);
            const results = await res.json();

            if (!results || results.length === 0) {
                suggestionBox.innerHTML = `<p class="no-results">No facilities found.</p>`;
                return;
            }

            suggestionBox.innerHTML = results
                .map(r => `
                    <div class="facility-card"
                        onclick='showDetails(${JSON.stringify(r).replace(/'/g, "&#39;")})'>
                        <h4>${r.facility_name}</h4>
                        <p class="type">${r.facility_type.replace("_", " ")}</p>
                    </div>
                `)
                .join('');

        } catch (e) {
            suggestionBox.innerHTML = `<p class="error">Error fetching facilities.</p>`;
        }
    }, 400);
}

/* -------------------- SHOW FACILITY DETAILS (Modal Popup) ----------------------- */
function showDetails(data) {
    // Hide suggestions
    document.getElementById("suggestions").innerHTML = "";
    
    // Create modal overlay
    let modal = document.getElementById("ratesModal");
    if (!modal) {
        modal = document.createElement("div");
        modal.id = "ratesModal";
        modal.className = "rates-modal-overlay";
        document.body.appendChild(modal);
    }
    
    modal.innerHTML = `
        <div class="rates-modal-content">
            <button class="rates-modal-close" onclick="closeRatesModal()">&times;</button>
            <div class="facility-info">
                <h3>${data.facility_name}</h3>
                <p><strong>Type:</strong> ${data.facility_type.replace("_"," ")}</p>
                ${data.capacity ? `<p><strong>Capacity:</strong> ${data.capacity}</p>` : ''}

                <div class="rate-grid">
                    <div><strong>Practice (Working Days):</strong> <span>${formatRate(data.practice_working_hours)}</span></div>
                    <div><strong>Practice (Other Days):</strong> <span>${formatRate(data.practice_other_hours)}</span></div>
                    <div><strong>Tournament Full Day (Working):</strong> <span>${formatRate(data.tournament_full_day_working)}</span></div>
                    <div><strong>Tournament Half Day (Working):</strong> <span>${formatRate(data.tournament_half_day_working)}</span></div>
                    <div><strong>Tournament Full Day (Other Days):</strong> <span>${formatRate(data.tournament_full_day_other)}</span></div>
                    <div><strong>Tournament Half Day (Other Days):</strong> <span>${formatRate(data.tournament_half_day_other)}</span></div>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.add("active");
    
    // Close on overlay click
    modal.onclick = (e) => {
        if (e.target === modal) closeRatesModal();
    };
}

function closeRatesModal() {
    const modal = document.getElementById("ratesModal");
    if (modal) modal.classList.remove("active");
    // Clear search input
    document.getElementById("search_facility_name").value = "";
}

function formatRate(val) {
    return val ? `Rs. ${parseFloat(val).toFixed(2)}` : "-";
}

/* -------------------- LOAD FACILITIES INTO DROPDOWN ----------------------- */
async function loadFacilities() {
    try {
        const res = await fetch(API_BASE);
        const data = await res.json();

        facilityPrices = data;

        const select = document.getElementById("facility_id");
        select.innerHTML = '<option value="">-- Choose a facility --</option>';

        if (data && Array.isArray(data)) {
            data.forEach(f => {
                select.innerHTML += `<option value="${f.id}">${f.facility_name}</option>`;
            });
        }
    } catch (e) {
        console.error("Error loading facilities:", e);
    }
}

/* -------------------- SUBMIT RESERVATION ----------------------- */
async function submitReservation(e) {
    e.preventDefault();

    const form = document.getElementById("facilityReservationForm");
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const slotId = document.getElementById("slot_id").value;
    const purpose = document.getElementById("purpose").value;
    const msg = document.getElementById("reservationMessage");

    if (!facilityId || !date || !slotId || !purpose) {
        msg.innerHTML = `<div class="error">✘ Please fill all fields</div>`;
        msg.style.display = "block";
        return;
    }

    try {
        const formData = new FormData();
        formData.append("facility_id", facilityId);
        formData.append("date", date);
        formData.append("slot_id", slotId);
        formData.append("purpose", purpose);

        const response = await fetch(BOOKING_API, {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            // Redirect to payment page immediately
            showFloatingMessage("Reservation created! Redirecting to payment...", "success");
            window.location.href = `/uoc-sports/public/payment?booking_id=${result.booking_id}`;
        } else {
            // Check if user needs to login
            if (result.redirect) {
                window.location.href = result.redirect;
            } else {
                msg.innerHTML = `<div class="error">✘ ${result.message}</div>`;
                msg.style.display = "block";
                showFloatingMessage(result.message, "error");
            }
        }
    } catch (err) {
        msg.innerHTML = `<div class="error">✘ Error: ${err.message}</div>`;
        msg.style.display = "block";
        showFloatingMessage("Booking error. Please try again.", "error");
    }
}

// -------------------- AUTO-REFRESH EVERY 10 MINUTES ---------------------
let chartRefreshInterval = null;

function startChartAutoRefresh() {
    if (chartRefreshInterval) clearInterval(chartRefreshInterval);
    chartRefreshInterval = setInterval(async () => {
        const facilityId = document.getElementById('facility_id').value;
        const date = document.getElementById('date').value;
        if (facilityId && date) {
            await generateReservationChart(facilityId, date);
        }
    }, 10 * 60 * 1000); // 10 minutes
}

// Start auto-refresh when both facility and date are set
document.getElementById('facility_id').addEventListener('change', () => {
    const facilityId = document.getElementById('facility_id').value;
    const date = document.getElementById('date').value;
    if (facilityId && date) startChartAutoRefresh();
});
document.getElementById('date').addEventListener('change', () => {
    const facilityId = document.getElementById('facility_id').value;
    const date = document.getElementById('date').value;
    if (facilityId && date) startChartAutoRefresh();
});

// -------------------- MONTHLY CALENDAR OVERVIEW ---------------------
async function renderMonthlyCalendar() {
    const calendarEl = document.getElementById('monthlyCalendar');
    const labelEl = document.getElementById('calendarMonthLabel');
    
    const month = currentCalendarDate.getMonth() + 1;
    const year = currentCalendarDate.getFullYear();
    
    labelEl.textContent = currentCalendarDate.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    try {
        const response = await fetch(`${MONTHLY_BOOKINGS_API}?month=${month}&year=${year}`);
        const result = await response.json();
        
        if (!result.success) throw new Error(result.message);
        
        const bookings = result.data; // Grouped by date
        
        // Build Grid
        const firstDay = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        
        let html = `
            <div class="calendar-grid-header">Sun</div>
            <div class="calendar-grid-header">Mon</div>
            <div class="calendar-grid-header">Tue</div>
            <div class="calendar-grid-header">Wed</div>
            <div class="calendar-grid-header">Thu</div>
            <div class="calendar-grid-header">Fri</div>
            <div class="calendar-grid-header">Sat</div>
        `;
        
        // Padding
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day padding"></div>';
        }
        
        // Days
        const todayStr = new Date().toISOString().split('T')[0];
        
        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const isToday = (dateStr === todayStr);
            const dayBookings = bookings[dateStr] || [];
            
            const bookedClass = dayBookings.length > 0 ? 'booked' : '';
            const todayClass = isToday ? 'today' : '';
            
            // Build tooltip content
            let tooltip = '';
            if (dayBookings.length > 0) {
                tooltip = 'Bookings:\n' + dayBookings.map(b => `- ${b.facility_name} (${b.slot})`).join('\n');
            }
            
            html += `
                <div class="calendar-day ${bookedClass} ${todayClass}" title="${tooltip}" onclick="selectDateFromCalendar('${dateStr}', ${dayBookings.length > 0})">
                    <span class="day-number">${d}</span>
                    ${dayBookings.length > 0 ? `<span class="booking-indicator">${dayBookings.length}</span>` : ''}
                </div>
            `;
        }
        
        calendarEl.innerHTML = html;
        
    } catch (e) {
        console.error("Calendar Error:", e);
        calendarEl.innerHTML = `<p class="error">Failed to load calendar</p>`;
    }
}

function changeCalendarMonth(offset) {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() + offset);
    renderMonthlyCalendar();
}

function selectDateFromCalendar(dateStr, isBooked) {
    if (isBooked) {
        window.location.href = '/uoc-sports/public/my-bookings?date=' + dateStr;
        return;
    }
    const dateInput = document.getElementById('date');
    dateInput.value = dateStr;
    
    // Trigger update
    handleFacilityChange();
}

</script>
</html>