<section id="add-equipment">
  <h2>Add Equipment</h2>
  <form id="add-equipment-form" enctype="multipart/form-data">
    
    <div class="input-div">
      <label for="sport">Sport</label>
      <select id="sport" name="sport_id" onchange="loadEquipments()" required>
        <option value="">Loading sports...</option>
      </select>
    </div>

    <div class="input-div">
      <label for="equipment-name">Equipment Name</label>
      <select id="equipment" name="equipment_id" required>
        <option value="">Loading sports...</option>
      </select>
    </div>

    <div class="input-div">
      <label for="quantity">Number of Items</label>
      <input type="number" id="quantity" name="quantity" min="1" required>
    </div>

    <div class="input-div">
      <label for="date">Date</label>
      <div style="display:flex; gap:10px;">
        <input type="date" id="date" name="date" style="flex-grow: 1;" required>
        <button type="button" class="btn today-btn" onclick="setToday()">Today</button>
      </div>
    </div>


    <div class="input-div">
      <label for="condition">Condition</label>
      <select id="condition" name="equipment_condition" required>
        <option value="USABLE">Usable</option>
        <option value="DAMAGED">Damaged</option>
        <option value="REPAIR">Needs Repair</option>
      </select>
    </div>

    <div class="input-div">
      <label for="remarks">Special Remarks</label>
      <textarea id="remarks" name="remarks" rows="3"></textarea>
    </div>

    <button type="submit" class="btn">Add Equipment</button>
    <div id="form-message"></div>
  </form>
</section>

<section id="add-equipment-type">
  <h2>Add New Equipment Type</h2>

  <form id="add-equipment-type-form" enctype="multipart/form-data">

    <!-- Sport Selection -->
    <div class="input-div">
      <label for="new-equipment-sport">Sport</label>
      <select id="new-equipment-sport" name="sport_id" required>
        <option value="">Select a sport</option>
        <!-- You can reuse the same sports loader for this -->
      </select>
    </div>

    <!-- Equipment Name -->
    <div class="input-div">
      <label for="new-equipment-name">Equipment Name</label>
      <input 
        type="text" 
        id="new-equipment-name" 
        name="equipment_name" 
        placeholder="Eg: Boxing Gloves, Cricket Bat"
        required
      >
    </div>

    <!-- Optional Image Upload -->
    <div class="input-div">
      <label for="equipment-image">Equipment Image (Optional)</label>
      <input type="file" id="equipment-image" name="image" accept="image/*">
    </div>

    <button type="submit" class="btn">Add Equipment Type</button>
    <div id="equipment-type-message"></div>

  </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const sportSelect = document.getElementById("sport");
  const equipmentSelect = document.getElementById("equipment");

  const newSportSelect = document.getElementById("new-equipment-sport");

  // Load sports
  try {
    const resSpo = await fetch("admin-equipments/get-sports");
    const data = await resSpo.json();
    sportSelect.innerHTML = '<option value="">Select a sport</option>';
    if (data.status === "success") {
      data.data.forEach(s => {
        sportSelect.innerHTML += `<option value="${s.sport_id}">${s.sport_name}</option>`;
      });
    }
  } catch {
    sportSelect.innerHTML = '<option value="">Error Loading Sports</option>';
  }

  // Submit form
  document.getElementById("add-equipment-form").addEventListener("submit", async e => {
  e.preventDefault();

  const form = e.target;
  const msg = document.getElementById("form-message");
  const formData = new FormData(form);

  msg.textContent = "Submitting...";
  msg.style.color = "blue";

  try {
    const res = await fetch("admin-equipments/add-stock", {
      method: "POST",
      body: formData
    });

    const result = await res.json();

    if (result.status === "success") {
      msg.textContent = result.message;
      msg.style.color = "green";
      form.reset();
    } else {
      msg.textContent = result.message;
      msg.style.color = "red";
    }

  } catch {
    msg.textContent = "Network error.";
    msg.style.color = "red";
  }
});


  if (newSportSelect) {
    newSportSelect.innerHTML = sportSelect.innerHTML;
  }

});

  //Load equipments
  async function loadEquipments() {
  const sportSelect = document.getElementById("sport");
  const equipmentSelect = document.getElementById("equipment");
  const sportId = sportSelect.value;

  try {
    const resEqu = await fetch(`admin-equipments/get-equipments?sport_id=${sportId}`);
    const dataEqu = await resEqu.json();
    
    equipmentSelect.innerHTML = '<option value="">Select an equipment</option>';
    if (dataEqu.status === "success") {
      if (dataEqu.data.length === 0) {
        equipmentSelect.innerHTML = '<option value="">Sorry, Failed to Find Equipments</option>';
      } else {
        dataEqu.data.forEach(e => {
          equipmentSelect.innerHTML += `<option value="${e.equipment_id}">${e.equipment_name}</option>`;
        });
      }
    }
  } catch {
    equipmentSelect.innerHTML = '<option value="">Error Loading equipments</option>';
  }
}

function setToday() {
  const today = new Date().toISOString().split("T")[0];
  document.getElementById("date").value = today;
}

document.getElementById("add-equipment-type-form").addEventListener("submit", async e => {
  e.preventDefault();

  const form = e.target;
  const msg = document.getElementById("equipment-type-message");
  const formData = new FormData(form);

  msg.textContent = "Adding equipment...";
  msg.style.color = "blue";

  try {
    const res = await fetch("admin-equipments/add-equipment-type", {
      method: "POST",
      body: formData
    });

    const result = await res.json();

    if (result.status === "success") {
      msg.textContent = result.message;
      msg.style.color = "green";
      form.reset();

      // 🔥 AUTO reload equipment list if a sport is already selected
      loadEquipments();

    } else {
      msg.textContent = result.message;
      msg.style.color = "red";
    }

  } catch {
    msg.textContent = "Network error while adding equipment.";
    msg.style.color = "red";
  }
});


</script>
