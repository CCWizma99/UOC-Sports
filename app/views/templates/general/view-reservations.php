<div class="container" style="margin-top: 120px;">
    <section id="reserved-section" class="reserved-section">
        <h2><i class="fas fa-box-open"></i> Reserved Items</h2>
        <div class="reserved-container" id="reserved-container">
            <p>Loading reserved items...</p>
        </div>
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    fetchReservedItems();
});

function fetchReservedItems() {
    fetch("/uoc-sports/public/reserve-equipments/view")
        .then(res => res.json())
        .then(response => {
            const container = document.getElementById("reserved-container");
            container.innerHTML = "";
            const data = response.data;

            if (!data || data.length === 0) {
                container.innerHTML = "<p class='no-reservations'><i class='fas fa-inbox'></i> No reserved items yet.</p>";
                return;
            }

            data.forEach(item => {
                const statusClass = item.status.toLowerCase();
                container.innerHTML += `
                    <div class="reserved-item">
                        <img 
                            src="/uoc-sports/public/images/equipment-types/${item.image_name}" 
                            alt="${item.equipment_name}"
                            onerror="this.onerror=null; this.src='https://via.placeholder.com/120?text=${item.equipment_name.charAt(0)}';"
                        >
                        <div class="reserved-details">
                            <h3>${item.equipment_name}</h3>
                            <p><i class="fas fa-calendar"></i> <strong>Reserved on:</strong> ${new Date(item.request_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                            <p><i class="fas fa-clock"></i> <strong>Time:</strong> ${item.start_time} - ${item.end_time}</p>
                            <p><i class="fas fa-bullseye"></i> <strong>Purpose:</strong> ${item.purpose || 'N/A'}</p>
                            <span class="status-badge ${statusClass}">
                                <i class="fas fa-${statusClass === 'active' ? 'check-circle' : 'times-circle'}"></i>
                                ${item.status}
                            </span>
                        </div>
                        ${item.status === 'ACTIVE' ? `
                            <button class="cancel-reservation" onclick="cancelReservation('${item.request_id}')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        ` : ''}
                    </div>
                `;
            });
        })
        .catch(() => {
            document.getElementById("reserved-container").innerHTML = "<p class='no-reservations'><i class='fas fa-exclamation-triangle'></i> Error loading reserved items.</p>";
        });
}

function cancelReservation(reservationId) {
    UI.confirm('Are you sure you want to cancel this reservation?', () => {
        fetch("/uoc-sports/public/reserve-equipments/cancel", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "reservation_id=" + encodeURIComponent(reservationId)
        })
        .then(res => res.text())
        .then(msg => {
            UI.showToast(msg, 'success');
            fetchReservedItems();
        })
        .catch(() => UI.showToast("Error cancelling reservation.", 'error'));
    }, null, true);
}
</script>
