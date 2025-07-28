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
        let html = `<p><strong>Date:</strong> ${date}</p>`;
        html += `
          <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
            <thead>
              <tr>
                <th>#</th>
                <th>Booked By</th>
                <th>Facility</th>
                <th>Time</th>
                <th>Status</th>
                <th>Payment</th>
              </tr>
            </thead>
            <tbody>
        `;

        data.data.forEach((booking, index) => {
          html += `
            <tr>
              <td>${index + 1}</td>
              <td>${booking.user_name}</td>
              <td>${booking.facility_id}</td>
              <td>${booking.start_time} - ${booking.ent_time}</td>
              <td>${booking.status}</td>
              <td>${booking.payment_status}</td>
            </tr>
          `;
        });

        html += `</tbody></table>`;
        details.innerHTML = html;
      } else {
        details.innerHTML = `<p><strong>Date:</strong> ${date}</p><p>No bookings on this day.</p>`;
      }
    });
}

const today = new Date();
const currentMonth = today.getMonth();
const currentYear = today.getFullYear();

Promise.all([
  fetchBookedDates(currentMonth + 1, currentYear),
  fetchBookedDates(currentMonth + 2, currentYear)
]).then(() => {
  generateCalendar('calendar-current-month', currentYear, currentMonth);
  generateCalendar('calendar-next-month', currentYear, currentMonth + 1);
});
