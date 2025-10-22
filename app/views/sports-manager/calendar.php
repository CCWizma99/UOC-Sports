<div class="calendar-container">
    <div class="calendar-header">
      <button id="prevMonth">&#10094;</button>
      <div id="monthYear"></div>
      <button id="nextMonth">&#10095;</button>
    </div>

    <div class="calendar">
      <div class="day-names">
        <div>Sun</div>
        <div>Mon</div>
        <div>Tue</div>
        <div>Wed</div>
        <div>Thu</div>
        <div>Fri</div>
        <div>Sat</div>
      </div>
      <div id="calendarDays" class="days-grid"></div>
    </div>
  </div>

  <script>
    const monthYear = document.getElementById("monthYear");
const calendarDays = document.getElementById("calendarDays");
const prevMonth = document.getElementById("prevMonth");
const nextMonth = document.getElementById("nextMonth");

let currentDate = new Date();

// Example scheduled practice dates (YYYY-MM-DD format)
const scheduledPractices = {
  "2025-10-10": "Cricket Practice - 4 PM",
  "2025-10-12": "Football Training - 3 PM",
  "2025-10-16": "Badminton Session - 10 AM",
  "2025-10-21": "Team Meeting - 5 PM"
};

function renderCalendar() {
  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);

  const firstDayIndex = firstDay.getDay();
  const lastDate = lastDay.getDate();

  monthYear.textContent = `${firstDay.toLocaleString("default", { month: "long" })} ${year}`;
  calendarDays.innerHTML = "";

  // Add blank days for alignment
  for (let i = 0; i < firstDayIndex; i++) {
    const blank = document.createElement("div");
    calendarDays.appendChild(blank);
  }

  // Add days
  for (let d = 1; d <= lastDate; d++) {
    const dayElem = document.createElement("div");
    dayElem.classList.add("day");

    const dateString = `${year}-${String(month + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;

    if (scheduledPractices[dateString]) {
      dayElem.classList.add("scheduled");
      dayElem.setAttribute("data-message", scheduledPractices[dateString]);
    }

    dayElem.textContent = d;
    calendarDays.appendChild(dayElem);
  }
}

prevMonth.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

nextMonth.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

renderCalendar();

  </script>