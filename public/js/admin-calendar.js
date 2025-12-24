let bookedDates = [];

function fetchBookedDates(month, year) {
  return fetch(`api/get-booked-days.php?month=${month}&year=${year}`)
    .then(res => res.json())
    .then(data => {
      if (data.dates) {
        bookedDates.push(...data.dates);
      } else {
        console.error('Error fetching booked dates:', data.error || 'Unknown error');
      }
    });
}

function generateCalendar(containerId, year, month) {
  const container = document.getElementById(containerId);
  container.innerHTML = '';

  const date = new Date(year, month, 1);
  const monthName = date.toLocaleString('default', { month: 'long' });

  const table = document.createElement('table');
  table.classList.add('booking-calendar');
  table.innerHTML = `<caption>${monthName} ${year}</caption>
    <tr>
      <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
      <th>Thu</th><th>Fri</th><th>Sat</th>
    </tr>`;

  let row = document.createElement('tr');
  for (let i = 0; i < date.getDay(); i++) {
    row.appendChild(document.createElement('td'));
  }

  while (date.getMonth() === month) {
    if (date.getDay() === 0 && row.children.length) {
      table.appendChild(row);
      row = document.createElement('tr');
    }

    const cell = document.createElement('td');
    const day = date.getDate();
    const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;

    cell.textContent = day;
    cell.setAttribute('data-date', dateStr);
    cell.classList.add('calendar-day');

    if (bookedDates.includes(dateStr)) {
      cell.classList.add('booked');
    }

    row.appendChild(cell);
    date.setDate(day + 1);
  }

  table.appendChild(row);
  container.appendChild(table);

  // Bind click events after DOM update
  table.querySelectorAll('.calendar-day').forEach(day => {
    day.addEventListener('click', () => handleDayClick(day));
  });
}

function handleDayClick(day) {
  const date = day.getAttribute('data-date');

  fetch(`/uoc-sports/public/api/get-bookings?date=${date}`)
    .then(res => res.json())
    .then(data => {
      const infoBox = document.getElementById('booking-info');
      const details = document.getElementById('booking-details');

      infoBox.style.display = 'block';

      if (data.booked && data.data.length > 0) {
        let html = `<p class="booking-date"><strong>Date:</strong> ${date}</p>`;
        html += `<div class="booking-cards">`;

        data.data.forEach((booking, index) => {
          html += `
            <div class="booking-card">
              <div class="booking-card-header">
                <span class="booking-number">#${index + 1}</span>
                <span class="booking-status ${booking.status.toLowerCase()}">${booking.status}</span>
              </div>
              <div class="booking-card-body">
                <div class="booking-field">
                  <span class="field-label">Booked By</span>
                  <span class="field-value">${booking.user_name}</span>
                </div>
                <div class="booking-field">
                  <span class="field-label">Facility</span>
                  <span class="field-value">${booking.facility_id}</span>
                </div>
                <div class="booking-field">
                  <span class="field-label">Time</span>
                  <span class="field-value">${booking.start_time || 'N/A'} - ${booking.end_time || 'N/A'}</span>
                </div>
                <div class="booking-field">
                  <span class="field-label">Payment</span>
                  <span class="field-value ${booking.payment_status === 'COMPLETE' ? 'paid' : 'pending'}">${booking.payment_status}</span>
                </div>
              </div>
            </div>
          `;
        });

        html += `</div>`;
        details.innerHTML = html;
      } else {
        details.innerHTML = `<p class="booking-date"><strong>Date:</strong> ${date}</p><p class="no-bookings">No bookings on this day.</p>`;
      }
    });
}

const today = new Date();
const currentMonth = today.getMonth();
const currentYear = today.getFullYear();

// Calculate next month with year rollover
const nextMonth = (currentMonth + 1) % 12;
const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;

Promise.all([
  fetchBookedDates(currentMonth + 1, currentYear),
  fetchBookedDates(nextMonth + 1, nextYear)
]).then(() => {
  generateCalendar('calendar-current-month', currentYear, currentMonth);
  generateCalendar('calendar-next-month', nextYear, nextMonth);
});
