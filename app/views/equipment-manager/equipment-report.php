

<?php 
// Display all equipments in a table

echo '<table border="1" cellpadding="10">';
echo '<tr>
        <th>Equipment ID</th>
        <th>Category</th>
        <th>Code</th>
        <th>Status</th>
        <th>Reserved Person Name</th>
        <th>Reserved Person ID</th>
        <th>Reserved Date</th>
        <th>Reserved Time Slot</th>
        <th>Claimed/Return</th>
      </tr>';

foreach ($allEquipments as $eq) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($eq['equipment_id']) . '</td>';
    echo '<td>' . htmlspecialchars($eq['category']) . '</td>';
    echo '<td>' . htmlspecialchars($eq['code']) . '</td>';
    echo '<td>' . htmlspecialchars($eq['availability_status']) . '</td>';
    echo '<td>' . htmlspecialchars($eq['reserved_person_name'] ?? '-') . '</td>';
    echo '<td>' . htmlspecialchars($eq['reserved_person_id'] ?? '-') . '</td>';
    echo '<td>' . htmlspecialchars($eq['reserved_date'] ?? '-') . '</td>';
    echo '<td>' . htmlspecialchars($eq['reserved_time_slot'] ?? '-') . '</td>';
    echo '<td>' . htmlspecialchars($eq['claimed_return'] ?? '-') . '</td>';
    echo '</tr>';
}
echo '</table>';

?>
