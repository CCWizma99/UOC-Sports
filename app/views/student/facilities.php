<?php 
header("Location: /uoc-sports/public/facility-reservation");
exit;
?>
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
        @import url(/uoc-sports/public/css/general/facility-reservation-page.css?v=3.20);

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

        /* Cancellation Modal */
        .cancel-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .cancel-modal-overlay.active {
            display: flex;
        }

        .cancel-modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalPop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .cancel-modal-content h3 {
            color: #d32f2f;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cancel-modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            transition: color 0.2s;
        }

        .cancel-modal-close:hover {
            color: #333;
        }

        .cancel-form-group {
            margin-bottom: 1.5rem;
        }

        .cancel-form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }

        .cancel-form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 10px;
            min-height: 120px;
            font-family: inherit;
            font-size: 0.95rem;
            resize: none;
            transition: border-color 0.2s;
        }

        .cancel-form-group textarea:focus {
            outline: none;
            border-color: #d32f2f;
        }

        .btn-submit-cancel {
            width: 100%;
            padding: 1rem;
            background: #d32f2f;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-cancel:hover {
            background: #b71c1c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(211, 47, 47, 0.3);
        }

        .badge-status-pending_cancel {
            background-color: #ff9800;
            color: white;
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
<body class="">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

    <!-- Cancellation Request Modal -->
    <div id="cancelModal" class="cancel-modal-overlay">
        <div class="cancel-modal-content">
            <button class="cancel-modal-close" onclick="closeCancelModal()">&times;</button>
            <h3><i class="fas fa-exclamation-circle"></i> Request Cancellation</h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 1.5rem;">
                Please provide a reason for cancelling this reservation. Your request will be reviewed by the administrator.
            </p>
            
            <input type="hidden" id="cancelBookingId">
            
            <div class="cancel-form-group">
                <label for="cancelReason">Reason for Cancellation</label>
                <textarea id="cancelReason" placeholder="Describe why you need to cancel (e.g., Change of plans, Weather, etc.)"></textarea>
            </div>
            
            <button class="btn-submit-cancel" onclick="submitCancelRequest()">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
        </div>
    </div>

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
                                <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="form-row price-row">
                            <label><i class="fas fa-tag"></i> Base Price</label>
                            <div class="price-display" id="priceDisplay">Rs. 0.00</div>
                        </div>

                        <div class="form-row">
                            <label for="slot_id"><i class="fas fa-clock"></i> Select Slot</label>
                            <select id="slot_id" name="slot_id" required>
                                <option value="">-- Select date/facility first --</option>
                            </select>
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
            </div>

            <!-- My Bookings Card -->
            <div class="portal-card">
                <section class="my-bookings-section">
                    <h2><i class="fas fa-clipboard-list"></i> My Facility Bookings</h2>
                    <div id="myBookingsContainer" class="bookings-grid">
                        <p class="loading-msg">Loading your bookings...</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <script>
    /* -------------------- BASIC CONFIG ----------------------- */
    let timeout = null;
    let facilityPrices = [];

    const API_BASE = "/uoc-sports/public/api/get-facility-rates.php";
    const BOOKING_API = "/uoc-sports/public/create-facility-booking";
    const SLOTS_API = "/uoc-sports/public/get-reserved-slots";
    const VIEW_RESERVATIONS_API = "/uoc-sports/public/reserve-facilities/view-reservations";
    const CANCEL_RESERVATION_API = "/uoc-sports/public/reserve-facilities/cancel";

    /* -------------------- DATE VALIDATION ----------------------- */
    const dateInput = document.getElementById("date");
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 30); // Allow up to 30 days in advance

    const toISO = d => d.toISOString().split('T')[0];
    dateInput.min = toISO(today);
    dateInput.max = toISO(maxDate);

    /* -------------------- INITIALIZATION ----------------------- */
    document.addEventListener("DOMContentLoaded", () => {
        loadFacilities();
        loadMyBookings();
        
        document.getElementById("facility_id").addEventListener("change", updateAvailableSlots);
        document.getElementById("date").addEventListener("change", updateAvailableSlots);
        document.getElementById("slot_id").addEventListener("change", updatePriceDisplay);
    });

    /* -------------------- LOAD FACILITIES ----------------------- */
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

    /* -------------------- UPDATE SLOTS & PRICE ----------------------- */
    async function updateAvailableSlots() {
        const facilityId = document.getElementById("facility_id").value;
        const date = document.getElementById("date").value;
        const slotSelect = document.getElementById("slot_id");
        const priceDisplay = document.getElementById("priceDisplay");

        if (!facilityId || !date) {
            slotSelect.innerHTML = '<option value="">-- Select date/facility first --</option>';
            priceDisplay.textContent = "Rs. 0.00";
            return;
        }

        slotSelect.innerHTML = '<option value="">Checking availability...</option>';

        try {
            const res = await fetch(`${SLOTS_API}?facility_id=${facilityId}&date=${date}`);
            const slots = await res.json();

            slotSelect.innerHTML = '<option value="">-- Select a slot --</option>';
            slots.forEach(slot => {
                const option = document.createElement("option");
                option.value = slot.id;
                option.textContent = `${slot.type} - Rs. ${parseFloat(slot.price).toFixed(2)}`;
                if (slot.taken) {
                    option.disabled = true;
                    option.textContent += " (Reserved)";
                }
                slotSelect.appendChild(option);
            });
        } catch (e) {
            slotSelect.innerHTML = '<option value="">Error loading slots</option>';
        }
    }

    function updatePriceDisplay() {
        const slotSelect = document.getElementById("slot_id");
        const priceDisplay = document.getElementById("priceDisplay");
        const selected = slotSelect.options[slotSelect.selectedIndex];
        
        if (selected && selected.value) {
            const text = selected.textContent;
            const priceMatch = text.match(/Rs\. (\d+\.\d+)/);
            if (priceMatch) {
                priceDisplay.textContent = `Rs. ${priceMatch[1]}`;
            }
        } else {
            priceDisplay.textContent = "Rs. 0.00";
        }
    }

    /* -------------------- LOAD MY BOOKINGS (CARDS) ----------------------- */
    async function loadMyBookings() {
        const container = document.getElementById("myBookingsContainer");
        try {
            const res = await fetch(VIEW_RESERVATIONS_API);
            const packet = await res.json();
            const data = packet.data || [];

            if (data.length === 0) {
                container.innerHTML = '<p class="no-results" style="grid-column: 1/-1;">You have no active facility bookings.</p>';
                return;
            }

            container.innerHTML = "";
            const now = new Date();
            const nextMonth = new Date();
            nextMonth.setMonth(now.getMonth() + 1);

            const thisMonthKey = now.getFullYear() + "-" + String(now.getMonth() + 1).padStart(2, '0');
            const nextMonthKey = nextMonth.getFullYear() + "-" + String(nextMonth.getMonth() + 1).padStart(2, '0');

            const months = [
                { title: now.toLocaleString('default', { month: 'long', year: 'numeric' }), key: thisMonthKey },
                { title: nextMonth.toLocaleString('default', { month: 'long', year: 'numeric' }), key: nextMonthKey }
            ];

            months.forEach(month => {
                const monthBookings = data.filter(b => b.date.startsWith(month.key));
                if (monthBookings.length === 0) return;

                const sectionHeader = document.createElement("h4");
                sectionHeader.className = "month-group-title";
                sectionHeader.textContent = month.title;
                container.appendChild(sectionHeader);

                const groupContainer = document.createElement("div");
                groupContainer.className = "bookings-month-grid";
                
                monthBookings.forEach(booking => {
                    const card = document.createElement("div");
                    card.className = `booking-card ${booking.status.toLowerCase()} ${booking.flag_status === 'FLAGGED' ? 'flagged' : ''}`;
                    
                    let actionButtons = "";
                    if (booking.status === 'BOOKED' || booking.flag_status === 'FLAGGED') {
                        if (booking.payment_status === 'INCOMPLETE' || booking.flag_status === 'FLAGGED') {
                            actionButtons += `<button class="card-btn btn-pay" onclick="payBooking('${booking.booking_id}')"><i class="fas fa-wallet"></i> ${booking.flag_status === 'FLAGGED' ? 'Fix Payment' : 'Pay Now'}</button>`;
                        }
                        actionButtons += `<button class="card-btn btn-cancel" onclick="openCancelModal('${booking.booking_id}')"><i class="fas fa-times"></i> Request Cancel</button>`;
                    }

                    let statusBadges = `
                        <span class="status-badge badge-status-${booking.status.toLowerCase()}">${booking.status.replace('_', ' ')}</span>
                        <span class="status-badge badge-payment-${booking.payment_status.toLowerCase()}">${booking.payment_status}</span>
                    `;

                    if (booking.flag_status === 'FLAGGED') {
                        statusBadges += `<span class="status-badge badge-status-flagged">ISSUE</span>`;
                    }

                    card.innerHTML = `
                        <div class="booking-card-header">
                            <h3 class="facility-name">${booking.facility_name}</h3>
                            <span class="booking-id">#${booking.booking_id}</span>
                        </div>
                        <div class="booking-card-body">
                            <div class="booking-info-item"><i class="fas fa-calendar-day"></i> ${booking.date}</div>
                            <div class="booking-info-item"><i class="fas fa-clock"></i> ${booking.start_time} - ${booking.end_time}</div>
                            <div class="booking-info-item"><i class="fas fa-info-circle"></i> ${booking.purpose}</div>
                            <div class="booking-status-badges">${statusBadges}</div>
                            ${booking.flag_status === 'FLAGGED' ? `<div class="flag-reason-note"><strong>Admin:</strong> ${booking.flag_reason}</div>` : ''}
                        </div>
                        ${actionButtons ? `<div class="booking-card-footer">${actionButtons}</div>` : ''}
                    `;
                    groupContainer.appendChild(card);
                });
                container.appendChild(groupContainer);
            });

            // If no bookings for either month, show generic message
            if (container.children.length === 0) {
                 container.innerHTML = '<p class="no-results">No upcoming bookings for this month or next month.</p>';
            }

        } catch (e) {
            container.innerHTML = `<p class="error" style="grid-column: 1/-1;">Failed to load bookings. ${e.message}</p>`;
        }
    }

    /* -------------------- SUBMIT RESERVATION ----------------------- */
    async function submitReservation(e) {
        e.preventDefault();
        const msg = document.getElementById("reservationMessage");
        const formData = new FormData(e.target);

        try {
            const response = await fetch(BOOKING_API, { method: "POST", body: formData });
            const result = await response.json();

            if (result.success) {
                showFloatingMessage("Redirecting to payment...", "success");
                window.location.href = `/uoc-sports/public/payment?booking_id=${result.booking_id}`;
            } else {
                msg.innerHTML = `<div class="error" style="color:red; padding:10px; border:1px solid red; border-radius:8px; margin-top:10px;">✘ ${result.message}</div>`;
                showFloatingMessage(result.message, "error");
            }
        } catch (err) {
            showFloatingMessage("Execution failed.", "error");
        }
    }

    /* -------------------- CARD ACTIONS ----------------------- */
    window.payBooking = (id) => {
        window.location.href = `/uoc-sports/public/payment?booking_id=${id}`;
    };

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
                loadMyBookings();
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

    /* -------------------- UTIL ----------------------- */
    async function searchFacilities() {
        const name = document.getElementById("search_facility_name").value.trim();
        const suggestionBox = document.getElementById("suggestions");
        if (!name) { suggestionBox.innerHTML = ""; return; }
        clearTimeout(timeout);
        timeout = setTimeout(async () => {
            try {
                const res = await fetch(`${API_BASE}?facility_name=${encodeURIComponent(name)}`);
                const results = await res.json();
                if (!results || results.length === 0) {
                    suggestionBox.innerHTML = `<p class="no-results">No facilities found.</p>`;
                    return;
                }
                suggestionBox.innerHTML = results.map(r => `
                    <div class="facility-card" onclick="selectFacilityDropdown('${r.id}')">
                        <h4>${r.facility_name}</h4>
                        <p class="type">${r.facility_type.replace(/_/g, " ")}</p>
                    </div>
                `).join('');
            } catch (e) { suggestionBox.innerHTML = `<p class="error">Error searching.</p>`; }
        }, 400);
    }

    window.selectFacilityDropdown = (id) => {
        document.getElementById("facility_id").value = id;
        document.getElementById("suggestions").innerHTML = "";
        document.getElementById("search_facility_name").value = "";
        updateAvailableSlots();
    };

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
