<form id="budgetForm">
    <label for="sport">Sport:</label>
    <select id="sport" name="sport_id" required>
        <option value="">Select Sport</option>
        <option value="1">Cricket</option>
        <option value="2">Football</option>
        <option value="3">Basketball</option>
    </select>

    <label for="year">Year:</label>
    <input type="number" id="year" name="year" placeholder="2025" required>

    <label for="allocated_amount">Allocated Amount:</label>
    <input type="number" id="allocated_amount" name="allocated_amount" placeholder="100000" required>

    <label for="spent_amount">Spent Amount:</label>
    <input type="number" id="spent_amount" name="spent_amount" placeholder="0" value="0">

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="3" placeholder="Enter description"></textarea>

    <button type="submit">Allocate Budget</button>
    <div id="responseMsg" style="text-align:center; margin-top:15px;"></div>
</form>


<script>
document.getElementById('budgetForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('./admin-budget/add-budget', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        const msgDiv = document.getElementById('responseMsg');

        if (result.status === 'success') {
            msgDiv.style.color = 'green';
            msgDiv.textContent = 'Budget allocated successfully!';
            form.reset();
        } else {
            msgDiv.style.color = 'red';
            msgDiv.textContent = result.message || 'Failed to allocate budget.';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('responseMsg').textContent = 'An error occurred.';
    }
});
</script>