<section id="add-equipment">
  <h2>Add Equipment</h2>
  <form id="add-equipment-form" enctype="multipart/form-data">
    <div class="input-div">
      <label for="equipment-name">Equipment Name</label>
      <input type="text" id="equipment-name" name="equipment_name" required>
    </div>

    <div class="input-div">
      <label for="sport">Sport</label>
      <select id="sport" name="sport_id" required>
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

    <div class="input-div">
      <label for="equipment-images">Upload Images</label>
      <input type="file" id="equipment-images" name="images[]" multiple>
      <div id="file-preview"></div>
    </div>

    <button type="submit" class="btn">Add Equipment</button>
    <div id="form-message"></div>
  </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const sportSelect = document.getElementById("sport");
  const previewDiv = document.getElementById("file-preview");
  const fileInput = document.getElementById("equipment-images");
  const msg = document.getElementById("form-message");

  // Load sports
  try {
    const res = await fetch("admin-equipments/get-sports");
    const data = await res.json();
    sportSelect.innerHTML = '<option value="">Select a sport</option>';
    if (data.status === "success") {
      data.data.forEach(s => {
        sportSelect.innerHTML += `<option value="${s.sport_id}">${s.sport_name}</option>`;
      });
    }
  } catch {
    sportSelect.innerHTML = '<option value="">Error loading sports</option>';
  }

  // Image preview
  fileInput.addEventListener("change", () => {
    previewDiv.innerHTML = "";
    [...fileInput.files].forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.width = "100px";
        img.style.height = "100px";
        img.style.objectFit = "cover";
        img.style.marginRight = "5px";
        img.style.borderRadius = "8px";
        previewDiv.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  });

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
