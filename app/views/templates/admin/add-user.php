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

<?php

$sportModel = new Sport();
$sports = $sportModel->getSports();

?>

<script>
document.getElementById('user-type').addEventListener('change', function () {
    const extraFields = document.getElementById('extra-fields');
    extraFields.innerHTML = '';

    if (this.value === 'SPT') {
        // Encode PHP array into JS
        const sports = <?php echo json_encode($sports ?? []); ?>;

        let options = `<option value="">Select Sport</option>`;
        sports.forEach(sport => {
            options += `<option value="${sport.sport_id}">${sport.sport_name}</option>`;
        });

        extraFields.innerHTML = `
            <div class="input-field">
                <label for="user-sport">Select Sport</label>
                <select id="user-sport" name="sport_name">
                    ${options}
                </select>
            </div>
        `;
    } 
    else if (this.value === 'REG') {
        extraFields.innerHTML = `
            <div class="input-field">
                <label for="user-faculty">Select Faculty</label>
                <select id="user-faculty" name="faculty">
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
</script>
