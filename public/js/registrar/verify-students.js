// Search functionality
const searchInput = document.getElementById('searchInput');
const tableRows = document.querySelectorAll('#studentTableBody tr');

searchInput.addEventListener('keyup', () => {
  const query = searchInput.value.toLowerCase();
  tableRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', (e) => {
  const filter = e.target.value;
  tableRows.forEach(row => {
    const status = row.querySelector('.status').textContent.toLowerCase();
    row.style.display = (filter === 'all' || status === filter) ? '' : 'none';
  });
});

// Approve / Reject click
document.querySelectorAll('.approve-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const status = btn.closest('tr').querySelector('.status');
    status.textContent = 'Approved';
    status.className = 'status approved';
  });
});

document.querySelectorAll('.reject-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const status = btn.closest('tr').querySelector('.status');
    status.textContent = 'Rejected';
    status.className = 'status rejected';
  });
});
