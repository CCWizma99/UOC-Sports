<div class="container">
  <section id="reserve-equipment">
    <h2><i class="fas fa-clipboard-list"></i> Reserve Equipment</h2>
    <form id="reserve-equipment-form">
      <div class="input-div">
        <label for="equipment-search"><i class="fas fa-search"></i> Search Equipment</label>
        <input type="text" id="equipment-search" name="equipment_name" placeholder="Start typing equipment name..." autocomplete="off" required>
        <ul id="suggestions"></ul>
      </div>

      <div id="reserved-times-div" class="reserved-times-box">
        <h3><i class="fas fa-calendar-alt"></i> Reserved Times</h3>
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
        <label for="student-id"><i class="fas fa-id-card"></i> Student ID</label>
        <input type="text" id="student-id" name="student_id" value="<?= htmlspecialchars($student_id['student_id'])?>" readonly>
      </div>

      <div class="input-row">
        <div class="input-div half">
          <label for="date"><i class="fas fa-calendar"></i> Date</label>
          <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="input-div half">
          <label for="start-time"><i class="fas fa-clock"></i> Start Time</label>
          <input type="time" id="start-time" name="start_time" min="06:00" max="20:00" step="1800" required>
        </div>

        <div class="input-div half">
          <label for="end-time"><i class="fas fa-clock"></i> End Time</label>
          <input type="time" id="end-time" name="end_time" min="06:00" max="20:00" step="1800" required>
        </div>
      </div>

      <div class="input-div">
        <label for="purpose"><i class="fas fa-bullseye"></i> Purpose</label>
        <textarea id="purpose" name="purpose" rows="2" placeholder="Enter the purpose of reservation..." required></textarea>
      </div>

      <div class="input-div">
        <label for="notes"><i class="fas fa-sticky-note"></i> Additional Notes</label>
        <textarea id="notes" name="notes" rows="2" placeholder="Any additional information (optional)"></textarea>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-check-circle"></i> Reserve Equipment
      </button>
      <div id="reserve-message"></div>
    </form>
  </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("equipment-search");
  const suggestions = document.getElementById("suggestions");
  const msg = document.getElementById("reserve-message");
  const timesDiv = document.getElementById("reserved-times-div");
  const timesTable = document.getElementById("reserved-times").querySelector("tbody");
  let selectedEquipmentId = null;

  suggestions.style.display = "none";

  searchInput.addEventListener("input", async () => {
    const q = searchInput.value.trim();
    if (q.length < 1) {
      suggestions.innerHTML = "";
      suggestions.style.display = "none";
      return;
    }

    const res = await fetch(`/uoc-sports/public/reserve-equipments/search?q=${encodeURIComponent(q)}`);
    const data = await res.json();
    suggestions.innerHTML = "";
    suggestions.style.display = "none";

    if (data.status === "success" && data.data.length > 0) {
      suggestions.style.display = "block";
      data.data.forEach(eq => {
        const li = document.createElement("li");
        
        // Equipment image
        const img = document.createElement("img");
        img.className = "suggestion-image";
        if (eq.image_name && eq.image_name.trim() !== '') {
          img.src = `/uoc-sports/public/images/equipment-types/${eq.image_name}`;
        } else {
          img.src = `https://via.placeholder.com/50?text=${eq.equipment_name.charAt(0)}`;
        }
        img.alt = eq.equipment_name;
        img.onerror = function() {
          this.src = `https://via.placeholder.com/50?text=${eq.equipment_name.charAt(0)}`;
        };
        
        // Equipment details
        const details = document.createElement("div");
        details.className = "suggestion-details";
        
        const name = document.createElement("div");
        name.className = "suggestion-name";
        name.textContent = eq.equipment_name;
        
        const sport = document.createElement("div");
        sport.className = "suggestion-sport";
        sport.textContent = eq.sport_name;
        
        details.appendChild(name);
        details.appendChild(sport);
        
        // Availability indicator
        const availability = document.createElement("div");
        availability.className = "suggestion-availability";
        const availQty = parseInt(eq.available_quantity);
        
        if (availQty > 10) {
          availability.classList.add("availability-high");
          availability.textContent = `${availQty} available`;
        } else if (availQty > 0) {
          availability.classList.add("availability-low");
          availability.textContent = `${availQty} left`;
        } else {
          availability.classList.add("availability-none");
          availability.textContent = "Unavailable";
        }
        
        li.appendChild(img);
        li.appendChild(details);
        li.appendChild(availability);
        li.dataset.id = eq.equipment_id;
        li.addEventListener("click", () => selectEquipment(eq));
        suggestions.appendChild(li);
      });
    } else if (data.status === "success" && data.data.length === 0) {
      suggestions.style.display = "block";
      const li = document.createElement("li");
      li.textContent = "No available equipment found";
      li.style.color = "#999";
      li.style.cursor = "default";
      suggestions.appendChild(li);
    }
  });

  function selectEquipment(eq) {
    searchInput.value = eq.equipment_name;
    selectedEquipmentId = eq.equipment_id;
    suggestions.innerHTML = "";
    loadReservedTimes(eq.equipment_id);
  }

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

  const roundTo30 = (timeStr) => {
    if (!timeStr) return "";
    const [hours, minutes] = timeStr.split(':').map(Number);
    const roundedMinutes = minutes < 15 ? 0 : (minutes < 45 ? 30 : 0);
    let roundedHours = (minutes >= 45) ? (hours + 1) % 24 : hours;
    return `${String(roundedHours).padStart(2, '0')}:${String(roundedMinutes).padStart(2, '0')}`;
  };

  ['start-time', 'end-time'].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('change', () => {
      const rounded = roundTo30(el.value);
      if (rounded !== el.value) {
        el.value = rounded;
      }
    });
  });

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
