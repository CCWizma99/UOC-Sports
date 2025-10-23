<section id="reserved-section" class="reserved-section">
  <h2>Reserved Items</h2>
  <div class="reserved-container" id="reserved-container">
    <p>Loading reserved items...</p>
  </div>
</section>

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

            const data = response.data; // Use the 'data' array from backend

            if (!data || data.length === 0) {
                container.innerHTML = "<p class='no-reservations'>No reserved items yet.</p>";
                return;
            }

            data.forEach(item => {
                container.innerHTML += `
                    <div class="reserved-item">
                        <img src="/uoc-sports/public/images/equipment/${item.image}" alt="${item.equipment_name}">
                        <div class="reserved-details">
                            <h3>${item.equipment_name}</h3>
                            <p>Reserved on: ${item.request_date}</p>
                            <p>Status: ${item.status}</p>
                        </div>
                        <button class="cancel-reservation" onclick="cancelReservation('${item.request_id}')">Cancel</button>
                    </div>
                `;
            });
        })
        .catch(() => {
            document.getElementById("reserved-container").innerHTML = "<p>Error loading reserved items.</p>";
        });
}

function cancelReservation(reservationId) {
    fetch("/uoc-sports/public/reserve-equipments/cancel", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "reservation_id=" + encodeURIComponent(reservationId)
    })
    .then(res => res.text())
    .then(msg => {
        showFloatingMessage(msg, "success");
        fetchReservedItems();
    })
    .catch(() => showFloatingMessage("Error cancelling reservation.", "error"));
}
</script>

