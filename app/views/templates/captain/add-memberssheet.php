<div class="container">
    <div class="header">
        <h1 class="title">Add Members</h1>
        <h3 class="info-title">UOC Volleyball Team</h3>
    </div>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by Student name or Id" onkeyup="filterTable()">
    </div>

    

    <div class="members-table">
        <div class="table-header">
            <div>Student Name</div>
            <div>ID Number</div>
            <div>Faculty</div>
            <div>Action</div>
        </div>

        <div class="table-row">
            <div class="student-name">J. Balakrishnan</div>
            <div class="student-id">2023/IS/012</div>
            <div class="faculty">UCSC</div>
            <div>
                <button class="action-btn remove" onclick="removeMember(this)">Remove</button>
            </div>
        </div>

        <div class="table-row">
            <div class="student-name">Jayaweera M. A. J. C. S.</div>
            <div class="student-id">2023/IS/043</div>
            <div class="faculty">UCSC</div>
            <div>
                <button class="action-btn add" onclick="addMember(this)">Add</button>
            </div>
        </div>

        <div class="table-row">
            <div class="student-name">Rajapaksha K. A. G. S. M.</div>
            <div class="student-id">2023/IS/079</div>
            <div class="faculty">UCSC</div>
            <div>
                <button class="action-btn remove" onclick="removeMember(this)">Remove</button>
            </div>
        </div>

        <div class="table-row">
            <div class="student-name">Hettiarachchi H. H. K. C. C.</div>
            <div class="student-id">2023/IS/034</div>
            <div class="faculty">UCSC</div>
            <div>
                <button class="action-btn add" onclick="addMember(this)">Add</button>
            </div>
        </div>
    </div>
    <!-- Team Members Table -->
<h2 class="table-title">Current Team Members</h2>

<div class="members-table">
    <div class="table-header">
        <div>Student Name</div>
        <div>ID Number</div>
        <div>Faculty</div>
        <div>Action</div>
    </div>

    <div class="table-row">
        <div class="student-name">J. Balakrishnan</div>
        <div class="student-id">2023/IS/012</div>
        <div class="faculty">Information Systems</div>
        <div>
            <button class="action-btn remove" onclick="removeRow(this)">Remove</button>
        </div>
    </div>

    <div class="table-row">
        <div class="student-name">Rajapaksha K. A. G. S. M.</div>
        <div class="student-id">2023/IS/079</div>
        <div class="faculty">Information Systems</div>
        <div>
            <button class="action-btn remove" onclick="removeRow(this)">Remove</button>
        </div>
    </div>
</div>
</div>

<script>
    function addMember(btn) {
        btn.textContent = 'Remove';
        btn.classList.remove('add');
        btn.classList.add('remove');
    }

    function removeMember(btn) {
        btn.textContent = 'Add';
        btn.classList.remove('remove');
        btn.classList.add('add');
    }

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.members-table .table-row');

        rows.forEach(row => {
            const name = row.querySelector('.student-name').textContent.toLowerCase();
            const id = row.querySelector('.student-id').textContent.toLowerCase();
            if(name.includes(filter) || id.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>



<script>
function removeRow(button) {
    const row = button.closest('.table-row');
    row.remove();
}
</script>

