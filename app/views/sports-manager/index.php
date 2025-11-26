<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sport Manager | UOC Sports E-Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/sports-manager/index.css");
    @import url("/uoc-sports/public/css/general/footer.css");
</style>
</head> 

<body>
   <?php
    require "../app/views/templates/general/header.php";
    require "../app/views/sports-manager/header-subnav.php";  
?>
<div class="calendar-container page-wrapper">
    
    <!-- Calendar Header -->
    <div class="calendar-header">
      <button id="prevMonth">&#10094;</button>
      <div id="monthYear"></div>
      <button id="nextMonth">&#10095;</button>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar">
      <div class="day-names">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
        <div>Thu</div><div>Fri</div><div>Sat</div>
      </div>
      <div id="calendarDays" class="days-grid"></div>
    </div>


    <!-- Balance Box -->
    <div class="balance-container">
      <div class="balance-box">
        <h2>Remaining Balance</h2>
        <div class="balance-amount" id="balance">Rs 0.00</div>
        <div class="progress-bar"><div class="progress" id="progress"></div></div>
        <div class="percentage" id="percent">0% Expenses</div>
      </div>
    </div>


    <!-- NEW: Horizontal Chart Layout -->
    <div class="chart-wrapper" id="chartBox">
        <div class="chart-title">Yearly Expenses Overview</div>

        <div class="chart-grid" id="chartGrid"></div>
    </div>

</div>


<script>
/* Calendar script stays unchanged */
const monthYear = document.getElementById("monthYear");
const calendarDays = document.getElementById("calendarDays");
const prevMonth = document.getElementById("prevMonth");
const nextMonth = document.getElementById("nextMonth");
let currentDate = new Date();

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

  monthYear.textContent = `${firstDay.toLocaleString("default", { month: "long" })} ${year}`;
  calendarDays.innerHTML = "";

  for (let i = 0; i < firstDay.getDay(); i++) {
    calendarDays.appendChild(document.createElement("div"));
  }

  for (let d = 1; d <= lastDay.getDate(); d++) {
    const dayElem = document.createElement("div");
    dayElem.classList.add("day");

    const ds = `${year}-${String(month + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
    if (scheduledPractices[ds]) {
      dayElem.classList.add("scheduled");
      dayElem.setAttribute("data-message", scheduledPractices[ds]);
    }

    dayElem.textContent = d;
    calendarDays.appendChild(dayElem);
  }
}

prevMonth.onclick = () => { currentDate.setMonth(currentDate.getMonth() - 1); renderCalendar(); };
nextMonth.onclick = () => { currentDate.setMonth(currentDate.getMonth() + 1); renderCalendar(); };
renderCalendar();



/* --------------------------
   HORIZONTAL BAR CHART JS
---------------------------*/

const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const expenses = [12000, 9500, 11000, 8700, 13200, 9000, 9800, 10200, 9400, 8900, 11300, 9700];

const totalIncome = 150000;
const totalExpense = expenses.reduce((a,b)=>a+b);
const remaining = totalIncome - totalExpense;
const pct = ((totalExpense/totalIncome)*100).toFixed(2);

document.getElementById("balance").innerText = `Rs ${remaining.toLocaleString("en-IN")}`;
document.getElementById("percent").innerText = `${pct}% Expenses`;
document.getElementById("progress").style.width = `${pct}%`;


const chartGrid = document.getElementById("chartGrid");
const maxVal = Math.max(...expenses);

// Build horizontal bars
months.forEach((m,i)=>{
    const row = document.createElement("div");
    row.classList.add("chart-row");

    const label = document.createElement("div");
    label.classList.add("row-label");
    label.textContent = m;

    const bar = document.createElement("div");
    bar.classList.add("row-bar");
    bar.style.width = (expenses[i] / maxVal * 100) + "%";

    const val = document.createElement("span");
    val.classList.add("bar-value");
    val.textContent = "Rs "+expenses[i].toLocaleString("en-IN");

    bar.appendChild(val);
    row.appendChild(label);
    row.appendChild(bar);
    chartGrid.appendChild(row);
});
</script>
<?php
    require "../app/views/templates/general/footer.php";      
    ?>
</body>
</html>