<div class="facility-reservation-container">
  <h3>Reserve a Facility</h3>

  <form id="facilityReservationForm" onsubmit="submitReservation(event)">
    <div class="form-row">
      <label for="facility_id">Select Facility</label>
      <select id="facility_id" name="facility_id" required></select>
    </div>

    <div class="form-row">
      <label for="date">Date</label>
      <input type="date" id="date" name="date" required>
    </div>

    <div class="form-row">
      <label for="start_time">Start Time</label>
      <input type="time" id="start_time" name="start_time" required>
    </div>

    <div class="form-row">
      <label for="end_time">End Time</label>
      <input type="time" id="end_time" name="end_time" required>
    </div>

    <div class="form-row">
      <label for="purpose">Purpose</label>
      <textarea id="purpose" name="purpose" maxlength="300" placeholder="Briefly describe the purpose..." required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit Reservation</button>
  </form>

  <div id="reservationMessage"></div>
</div>

<script>
const FACILITY_API = '/uoc-sports/public/api/get-facility-rates.php';
const BOOKING_API = '/uoc-sports/public/create-facility-booking';

// Load facilities into dropdown
async function loadFacilities() {
  const res = await fetch(FACILITY_API);
  const data = await res.json();

  const select = document.getElementById('facility_id');
  select.innerHTML = '<option value="">Select a facility</option>';
  data.forEach(f => {
    select.innerHTML += `<option value="${f.id}">${f.facility_name}</option>`;
  });
}
loadFacilities();

// Submit reservation
async function submitReservation(event) {
  event.preventDefault();
  const form = document.getElementById('facilityReservationForm');
  const formData = new FormData(form);

  try {
    const response = await fetch(BOOKING_API, {
      method: 'POST',
      body: formData
    });
    const result = await response.json();

    const msgBox = document.getElementById('reservationMessage');
    if (result.success) {
      msgBox.innerHTML = `<div class="success">✅ ${result.message}</div>`;
      form.reset();
    } else {
      msgBox.innerHTML = `<div class="error">❌ ${result.message}</div>`;
    }
  } catch (error) {
    document.getElementById('reservationMessage').innerHTML = `<div class="error">Error: ${error.message}</div>`;
  }
}
</script>
