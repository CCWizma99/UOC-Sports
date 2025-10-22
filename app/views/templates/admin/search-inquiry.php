<div class="search-container">
    <h2>Search Inquiries</h2>
    <input type="text" id="search" placeholder="Type inquiry ID, email, or subject...">

    <table id="resultTable">
        <thead>
            <tr>
                <th>Inquiry ID</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="resultsBody">
            <tr><td colspan="5" style="text-align:center; color:gray;">Start typing to search...</td></tr>
        </tbody>
    </table>
</div>

<script>
const searchInput = document.getElementById('search');
const resultsBody = document.getElementById('resultsBody');

searchInput.addEventListener('keyup', async () => {
    const query = searchInput.value.trim();

    if (!query) {
        resultsBody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:gray;">Start typing to search...</td></tr>';
        return;
    }

    const res = await fetch(`/uoc-sports/public/admin-inquiry/search?q=${encodeURIComponent(query)}`);
    const data = await res.json();

    if (data.length === 0) {
        resultsBody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:gray;">No inquiries found</td></tr>';
        return;
    }

    resultsBody.innerHTML = data.map(i => `
        <tr>
            <td>${i.inquiry_id}</td>
            <td>${i.email}</td>
            <td>${i.subject}</td>
            <td>${i.date}</td>
            <td class="status ${i.status === 'RESOLVED' ? 'resolved' : 'not-resolved'}">${i.status}</td>
        </tr>
    `).join('');
});
</script>
