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
    </style>
</head>
<body class="mesh-sporty">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

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

        <!-- Equipment Reservations -->
        <div class="bookings-card">
            <div class="card-header">
                <h2><i class="fas fa-dumbbell"></i> Equipment Reservations</h2>
            </div>
            <div id="equipment-bookings-container">
                <p class="loading">Loading equipment reservations...</p>
            </div>
        </div>
    </div>

    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        loadFacilityBookings();
        loadEquipmentBookings();
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
                const data = packet.data || [];
                if (data.length === 0) {
                    container.innerHTML = "<p class='loading'>No facility bookings found.</p>";
                    return;
                }
                
                let html = `<table class="booking-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Facility</th><th>Date</th><th>Slot/Time</th><th>Status</th><th>Payment</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>`;
                
                data.forEach(item => {
                    html += `<tr>
                        <td>#${item.booking_id}</td>
                        <td>${item.facility_name}</td>
                        <td>${item.date}</td>
                        <td>${item.start_time} - ${item.end_time}</td>
                        <td><span class="badge ${item.status.toLowerCase()}">${item.status}</span></td>
                        <td><span class="badge ${item.payment_status.toLowerCase()}">${item.payment_status}</span></td>
                        <td>
                            ${item.status === 'BOOKED' ? `
                                <button class="btn-sm btn-outline-danger" onclick="cancelFacility('${item.booking_id}')">Cancel</button>
                                ${item.payment_status === 'INCOMPLETE' ? `<button class="btn-sm" style="background:#5e2d91;color:white;" onclick="payNow('${item.booking_id}')">Pay</button>` : ''}
                            ` : ''}
                        </td>
                    </tr>`;
                });
                
                html += `</tbody></table>`;
                container.innerHTML = html;
            }
        } catch (e) {
            container.innerHTML = "<p class='loading'>Error loading data.</p>";
        }
    }

    async function loadEquipmentBookings() {
        const container = document.getElementById("equipment-bookings-container");
        try {
            const res = await fetch("/uoc-sports/public/reserve-equipments/view"); // From previous student-portal.php logic
            const packet = await res.json();
            const data = packet.data || [];

            if (data.length === 0) {
                container.innerHTML = "<p class='loading'>No equipment reservations found.</p>";
                return;
            }

            let html = `<table class="booking-table">
                <thead>
                    <tr>
                        <th>Item</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>`;
            
            data.forEach(item => {
                html += `<tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="/uoc-sports/public/images/equipment-types/${item.image_name}" width="30" onerror="this.style.display='none'">
                            ${item.equipment_name}
                        </div>
                    </td>
                    <td>${new Date(item.request_date).toLocaleDateString()}</td>
                    <td>${item.start_time} - ${item.end_time}</td>
                    <td><span class="badge ${item.status.toLowerCase()}">${item.status}</span></td>
                    <td>
                        ${item.status === 'ACTIVE' ? `
                            <button class="btn-sm btn-outline-danger" onclick="cancelEquipment('${item.request_id}')">Cancel</button>
                        ` : ''}
                    </td>
                </tr>`;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        } catch (e) {
            container.innerHTML = "<p class='loading'>Error loading data.</p>";
        }
    }

    window.cancelFacility = async (id) => {
        if(!confirm("Cancel this booking?")) return;
        try {
            const fd = new FormData(); fd.append("booking_id", id);
            await fetch("/uoc-sports/public/reserve-facilities/cancel", { method: "POST", body: "booking_id="+id, headers: {"Content-Type": "application/x-www-form-urlencoded"} });
            loadFacilityBookings();
        } catch(e) { alert("Error cancelling"); }
    };

    window.cancelEquipment = async (id) => {
        if(!confirm("Cancel this reservation?")) return;
        try {
            await fetch("/uoc-sports/public/reserve-equipments/cancel", { 
                method: "POST", 
                body: "reservation_id="+id, 
                headers: {"Content-Type": "application/x-www-form-urlencoded"} 
            });
            loadEquipmentBookings();
        } catch(e) { alert("Error cancelling"); }
    };

    window.payNow = (id) => {
        window.location.href = `/uoc-sports/public/payment?booking_id=${id}`;
    };
    </script>
</body>
</html>
