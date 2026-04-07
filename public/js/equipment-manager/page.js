// Real-time search functionality for lost items table
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tableBody.getElementsByTagName('tr');
            
            // Loop through all table rows
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                
                // Skip the "no items found" row
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) {
                    continue;
                }
                
                // Get the item name from the first column (index 0)
                const itemNameCell = row.cells[0];
                
                if (itemNameCell) {
                    const itemName = itemNameCell.textContent || itemNameCell.innerText;
                    
                    // Check if the search term exists anywhere in the item name (case-insensitive)
                    if (itemName.toLowerCase().indexOf(searchTerm) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
            
            // Check if all rows are hidden and show a message
            checkEmptyResults();
        });
    }
    
    function checkEmptyResults() {
        const rows = tableBody.getElementsByTagName('tr');
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            if (rows[i].style.display !== 'none' && rows[i].cells.length > 1) {
                visibleCount++;
            }
        }
        
        // Find or create the "no results" row
        let noResultsRow = document.getElementById('noResultsRow');
        
        if (visibleCount === 0 && searchInput.value.trim() !== '') {
            // Show no results message
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = '<td colspan="10" style="text-align: center; padding: 2rem; color: #6b7280;">No items match your search</td>';
                tableBody.appendChild(noResultsRow);
            } else {
                noResultsRow.style.display = '';
            }
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }
});
