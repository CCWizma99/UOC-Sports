<div class="facility-search">
  <h3>Search Facility Rates</h3>
  <input 
    type="text" 
    id="search_facility_name" 
    placeholder="Type a facility name..."
    oninput="searchFacilities()"
  />

  <div id="suggestions" class="suggestions"></div>
  <div id="facilityDetails" class="facility-details hidden"></div>
</div>

<script>
const API_BASE = '/uoc-sports/public/api/get-facility-rates.php';
let timeout = null;

async function searchFacilities() {
  const name = document.getElementById('search_facility_name').value.trim();
  const suggestionBox = document.getElementById('suggestions');
  const detailsBox = document.getElementById('facilityDetails');

  if (!name) {
    suggestionBox.innerHTML = '';
    detailsBox.innerHTML = '';
    return;
  }

  clearTimeout(timeout);
  timeout = setTimeout(async () => {
    try {
      const response = await fetch(`${API_BASE}?facility_name=${encodeURIComponent(name)}`);
      const results = await response.json();

      if (!Array.isArray(results) || results.length === 0) {
        suggestionBox.innerHTML = '<p class="no-results">No facilities found.</p>';
        return;
      }

      // Create suggestion cards
      suggestionBox.innerHTML = results.map(r => `
        <div class="facility-card" onclick='showDetails(${JSON.stringify(r)})'>
          <h4>${r.facility_name}</h4>
          <p class="type">${r.facility_type.replace('_', ' ')}</p>
        </div>
      `).join('');

    } catch (error) {
      suggestionBox.innerHTML = `<p class="error">Error fetching facilities.</p>`;
    }
  }, 400); // debounce
}

function showDetails(data) {
  const detailsBox = document.getElementById('facilityDetails');
  detailsBox.classList.remove('hidden');
  detailsBox.classList.add('show');
  document.getElementById('suggestions').innerHTML = '';

  detailsBox.innerHTML = `
    <div class="facility-info">
      <h3>${data.facility_name}</h3>
      <p><strong>Type:</strong> ${data.facility_type.replace('_', ' ')}</p>
      ${data.capacity ? `<p><strong>Capacity:</strong> ${data.capacity}</p>` : ''}
      <div class="rate-grid">
        <div><strong>Practice (Working Days):</strong><span>${formatRate(data.practice_working_hours)}</span></div>
        <div><strong>Practice (Other Days):</strong><span>${formatRate(data.practice_other_hours)}</span></div>
        <div><strong>Tournament Full Day (Working Days):</strong><span>${formatRate(data.tournament_full_day_working)}</span></div>
        <div><strong>Tournament Half Day (Working Days):</strong><span>${formatRate(data.tournament_half_day_working)}</span></div>
        <div><strong>Tournament Full Day (Other Days):</strong><span>${formatRate(data.tournament_full_day_other)}</span></div>
        <div><strong>Tournament Half Day (Other Days):</strong><span>${formatRate(data.tournament_half_day_other)}</span></div>
      </div>
    </div>
  `;
}

function formatRate(val) {
  return val ? `Rs. ${parseFloat(val).toFixed(2)}` : '-';
}
</script>
