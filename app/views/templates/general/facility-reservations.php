<style>
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
<div class="container">


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

<!-- Facility Reservation Form -->
<section class="section facility-reservation-container">
    <h3><i class="fas fa-calendar-plus"></i> Reserve a Facility <span style="font-size: 0.7rem; color: #4b0082; background: #eee; padding: 2px 5px; border-radius: 4px; vertical-align: middle; margin-left: 10px;">Updated UI</span></h3>

    <div id="parallelBookingAlert" class="parallel-booking-alert" style="display: none; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; align-items: center; gap: 10px;">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Parallel booking is happening. please refresh in 10 minutes for check availability.</span>
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

        <!-- Price Display -->
        <div class="form-row price-row">
            <label><i class="fas fa-tag"></i> Base Price</label>
            <div class="price-display" id="priceDisplay">Rs. 0.00</div>
        </div>

        <!-- Reservation Chart -->
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

        <!-- Slot Selection (Hidden) -->
        <input type="hidden" id="slot_id" name="slot_id" required>

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

<script>

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

document.getElementById("date").min = new Date().toISOString().split("T")[0];

document.addEventListener("DOMContentLoaded", () => {
    loadFacilities();
    
    // Add event listeners for both fields to trigger updates
    document.getElementById("date").addEventListener("change", handleFacilityChange);
    document.getElementById("facility_id").addEventListener("change", handleFacilityChange);
});

/* -------------------- HEARTBEAT LOGIC ----------------------- */
let heartbeatInterval = null;
const HEARTBEAT_API = "/uoc-sports/public/reserve-facilities/heartbeat";

function startHeartbeat(facilityId) {
    stopHeartbeat();
    heartbeatInterval = setInterval(async () => {
        try {
            const formData = new FormData();
            formData.append("facility_id", facilityId);

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
    }, 1000);
}

function stopHeartbeat() {
    if (heartbeatInterval) {
        clearInterval(heartbeatInterval);
        heartbeatInterval = null;
    }
    const alertBox = document.getElementById("parallelBookingAlert");
    if (alertBox) alertBox.style.display = "none";
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

    // Start heartbeat
    startHeartbeat(facilityId);

    // If date is missing, we still tracked the facility but can't show chart/slots
    if (!date) {
        chartContainer.classList.add("hidden");
        document.getElementById("priceDisplay").textContent = "Select a date";
        slotSelect.innerHTML = '<option value="">Select date first</option>';
        return;
    }

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
    const dateParam = selectedDate || new Date().toISOString().split('T')[0];

    // Update Label
    const dateObj = new Date(dateParam);
    const monthName = dateObj.toLocaleString('default', { month: 'long', year: 'numeric' });
    const labelEl = document.getElementById("chartMonthLabel");
    if(labelEl) labelEl.textContent = monthName;

    try {
        // 1. Fetch Reservations
        const res = await fetch(`/uoc-sports/public/reserve-facilities/chart?facility_id=${facilityId}&date=${dateParam}`);
        const data = await res.json();

        // 2. Fetch Slots Definitions
        const slotsRes = await fetch(`${SLOTS_API}?facility_id=${facilityId}&date=${dateParam}`);
        const slots = await slotsRes.json();

        if (!data || !slots) {
            chart.innerHTML = "<p>Error loading data.</p>";
            return;
        }

        let html = '<div class="chart-wrapper">';
        
        // --- Legend (Dynamic) ---
        html += '<div class="chart-legend-col">';
        html += '<div class="legend-header-placeholder"></div>';
        slots.forEach(slot => {
             let name = slot.type.split('(')[0].trim();
             html += `<div class="legend-label" title="${slot.type}">${name}</div>`;
        });
        html += '</div>';

        // --- Grid ---
        html += '<div class="date-columns-container" id="daysContainer">';
        
        const chartData = Array.isArray(data) ? data : []; 

        chartData.forEach(dayItem => {
            const dateStr = dayItem.date;
            const isSelected = dateStr === dateParam;
            const highlightClass = isSelected ? 'highlighted' : '';
            const idAttr = isSelected ? 'id="selectedDateCol"' : '';
            
            html += `<div class="date-column ${highlightClass}" id="col-${dateStr}" ${idAttr}>
                <div class="date-label">${formatDateShort(dateStr)}</div>`;

            // Loop through defined slots
            slots.forEach(slotDef => {
                const isTaken = dayItem.slots && dayItem.slots[slotDef.id];
                
                if (isTaken) {
                    html += `<div class="slot taken" title="Reserved"></div>`;
                } else {
                    html += `
                        <div class="slot available" 
                             onclick="selectSlot(this, '${slotDef.id}', '${dateStr}')" 
                             title="Available: ${slotDef.type}">
                        </div>`;
                }
            });

            html += '</div>';
        });

        html += '</div></div>';
        chart.innerHTML = html;

        setTimeout(() => {
            const selectedCol = document.getElementById("selectedDateCol");
            const container = document.getElementById("daysContainer");
            if (selectedCol && container) {
                 const scrollLeft = selectedCol.offsetLeft - container.offsetLeft - (container.clientWidth / 2) + (selectedCol.clientWidth / 2);
                 container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
            }
        }, 100);

    } catch (e) {
        console.error("Error generating chart:", e);
        chart.innerHTML = "<p>Error loading chart.</p>";
    }
}

/* -------------------- HANDLE SLOT SELECTION ----------------------- */
function selectSlot(el, slotId, dateStr) {
    document.querySelectorAll('.slot.selected').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');

    document.getElementById('slot_id').value = slotId;
    
    // Update date if needed
    const dateInput = document.getElementById('date');
    if (dateInput.value !== dateStr) {
        dateInput.value = dateStr;
    }

    // Update Price
    const facilityId = document.getElementById("facility_id").value;
    const facility = facilityPrices.find(f => f.id == facilityId);
    
    if (facility) {
        const d = new Date(dateStr);
        const day = d.getDay();
        const isWorking = (day >= 1 && day <= 5);
        
        let price = 0;
        // Simple logic or map lookup. 
        // We know standard IDs: MORNING, AFTERNOON, FULL
        if (slotId === 'MORNING') {
            price = isWorking ? facility.practice_working_hours : facility.practice_other_hours;
        } else if (slotId === 'AFTERNOON') {
            price = isWorking ? facility.practice_working_hours : facility.practice_other_hours;
        } else if (slotId === 'FULL') {
            price = isWorking ? facility.tournament_full_day_working : facility.tournament_full_day_other;
        }
        
        document.getElementById("priceDisplay").textContent = `Rs. ${parseFloat(price).toFixed(2)}`;
    }
}

function formatDateShort(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const days = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
    const day = days[date.getDay()];
    const dateNum = date.getDate();
    return `<span class="day-name">${day}</span><span class="day-num">${dateNum}</span>`;
}

/* -------------------- PAYMENT REDIRECT ----------------------- */
function payNow(id) {
    window.location.href = `/uoc-sports/public/payment/process?booking_id=${id}`;
}


/* -------------------- CHANGE MONTH ----------------------- */
function changeMonth(offset) {
    const dateInput = document.getElementById("date");
    if (!dateInput.value) return;

    const current = new Date(dateInput.value);
    current.setMonth(current.getMonth() + offset, 1);
    
    // Format YYYY-MM-DD
    const year = current.getFullYear();
    const month = String(current.getMonth() + 1).padStart(2, '0');
    const day = String(current.getDate()).padStart(2, '0');
    const newDateStr = `${year}-${month}-${day}`;
    
    // Min date check
    if (newDateStr < dateInput.min) {
        const minDate = new Date(dateInput.min);
        if (current < minDate && current.getMonth() !== minDate.getMonth()) {
             return; 
        } else if (current.getMonth() === minDate.getMonth()) {
             dateInput.value = dateInput.min;
        } else {
             dateInput.value = newDateStr;
        }
    } else {
        dateInput.value = newDateStr;
    }
    
    // Trigger update
    if (typeof loadFacilityDetails === 'function') {
        loadFacilityDetails();
    } else {
        generateReservationChart(document.getElementById("facility_id").value, dateInput.value);
        loadSlots();
    }
}


/* -------------------- CHANGE MONTH ----------------------- */
function changeMonth(offset) {
    const dateInput = document.getElementById("date");
    if (!dateInput.value) return;

    const current = new Date(dateInput.value);
    current.setMonth(current.getMonth() + offset, 1);
    
    // Format YYYY-MM-DD
    const year = current.getFullYear();
    const month = String(current.getMonth() + 1).padStart(2, '0');
    const day = String(current.getDate()).padStart(2, '0');
    const newDateStr = `${year}-${month}-${day}`;
    
    // Min date check
    if (newDateStr < dateInput.min) {
        const minDate = new Date(dateInput.min);
        if (current.getMonth() === minDate.getMonth() && current.getFullYear() === minDate.getFullYear()) {
             dateInput.value = dateInput.min;
        } else if (current < minDate) {
             return; 
        } else {
             dateInput.value = newDateStr;
        }
    } else {
        dateInput.value = newDateStr;
    }
    
    loadSlots(); // Also reload slots if dependent on month/date? 
    // Actually existing listener handles date change?
    // In template: document.getElementById("date").addEventListener("change", loadSlots); ?
    // Check main file listeners.
    // In facility-reservations.php snippet step 600, I don't see the listener explicitly but assume it exists or I should add call.
    // In facility-reservation.php it was `loadFacilityDetails`.
    // In template, `loadSlots` + `generateReservationChart` is triggered by what?
    // Let's assume we need to trigger whatever main listener does.
    // If we change input value programmatically, event DOES NOT fire automatically.
    
    // Manually trigger
    if (typeof loadFacilityDetails === 'function') {
        loadFacilityDetails();
    } else {
        // Fallback for template if function name differs
        generateReservationChart(document.getElementById("facility_id").value, dateInput.value);
        loadSlots();
    }
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

/* -------------------- SHOW FACILITY DETAILS ----------------------- */
function showDetails(data) {
    const box = document.getElementById("facilityDetails");
    box.classList.remove("hidden");

    box.innerHTML = `
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
    `;
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
            
            // Scroll to reservation form if needed
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

