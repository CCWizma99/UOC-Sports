<section id="reserve-equipment">
  <h2>Reserve Equipment</h2>
  <form id="reserve-equipment-form">
    <div class="input-div">
      <label for="equipment-search">Search Equipment</label>
      <input type="text" id="equipment-search" name="equipment_name" placeholder="Start typing..." autocomplete="off" required>
      <ul id="suggestions"></ul>
    </div>

    <div id="reserved-times-div" class="reserved-times-box">
      <h3>Reserved Times</h3>
      <table id="reserved-times" class="styled-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Reserved By</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="input-div">
      <label for="student-id">Student ID</label>
      <input type="text" id="student-id" name="student_id" required>
    </div>

    <div class="input-row">
      <div class="input-div half">
        <label for="date">Date</label>
        <input type="date" id="date" name="date" required>
      </div>

      <div class="input-div half">
        <label for="start-time">Start Time</label>
        <input type="time" id="start-time" name="start_time" required>
      </div>

      <div class="input-div half">
        <label for="end-time">End Time</label>
        <input type="time" id="end-time" name="end_time" required>
      </div>
    </div>

    <div class="input-div">
      <label for="purpose">Purpose</label>
      <textarea id="purpose" name="purpose" rows="2" required></textarea>
    </div>

    <div class="input-div">
      <label for="notes">Additional Notes</label>
      <textarea id="notes" name="notes" rows="2"></textarea>
    </div>

    <button type="submit" class="btn">Reserve Equipment</button>
    <div id="reserve-message"></div>
  </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("equipment-search");
  const suggestions = document.getElementById("suggestions");
  const msg = document.getElementById("reserve-message");
  const timesDiv = document.getElementById("reserved-times-div");
  const timesTable = document.getElementById("reserved-times").querySelector("tbody");
  let selectedEquipmentId = null;

  // 🔍 Equipment name suggestion
  searchInput.addEventListener("input", async () => {
    const q = searchInput.value.trim();
    if (q.length < 2) {
      suggestions.innerHTML = "";
      return;
    }

    const res = await fetch(`/uoc-sports/public/reserve-equipments/search?q=${encodeURIComponent(q)}`);
    const data = await res.json();
    suggestions.innerHTML = "";

    if (data.status === "success") {
      data.data.forEach(eq => {
        const li = document.createElement("li");
        li.textContent = `${eq.equipment_name} (${eq.sport_name})`;
        li.dataset.id = eq.equipment_id;
        li.addEventListener("click", () => selectEquipment(eq));
        suggestions.appendChild(li);
      });
    }
  });

  function selectEquipment(eq) {
    searchInput.value = eq.equipment_name;
    selectedEquipmentId = eq.equipment_id;
    suggestions.innerHTML = "";
    loadReservedTimes(eq.equipment_id);
  }

  // ⏱ Load reserved times
  async function loadReservedTimes(equipmentId) {
    const res = await fetch(`/uoc-sports/public/reserve-equipments/get-times?equipment_id=${equipmentId}`);
    const data = await res.json();
    timesTable.innerHTML = "";

    if (data.status === "success" && data.data.length > 0) {
      timesDiv.style.display = "block";
      data.data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.request_date}</td>
          <td>${row.start_time}</td>
          <td>${row.end_time}</td>
          <td>${row.student_id}</td>
        `;
        timesTable.appendChild(tr);
      });
    } else {
      timesDiv.style.display = "none";
    }
  }

  // 🧾 Submit reservation
  document.getElementById("reserve-equipment-form").addEventListener("submit", async e => {
    e.preventDefault();
    msg.textContent = "";
    msg.className = "";

    if (!selectedEquipmentId) {
      msg.textContent = "Please select a valid equipment from the suggestions.";
      msg.classList.add("error");
      return;
    }

    const formData = new FormData(e.target);
    formData.append("equipment_id", selectedEquipmentId);

    const res = await fetch("/uoc-sports/public/reserve-equipments/add", {
      method: "POST",
      body: formData
    });

    const result = await res.json();
    msg.textContent = result.message;
    msg.classList.add(result.status === "success" ? "success" : "error");

    if (result.status === "success") {
      e.target.reset();
      loadReservedTimes(selectedEquipmentId);
    }
  });
});
</script>
