<section id="add-match-result">
  <h2>Add Match Result</h2>
  <form id="add-match-form">
    
    <!-- Tournament Select -->
    <div class="input-div">
      <label for="tournament">Tournament</label>
      <select id="tournament" name="tournament_id" required>
        <option value="">Loading tournaments...</option>
      </select>
    </div>

    <!-- Sport Select -->
    <div class="input-div">
      <label for="sport">Sport</label>
      <select id="sport" name="sport_id" required>
        <option value="">Select sport</option>
      </select>
    </div>

    <!-- Match Name -->
    <div class="input-div">
      <label for="match-name">Match Name</label>
      <input type="text" id="match-name" name="match_name" placeholder="Quarter Final / Semi Final..." required>
    </div>

    <!-- Match Date -->
    <div class="input-div">
      <label for="match-date">Match Date</label>
      <input type="datetime-local" id="match-date" name="match_date" required>
    </div>

    <!-- Winner (Student) -->
    <div class="input-div">
      <label for="winner">Winner (Student)</label>
      <select id="winner" name="winner_id">
        <option value="">Select winner (optional)</option>
      </select>
    </div>

    <!-- Dynamic fields container -->
    <div id="dynamic-fields"></div>

    <button type="submit" class="btn">Add Result</button>
    <div id="form-message"></div>
  </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const tournamentSelect = document.getElementById("tournament");
  const sportSelect = document.getElementById("sport");
  const winnerSelect = document.getElementById("winner");
  const dynamicFields = document.getElementById("dynamic-fields");
  const form = document.getElementById("add-match-form");
  const msg = document.getElementById("form-message");

  // === Load tournaments ===
  try {
    const res = await fetch("admin-sport/get-tournaments");
    const data = await res.json();
    tournamentSelect.innerHTML = '<option value="">Select tournament</option>';
    if(data.status === "success"){
      data.data.forEach(t => {
        const opt = document.createElement("option");
        opt.value = t.tournament_id;
        opt.textContent = t.tournament_name;
        tournamentSelect.appendChild(opt);
      });
    }
    else if (data.status === "empty"){
      tournamentSelect.innerHTML = '<option>' + data.data + '</option>';
    }
  } catch(err){ tournamentSelect.innerHTML = '<option>Error loading tournaments</option>'; }

  // === Load sports ===
  try {
    const res = await fetch("admin-sport/get-sports");
    const data = await res.json();
    sportSelect.innerHTML = '<option value="">Select sport</option>';
    if(data.status === "success"){
      data.data.forEach(s => {
        const opt = document.createElement("option");
        opt.value = s.sport_id;
        opt.textContent = s.sport_name;
        sportSelect.appendChild(opt);
      });
    }
  } catch(err){ sportSelect.innerHTML = '<option>Error loading sports</option>'; }

  // === Load students for winner select ===
  try {
    const res = await fetch("admin-sport/get-students");
    const data = await res.json();
    winnerSelect.innerHTML = '<option value="">Select winner (optional)</option>';
    if(data.status === "success"){
      data.data.forEach(s => {
        const opt = document.createElement("option");
        opt.value = s.user_id;
        opt.textContent = s.name;
        winnerSelect.appendChild(opt);
      });
    }
  } catch(err){ winnerSelect.innerHTML = '<option>Error loading students</option>'; }

  // === Dynamic fields based on sport ===
  sportSelect.addEventListener("change", async () => {
    const sportId = sportSelect.value;
    dynamicFields.innerHTML = "Loading fields...";
    if(!sportId) return;
    try {
      const res = await fetch(`admin-sport/get-sport-fields?sport_id=${sportId}`);
      const data = await res.json();
      dynamicFields.innerHTML = '';
      if(data.status === "success"){
        data.data.forEach(f => {
          const div = document.createElement("div");
          div.classList.add("input-div");
          div.innerHTML = `<label for="${f.field_name}">${f.field_label} ${f.unit ? '('+f.unit+')' : ''}</label>
                           <input type="${f.data_type === 'INT' || f.data_type === 'FLOAT' ? 'number' : 'text'}" 
                                  step="${f.data_type==='FLOAT' ? '0.01' : '1'}" 
                                  id="${f.field_name}" 
                                  name="fields[${f.field_name}]">`;
          dynamicFields.appendChild(div);
        });
      } else dynamicFields.innerHTML = '<p>No fields defined for this sport.</p>';
    } catch(err){ dynamicFields.innerHTML = '<p>Error loading fields.</p>'; }
  });

  // === Form submission ===
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    try {
      const res = await fetch("admin-tournament/add-result", {
        method: "POST",
        body: formData
      });
      const data = await res.json();
      if(data.status === "success"){
        msg.textContent = "Result added successfully!";
        msg.style.color = "green";
        form.reset();
        dynamicFields.innerHTML = '';
      } else {
        msg.textContent = data.message;
        msg.style.color = "red";
      }
    } catch(err){
      msg.textContent = "Error submitting form!";
      msg.style.color = "red";
    }
  });

});
</script>
