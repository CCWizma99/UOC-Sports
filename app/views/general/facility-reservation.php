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
        @import url(/uoc-sports/public/css/general/facility-reservation-page.css?v=3.21);
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

        /* Cancellation Modal Styles */
        .cancel-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            opacity: 0;
            transition: all 0.3s ease;
            align-items: center;
            justify-content: center;
        }

        .cancel-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .cancel-modal-content {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 500px;
            transform: scale(0.9);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .cancel-modal-overlay.active .cancel-modal-content {
            transform: scale(1);
        }

        .cancel-modal-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .cancel-modal-header i {
            font-size: 3rem;
            color: #ef4444;
            margin-bottom: 1rem;
            display: block;
        }

        .cancel-modal-header h3 {
            font-size: 1.5rem;
            color: #1a1a1a;
            font-weight: 800;
        }

        .cancel-modal-body textarea {
            width: 100%;
            min-height: 120px;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            resize: none;
            transition: all 0.3s ease;
        }

        .cancel-modal-body textarea:focus {
            outline: none;
            border-color: #4b0082;
            box-shadow: 0 0 0 4px rgba(75, 0, 130, 0.1);
        }

        .cancel-modal-footer {
            display: flex;
            gap: 1rem;
        }

        .btn-cancel-modal {
            flex: 1;
            padding: 0.8rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-align: center;
        }

        .btn-close-cancel { background: #f3f4f6; color: #4b5563; }
        .btn-submit-cancel { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }

        .btn-cancel-modal:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .overview-card.pending_cancel { border-left: 4px solid #f97316; }

        /* Policy Modal Specifics */
        .policy-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 11000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .policy-modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .policy-modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 2rem;
            width: 90%;
            max-width: 550px;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }

        .policy-modal-overlay.active .policy-modal-content {
            transform: translateY(0);
        }

        .policy-icon {
            width: 70px;
            height: 70px;
            background: #f0ebff;
            color: #4b0082;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .policy-body {
            max-height: 300px;
            overflow-y: auto;
            margin: 1.5rem 0;
            padding-right: 10px;
            text-align: left;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #4b5563;
        }

        .policy-body b { color: #111; }
        .policy-body ul { padding-left: 1.2rem; }
        .policy-body li { margin-bottom: 0.8rem; }

        .policy-footer {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-policy {
            flex: 1;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-policy-decline { background: #f3f4f6; color: #4b5563; }
        .btn-policy-agree { 
            background: linear-gradient(135deg, #4b0082 0%, #6d28d9 100%); 
            color: white;
            box-shadow: 0 4px 15px rgba(75, 0, 130, 0.3);
        }

        .btn-policy:hover { transform: translateY(-2px); filter: brightness(1.1); }
    </style>
</head>
<body>
    <?php require '../app/views/templates/general/header.php'; ?>
    
    <!-- Cancellation Modal -->
    <div id="cancelModal" class="cancel-modal-overlay">
        <div class="cancel-modal-content">
            <div class="cancel-modal-header">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Request Cancellation</h3>
                <p style="color: #666; font-size: 0.9rem;">Please provide a reason for cancelling this booking.</p>
            </div>
            <div class="cancel-modal-body">
                <input type="hidden" id="cancelBookingId">
                <textarea id="cancelReason" placeholder="Describe why you need to cancel (e.g., medical reason, emergency)..." maxlength="500"></textarea>
            </div>
            <div class="cancel-modal-footer">
                <button type="button" class="btn-cancel-modal btn-close-cancel" onclick="closeCancelModal()">Dismiss</button>
                <button type="button" class="btn-cancel-modal btn-submit-cancel" onclick="submitCancelRequest()">Submit Request</button>
            </div>
        </div>
    </div>

    <!-- Policy Confirmation Modal -->
    <div id="policyModal" class="policy-modal-overlay">
        <div class="policy-modal-content">
            <div class="policy-icon"><i class="fas fa-file-contract"></i></div>
            <h2 style="text-align:center; color:#111; margin-bottom:0.5rem;">Booking Policies</h2>
            <p style="text-align:center; color:#666; font-size:0.9rem;">Please review and accept our terms to proceed.</p>
            
            <div class="policy-body">
                <ul>
                    <li><b>Discretionary Refunds:</b> Once a booking is created, any refunding is solely dependent on the <b>Physical Education Department's</b> discretion.</li>
                    <li><b>Facility Usage:</b> Users must explicitly follow all facility-specific instructions and maintaining the property's integrity.</li>
                    <li><b>Equipment:</b> Any damage to facility equipment will be charged to the person who made the reservation.</li>
                </ul>
            </div>

            <div class="policy-footer">
                <button type="button" class="btn-policy btn-policy-decline" onclick="closePolicyModal()">Go Back</button>
                <button type="button" class="btn-policy btn-policy-agree" onclick="confirmPolicyAndSubmit()">I Agree & Proceed</button>
            </div>
        </div>
    </div>

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

            <!-- My Bookings Card-Table Section (Replaces Old Monthly Grid) -->
            <section class="section my-bookings-overview">
                <div class="calendar-header">
                    <h3><i class="fas fa-clipboard-check"></i> My Upcoming Reservations</h3>
                    <div class="calendar-nav">
                        <span id="overviewScheduleLabel">This Month & Next Month</span>
                    </div>
                </div>
                
                <div id="overviewBookingsContainer" class="overview-bookings-grid">
                    <p class="loading-msg"><i class="fas fa-spinner fa-spin"></i> Loading your schedule...</p>
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
const CANCEL_RESERVATION_API = "/uoc-sports/public/reserve-facilities/cancel";

let currentCalendarDate = new Date();

document.addEventListener("DOMContentLoaded", () => {
    loadFacilities();
    renderBookingOverview();
    
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

    // Show Policy Modal Instead of Submitting
    openPolicyModal();
}

function openPolicyModal() {
    document.getElementById("policyModal").classList.add("active");
}

function closePolicyModal() {
    document.getElementById("policyModal").classList.remove("active");
}

async function confirmPolicyAndSubmit() {
    closePolicyModal();
    
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const slotId = document.getElementById("slot_id").value;
    const purpose = document.getElementById("purpose").value;
    const msg = document.getElementById("reservationMessage");

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
            // Redirect back to same page to show countdown/payment shortcut
            showFloatingMessage("Reservation created successfully!", "success");
            setTimeout(() => {
                window.location.href = `/uoc-sports/public/facility-reservation?success=1`;
            }, 1000);
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

// -------------------- BOOKING OVERVIEW (CARD TABLE) ---------------------
async function renderBookingOverview() {
    const container = document.getElementById('overviewBookingsContainer');
    if (!container) return;

    try {
        const res = await fetch(RESERVATIONS_API);
        const packet = await res.json();
        const data = packet.data || [];

        if (data.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-day"></i>
                    <p>No reservations found for this month or next month.</p>
                </div>
            `;
            return;
        }

        // Get Current and Next Month labels
        const now = new Date();
        const nextMonthDate = new Date();
        nextMonthDate.setMonth(now.getMonth() + 1);

        const currentMonthName = now.toLocaleString('default', { month: 'long' });
        const nextMonthName = nextMonthDate.toLocaleString('default', { month: 'long' });

        const thisMonthKey = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, '0');
        const nextMonthKey = nextMonthDate.getFullYear() + "-" + String(nextMonthDate.getMonth() + 1).padStart(2, '0');

        // Filter and Group
        const thisMonthBookings = data.filter(b => b.date.startsWith(thisMonthKey));
        const nextMonthBookings = data.filter(b => b.date.startsWith(nextMonthKey));

        let html = '';

        const renderMonthSection = (title, bookings) => {
            if (bookings.length === 0) return '';
            let sectionHtml = `<div class="month-group"><h4>${title}</h4><div class="overview-cards">`;
            bookings.forEach(b => {
                const isPaid = b.payment_status === 'COMPLETE';
                const statusClass = b.status.toLowerCase();
                const paymentClass = isPaid ? 'pay-complete' : 'pay-pending';

                sectionHtml += `
                    <div class="overview-card ${statusClass} ${paymentClass}">
                        <div class="card-info">
                            <span class="card-facility">${b.facility_name}</span>
                            <span class="card-date"><i class="fas fa-clock"></i> ${b.date} | ${b.start_time}</span>
                            ${!isPaid && b.status === 'BOOKED' ? `
                                <div class="timer-wrapper">
                                    <i class="fas fa-hourglass-half"></i> 
                                    Expires in: <span class="pay-countdown" data-created="${b.created_at}">--:--</span>
                                </div>
                            ` : ''}
                        </div>
                        <div class="card-actions">
                            ${!isPaid && b.status === 'BOOKED' ? `<button class="card-pay-btn" onclick="payBooking('${b.booking_id}')"><i class="fas fa-wallet"></i> Pay Now</button>` : ''}
                            ${b.status === 'BOOKED' ? `<button class="card-cancel-btn" onclick="openCancelModal('${b.booking_id}')"><i class="fas fa-times"></i> Request Cancel</button>` : ''}
                            ${b.status === 'PENDING_CANCEL' ? `<span class="status-pill pending">Pending Request</span>` : ''}
                            ${b.status === 'CANCELLED' ? `<span class="status-pill cancelled">Cancelled</span>` : ''}
                        </div>
                    </div>
                `;
            });
            sectionHtml += `</div></div>`;
            return sectionHtml;
        };

        html += renderMonthSection(currentMonthName, thisMonthBookings);
        html += renderMonthSection(nextMonthName, nextMonthBookings);

        if (!html) {
            html = '<p class="no-results">No upcoming bookings for April or May.</p>';
        }

        container.innerHTML = html;
        startPaymentCountdowns();

    } catch (e) {
        console.error("Overview Error:", e);
        container.innerHTML = `<p class="error">Log in to view your booking schedule.</p>`;
    }
}

/* -------------------- CANCELLATION REQUESTS ----------------------- */
window.openCancelModal = (id) => {
    document.getElementById("cancelBookingId").value = id;
    document.getElementById("cancelReason").value = "";
    document.getElementById("cancelModal").classList.add("active");
};

window.closeCancelModal = () => {
    document.getElementById("cancelModal").classList.remove("active");
};

window.submitCancelRequest = async () => {
    const id = document.getElementById("cancelBookingId").value;
    const reason = document.getElementById("cancelReason").value.trim();

    if (!reason) {
        showFloatingMessage("Please provide a reason for cancellation.", "error");
        return;
    }

    const btn = document.querySelector(".btn-submit-cancel");
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    try {
        const formData = new URLSearchParams();
        formData.append('booking_id', id);
        formData.append('reason', reason);

        const res = await fetch(CANCEL_RESERVATION_API, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString()
        });
        
        const result = await res.json();
        
        if (result.success) {
            showFloatingMessage(result.message, "success");
            closeCancelModal();
            renderBookingOverview(); // Refresh overview
        } else {
            showFloatingMessage(result.message, "error");
        }
    } catch (e) {
        showFloatingMessage("Request execution failed.", "error");
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
};

/* -------------------- PAYMENT COUNTDOWN TIMER ----------------------- */
let paymentTimerInterval = null;

function startPaymentCountdowns() {
    if (paymentTimerInterval) clearInterval(paymentTimerInterval);

    const updateTimers = () => {
        const countdowns = document.querySelectorAll(".pay-countdown");
        if (countdowns.length === 0) {
            clearInterval(paymentTimerInterval);
            return;
        }

        countdowns.forEach(el => {
            const createdAt = new Date(el.dataset.created).getTime();
            const now = new Date().getTime();
            const expiryTime = createdAt + (30 * 60 * 1000); // 30 minutes
            const timeLeft = expiryTime - now;

            if (timeLeft <= 0) {
                el.innerText = "Expired";
                el.closest(".overview-card").style.opacity = "0.5";
                // Optionally refresh the list to remove expired items
            } else {
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                el.innerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        });
    };

    updateTimers();
    paymentTimerInterval = setInterval(updateTimers, 1000);
}

window.payBooking = (id) => {
    window.location.href = `/uoc-sports/public/payment?booking_id=${id}`;
};

</script>
</html>