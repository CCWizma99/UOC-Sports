<?php 
if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<table class="equipment-table">
    <thead>
        <tr>
            <th>Equipment ID</th>
            <th>Category</th>
            <th>Code</th>
            <th>Status</th>
            <th>Reserved Person</th>
            <th>Person ID</th>
            <th>Reserved Date</th>
            <th>Time Slot</th>
            <th>Claimed/Return</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($allEquipments)): ?>
        <?php foreach($allEquipments as $equipment): ?>
            <tr>
                <td><?= htmlspecialchars($equipment['equipment_id']) ?></td>
                <td><?= htmlspecialchars($equipment['equipment_category']) ?></td>
                <td><?= htmlspecialchars($equipment['code']) ?></td>
                <td><?= htmlspecialchars($equipment['availability_status']) ?></td>
                <td><?= htmlspecialchars($equipment['reserved_person_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($equipment['reserved_person_id'] ?? '-') ?></td>
                <td><?= htmlspecialchars($equipment['reserved_date'] ?? '-') ?></td>
                <td><?= htmlspecialchars($equipment['reserved_time'] ?? '-') ?></td>
                <td><?= htmlspecialchars($equipment['return_time'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No equipment found</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>