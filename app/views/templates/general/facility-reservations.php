<div class="container">

    <!-- My Reservations Section -->
    <section class="section my-reservations">
        <h2><i class="fas fa-bookmark"></i> My Reservations</h2>

        <?php if (!isset($user_id)): ?>
            <div class="auth-message">
                <p>Please <a href="/uoc-sports/public/sign-in">log in</a> to view your reservations.</p>
            </div>
        <?php else: ?>
            <div class="reservation-list">
                <!-- Loaded via JS -->
            </div>
        <?php endif; ?>
    </section>

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
        <h3><i class="fas fa-calendar-plus"></i> Reserve a Facility</h3>

        <form id="facilityReservationForm" onsubmit="submitReservation(event)">
            <div class="form-row">
                <label for="facility_id"><i class="fas fa-building"></i> Select Facility</label>
                <select id="facility_id" name="facility_id" required onchange="handleFacilityChange()"></select>
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

            <div class="form-row full-width">
                <label for="date"><i class="fas fa-calendar"></i> Date</label>
                <input type="date" id="date" name="date" required onchange="loadSlots()">
            </div>

            <div class="form-row full-width">
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

<script>
/* -------------------- BASIC CONFIG ----------------------- */
document.getElementById("date").min = new Date().toISOString().split("T")[0];

document.addEventListener("DOMContentLoaded", () => {
    loadMyReservations();
    loadFacilities();
});

const API_BASE = "/uoc-sports/public/api/get-facility-rates.php";
const BOOKING_API = "/uoc-sports/public/create-facility-booking";
const SLOTS_API = "/uoc-sports/public/api/get-facility-slots.php";
const RESERVATIONS_API = "/uoc-sports/public/reserve-facilities/view";
let currentPrice = 0;
let facilityPrices = {};

/* -------------------- HANDLE FACILITY CHANGE ----------------------- */
async function handleFacilityChange() {
    const facilityId = document.getElementById("facility_id").value;
    const chartContainer = document.getElementById("reservationChartContainer");
    
    if (!facilityId) {
        chartContainer.classList.add("hidden");
        document.getElementById("priceDisplay").textContent = "Rs. 0.00";
        return;
    }

    // Get facility price from loaded data
    try {
        const res = await fetch(API_BASE);
        const facilities = await res.json();
        const facility = facilities.find(f => f.id == facilityId);
        
        if (facility) {
            // Display practice working hours as base price
            currentPrice = parseFloat(facility.practice_working_hours) || 0;
            document.getElementById("priceDisplay").textContent = `Rs. ${currentPrice.toFixed(2)}`;
            facilityPrices = facility;
            
            // Generate and show reservation chart
            generateReservationChart(facilityId);
            chartContainer.classList.remove("hidden");
        }
    } catch (e) {
        console.error("Error fetching facility:", e);
    }
}

/* -------------------- GENERATE RESERVATION CHART ----------------------- */
function generateReservationChart(facilityId) {
    // MOCKUP DATA - Replace with actual backend data later
    const mockReservations = {
        "1": [
            { date: "2025-11-21", slots: [false, false, true, true, false, true, false, true] },
            { date: "2025-11-22", slots: [false, true, true, false, false, false, true, false] },
            { date: "2025-11-23", slots: [true, false, false, true, true, false, false, true] },
            { date: "2025-11-24", slots: [false, false, false, false, true, false, false, false] },
            { date: "2025-11-25", slots: [true, true, false, false, false, false, true, false] },
            { date: "2025-11-26", slots: [false, false, true, true, true, false, false, false] },
            { date: "2025-11-27", slots: [false, true, false, false, false, true, true, false] }
        ]
    };

    const slotTimes = ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];
    const reservations = mockReservations[facilityId] || [];
    const chart = document.getElementById("reservationChart");
    
    let html = '<div class="chart-grid">';
    
    // Add time labels on the left
    html += '<div class="time-labels">';
    slotTimes.forEach(time => {
        html += `<div class="time-label">${time}</div>`;
    });
    html += '</div>';
    
    // Add date columns
    html += '<div class="date-columns">';
    
    reservations.forEach(day => {
        html += `<div class="date-column">
            <div class="date-label">${formatDateShort(day.date)}</div>`;
        
        day.slots.forEach((isTaken, index) => {
            html += `<div class="slot ${isTaken ? 'taken' : 'available'}" title="${isTaken ? 'Taken' : 'Available'} - ${slotTimes[index]}"></div>`;
        });
        
        html += '</div>';
    });
    
    html += '</div></div>';
    
    chart.innerHTML = html;
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

/* -------------------- LOAD MY RESERVATIONS ----------------------- */
function loadMyReservations() {
    fetch(RESERVATIONS_API)
        .then(res => res.json())
        .then(response => {
            const list = document.querySelector(".reservation-list");
            if (!list) return; // User not logged in

            list.innerHTML = "";

            const items = response.data;

            if (!items || items.length === 0) {
                list.innerHTML = `<p class="no-reservations">No reservations yet.</p>`;
                return;
            }

            items.forEach(item => {
                const status = item.payment_status === "paid" ? "paid" : "pending";

                list.innerHTML += `
                    <div class="reservation-item ${status === "pending" ? "unpaid" : ""}">
                        <div class="info">
                            <h3>${item.facility_name}</h3>
                            <p>
                                <strong>${item.date}</strong> |
                                ${item.start_time} - ${item.end_time} |
                                <span>${item.purpose}</span>
                            </p>
                        </div>

                        <div class="status">
                            <span class="status-tag ${status}">
                                ${status === "paid" ? "Paid" : "Pending Payment"}
                            </span>

                            <div class="actions">
                                ${status === "pending" ? `
                                <button class="btn pay-btn" onclick="payNow('${item.booking_id}')">
                                    <i class="fas fa-credit-card"></i> Pay
                                </button>` : ""}

                                <button class="btn cancel-btn" onclick="cancelFacilityReservation('${item.booking_id}')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(err => {
            const list = document.querySelector(".reservation-list");
            if (list) {
                list.innerHTML = `<p class="no-reservations">Unable to load reservations.</p>`;
            }
        });
}

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
    })
    .catch(() => showFloatingMessage("Error cancelling booking.", "error"));
}

/* -------------------- PAYMENT REDIRECT ----------------------- */
function payNow(id) {
    window.location.href = `/uoc-sports/public/payment/process?booking_id=${id}`;
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
                        onclick='showDetails(JSON.parse(decodeURIComponent("${encodeURIComponent(JSON.stringify(r))}")))'>
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

/* -------------------- LOAD AVAILABLE SLOTS ----------------------- */
async function loadSlots() {
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const slotSelect = document.getElementById("slot_id");
    
    if (!facilityId || !date) {
        slotSelect.innerHTML = '<option value="">Select facility and date first</option>';
        return;
    }

    // MOCKUP SLOTS DATA - Replace with actual backend later
    const mockSlots = [
        { id: 1, start_time: "08:00", end_time: "09:00", price: 500 },
        { id: 2, start_time: "09:00", end_time: "10:00", price: 500 },
        { id: 3, start_time: "10:00", end_time: "11:00", price: 500 },
        { id: 4, start_time: "11:00", end_time: "12:00", price: 600 },
        { id: 5, start_time: "12:00", end_time: "13:00", price: 500 },
        { id: 6, start_time: "13:00", end_time: "14:00", price: 500 },
        { id: 7, start_time: "14:00", end_time: "15:00", price: 600 },
        { id: 8, start_time: "15:00", end_time: "16:00", price: 500 }
    ];

    try {
        // Uncomment when backend is ready
        // const res = await fetch(`${SLOTS_API}?facility_id=${facilityId}&date=${date}`);
        // const slots = await res.json();
        
        const slots = mockSlots;

        slotSelect.innerHTML = '<option value="">-- Choose a slot --</option>';

        if (!slots || slots.length === 0) {
            slotSelect.innerHTML += '<option disabled>No available slots</option>';
            return;
        }

        slots.forEach(slot => {
            slotSelect.innerHTML += `<option value="${slot.id}" data-price="${slot.price}">
                ${slot.start_time} - ${slot.end_time} (Rs. ${parseFloat(slot.price).toFixed(2)})
            </option>`;
        });
    } catch (e) {
        console.error("Error loading slots:", e);
        slotSelect.innerHTML += '<option disabled>Error loading slots</option>';
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
            form.reset();
            document.getElementById("priceDisplay").textContent = "Rs. 0.00";
            loadMyReservations();
            
            // Scroll to reservations
            setTimeout(() => {
                document.querySelector(".my-reservations").scrollIntoView({ behavior: "smooth" });
            }, 500);
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

