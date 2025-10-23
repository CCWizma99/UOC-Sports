<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Yearly Expenses Chart</title>

  <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/sports-manager/barchart.css);
        </style>
</head> 

<body>
  <div class="container">
    <div class="balance-box">
      <h2>Remaining Balance</h2>
      <div class="balance-amount" id="balance">Rs 0.00</div>
      <div class="progress-bar">
        <div class="progress" id="progress"></div>
      </div> 
      <div class="percentage" id="percent">0% Expenses </div>
    </div>
    
    

    <div class="chart-wrapper" id="chartBox">
      <div class="y-axis" id="yAxis">
        
      </div>
         <div class="y-axis-title">Expenses (Rs)</div>
    <div class="x-axis-title">Months</div> 
    </div>

    
  </div>

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

  <script>
    // Example Data
    const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    const expenses = [12000, 9500, 11000, 8700, 13200, 9000, 9800, 10200, 9400, 8900, 11300, 9700];
    const totalIncome = 150000;

    // Calculate totals
    const totalExpense = expenses.reduce((a, b) => a + b, 0);
    const remaining = totalIncome - totalExpense;
    const percentage = ((totalExpense / totalIncome) * 100).toFixed(2);

    // Update top balance section
    document.getElementById("balance").innerText = `Rs ${remaining.toLocaleString("en-IN")}`;
    document.getElementById("percent").innerText = `${percentage}% Expenses`;
    document.getElementById("progress").style.width = `${percentage}%`;

    // Chart setup
    const chartBox = document.getElementById("chartBox");
    const yAxis = document.getElementById("yAxis");
    const maxExpense = Math.max(...expenses);
    const chartHeight = 350;

    // Generate Y-axis values 
    const yAxisValues = [0, 2500, 5000, 10000, 12500, 15000, 17500, 20000]; 
    yAxisValues.forEach(val => {
      const label = document.createElement("div");
      label.textContent = val.toLocaleString("en-IN");
      yAxis.appendChild(label);
    });
    
    // Add bars + labels
    months.forEach((month, i) => {
      const val = expenses[i];
      const height = (val / maxExpense) * chartHeight;

      const bar = document.createElement("div");
      bar.classList.add("bar");
      bar.style.height = `${height}px`;

      const valLabel = document.createElement("div");
      valLabel.classList.add("value-label");
      valLabel.textContent = val.toLocaleString("en-IN");

      const monthLabel = document.createElement("div");
      monthLabel.classList.add("month-label");
      monthLabel.textContent = month;

      bar.appendChild(valLabel);
      bar.appendChild(monthLabel);
      chartBox.appendChild(bar);
    });
  </script>
</body>
</html>