<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/equipments-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="equipments-grid-container">
        <div class="equipments-grid-left">
            <div class="analytics-link-box">
                <a href="./admin-equipment-analytics" class="btn-analytics">
                    <i class="fas fa-chart-line"></i>
                    View Analytics
                </a>
            </div>
            <div id="Search-equipment">
                <h2>Search Equipment</h2>
                <input type="text" id="equipment-search" placeholder="Type equipment name, ID, or category">
                <div id="search-results">
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>Search Equipment</h3>
                        <p>Type a name, ID, or category to find equipment</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="equipments-grid-right">
            <div class="equipments-forms-container">
                <section id="add-equipment">
                    <h2>Add Equipment Stock</h2>
                    <form id="add-equipment-form" enctype="multipart/form-data" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>
                        
                        <div class="input-div">
                            <label for="sport">Sport <span class="required">*</span></label>
                            <select id="sport" name="sport_id" onchange="loadEquipments()" 
                                    aria-required="true" required>
                                <option value="">Loading sports...</option>
                            </select>
                        </div>

                        <div class="input-div">
                            <label for="equipment-name">Equipment Name <span class="required">*</span></label>
                            <select id="equipment" name="equipment_id" 
                                    aria-required="true" required>
                                <option value="">Select a sport first</option>
                            </select>
                        </div>

                        <div class="input-div">
                            <label for="quantity">Number of Items <span class="required">*</span></label>
                            <input type="number" id="quantity" name="quantity" min="1" 
                                   inputmode="numeric" aria-required="true" required>
                        </div>

                        <div class="input-div">
                            <label for="date">Date</label>
                            <div style="display:flex; gap:10px;">
                                <input type="date" id="date" name="date" style="flex-grow: 1;" required>
                                <button type="button" class="btn today-btn" onclick="setToday()">Today</button>
                            </div>
                        </div>

                        <div class="input-div">
                            <label for="remarks">Special Remarks</label>
                            <textarea id="remarks" name="remarks" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn">Add Equipment</button>
                    </form>
                </section>

                <section id="add-equipment-type">
                    <h2>Add New Equipment Type</h2>

                    <form id="add-equipment-type-form" enctype="multipart/form-data" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="input-div">
                            <label for="new-equipment-sport">Sport <span class="required">*</span></label>
                            <select id="new-equipment-sport" name="sport_id" 
                                    aria-required="true" required>
                                <option value="">Select a sport</option>
                            </select>
                        </div>

                        <div class="input-div">
                            <label for="new-equipment-name">Equipment Name</label>
                            <input 
                                type="text" 
                                id="new-equipment-name" 
                                name="equipment_name" 
                                placeholder="Eg: Boxing Gloves, Cricket Bat"
                                required
                            >
                        </div>

                        <div class="input-div">
                            <label for="equipment-image">Equipment Image (Optional)</label>
                            <input type="file" id="equipment-image" name="image" accept="image/*">
                        </div>

                        <button type="submit" class="btn">Add Equipment Type</button>

                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
// Search Equipment Script
document.getElementById('equipment-search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('search-results');

    if (!query) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch('admin-equipments/search-equipment?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                let html = '<ul>';
                data.data.forEach(eq => {
                    const imgSrc = eq.image_name 
                        ? `./images/equipment-types/${eq.image_name}` 
                        : `./images/equipment-types/default.png`;

                    html += `
                    <li>
                        <img src="${imgSrc}" alt="${eq.equipment_name}">
                        <div class="equipment-info">
                            <strong>${eq.equipment_name} (${eq.category})</strong>
                            <span>Quantity: ${eq.quantity}</span>
                        </div>
                    </li>
                    `;
                });
                html += '</ul>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<p>No results found</p>';
            }
        })
        .catch(err => console.error('Error fetching equipment:', err));
});
</script>

<script>
// Add Equipment Scripts
document.addEventListener("DOMContentLoaded", async () => {
    const sportSelect = document.getElementById("sport");
    const equipmentSelect = document.getElementById("equipment");
    const newSportSelect = document.getElementById("new-equipment-sport");

    // Load sports
    try {
        const resSpo = await fetch("admin-equipments/get-sports");
        const data = await resSpo.json();
        sportSelect.innerHTML = '<option value="">Select a sport</option>';
        if (data.status === "success") {
            data.data.forEach(s => {
                sportSelect.innerHTML += `<option value="${s.sport_id}">${s.sport_name}</option>`;
            });
        }
    } catch {
        sportSelect.innerHTML = '<option value="">Error Loading Sports</option>';
    }

    // Submit add equipment form
    document.getElementById("add-equipment-form").addEventListener("submit", async e => {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        try {
            const res = await fetch("admin-equipments/add-stock", {
                method: "POST",
                body: formData
            });

            const result = await res.json();

            if (result.status === "success") {
                showNotification(result.message, "success");
                form.reset();
            } else {
                showNotification(result.message, "error");
            }

        } catch {
            showNotification("Network error.", "error");
        }
    });

    if (newSportSelect) {
        newSportSelect.innerHTML = sportSelect.innerHTML;
    }
});

// Load equipments based on sport selection
async function loadEquipments() {
    const sportSelect = document.getElementById("sport");
    const equipmentSelect = document.getElementById("equipment");
    const sportId = sportSelect.value;

    try {
        const resEqu = await fetch(`admin-equipments/get-equipments?sport_id=${sportId}`);
        const dataEqu = await resEqu.json();
        
        equipmentSelect.innerHTML = '<option value="">Select an equipment</option>';
        if (dataEqu.status === "success") {
            if (dataEqu.data.length === 0) {
                equipmentSelect.innerHTML = '<option value="">No equipments found</option>';
            } else {
                dataEqu.data.forEach(e => {
                    equipmentSelect.innerHTML += `<option value="${e.equipment_id}">${e.equipment_name}</option>`;
                });
            }
        }
    } catch {
        equipmentSelect.innerHTML = '<option value="">Error Loading equipments</option>';
    }
}

function setToday() {
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("date").value = today;
}

document.getElementById("add-equipment-type-form").addEventListener("submit", async e => {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const res = await fetch("admin-equipments/add-equipment-type", {
            method: "POST",
            body: formData
        });

        const result = await res.json();

        if (result.status === "success") {
            showNotification(result.message, "success");
            form.reset();
            loadEquipments();
        } else {
            showNotification(result.message, "error");
        }

    } catch {
        showNotification("Network error while adding equipment.", "error");
    }
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-equipments");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/search-keyboard-nav.js"></script>
<script>
    SearchKeyboardNav.init({
        inputSelector: '#equipment-search',
        resultsSelector: '#search-results',
        itemSelector: 'li',
        actionSelector: 'li' // Click on the list item itself
    });
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
