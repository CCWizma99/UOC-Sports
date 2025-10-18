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