<?php
$allEquipments = [
    [
        "equipment_id" => 101,
        "equipment_category" => "Badminton",
        "code" => "BDM-001",
        "availability_status" => "Reserved",
        "reserved_person_name" => "K S Silva",
        "reserved_person_id" => "20231001",
        "reserved_date" => "2025-01-12",
        "reserved_time" => "10:00 - 12:00",
        "return_time" => "Pending"
    ],
    [
        "equipment_id" => 102,
        "equipment_category" => "Cricket",
        "code" => "CRT-006",
        "availability_status" => "Available",
        "reserved_person_name" => null,
        "reserved_person_id" => null,
        "reserved_date" => null,
        "reserved_time" => null,
        "return_time" => null
    ],
    [
        "equipment_id" => 103,
        "equipment_category" => "Football",
        "code" => "FTB-088",
        "availability_status" => "Reserved",
        "reserved_person_name" => "N D Perera",
        "reserved_person_id" => "20222045",
        "reserved_date" => "2025-01-11",
        "reserved_time" => "14:00 - 16:00",
        "return_time" => "Pending"
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Equipment Report</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    @import url("/uoc-sports/public/css/global.css");
    @import url("/uoc-sports/public/css/general/header.css");
    @import url("/uoc-sports/public/css/sports-manager/sub-nav.css");
    @import url("/uoc-sports/public/css/general/footer.css");
    @import url("/uoc-sports/public/css/equipment-manager/equipment.css");
    @import url("/uoc-sports/public/css/equipment-manager/report.css");
  </style>
</head>
<body>
<?php
    require "../app/views/templates/general/header.php";
    require "../app/views/equipment-manager/header-subnav.php";
?>
<div class="btn">
    <span class="report-btn"><a href="../equipment-report">Equipment Reservations</a></span>
    <span class="add-new"><a href="../add-equipment">Update Equipments</a></span>
</div>
<div class="report-container">

    <div class="report-header">
        <h2>Equipment Report</h2>
    </div>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search equipment...">
    </div>

    <div class="table-wrapper">
        <table class="equipment-table" id="equipmentTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Equipment ID<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(1)">Category<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(2)">Code<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(3)">Status<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(4)">Reserved Person<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(5)">Person ID<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(6)">Reserved Date<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(7)">Time Slot<span class="sort-indicator">↕</span></th>
                    <th onclick="sortTable(8)">Claimed/Return<span class="sort-indicator">↕</span></th>
                </tr>
            </thead>

            <tbody id="tableBody">
            <?php if(!empty($allEquipments)): ?>
                <?php foreach($allEquipments as $equipment): ?>
                    <tr>
                        <td><?= $equipment['equipment_id'] ?></td>
                        <td><?= $equipment['equipment_category'] ?></td>
                        <td><?= $equipment['code'] ?></td>
                        <td class="status-<?= $equipment['availability_status'] ?>">
                            <?= $equipment['availability_status'] ?>
                        </td>
                        <td><?= $equipment['reserved_person_name'] ?? '-' ?></td>
                        <td><?= $equipment['reserved_person_id'] ?? '-' ?></td>
                        <td><?= $equipment['reserved_date'] ?? '-' ?></td>
                        <td><?= $equipment['reserved_time'] ?? '-' ?></td>
                        <td><?= $equipment['return_time'] ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9">No equipment found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ------------------- Sorting & Searching JS ------------------- -->
<script>
// ---------- Search Filter ----------
document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#equipmentTable tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// ---------- Sort Table ----------
let sortDirection = true;

function sortTable(colIndex) {
    const tbody = document.getElementById("tableBody");
    const rows = Array.from(tbody.querySelectorAll("tr"));

    rows.sort((a, b) => {
        let A = a.children[colIndex].innerText.toLowerCase();
        let B = b.children[colIndex].innerText.toLowerCase();

        if (!isNaN(A) && !isNaN(B)) {
            return sortDirection ? A - B : B - A;
        }

        return sortDirection 
            ? A.localeCompare(B)
            : B.localeCompare(A);
    });

    sortDirection = !sortDirection;

    rows.forEach(r => tbody.appendChild(r));
}
</script>

</body>
</html>
