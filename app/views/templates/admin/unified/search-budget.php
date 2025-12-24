<section id="search-budget">
    <h2>Search Sport Budget</h2>
    <input type="text" id="sport-inp" placeholder="Type a sport name...">

    <div class="output-div" id="budget-results"></div>
</section>

<script>
document.getElementById('sport-inp').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('budget-results');

    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch(`./admin-budget/search-budget?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = '';
            if (data.status === 'success' && data.data.length > 0) {
                let grouped = {};

                // Group by budget_id
                data.data.forEach(item => {
                    if (!grouped[item.budget_id]) {
                        grouped[item.budget_id] = {
                            sport_name: item.sport_name,
                            allocation_date: item.allocation_date,
                            manager_name: item.manager_name,
                            manager_contact: item.manager_contact,
                            allocated_amount: item.allocated_amount,
                            spent_amount: item.spent_amount,
                            remaining_amount: item.remaining_amount,
                            transactions: []
                        };
                    }
                    if (item.transaction_id) {
                        grouped[item.budget_id].transactions.push(item);
                    }
                });

                for (let id in grouped) {
                    const b = grouped[id];
                    let transactionsHTML = '';

                    if (b.transactions.length > 0) {
                        transactionsHTML = `
                            <table class="transactions">
                                <tr>
                                    <th>ID</th>
                                    <th>Purpose</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                                ${b.transactions.map(t => `
                                    <tr>
                                        <td>${t.transaction_id}</td>
                                        <td>${t.purpose}</td>
                                        <td>Rs. ${t.transaction_amount}</td>
                                        <td>${t.timestamp}</td>
                                    </tr>
                                `).join('')}
                            </table>
                        `;
                    } else {
                        transactionsHTML = '<p>No transactions recorded.</p>';
                    }

                    resultsDiv.innerHTML += `
                        <div class="budget-card">
                            <div class="sport-name">Sport: ${b.sport_name}</div>
                            <div class="allocation-date">Budget Allocated On - ${b.allocation_date}</div>
                            <div class="sport-manager">
                                Sport Manager - ${b.manager_name} (${b.manager_contact})
                            </div>
                            <div class="allocated-budget">Allocated Budget - Rs. ${b.allocated_amount}</div>
                            <div class="total-exp">Expenditure - Rs. ${b.spent_amount}</div>
                            <strong class="total-rem">Remaining Amount - Rs. ${b.remaining_amount}</strong>
                            <div class="transaction-container">
                                <h3>Transactions - ${b.sport_name}</h3>
                                ${transactionsHTML}
                            </div>
                        </div>
                    `;
                }
            } else {
                resultsDiv.innerHTML = '<p>No results found.</p>';
            }
        })
        .catch(err => {
            console.error(err);
            resultsDiv.innerHTML = '<p>Error fetching data.</p>';
        });
});
</script>
