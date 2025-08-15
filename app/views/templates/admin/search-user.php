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
                let html = '<table>';
                html += `
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.user_id}</td>
                            <td>${user.fname} ${user.lname}</td>
                            <td>${user.type}</td>
                            <td>
                                <a href="./user.php?id=${user.user_id}" class="action-link" title="View User">
                                    <i class="fa-solid fa-circle-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

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