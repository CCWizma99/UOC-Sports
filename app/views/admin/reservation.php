<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/reservation-details.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<section id="reservation-details">
    <h2>Reservation Details</h2>
    
    <div id="loading-message">
        <p>Loading reservation details...</p>
    </div>

    <div id="error-message" style="display: none;">
        <p class="error-text"></p>
    </div>

    <div id="details-container" style="display: none;">
        <div class="details-card">
            <div class="card-header">
                <h3>Booking Information</h3>
                <span class="status-badge" id="status-badge"></span>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <label>Booking ID:</label>
                    <span id="booking-id"></span>
                </div>
                <div class="detail-item">
                    <label>Date:</label>
                    <span id="date"></span>
                </div>
                <div class="detail-item">
                    <label>Time Slot:</label>
                    <span id="time-slot"></span>
                </div>
                <div class="detail-item">
                    <label>Facility:</label>
                    <span id="facility"></span>
                </div>
                <div class="detail-item">
                    <label>User Name:</label>
                    <span id="user-name"></span>
                </div>
                <div class="detail-item">
                    <label>User Email:</label>
                    <span id="user-email"></span>
                </div>
                <div class="detail-item">
                    <label>User Type:</label>
                    <span id="user-type"></span>
                </div>
                <div class="detail-item">
                    <label>Payment Status:</label>
                    <span id="payment-status"></span>
                </div>
                <div class="detail-item full-width">
                    <label>Purpose:</label>
                    <span id="purpose"></span>
                </div>
                <div class="detail-item full-width" id="rejection-reason-container" style="display: none;">
                    <label>Rejection Reason:</label>
                    <span id="rejection-reason" class="rejection-text"></span>
                </div>
            </div>

            <div class="action-buttons" id="action-buttons">
                <button class="btn-accept" id="accept-btn">
                    <i class="fa-solid fa-check"></i> Accept Reservation
                </button>
                <button class="btn-reject" id="reject-btn">
                    <i class="fa-solid fa-times"></i> Reject Reservation
                </button>
            </div>
        </div>

        <!-- Weekly Reservations Section -->
        <div class="weekly-reservations">
            <h3>Reservations This Week</h3>
            <div id="weekly-loading">
                <p>Loading weekly reservations...</p>
            </div>
            <div id="weekly-table-container" style="display: none;">
                <table class="weekly-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Facility</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody id="weekly-tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Reject Modal -->
<div id="reject-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Reservation</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <label for="rejection-reason-input">Rejection Reason:</label>
            <textarea id="rejection-reason-input" rows="4" placeholder="Enter the reason for rejecting this reservation..."></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="cancel-reject">Cancel</button>
            <button class="btn-confirm-reject" id="confirm-reject">Confirm Rejection</button>
        </div>
    </div>
</div>

<?php require '../app/views/templates/admin/footer.php'; ?>

<script>
const urlParams = new URLSearchParams(window.location.search);
const bookingId = urlParams.get('id');

if (!bookingId) {
    document.getElementById('loading-message').style.display = 'none';
    document.getElementById('error-message').style.display = 'block';
    document.querySelector('.error-text').textContent = 'No booking ID provided.';
} else {
    loadReservationDetails();
}

function loadReservationDetails() {
    fetch(`/uoc-sports/public/api/reservation-details.php?id=${bookingId}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            // Hide loading, show details
            document.getElementById('loading-message').style.display = 'none';
            document.getElementById('details-container').style.display = 'block';

            // Populate details
            document.getElementById('booking-id').textContent = data.booking_id;
            document.getElementById('date').textContent = data.date;
            document.getElementById('time-slot').textContent = data.time_range;
            document.getElementById('facility').textContent = data.facility_name;
            document.getElementById('user-name').textContent = data.user_name;
            document.getElementById('user-email').textContent = data.user_email;
            document.getElementById('user-type').textContent = data.user_type;
            document.getElementById('payment-status').textContent = data.payment_status;
            document.getElementById('purpose').textContent = data.purpose;

            // Status badge
            const statusBadge = document.getElementById('status-badge');
            statusBadge.textContent = data.status;
            statusBadge.className = 'status-badge status-' + data.status.toLowerCase();

            // Show rejection reason if rejected
            if (data.status === 'REJECTED' && data.rejection_reason) {
                document.getElementById('rejection-reason-container').style.display = 'flex';
                document.getElementById('rejection-reason').textContent = data.rejection_reason;
            }

            // Show/hide action buttons based on status
            if (data.status === 'BOOKED') {
                document.getElementById('action-buttons').style.display = 'flex';
            } else {
                document.getElementById('action-buttons').style.display = 'none';
            }

            // Load weekly reservations
            loadWeeklyReservations(data.date);
        })
        .catch(err => {
            document.getElementById('loading-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'block';
            document.querySelector('.error-text').textContent = err.message;
        });
}

function loadWeeklyReservations(date) {
    fetch(`/uoc-sports/public/api/week-reservations-api.php?date=${date}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('weekly-loading').style.display = 'none';
            document.getElementById('weekly-table-container').style.display = 'block';

            const tbody = document.getElementById('weekly-tbody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No reservations found for this week.</td></tr>';
                return;
            }

            data.forEach(r => {
                const row = document.createElement('tr');
                if (r.booking_id === bookingId) {
                    row.classList.add('current-reservation');
                }

                row.innerHTML = `
                    <td>${r.booking_id}</td>
                    <td>${r.date}</td>
                    <td>${r.start_time}</td>
                    <td>${r.facility_name}</td>
                    <td>${r.user_name}</td>
                    <td><span class="status-badge status-${r.status.toLowerCase()}">${r.status}</span></td>
                    <td>${r.payment_status}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error('Error loading weekly reservations:', err);
        });
}

// Accept button
document.getElementById('accept-btn').addEventListener('click', () => {
    if (!confirm('Are you sure you want to accept this reservation?')) {
        return;
    }

    fetch('/uoc-sports/public/api/accept-reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Reservation accepted successfully!');
            location.reload();
        } else {
            alert(data.error || 'Failed to accept reservation.');
        }
    })
    .catch(err => {
        alert('An error occurred: ' + err.message);
    });
});

// Reject button - open modal
document.getElementById('reject-btn').addEventListener('click', () => {
    document.getElementById('reject-modal').style.display = 'flex';
});

// Close modal
document.querySelector('.close-modal').addEventListener('click', () => {
    document.getElementById('reject-modal').style.display = 'none';
});

document.getElementById('cancel-reject').addEventListener('click', () => {
    document.getElementById('reject-modal').style.display = 'none';
});

// Confirm rejection
document.getElementById('confirm-reject').addEventListener('click', () => {
    const reason = document.getElementById('rejection-reason-input').value.trim();

    if (!reason) {
        alert('Please enter a rejection reason.');
        return;
    }

    fetch('/uoc-sports/public/api/reject-reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: bookingId, reason: reason })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Reservation rejected successfully!');
            location.reload();
        } else {
            alert(data.error || 'Failed to reject reservation.');
        }
    })
    .catch(err => {
        alert('An error occurred: ' + err.message);
    });
});

// Set active sidebar item
var currentPage = document.getElementById("sidebar-reservations");
currentPage.classList.add("active");
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</body>
</html>
