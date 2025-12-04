<section id="add-equipment">
  <h2>Add Equipment</h2>
  <form id="add-equipment-form" enctype="multipart/form-data">
    
    <div class="input-div">
      <label for="sport">Sport</label>
      <select id="sport" name="sport_id" onchange="loadEquipmets()" required>
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
      <input type="date" id="date" name="date" required>
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

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const sportSelect = document.getElementById("sport");
  const equipmentSelect = document.getElementById("equipment");

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

  //Load equipments
  function loadEquipmets() {
    try {
      const resEqu = fetch(`admin-equipments/get-equipments?sport_id=${variable}`);
      const dataEqu = resEqu.json();
      equipmentSelect.innerHTML = '<option>Select an equipment</option>';
      if (dataEqu.status === "success") {
        if (dataEqu.data.length === 0){
          equipmentSelect.innerHTML = '<option value="">Sorry, Failed to Find Equipments</option>';
        }
        else{
          dataEqu.data.forEach(e=>{
            equipmentSelect.innerHTML += `<option value="${e.equipment_id}">${e.equipment_name}</option>`;
          })
        }
      }
    } catch {
      equipmentSelect.innerHTML = '<option value="">Error Loading equipments</option>';
    }
  }

  // Submit form
  document.getElementById("add-equipment-form").addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    msg.textContent = "Submitting...";
    msg.style.color = "blue";

    try {
      const res = await fetch("admin-equipments/add", { method: "POST", body: formData });
      const result = await res.json();
      if (result.status === "success") {
        msg.textContent = "Equipment added successfully!";
        msg.style.color = "green";
        e.target.reset();
        previewDiv.innerHTML = "";
      } else {
        msg.textContent = result.message;
        msg.style.color = "red";
      }
    } catch {
      msg.textContent = "Error submitting form.";
      msg.style.color = "red";
    }
  });
});
</script>
