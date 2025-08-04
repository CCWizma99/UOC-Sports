<section id="search-user">
    <h2>Search Users</h2>
    <input type="text" name="search-user-inp" id="search-user-inp" title="Enter user ID No. or Name" placeholder="Enter User ID or Name">
    <div class="search-output"></div>
</section>
<script>
    document.getElementById('search-user-inp').addEventListener('input', function () {
    const query = this.value.trim();

    if (query.length === 0) {
        document.querySelector('.search-output').innerHTML = '';
        return;
    }

    fetch(`/uoc-sports/public/api/search-user.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            const outputDiv = document.querySelector('.search-output');
            if (data.length > 0) {
                let html = '<ul>';
                data.forEach(user => {
                    html += `<li><strong>${user.user_id}</strong> - ${user.fname} ${user.lname}</li>`;
                });
                html += '</ul>';
                outputDiv.innerHTML = html;
            } else {
                outputDiv.innerHTML = '<p>No users found.</p>';
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            document.querySelector('.search-output').innerHTML = '<p>Error occurred.</p>';
        });
});

</script>