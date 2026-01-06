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
        @import url(/uoc-sports/public/css/general/facility-reservation-page.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .mesh-sporty {
            background: 
                linear-gradient(rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
            min-height: 100vh;
        }
    </style>
</head>
<body class="mesh-sporty">
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

            <!-- My Reservations Section - Calendar View -->
            <section class="section my-reservations-calendar">
                <div class="calendar-header">
                    <h2><i class="fas fa-calendar-alt"></i> My Reservations</h2>
                </div>

                <?php if (!isset($_SESSION['user_id']) && !isset($user_id)): ?>
                    <div class="auth-message">
                        <p>Please <a href="/uoc-sports/public/sign-in">log in</a> to view your reservations.</p>
                    </div>
                <?php else: ?>
                    <div class="user-calendar-container">
                        <div class="calendar-nav">
                            <button class="cal-nav-btn" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                            <span id="calendarMonthLabel">Loading...</span>
                            <button class="cal-nav-btn" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div id="userCalendar" class="user-calendar"></div>
                        <div id="reservationDetails" class="reservation-details">
                            <p class="select-date-msg"><i class="fas fa-hand-pointer"></i> Click on a date to view details</p>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- RIGHT PANEL: RESERVATION FORM -->
        <div class="right-panel">
            <section class="section facility-reservation-container">
                <h3><i class="fas fa-calendar-plus"></i> Reserve a Facility</h3>

                <form id="facilityReservationForm" onsubmit="submitReservation(event)">
                    <div class="form-row">
                        <label for="date"><i class="fas fa-calendar"></i> Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-row">
                        <label for="facility_id"><i class="fas fa-building"></i> Select Facility</label>
                        <select id="facility_id" name="facility_id" required disabled>
                            <option value="">-- Choose a facility --</option>
                        </select>
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
                                <span class="legend-item"><span class="legend-box taken"></span>Taken</span>
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
                        <i class="fas fa-check-circle"></i> Submit Reservation
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

document.addEventListener("DOMContentLoaded", () => {
    loadMyReservations();
    loadFacilities();
    
    // Facility dropdown disabled until date is picked
    document.getElementById("facility_id").disabled = true;

    // Enable facility dropdown after picking date
    document.getElementById("date").addEventListener("change", async () => {
        const date = document.getElementById("date").value;
        const facilitySelect = document.getElementById("facility_id");
        const chartContainer = document.getElementById("reservationChartContainer");

        if (!date) {
            facilitySelect.disabled = true;
            chartContainer.classList.add("hidden");
            return;
        }

        facilitySelect.disabled = false;
        // Reset selection when date changes
        facilitySelect.value = "";
        document.getElementById("priceDisplay").textContent = "Rs. 0.00";
        document.getElementById("slot_id").innerHTML = '<option value="">Select facility first</option>';
    });

    // Add event listener for facility change
    document.getElementById("facility_id").addEventListener("change", handleFacilityChange);
});

/* -------------------- HANDLE FACILITY CHANGE ----------------------- */
async function handleFacilityChange() {
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const chartContainer = document.getElementById("reservationChartContainer");
    const slotSelect = document.getElementById("slot_id");

    // Reset if no facility selected
    if (!facilityId || !date) {
        chartContainer.classList.add("hidden");
        document.getElementById("priceDisplay").textContent = "Rs. 0.00";
        slotSelect.innerHTML = '<option value="">Select facility and date first</option>';
        return;
    }

    const facility = facilityPrices.find(f => f.id == facilityId);
    if (!facility) return;

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
    await generateReservationChart(facilityId);
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
async function generateReservationChart(facilityId) {
    const chart = document.getElementById("reservationChart");

    try {
        const res = await fetch(`/uoc-sports/public/reserve-facilities/chart?facility_id=${facilityId}`);
        const reservations = await res.json();

        if (!reservations || reservations.length === 0) {
            chart.innerHTML = "<p>No reservations data available.</p>";
            return;
        }

        const slotTypes = ["MORNING", "AFTERNOON", "FULL"];
        let html = '<div class="chart-grid">';

        // Slot labels
        html += '<div class="time-labels">';
        slotTypes.forEach(slot => html += `<div class="time-label">${slot}</div>`);
        html += '</div>';

        // Date columns
        html += '<div class="date-columns">';

        reservations.forEach(day => {
            html += `<div class="date-column">
                <div class="date-label">${formatDateShort(day.date)}</div>`;

            slotTypes.forEach(slotType => {
                const isTaken = day.slots[slotType] || false;
                html += `<div class="slot ${isTaken ? 'taken' : 'available'}" title="${isTaken ? 'Taken' : 'Available'}"></div>`;
            });

            html += '</div>';
        });

        html += '</div></div>';
        chart.innerHTML = html;

    } catch (e) {
        console.error("Error loading chart:", e);
        chart.innerHTML = "<p>Error loading chart.</p>";
    }
}

function formatDateShort(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const day = days[date.getDay()];
    const dateNum = date.getDate();
    return `${day} ${dateNum}`;
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

/* -------------------- USER CALENDAR VARIABLES ----------------------- */
let userReservations = [];
let calendarYear, calendarMonth;

/* -------------------- LOAD MY RESERVATIONS (Calendar) ----------------------- */
function loadMyReservations() {
    const calendarContainer = document.getElementById('userCalendar');
    if (!calendarContainer) return; // User not logged in

    // Initialize to current month
    const now = new Date();
    calendarYear = now.getFullYear();
    calendarMonth = now.getMonth();

    fetch(RESERVATIONS_API)
        .then(res => res.json())
        .then(response => {
            userReservations = response.data || [];
            renderUserCalendar();
        })
        .catch(err => {
            console.error("Error loading reservations:", err);
            calendarContainer.innerHTML = '<p class="no-reservations">Unable to load reservations.</p>';
        });
}

/* -------------------- RENDER USER CALENDAR ----------------------- */
function renderUserCalendar() {
    const container = document.getElementById('userCalendar');
    const labelEl = document.getElementById('calendarMonthLabel');
    
    const date = new Date(calendarYear, calendarMonth, 1);
    const monthName = date.toLocaleString('default', { month: 'long' });
    labelEl.textContent = `${monthName} ${calendarYear}`;

    // Build reservation lookup by date
    const reservationsByDate = {};
    userReservations.forEach(r => {
        if (!reservationsByDate[r.date]) reservationsByDate[r.date] = [];
        reservationsByDate[r.date].push(r);
    });

    // Generate calendar HTML
    let html = `<table class="user-booking-calendar">
        <thead><tr>
            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
        </tr></thead><tbody>`;

    let row = '<tr>';
    // Empty cells for days before first
    for (let i = 0; i < date.getDay(); i++) {
        row += '<td class="empty"></td>';
    }

    while (date.getMonth() === calendarMonth) {
        if (date.getDay() === 0 && row !== '<tr>') {
            html += row + '</tr>';
            row = '<tr>';
        }

        const day = date.getDate();
        const dateStr = `${calendarYear}-${(calendarMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayReservations = reservationsByDate[dateStr] || [];
        
        // Determine status class
        let statusClass = '';
        if (dayReservations.length > 0) {
            // Check if any are pending
            const hasPending = dayReservations.some(r => r.payment_status === 'INCOMPLETE');
            const hasCancelled = dayReservations.some(r => r.status === 'CANCELLED');
            const allPaid = dayReservations.every(r => r.payment_status === 'COMPLETE');
            
            if (hasCancelled) statusClass = 'cancelled';
            else if (hasPending) statusClass = 'pending';
            else if (allPaid) statusClass = 'paid';
            else statusClass = 'has-booking';
        }

        const isToday = dateStr === new Date().toISOString().split('T')[0];
        
        row += `<td class="calendar-day ${statusClass} ${isToday ? 'today' : ''}" 
                    data-date="${dateStr}" 
                    onclick="showDayReservations('${dateStr}')">
                    ${day}
                    ${dayReservations.length > 0 ? `<span class="dot"></span>` : ''}
                </td>`;

        date.setDate(day + 1);
    }

    // Fill remaining cells
    while (row.split('<td').length <= 7) {
        row += '<td class="empty"></td>';
    }
    html += row + '</tr></tbody></table>';

    container.innerHTML = html;
}

/* -------------------- SHOW DAY RESERVATIONS ----------------------- */
function showDayReservations(dateStr) {
    const detailsDiv = document.getElementById('reservationDetails');
    const dayReservations = userReservations.filter(r => r.date === dateStr);

    // Highlight selected date
    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
    document.querySelector(`.calendar-day[data-date="${dateStr}"]`)?.classList.add('selected');

    if (dayReservations.length === 0) {
        detailsDiv.innerHTML = `
            <div class="details-header">
                <p class="booking-date"><strong>${formatDisplayDate(dateStr)}</strong></p>
                <button class="details-close-btn" onclick="closeReservationDetails()"><i class="fas fa-times"></i></button>
            </div>
            <p class="no-bookings">No reservations on this day.</p>
        `;
        return;
    }

    let html = `<div class="details-header">
                    <p class="booking-date"><strong>${formatDisplayDate(dateStr)}</strong></p>
                    <button class="details-close-btn" onclick="closeReservationDetails()"><i class="fas fa-times"></i></button>
                </div>
                <div class="reservation-cards">`;

    dayReservations.forEach(item => {
        const status = item.payment_status === "INCOMPLETE" ? "pending" : "paid";
        html += `
            <div class="reservation-card ${status}">
                <div class="card-header">
                    <span class="facility-name">${item.facility_name}</span>
                    <span class="status-badge ${status}">${status === 'paid' ? 'Paid' : 'Pending'}</span>
                </div>
                <div class="card-body">
                    <div class="time-info">
                        <i class="fas fa-clock"></i> ${item.start_time} - ${item.end_time}
                    </div>
                    <div class="purpose-info">${item.purpose || 'No purpose specified'}</div>
                </div>
                <div class="card-actions">
                    ${status === "pending" ? `
                    <button class="btn pay-btn" onclick="payNow('${item.booking_id}')">
                        <i class="fas fa-credit-card"></i> Pay
                    </button>` : ""}
                    <button class="btn cancel-btn" onclick="cancelFacilityReservation('${item.booking_id}')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        `;
    });

    html += '</div>';
    detailsDiv.innerHTML = html;
}

function formatDisplayDate(dateStr) {
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

function closeReservationDetails() {
    document.getElementById('reservationDetails').innerHTML = `
        <p class="select-date-msg"><i class="fas fa-hand-pointer"></i> Click on a date to view details</p>
    `;
    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
}

/* -------------------- CALENDAR NAVIGATION ----------------------- */
document.addEventListener('DOMContentLoaded', () => {
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            calendarMonth--;
            if (calendarMonth < 0) {
                calendarMonth = 11;
                calendarYear--;
            }
            renderUserCalendar();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            calendarMonth++;
            if (calendarMonth > 11) {
                calendarMonth = 0;
                calendarYear++;
            }
            renderUserCalendar();
        });
    }
});

/* -------------------- CANCEL RESERVATION ----------------------- */
function cancelFacilityReservation(id) {
    if (!confirm("Are you sure you want to cancel this reservation?")) return;
    
    fetch("/uoc-sports/public/reserve-facilities/cancel", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "booking_id=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(msg => {
        showFloatingMessage(msg, "success");
        loadMyReservations();
        // Refresh chart if visible
        const facilityId = document.getElementById("facility_id").value;
        if (facilityId) {
            generateReservationChart(facilityId);
        }
    })
    .catch(() => showFloatingMessage("Error cancelling booking.", "error"));
}

/* -------------------- PAYMENT REDIRECT ----------------------- */
function payNow(id) {
    window.location.href = `/uoc-sports/public/payment?booking_id=${id}`;
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
            msg.innerHTML = `<div class="success">✔ ${result.message}</div>`;
            msg.style.display = "block";
            showFloatingMessage("Reservation successful!", "success");
            
            // Reset form
            form.reset();
            document.getElementById("priceDisplay").textContent = "Rs. 0.00";
            document.getElementById("facility_id").disabled = true;
            document.getElementById("reservationChartContainer").classList.add("hidden");
            
            // Reload reservations
            loadMyReservations();
            
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

</script>
</html>