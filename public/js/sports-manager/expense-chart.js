document.addEventListener('DOMContentLoaded', () => {
    loadExpenseChart();

    // Filters
    const sportSelect = document.getElementById("sport");
    const yearSelect = document.getElementById("year");

    if (sportSelect) sportSelect.addEventListener("change", loadExpenseChart);
    if (yearSelect) yearSelect.addEventListener("change", loadExpenseChart);
});

// Load data from backend
function loadExpenseChart() {
    const sportSelect = document.getElementById("sport");
    const yearSelect = document.getElementById("year");
    
    const sportId = sportSelect ? sportSelect.value : "";
    const selectedYear = yearSelect ? yearSelect.value : new Date().getFullYear();

    // Dummy data for demonstration
    const dummyData = {
        "January": 1500,
        "February": 2200,
        "March": 1800,
        "April": 2500,
        "May": 2000,
        "June": 300,
        "July": 1200,
        "August": 2800,
        "September": 1600,
        "October": 2400,
        "November": 1900,
        "December": 3000
    };

    // Use dummy data instead of fetch
    initExpenseChart(dummyData);
    
    // Uncomment below when backend is ready
    /*
    fetch(`/project/uoc-sports/app/views/sports-manager/get-expense.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => res.json())
        .then(data => initExpenseChart(data))
        .catch(err => console.error("Error loading chart:", err));
    */
}

function initExpenseChart(monthlyData) {
    const months = [
        "January","February","March","April","May","June",
        "July","August","September","October","November","December"
    ];

    const expenses = months.map(m => monthlyData[m] || 0);

    const chartBox = document.getElementById("chartBox");
    const yAxis = document.getElementById("yAxis");
    
    if (!chartBox || !yAxis) {
        console.error("Chart elements not found");
        return;
    }

    // Clear only yAxis, not chartBox (it contains yAxis)
    yAxis.innerHTML = "";

    const chartHeight = 280;
    const maxExpense = Math.max(...expenses);

    // Y-axis
    const step = Math.ceil(maxExpense / 6) || 1000;
    for (let i = 0; i <= 6; i++) {
        const label = document.createElement("div");
        label.textContent = (step * i).toLocaleString("en-IN");
        yAxis.appendChild(label);
    }

    // Create or get bars container
    let barsContainer = chartBox.querySelector('.bars-container');
    if (!barsContainer) {
        barsContainer = document.createElement('div');
        barsContainer.className = 'bars-container';
        barsContainer.style.cssText = 'flex: 1; display: flex; justify-content: space-around; align-items: flex-end; gap: 0.5rem; height: 280px;';
        // Insert after yAxis
        const yAxisElement = chartBox.querySelector('.y-axis');
        if (yAxisElement && yAxisElement.nextSibling) {
            chartBox.insertBefore(barsContainer, yAxisElement.nextSibling);
        } else {
            chartBox.appendChild(barsContainer);
        }
    } else {
        barsContainer.innerHTML = '';
    }

    // Bars
    expenses.forEach((value, i) => {
        const height = maxExpense === 0 ? 0 : (value / maxExpense) * chartHeight;

        const bar = document.createElement("div");
        bar.classList.add("bar");
        bar.style.cssText = `
            flex: 1;
            height: ${height}px;
            background: #2b0c4d;
            border-radius: 4px 4px 0 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
          
            min-height: ${height > 0 ? '0px' : '0'};
            width:15px;
            
        `;

        const valLabel = document.createElement("div");
        valLabel.classList.add("value-label");
        valLabel.style.cssText = 'font-size: 0.7rem; font-weight: 600; color: white; text-align: center;';
        valLabel.textContent = value > 0 ? value.toLocaleString("en-IN") : '';

        const monthLabel = document.createElement("div");
        monthLabel.classList.add("month-label");
        monthLabel.style.cssText = 'font-size: 0.7rem; color: #4b5563; font-weight: 500; position: absolute; bottom: -20px;';
        monthLabel.textContent = months[i].substring(0, 3);

        bar.appendChild(valLabel);
        bar.appendChild(monthLabel);
        barsContainer.appendChild(bar);
    });
}
