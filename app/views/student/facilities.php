<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-portal.css);
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);
        @import url(/uoc-sports/public/css/general/facility-reservation-page.css?v=3.17);

        .page-container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            padding: 20px 20px 0;
            height: calc(100vh - 120px);
            min-height: 0;
        }

        @media (max-width: 1024px) {
            .page-container {
                max-width: 95%;
                padding: 15px;
                margin-top: 160px; /* Added spacing for header */
            }
        }

        .portal-card {
            min-height: auto;
            overflow: auto;
            display: flex;
            flex-direction: column;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr; /* Search auto, Reserve takes remaining */
            gap: 15px;
            height: 100%;
            overflow: hidden;
        }

        /* Search Facility Styles */
        .facility-search {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .facility-search h2 {
            flex-shrink: 0;
            margin-bottom: 1rem;
        }

        .search-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        .facility-search input {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .facility-search input:focus {
            outline: none;
            border-color: #4b0082;
            box-shadow: 0 0 0 4px rgba(75, 0, 130, 0.1);
        }

        .suggestions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
            min-height: 0;
        }

        .facility-card {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0e6ff 100%);
            padding: 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: fit-content;
        }

        .facility-card:hover {
            border-color: #4b0082;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(75, 0, 130, 0.15);
        }

        .facility-card h4 {
            margin: 0 0 0.3rem 0;
            color: #1a1a1a;
            font-size: 1rem;
            font-weight: 700;
        }

        .facility-card .type {
            margin: 0;
            color: #4b0082;
            font-size: 0.8rem;
            text-transform: capitalize;
            font-weight: 500;
        }

        .no-results, .error {
            text-align: center;
            color: #888;
            padding: 20px;
        }

        /* Reserve Facility Form Styles */
        .facility-reservation-container form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .form-row label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #333;
        }

        .form-row input,
        .form-row select,
        .form-row textarea {
            padding: 0.5rem 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .form-row textarea {
            min-height: 60px;
            resize: vertical;
        }

        .price-display {
            padding: 0.7rem 1rem;
            background: linear-gradient(135deg, #111 0%, #4b0082 100%);
            color: white;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            display: flex;
            justify-content: center;
        }

        /* Modal Styles */
        .rates-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .rates-modal-overlay.active {
            display: flex;
        }

        .rates-modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            position: relative;
            max-height: 80vh;
            overflow-y: auto;
        }

        .rates-modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .facility-info h3 {
            color: #5e2d91;
            margin-bottom: 15px;
        }

        .rate-grid {
            display: grid;
            gap: 10px;
            margin-top: 15px;
        }

        .rate-grid div {
            padding: 10px;
            background: #f9f9f9;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
        }

        .floating-msg {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            opacity: 1;
            transition: opacity 0.5s;
        }

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

        .floating-msg.success {
            background: #4caf50;
        }

        .floating-msg.error {
            background: #f44336;
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
            <!-- Search Facilities Card -->
            <div class="portal-card">
                <section class="facility-search">
                    <h2><i class="fas fa-search"></i> Search Facility Rates</h2>
                    <div class="search-content">
                        <input 
                            type="text" 
                            id="search_facility_name" 
                            placeholder="Type a facility name (e.g., Cricket Oval, Tennis Court)..."
                            oninput="searchFacilities()"
                        />
                        <div id="suggestions" class="suggestions"></div>
                    </div>
                </section>
            </div>

            <!-- Reserve Facility Card -->
            <div class="portal-card">
                <section class="facility-reservation-container">
                    <h2><i class="fas fa-calendar-plus"></i> Reserve a Facility</h2>
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i> Select a date to view available facilities
                    </p>

                    <div id="parallelBookingAlert" class="parallel-booking-alert">
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

                        <div class="form-row price-row">
                            <label><i class="fas fa-tag"></i> Base Price</label>
                            <div class="price-display" id="priceDisplay">Rs. 0.00</div>
                        </div>

                        <div id="reservationChartContainer" class="reservation-chart-container hidden">
                            <div class="chart-controls">
                                <button type="button" class="nav-btn" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                                <div class="chart-month-label" id="chartMonthLabel"></div>
                                <button type="button" class="nav-btn" onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                            </div>
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

                        <!-- <div class="form-row"> removed old slot select -->

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
    </div>
    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <script>
    /* -------------------- BASIC CONFIG ----------------------- */
    let timeout = null;
    let currentPrice = 0;
    let facilityPrices = [];

    const API_BASE = "/uoc-sports/public/api/get-facility-rates.php";
    const BOOKING_API = "/uoc-sports/public/create-facility-booking";
    const SLOTS_API = "/uoc-sports/public/get-reserved-slots";

    /* -------------------- DATE VALIDATION ----------------------- */
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
            dateInput.value = "";
        }
    });

    /* -------------------- INITIALIZATION ----------------------- */
    document.addEventListener("DOMContentLoaded", () => {
        loadFacilities();
        
        // Add event listeners for both fields to trigger updates
        document.getElementById("facility_id").addEventListener("change", handleFacilityChange);
        document.getElementById("date").addEventListener("change", handleFacilityChange);
    });

/* -------------------- TOOLTIP LOGIC ----------------------- */
const chartTooltip = document.createElement('div');
chartTooltip.className = 'custom-chart-tooltip';
chartTooltip.style.cssText = 'position: fixed; background: #fff; border: 2px solid #5e2d91; border-radius: 8px; padding: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); z-index: 10000; pointer-events: none; display: none; font-size: 0.85rem; color: #333; min-width: 200px;';
document.body.appendChild(chartTooltip);

function showChartTooltip(e, facility, date, slotType, availability) {
    chartTooltip.style.display = 'block';
    
    // Format the date nicely
    const dateObj = new Date(date);
    const dateFormatted = dateObj.toLocaleDateString(undefined, { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    
    const isReserved = availability.toLowerCase().includes('reserved');
    const statusColor = isReserved ? '#dc3545' : '#28a745';

    chartTooltip.innerHTML = `
        <div style="font-weight:bold; color: #5e2d91; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 8px;">
            <i class="fas fa-info-circle"></i> Reservation Summary
        </div>
        <div style="margin-bottom: 3px;"><strong>Facility:</strong> ${facility}</div>
        <div style="margin-bottom: 3px;"><strong>Date:</strong> ${dateFormatted}</div>
        <div style="margin-bottom: 3px;"><strong>Slot:</strong> ${slotType}</div>
        <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #ccc;">
            <strong>Status:</strong> <span style="font-weight:bold; color: ${statusColor};">${availability}</span>
        </div>
    `;
    
    let x = e.clientX + 15;
    let y = e.clientY + 15;
    
    const rect = chartTooltip.getBoundingClientRect();
    if (x + rect.width > window.innerWidth) x = e.clientX - rect.width - 15;
    if (y + rect.height > window.innerHeight) y = e.clientY - rect.height - 15;
    
    chartTooltip.style.left = x + 'px';
    chartTooltip.style.top = y + 'px';
}

function hideChartTooltip() {
    chartTooltip.style.display = 'none';
}

/* -------------------- HEARTBEAT LOGIC ----------------------- */
let heartbeatInterval = null;
const HEARTBEAT_API = "/uoc-sports/public/reserve-facilities/heartbeat";

function startHeartbeat(facilityId) {
    stopHeartbeat();
    
    const performHeartbeat = async () => {
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
    };

    // Fire immediately
    performHeartbeat();
    
    // Then set interval
    heartbeatInterval = setInterval(performHeartbeat, 1000);
}

function stopHeartbeat() {
    if (heartbeatInterval) {
        clearInterval(heartbeatInterval);
        heartbeatInterval = null;
    }
    const alertBox = document.getElementById("parallelBookingAlert");
    if (alertBox) alertBox.classList.remove("active");
}

/* -------------------- HANDLE FACILITY CHANGE ----------------------- */
async function handleFacilityChange() {
    const facilityId = document.getElementById("facility_id").value;
    const date = document.getElementById("date").value;
    const chartContainer = document.getElementById("reservationChartContainer");

    if (!facilityId) {
        document.getElementById("priceDisplay").textContent = "Rs. 0.00";
        chartContainer.classList.add("hidden");
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
        // slotSelect.innerHTML = '<option value="">Select date first</option>'; // Removed as slotSelect is no longer used
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

    // Reset price display initially
    document.getElementById("priceDisplay").textContent = "Select a slot";

    // Load reservation chart
    await generateReservationChart(facilityId, date);
    chartContainer.classList.remove("hidden");
}

    /* -------------------- GENERATE RESERVATION CHART ----------------------- */
    async function generateReservationChart(facilityId, selectedDate) {
        const chart = document.getElementById("reservationChart");
        const dateParam = selectedDate || new Date().toISOString().split('T')[0];

        // Update Label
        const dateObj = new Date(dateParam);
        const monthName = dateObj.toLocaleString('default', { month: 'long', year: 'numeric' });
        const labelEl = document.getElementById("chartMonthLabel");
        if(labelEl) labelEl.textContent = monthName;

        // Get facility name for tooltip
        let facName = 'Facility';
        const facObj = facilityPrices.find(f => f.id == facilityId);
        if(facObj) facName = facObj.facility_name;

        try {
            const chartRes = await fetch(`/uoc-sports/public/reserve-facilities/chart?facility_id=${facilityId}&date=${dateParam}`);
            if (!chartRes.ok) throw new Error(`HTTP error! status: ${chartRes.status}`);
            
            const responseData = await chartRes.json();
            
            // Extract chart data
            const data = responseData.chart_data || [];

            const slotsRes = await fetch(`${SLOTS_API}?facility_id=${facilityId}&date=${dateParam}`);
            if (!slotsRes.ok) throw new Error(`HTTP error! status: ${slotsRes.status}`);
            const slots = await slotsRes.json();

            if (!data || !slots) {
                chart.innerHTML = "<p>Error: Invalid data received from server.</p>";
                return;
            }

            let html = '<div class="chart-wrapper">';
            
            // --- Legend ---
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

                slots.forEach(slotDef => {
                    const slotData = dayItem.slots && dayItem.slots[slotDef.id];
                    const isTaken = slotData && slotData.taken;
                    const bookerName = (isTaken && slotData.user) ? slotData.user : '';
                    const displayStatus = isTaken ? (bookerName ? `Reserved by ${bookerName}` : 'Reserved') : 'Available';
                    
                    const safeFacName = facName.replace(/'/g, "\\'");
                    const hoverEvents = `onmouseenter="showChartTooltip(event, '${safeFacName}', '${dateStr}', '${slotDef.type}', '${displayStatus}')" onmousemove="showChartTooltip(event, '${safeFacName}', '${dateStr}', '${slotDef.type}', '${displayStatus}')" onmouseleave="hideChartTooltip()"`;
                    
                    if (isTaken) {
                        html += `<div class="slot taken" ${hoverEvents}></div>`;
                    } else {
                        html += `
                            <div class="slot available" 
                                 onclick="selectSlot(this, '${slotDef.id}', '${dateStr}')" 
                                 ${hoverEvents}>
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
            chart.innerHTML = `<p>Error loading chart: ${e.message}</p>`;
        }
    }

    /* -------------------- SELECT SLOT ----------------------- */
    function selectSlot(el, slotId, dateStr) {
        document.querySelectorAll('.slot.selected').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');

        document.getElementById('slot_id').value = slotId;
        
        const dateInput = document.getElementById('date');
        if (dateInput.value !== dateStr) {
            dateInput.value = dateStr;
        }

        const facilityId = document.getElementById("facility_id").value;
        const facility = facilityPrices.find(f => f.id == facilityId);
        
        if (facility) {
            const d = new Date(dateStr);
            const day = d.getDay();
            const isWorking = (day >= 1 && day <= 5);
            
            let price = 0;
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

    /* -------------------- CHANGE MONTH ----------------------- */
    function changeMonth(offset) {
        const dateInput = document.getElementById("date");
        if (!dateInput.value) return;

        const current = new Date(dateInput.value);
        current.setMonth(current.getMonth() + offset, 1);
        
        const year = current.getFullYear();
        const month = String(current.getMonth() + 1).padStart(2, '0');
        const day = String(current.getDate()).padStart(2, '0');
        const newDateStr = `${year}-${month}-${day}`;
        
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
        
        handleFacilityChange();
    }

    /* -------------------- FACILITY SEARCH ----------------------- */
    let searchResults = {}; // Store search results by ID

    async function searchFacilities() {
        const name = document.getElementById("search_facility_name").value.trim();
        const suggestionBox = document.getElementById("suggestions");

        if (!name) {
            suggestionBox.innerHTML = "";
            searchResults = {};
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

                // Store results for later access
                searchResults = {};
                results.forEach(r => {
                    searchResults[r.id] = r;
                });

                suggestionBox.innerHTML = results
                    .map(r => `
                        <div class="facility-card" onclick="showDetails('${r.id}')">
                            <h4>${r.facility_name}</h4>
                            <p class="type">${r.facility_type.replace(/_/g, " ")}</p>
                        </div>
                    `)
                    .join('');

            } catch (e) {
                console.error("Search error:", e);
                suggestionBox.innerHTML = `<p class="error">Error fetching facilities. Please try again.</p>`;
            }
        }, 400);
    }

    /* -------------------- SHOW FACILITY DETAILS (Modal) ----------------------- */
    function showDetails(facilityId) {
        const data = searchResults[facilityId];
        if (!data) {
            console.error("Facility data not found for ID:", facilityId);
            return;
        }

        document.getElementById("suggestions").innerHTML = "";
        
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
        
        modal.onclick = (e) => {
            if (e.target === modal) closeRatesModal();
        };
    }

    function closeRatesModal() {
        const modal = document.getElementById("ratesModal");
        if (modal) modal.classList.remove("active");
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
                showFloatingMessage("Reservation created! Redirecting to payment...", "success");
                window.location.href = `/uoc-sports/public/payment?booking_id=${result.booking_id}`;
            } else {
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
    </script>
</body>
</html>
