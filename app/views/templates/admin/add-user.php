<section id="add-user">
    <h2>Add a User</h2>
    <div class="name-div">
        <div class="input-field">
            <label for="user-fname">First Name</label>
            <input type="text" name="fname" id="user-fname">
        </div>
        <div class="input-field">
            <label for="user-lname">Last Name</label>
            <input type="text" name="lname" id="user-lname">
        </div>
    </div>

    <div class="input-field">
        <label for="user-email">Email</label>
        <input type="email" name="email" id="user-email">
    </div>

    <div class="input-field">
        <label for="user-phone">Phone Number</label>
        <input type="tel" name="phone" id="user-phone" placeholder="+94XXXXXXXXX">
    </div>

    <div class="input-field">
        <label for="user-type">User Type</label>
        <select name="type" id="user-type">
            <option value="">Select User Type</option>
            <option value="SPT">Sport Manager</option>
            <option value="EQP">Equipment Manager</option>
            <option value="REG">Registrar</option>
        </select>
    </div>

    <!-- Dynamic fields container -->
    <div id="extra-fields"></div>

    <a href="#" class="add-user-btn" id="submit-user">Add User</a>
</section>

<script>
document.getElementById('user-type').addEventListener('change', function () {
    const extraFields = document.getElementById('extra-fields');
    extraFields.innerHTML = ''; // clear previous

    if (this.value === 'SPT') {
        extraFields.innerHTML = `
            <div class="input-field">
                <label for="sport-name">Enter Sport</label>
                <input type="text" id="sport-name" name="sport_name" placeholder="e.g. Football">
            </div>
        `;
    } 
    else if (this.value === 'REG') {
        extraFields.innerHTML = `
            <div class="input-field">
                <label for="faculty-select">Select Faculty</label>
                <select id="faculty-select" name="faculty">
                    <option value="">Select Faculty</option>
                    <option value="Science">Science</option>
                    <option value="Arts">Arts</option>
                    <option value="Management">Management</option>
                    <option value="Computing">Computing</option>
                </select>
            </div>
        `;
    }
});

document.getElementById("submit-user").addEventListener("click", async (e) => {
  e.preventDefault();

  const data = {
    fname: document.getElementById("user-fname").value.trim(),
    lname: document.getElementById("user-lname").value.trim(),
    email: document.getElementById("user-email").value.trim(),
    type: document.getElementById("user-type").value.trim(),
    phone: document.getElementById("user-phone")?.value.trim() || "",
    sport: document.getElementById("user-sport")?.value.trim() || "",
    faculty: document.getElementById("user-faculty")?.value.trim() || ""
  };

  const res = await fetch("admin-users/add-internal-user", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify(data)
  });
  const result = await res.json();
  alert(result.message);
});
</script>