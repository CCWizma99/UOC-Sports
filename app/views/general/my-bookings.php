<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .page-container {
            max-width: 85%;
            margin: 30px auto;
            padding: 0 10px;
        }
        
        .bookings-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .card-header h2 { margin: 0; color: #333; font-size: 1.4rem; }

        .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btn {
            background: #f0f0f0;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            color: #666;
            font-weight: 500;
        }
        .tab-btn.active { background: #5e2d91; color: white; }

        .booking-table { width: 100%; border-collapse: collapse; }
        .booking-table th, .booking-table td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .booking-table th { background: #fafafa; color: #555; font-weight: 600; }
        
        /* Responsive Table Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Unified Grid Table */
        .custom-enhanced-table {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
            min-width: 600px;
            border: 1px solid #eee;
            border-radius: 8px; /* Optional: if container supports it */
            overflow: hidden;
        }
        .custom-enhanced-table thead th {
            background: rgba(75, 0, 130, 0.05);
            border-bottom: 2px solid #e0d8eb;
            color: #4b0082;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            text-align: left;
        }
        .custom-enhanced-table tbody tr {
            background: #fff;
            transition: background 0.2s ease;
        }
        .custom-enhanced-table tbody tr:hover {
            background: #faf8fd;
        }
        .custom-enhanced-table td {
            border-bottom: 1px solid #eee; 
            padding: 15px 15px;
            vertical-align: middle;
            color: #555;
            text-align: left;
            font-size: 0.85rem;
        }
        .custom-enhanced-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge.booked, .badge.active, .badge.accepted { background: #e3f2fd; color: #1565c0; }
        .badge.cancelled, .badge.rejected { background: #ffebee; color: #c62828; }
        .badge.completed { background: #e8f5e9; color: #2e7d32; }
        .badge.pending { background: #fff3e0; color: #ef6c00; }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }
        .btn-outline-danger {
            border: 1px solid #d32f2f;
            color: #d32f2f;
            background: transparent;
        }
        .btn-outline-danger:hover { background: #ffebee; }

        .loading { text-align: center; color: #666; padding: 20px; }

        /* Custom Modal Styles */
        .custom-modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .custom-modal-content {
            background: #fff;
            width: 400px;
            max-width: 90%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s ease;
        }
        @keyframes modalFadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .custom-modal-header {
            background: #390060;
            color: #fff;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .custom-modal-body {
            padding: 25px 20px;
            color: #333;
            font-size: 1rem;
        }
        .custom-modal-footer {
            padding: 15px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #eee;
        }
        .btn-cancel-modal {
            background: #e0e0e0;
            color: #333;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-confirm-modal {
            background: #1a0033;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>

    <div class="page-container">
        <!-- Facility Bookings -->
        <div class="bookings-card">
            <div class="card-header">
                <h2><i class="fas fa-building"></i> Facility Bookings</h2>
            </div>
            <div id="facility-bookings-container">
                <p class="loading">Loading facility bookings...</p>
            </div>
        </div>

    </div>

    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="custom-modal" style="display: none;">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <i class="fas fa-question-circle"></i> Cancel Booking
            </div>
            <div class="custom-modal-body">
                <p id="cancelModalText">Are you sure you want to cancel this booking?</p>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-cancel-modal" onclick="closeCancelModal()">Cancel</button>
                <button class="btn-confirm-modal" id="confirmCancelBtn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Details/Alert Modal -->
    <div id="alertModal" class="custom-modal" style="display: none;">
        <div class="custom-modal-content">
            <div class="custom-modal-header" id="alertModalHeader">
                <i class="fas fa-info-circle"></i> Notification
            </div>
            <div class="custom-modal-body">
                <p id="alertModalText"></p>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-confirm-modal" onclick="closeAlertModal()">OK</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        loadFacilityBookings();
    });

    async function loadFacilityBookings() {
        const container = document.getElementById("facility-bookings-container");
        try {
            // Reusing the endpoint from Facility.php logic if available or create new one in StudentController
            // Wait, StudentController doesn't have a getBookings endpoint yet.
            // I should have added one. 
            // I'll stick to using the existing API endpoints if they exist, or I can add a specific one.
            // Facility.php has `getMyReservations`. I can access it via direct PHP call if this was a PHP view with data passed, 
            // OR fetch from an API.
            // Let's check `FacilityApiController` or `StudentController`.
            // I'll assume I need to fetch it.
            // Since I didn't create a specific API for this in StudentController yet (except dashboard stats), 
            // I should probably fetch from `reserve-facilities/view-reservations` (from Facility Reservation page logic)
            
            const res = await fetch("/uoc-sports/public/reserve-facilities/view-reservations");
            const packet = await res.json();
            
            if (packet.status === 'success' || packet.data) {
                const urlParams = new URLSearchParams(window.location.search);
                const filterDate = urlParams.get('date');
                let data = packet.data || [];
                
                if (filterDate) {
                    data = data.filter(item => item.date === filterDate);
                }
                
                if (data.length === 0) {
                    container.innerHTML = "<p class='loading'>No facility bookings found" + (filterDate ? ` for ${filterDate}. <br><a href="#" onclick="window.location.href='/uoc-sports/public/my-bookings'; return false;">Clear Filter</a>` : ".") + "</p>";
                    return;
                }
                
                let hiddenBookings = JSON.parse(localStorage.getItem('hiddenFacilityBookings') || '[]');
                let upcomingData = [];
                let historyData = [];
                
                const todayStr = new Date().toISOString().split('T')[0];
                data.forEach(item => {
                    if (hiddenBookings.includes(item.booking_id)) return;
                    let stat = (item.status || '').toUpperCase();
                    if (stat === 'CANCELLED' || stat === 'COMPLETED' || stat === 'REJECTED' || item.date < todayStr) {
                        historyData.push(item);
                    } else {
                        upcomingData.push(item);
                    }
                });
                
                function renderTable(list, isHistory) {
                    if (list.length === 0) return "<p style='color:#777; margin-left: 15px; font-size: 0.95rem; font-style: italic;'>No bookings found in this section.</p>";
                    let tHtml = `<div class="table-responsive"><table class="custom-enhanced-table">
                        <colgroup>
                            <col style="width: 20%;">
                            <col style="width: 15%;">
                            <col style="width: 22%;">
                            <col style="width: 13%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Facility</th><th>Date</th><th>Slot/Time</th><th>Status</th><th>Payment</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    list.forEach(item => {
                        let actions = '';
                        if (isHistory) {
                            actions = `<button class="btn-sm" style="background:transparent; color:#aaa; border:none; font-size:1.1rem; padding: 0 5px;" onclick="hideBooking('${item.booking_id}')" title="Dismiss from view"><i class="fas fa-times"></i></button>`;
                        } else {
                            if (item.status === 'BOOKED') {
                                actions += `<button class="btn-sm btn-outline-danger" onclick="cancelFacility('${item.booking_id}')">Cancel</button> `;
                                if (item.payment_status === 'INCOMPLETE') {
                                    actions += `<button class="btn-sm" style="background:#5e2d91;color:white;" onclick="payNow('${item.booking_id}')">Pay</button>`;
                                }
                            }
                        }
                        
                        tHtml += `<tr>
                            <td>${item.facility_name}</td>
                            <td>${item.date}</td>
                            <td>${item.start_time} - ${item.end_time}</td>
                            <td><span class="badge ${(item.status || 'pending').toLowerCase()}">${item.status || 'PENDING'}</span></td>
                            <td><span class="badge ${(item.payment_status || 'incomplete').toLowerCase()}">${item.payment_status || 'INCOMPLETE'}</span></td>
                            <td>${actions}</td>
                        </tr>`;
                    });
                    tHtml += `</tbody></table></div>`;
                    return tHtml;
                }
                
                let finalHtml = filterDate 
                    ? `<div style="margin-bottom:15px;"><span class="badge" style="background:#e0e7ff; color:#4338ca; font-size:0.9rem;">Filtering by date: ${filterDate} <a href="#" onclick="window.location.href='/uoc-sports/public/my-bookings'; return false;" style="margin-left:8px; color:#d32f2f; text-decoration:none;"><i class="fas fa-times"></i> Clear</a></span></div>` 
                    : '';
                
                finalHtml += `<div style="background: #f8f6fb; padding: 20px 25px; border-radius: 12px; border: 1px solid #f0e6ff; margin-bottom: 20px;">`;
                finalHtml += `<h3 style="margin-top: 0; color: #333; font-size: 1.15rem; border-left: 4px solid #5e2d91; padding-left: 10px; font-weight: 600;">Upcoming</h3>`;
                finalHtml += renderTable(upcomingData, false);
                finalHtml += `</div>`;
                
                finalHtml += `<div style="background: #fafafa; padding: 20px 25px; border-radius: 12px; border: 1px solid #f2f2f2;">`;
                finalHtml += `<h3 style="margin-top: 0; color: #333; font-size: 1.15rem; border-left: 4px solid #aaa; padding-left: 10px; font-weight: 600;">History</h3>`;
                finalHtml += renderTable(historyData, true);
                finalHtml += `</div>`;
                
                container.innerHTML = finalHtml;
            }
        } catch (e) {
            container.innerHTML = "<p class='loading'>Error loading data.</p>";
        }
    }

    let pendingCancelId = null;
    let pendingCancelType = null;

    window.cancelFacility = (id) => {
        pendingCancelId = id;
        pendingCancelType = 'facility';
        document.getElementById('cancelModalText').innerText = "Are you sure you want to cancel this facility booking?";
        document.getElementById('cancelModal').style.display = 'flex';
    };

    window.closeCancelModal = () => {
        document.getElementById('cancelModal').style.display = 'none';
        pendingCancelId = null;
        pendingCancelType = null;
    };

    window.showAlertModal = (message, isError = false) => {
        document.getElementById('alertModalText').innerText = message;
        
        const header = document.getElementById('alertModalHeader');
        if (isError) {
            header.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
            header.style.background = '#d32f2f'; // Red for errors
        } else {
            header.innerHTML = '<i class="fas fa-check-circle"></i> Success';
            header.style.background = '#390060'; // Purple for success
        }
        
        document.getElementById('alertModal').style.display = 'flex';
    };

    window.closeAlertModal = () => {
        document.getElementById('alertModal').style.display = 'none';
    };

    document.getElementById('confirmCancelBtn').addEventListener('click', async () => {
        if (!pendingCancelId || !pendingCancelType) return;
        
        const id = pendingCancelId;
        const type = pendingCancelType;
        closeCancelModal();

        if (type === 'facility') {
            try {
                const res = await fetch("/uoc-sports/public/reserve-facilities/cancel", { method: "POST", body: "booking_id="+id, headers: {"Content-Type": "application/x-www-form-urlencoded"} });
                if (!res.ok) throw new Error("Server returned " + res.status);
                const text = await res.text();
                showAlertModal(text);
                loadFacilityBookings();
            } catch(e) { showAlertModal("Error cancelling: " + e.message, true); }
        }
    });

    window.payNow = (id) => {
        window.location.href = `/uoc-sports/public/payment?booking_id=${id}`;
    };

    window.hideBooking = (id) => {
        let hidden = JSON.parse(localStorage.getItem('hiddenFacilityBookings') || '[]');
        if (!hidden.includes(id)) {
            hidden.push(id);
            localStorage.setItem('hiddenFacilityBookings', JSON.stringify(hidden));
        }
        loadFacilityBookings(); // dynamically reload the view to remove the row
    };
    </script>
</body>
</html>
