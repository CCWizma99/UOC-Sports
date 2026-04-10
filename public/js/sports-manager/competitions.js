// Toggle Add Participants Form
function toggleAddParticipantsForm() {
    const form = document.getElementById('addParticipantsForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
    }
}

function toggleCompetitionDetails(competitionId) {
    const mainRow = document.querySelector('tr.competition-row[data-competition-id="' + competitionId + '"]');
    const detailsRow = document.getElementById('competition-details-' + competitionId);
    if (!mainRow || !detailsRow) {
        return;
    }

    const currentlyVisible = detailsRow.style.display === 'table-row';
    const shouldShow = !currentlyVisible;

    detailsRow.style.display = shouldShow ? 'table-row' : 'none';
    mainRow.classList.toggle('is-expanded', shouldShow);

    const button = mainRow.querySelector('.expand-btn');
    if (button) {
        button.setAttribute('aria-expanded', shouldShow ? 'true' : 'false');
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.querySelectorAll('tr.competition-row');

            rows.forEach((row) => {
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                const detailRow = row.nextElementSibling;
                row.style.display = found ? '' : 'none';
                if (detailRow && detailRow.classList.contains('competition-details-row')) {
                    if (!found) {
                        detailRow.style.display = 'none';
                        row.classList.remove('is-expanded');
                        const button = row.querySelector('.expand-btn');
                        if (button) {
                            button.setAttribute('aria-expanded', 'false');
                        }
                    }
                    if (!found) {
                        detailRow.style.display = 'none';
                    }
                }
            });
        });
    }
});

// Sort table function
let sortDirection = {};

function sortTable(columnIndex) {
    const table = document.querySelector('.data-table table');
    const tbody = table.querySelector('tbody');
    const primaryRows = Array.from(tbody.querySelectorAll('tr.competition-row'));
    
    // Initialize sort direction for this column if not set
    if (sortDirection[columnIndex] === undefined) {
        sortDirection[columnIndex] = true; // true = ascending
    } else {
        sortDirection[columnIndex] = !sortDirection[columnIndex]; // toggle
    }
    
    const isAscending = sortDirection[columnIndex];
    
    // Sort rows
    primaryRows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return isAscending ? aNum - bNum : bNum - aNum;
        }
        
        // String comparison
        return isAscending ? 
            aValue.localeCompare(bValue) : 
            bValue.localeCompare(aValue);
    });
    
    // Re-append sorted rows
    primaryRows.forEach((row) => {
        const detailRow = row.nextElementSibling;
        tbody.appendChild(row);
        if (detailRow && detailRow.classList.contains('competition-details-row')) {
            tbody.appendChild(detailRow);
        }
    });
    
    // Update sort indicators
    const headers = table.querySelectorAll('th');
    headers.forEach((th, index) => {
        const indicator = th.querySelector('.sort-indicator');
        if (indicator) {
            if (index === columnIndex) {
                indicator.textContent = isAscending ? ' ▲' : ' ▼';
            } else {
                indicator.textContent = '';
            }
        }
    });
}
