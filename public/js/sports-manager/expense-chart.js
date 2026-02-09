document.addEventListener('DOMContentLoaded', () => {
    // Get sport ID from URL or window variable
    const urlParams = new URLSearchParams(window.location.search);
    const sportIdFromUrl = urlParams.get('sport');
    
    console.log('=== Expense Chart Initialization ===');
    console.log('URL:', window.location.href);
    console.log('Sport from URL:', sportIdFromUrl);
    console.log('window.selectedSportId:', window.selectedSportId);
    
    // Update window.selectedSportId if URL has sport parameter
    if (sportIdFromUrl) {
        window.selectedSportId = sportIdFromUrl;
        console.log('Updated window.selectedSportId to:', window.selectedSportId);
    }
    
    loadExpenseChart();
    loadBalanceData();
    
    // Set current month in competition month selector
    const competitionMonthSelect = document.getElementById('competitionMonth');
    if (competitionMonthSelect) {
        const currentMonth = new Date().getMonth() + 1;
        competitionMonthSelect.value = currentMonth.toString().padStart(2, '0');
    }
    
    // Don't load dashboard data immediately - PHP has already rendered it correctly
    // Only reload when user changes filters

    // Year filter
    const yearSelect = document.getElementById("year");
    if (yearSelect) {
        yearSelect.addEventListener("change", () => {
            console.log('Year changed to:', yearSelect.value);
            loadExpenseChart();
            loadBalanceData();
        });
    }
    
    // Competition month filter
    if (competitionMonthSelect) {
        competitionMonthSelect.addEventListener("change", loadDashboardData);
    }
});

// Load dashboard data (sessions and competitions)
function loadDashboardData() {
    const competitionMonthSelect = document.getElementById("competitionMonth");
    const month = competitionMonthSelect ? competitionMonthSelect.value : "";
    
    // Get sport ID from URL parameter or window.selectedSportId
    const urlParams = new URLSearchParams(window.location.search);
    const sportId = urlParams.get('sport') || window.selectedSportId || '';

    fetch(`/uoc-sports/public/api/get-dashboard-data.php?sport_id=${sportId}&month=${month}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateTodaySessions(data.todaySessions);
                updateUpcomingCompetitions(data.upcomingCompetitions);
            }
        })
        .catch(err => console.error("Error loading dashboard data:", err));
}

// Update Today's Sessions section
function updateTodaySessions(sessions) {
    const container = document.getElementById("todaySessionsContainer");
    if (!container) {
        console.error("Container todaySessionsContainer not found");
        return;
    }

    if (sessions.length === 0) {
        container.innerHTML = `
            <div class="session-item" style="text-align: center; color: #9ca3af; padding: 1rem;">
                No practice sessions scheduled for today
            </div>
            <a href="/uoc-sports/public/sport-manager/practice-sessions" class="view-all-link">View All Practice Sessions</a>
        `;
    } else {
        const sessionsHTML = sessions.map(session => {
            const startTime = new Date('1970-01-01 ' + session.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const endTime = new Date('1970-01-01 ' + session.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            const statusBadge = session.status ? `<span class="status-badge status-${session.status.toLowerCase()}">${session.status}</span>` : '';
            return `
                <div class="session-item">
                    <div class="session-details">${startTime} - ${endTime}</div>
                    <div class="session-com">${session.sport_name || 'Unknown Sport'}</div>
                    <div class="session-com">${session.location}</div>
                    ${statusBadge}
                </div>
            `;
        }).join('');
        
        container.innerHTML = sessionsHTML + `
            <a href="/uoc-sports/public/sport-manager/practice-sessions" class="view-all-link">View All Practice Sessions</a>
        `;
    }
}

// Update Upcoming Competitions section
function updateUpcomingCompetitions(competitions) {
    const container = document.getElementById("upcomingCompetitionsContainer");
    if (!container) {
        console.error("Container upcomingCompetitionsContainer not found");
        return;
    }

    if (competitions.length === 0) {
        container.innerHTML = `
            <div class="session-item" style="text-align: center; color: #9ca3af; padding: 1rem;">
                No upcoming competitions scheduled
            </div>
            <a href="/uoc-sports/public/sport-manager/competitions" class="view-all-link">View All Competitions</a>
        `;
    } else {
        const competitionsHTML = competitions.map(competition => {
            const dateText = competition.date ? 
                new Date(competition.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 
                'Date TBD';
            return `
                <div class="session-item">
                    <div class="session-details">${competition.competition_name}</div>
                    <div class="session-com">${competition.sport_name || 'Unknown Sport'}</div>
                    <div class="session-com">${dateText}</div>
                </div>
            `;
        }).join('');
        
        container.innerHTML = competitionsHTML + `
            <a href="/uoc-sports/public/sport-manager/competitions" class="view-all-link">View All Competitions</a>
        `;
    }
}

// Chart instance
let expenseChart = null;

// Load data from backend
function loadExpenseChart() {
    const yearSelect = document.getElementById("year");
    const selectedYear = yearSelect ? yearSelect.value : new Date().getFullYear();
    
    // Get sport ID from URL parameter or window.selectedSportId
    const urlParams = new URLSearchParams(window.location.search);
    const sportId = urlParams.get('sport') || window.selectedSportId || '';

    console.log('Loading expense chart:', { sportId, selectedYear });

    // Fetch real data from backend
    fetch(`/uoc-sports/public/api/get-expense-data.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
            console.log('Expense data response:', response);
            if (response.success) {
                createLineChart(response.data, sportId, selectedYear);
            } else {
                console.error("Error loading expense data:", response.message);
                createLineChart([], sportId, selectedYear);
            }
        })
        .catch(err => {
            console.error("Error loading chart:", err);
            createLineChart([], sportId, selectedYear);
        });
}

// Create line chart with cumulative expenses
function createLineChart(expenses, sportId, year) {
    const ctx = document.getElementById('expenseLineChart');
    
    if (!ctx) {
        console.error("Canvas element not found");
        return;
    }
    
    // Destroy existing chart if any
    if (expenseChart) {
        expenseChart.destroy();
        expenseChart = null;
    }
    
    if (expenses.length === 0) {
        // Show empty state but keep canvas
        const parentDiv = ctx.parentElement;
        const emptyMessage = parentDiv.querySelector('.empty-message');
        
        if (!emptyMessage) {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'empty-message';
            msgDiv.style.cssText = 'text-align: center; color: #6b7280; padding: 3rem; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80%;';
            msgDiv.textContent = `No expenses recorded for ${year}`;
            parentDiv.style.position = 'relative';
            parentDiv.appendChild(msgDiv);
        } else {
            emptyMessage.textContent = `No expenses recorded for ${year}`;
        }
        
        // Hide canvas but keep it in DOM
        ctx.style.display = 'none';
        return;
    }
    
    // Remove empty message if exists and show canvas
    const parentDiv = ctx.parentElement;
    const emptyMessage = parentDiv.querySelector('.empty-message');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    ctx.style.display = 'block';
    
    // Prepare data
    const labels = expenses.map(e => e.label);
    const cumulativeData = expenses.map(e => e.cumulative);
    const amounts = expenses.map(e => e.amount);
    
    expenseChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cumulative Expenses (Rs)',
                data: cumulativeData,
                borderColor: '#a855f7',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#a855f7',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#a855f7',
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#2b0c4d'
                    }
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            return expenses[index].expense_title;
                        },
                        label: function(context) {
                            const index = context.dataIndex;
                            return [
                                'Amount: Rs ' + amounts[index].toLocaleString('en-IN', {minimumFractionDigits: 2}),
                                'Total: Rs ' + cumulativeData[index].toLocaleString('en-IN', {minimumFractionDigits: 2})
                            ];
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#2b0c4d'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        color: '#6b7280',
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cumulative Expenses (Rs)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#2b0c4d'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rs ' + value.toLocaleString('en-IN');
                        },
                        color: '#6b7280',
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    });
}

// Load budget balance data
function loadBalanceData() {
    const yearSelect = document.getElementById("year");
    const selectedYear = yearSelect ? yearSelect.value : new Date().getFullYear();
    
    // Get sport ID from URL parameter or window.selectedSportId
    const urlParams = new URLSearchParams(window.location.search);
    const sportId = urlParams.get('sport') || window.selectedSportId || '';
    
    if (!sportId) {
        console.warn('No sport ID available for balance data');
        updateBalanceDisplay(0, 0, 0, 0);
        return;
    }
    
    console.log('Loading balance data:', { sportId, selectedYear });
    
    // Fetch balance data from backend
    fetch(`/uoc-sports/public/api/get-budget-balance.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
            console.log('Balance data response:', response);
            if (response.success && response.data) {
                const { allocated_amount, spent_amount, remaining_amount, spent_percentage } = response.data;
                updateBalanceDisplay(
                    parseFloat(remaining_amount) || 0,
                    parseFloat(allocated_amount) || 0,
                    parseFloat(spent_amount) || 0,
                    parseFloat(spent_percentage) || 0
                );
            } else {
                console.warn('No balance data:', response.message);
                updateBalanceDisplay(0, 0, 0, 0);
            }
        })
        .catch(err => {
            console.error('Error loading balance data:', err);
            updateBalanceDisplay(0, 0, 0, 0);
        });
}

// Update balance display
function updateBalanceDisplay(remaining, allocated, spent, percentage) {
    const balanceElement = document.getElementById('balance');
    const progressElement = document.getElementById('progress');
    const percentElement = document.getElementById('percent');
    
    if (balanceElement) {
        balanceElement.textContent = `Rs ${remaining.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    if (progressElement) {
        progressElement.style.width = `${Math.min(percentage, 100)}%`;
    }
    
    if (percentElement) {
        percentElement.textContent = `${percentage.toFixed(1)}% Expenses`;
    }
    
    console.log('Balance updated:', { remaining, allocated, spent, percentage });
}

// Legacy bar chart function - deprecated, using line chart now
/*
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

// Load budget balance data
function loadBalanceData() {
    const yearSelect = document.getElementById("year");
    const selectedYear = yearSelect ? yearSelect.value : new Date().getFullYear();
    
    // Get sport ID from URL parameter or window.selectedSportId
    const urlParams = new URLSearchParams(window.location.search);
    const sportId = urlParams.get('sport') || window.selectedSportId || '';
    
    if (!sportId) {
        console.warn('No sport ID available for balance data');
        updateBalanceDisplay(100000, 100000, 0, 0);
        return;
    }
    
    console.log('Loading balance data:', { sportId, selectedYear });
    
    // Fetch balance data from backend
    fetch(`/uoc-sports/public/api/get-budget-balance.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
            console.log('Balance data response:', response);
            if (response.success && response.data) {
                const { allocated_amount, spent_amount, remaining_amount, spent_percentage } = response.data;
                updateBalanceDisplay(
                    parseFloat(remaining_amount) || 0,
                    parseFloat(allocated_amount) || 100000,
                    parseFloat(spent_amount) || 0,
                    parseFloat(spent_percentage) || 0
                );
            } else {
                console.warn('No balance data:', response.message);
                updateBalanceDisplay(100000, 100000, 0, 0);
            }
        })
        .catch(err => {
            console.error('Error loading balance data:', err);
            updateBalanceDisplay(100000, 100000, 0, 0);
        });
}

// Update balance display
function updateBalanceDisplay(remaining, allocated, spent, percentage) {
    const balanceElement = document.getElementById('balance');
    const progressElement = document.getElementById('progress');
    const percentElement = document.getElementById('percent');
    
    if (balanceElement) {
        balanceElement.innerHTML = `Rs ${spent.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} <span style="font-size: 0.9rem; color: #6b7280;">/ Rs ${allocated.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</span>`;
    }
    
    if (progressElement) {
        progressElement.style.width = `${Math.min(percentage, 100)}%`;
    }
    
    if (percentElement) {
        percentElement.textContent = `${percentage.toFixed(1)}% of Budget Spent`;
    }
    
    console.log('Balance updated:', { remaining, allocated, spent, percentage });
}
*/
