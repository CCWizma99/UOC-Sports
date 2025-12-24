<form id="budgetForm">
    <h2>Add Budget</h2>
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

        if (result.status === 'success') {
            showNotification('Budget allocated successfully!', 'success');
            form.reset();
        } else {
            showNotification(result.message || 'Failed to allocate budget.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('An error occurred.', 'error');
    }
});
</script>