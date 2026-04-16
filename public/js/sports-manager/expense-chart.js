document.addEventListener('DOMContentLoaded', () => {
    // Get sport ID from URL or window variable
    const urlParams = new URLSearchParams(window.location.search);
    const sportIdFromUrl = urlParams.get('sport');




    
    // Update window.selectedSportId if URL has sport parameter
    if (sportIdFromUrl) {
        window.selectedSportId = sportIdFromUrl;
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

// Merge multiple expenses recorded on the same day into one chart point.
function aggregateExpensesByDate(expenses) {
    const dailyMap = new Map();

    expenses.forEach(expense => {
        const rawDate = expense.expense_date || expense.label;
        if (!rawDate) {
            return;
        }

        const dateObj = new Date(rawDate);
        const dateKey = Number.isNaN(dateObj.getTime()) ? String(rawDate) : dateObj.toISOString().slice(0, 10);
        const displayLabel = Number.isNaN(dateObj.getTime())
            ? String(rawDate)
            : dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const amount = parseFloat(expense.amount) || 0;

        if (!dailyMap.has(dateKey)) {
            dailyMap.set(dateKey, {
                dateKey: dateKey,
                label: displayLabel,
                dailyAmount: 0,
                count: 0
            });
        }

        const day = dailyMap.get(dateKey);
        day.dailyAmount += amount;
        day.count += 1;
    });

    const sorted = Array.from(dailyMap.values()).sort((a, b) => a.dateKey.localeCompare(b.dateKey));
    let runningTotal = 0;

    return sorted.map(day => {
        runningTotal += day.dailyAmount;
        return {
            ...day,
            cumulative: runningTotal
        };
    });
}

// Load data from backend
function loadExpenseChart() {
    const yearSelect = document.getElementById("year");
    const selectedYear = yearSelect ? yearSelect.value : new Date().getFullYear();
    
    // Get sport ID from URL parameter or window.selectedSportId
    const urlParams = new URLSearchParams(window.location.search);
    const sportId = urlParams.get('sport') || window.selectedSportId || '';


    // Fetch real data from backend
    fetch(`/uoc-sports/public/api/get-expense-data.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
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
    
    // Prepare data (single point per date)
    const aggregatedExpenses = aggregateExpensesByDate(expenses);
    const labels = aggregatedExpenses.map(e => e.label);
    const cumulativeData = aggregatedExpenses.map(e => e.cumulative);
    const dailyAmounts = aggregatedExpenses.map(e => e.dailyAmount);
    
    expenseChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cumulative Expenses (Rs)',
                data: cumulativeData,
                borderColor: '#7e22ce',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#7e22ce',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: '#7e22ce',
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
                    enabled: false,
                    external: function(context) {
                        // Get or create tooltip element
                        let tooltipEl = document.getElementById('chartjs-tooltip');

                        if (!tooltipEl) {
                            tooltipEl = document.createElement('div');
                            tooltipEl.id = 'chartjs-tooltip';
                            tooltipEl.className = 'chart-tooltip';
                            document.body.appendChild(tooltipEl);
                        }

                        // Hide if no tooltip
                        const tooltipModel = context.tooltip;
                        if (tooltipModel.opacity === 0) {
                            tooltipEl.style.opacity = '0';
                            return;
                        }

                        // Set Text
                        if (tooltipModel.body) {
                            const dataIndex = tooltipModel.dataPoints[0].dataIndex;
                            const expensePoint = aggregatedExpenses[dataIndex];
                            const amount = dailyAmounts[dataIndex];
                            const cumulative = cumulativeData[dataIndex];
                            
                            let innerHtml = '<div class="tooltip-header">';
                            innerHtml += expensePoint.label + ' (' + expensePoint.count + (expensePoint.count === 1 ? ' expense)' : ' expenses)');
                            innerHtml += '</div>';
                            
                            innerHtml += '<div class="tooltip-content">';
                            
                            innerHtml += '<div class="tooltip-row">';
                            innerHtml += '<div class="expense-detail">';
                            innerHtml += '<i class="fas fa-money-bill-wave tooltip-icon icon-amount"></i>';
                            innerHtml += '<span class="tooltip-label">Daily Total:</span>';
                            innerHtml += '</div>';
                            innerHtml += '<span class="tooltip-value">Rs ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</span>';
                            innerHtml += '</div>';
                            
                            innerHtml += '<div class="tooltip-row">';
                            innerHtml += '<div class="expense-detail">';
                            innerHtml += '<i class="fas fa-coins tooltip-icon icon-percent"></i>';
                            innerHtml += '<span class="tooltip-label">Cumulative:</span>';
                            innerHtml += '</div>';
                            innerHtml += '<span class="tooltip-value">Rs ' + cumulative.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</span>';
                            innerHtml += '</div>';

                            innerHtml += '<div class="tooltip-row">';
                            innerHtml += '<div class="expense-detail">';
                            innerHtml += '<i class="fas fa-list tooltip-icon icon-category"></i>';
                            innerHtml += '<span class="tooltip-label">Entries:</span>';
                            innerHtml += '</div>';
                            innerHtml += '<span class="tooltip-value">' + expensePoint.count + '</span>';
                            innerHtml += '</div>';
                            
                            innerHtml += '</div>';

                            tooltipEl.innerHTML = innerHtml;
                        }

                        const position = context.chart.canvas.getBoundingClientRect();
                        
                        // Display, position, and set styles for font
                        tooltipEl.style.opacity = '1';
                        tooltipEl.style.position = 'fixed';
                        
                        // Position tooltip near the data point with offset
                        const tooltipWidth = tooltipEl.offsetWidth;
                        const tooltipHeight = tooltipEl.offsetHeight;
                        
                        let left = position.left + window.scrollX + tooltipModel.caretX + 15;
                        let top = position.top + window.scrollY + tooltipModel.caretY - tooltipHeight / 2;
                        
                        // Adjust if tooltip goes off screen
                        if (left + tooltipWidth > window.innerWidth) {
                            left = position.left + window.scrollX + tooltipModel.caretX - tooltipWidth - 15;
                        }
                        
                        if (top < 0) {
                            top = 10;
                        } else if (top + tooltipHeight > window.innerHeight) {
                            top = window.innerHeight - tooltipHeight - 10;
                        }
                        
                        tooltipEl.style.left = left + 'px';
                        tooltipEl.style.top = top + 'px';
                        tooltipEl.style.pointerEvents = 'none';
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
                            return 'Rs ' + value.toLocaleString('en-US');
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

    
    // Fetch balance data from backend
    fetch(`/uoc-sports/public/api/get-budget-balance.php?sport_id=${sportId}&year=${selectedYear}`)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(response => {
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
    const isOverBudget = remaining < 0;
    
    if (balanceElement) {
        balanceElement.textContent = `Rs ${remaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 3 })}`;
        balanceElement.style.color = isOverBudget ? '#dc2626' : '';
    }
    
    if (progressElement) {
        progressElement.style.width = `${Math.min(percentage, 100)}%`;
    }
    
    if (percentElement) {
        percentElement.textContent = `${percentage.toFixed(1)}% Expenses`;
    }

    if (percentElement && percentElement.parentNode) {
        let warningElement = document.getElementById('balanceWarning');

        if (!warningElement) {
            warningElement = document.createElement('div');
            warningElement.id = 'balanceWarning';
            warningElement.style.marginTop = '0.5rem';
            warningElement.style.fontSize = '0.9rem';
            warningElement.style.fontWeight = '600';
            warningElement.style.color = '#b91c1c';
            warningElement.style.display = 'none';
            percentElement.parentNode.appendChild(warningElement);
        }

        if (isOverBudget) {
            warningElement.textContent = 'Allocated amount exceeded';
            warningElement.style.display = 'block';
        } else {
            warningElement.textContent = '';
            warningElement.style.display = 'none';
        }
    }

}

