<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Rate Management | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/uoc-sports/public/css/global.css">
    <link rel="stylesheet" href="/uoc-sports/public/css/admin/header.css">
    <link rel="stylesheet" href="/uoc-sports/public/css/admin/sidebar.css">
    <link rel="stylesheet" href="/uoc-sports/public/css/admin/footer.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4b0082, #6a0dad);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --theme-color: #4b0082;
            --success-color: #2e7d32;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fe;
            color: #2d3436;
        }

        .main-content-wrapper {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s ease;
        }

        .rates-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 30px;
            margin-top: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--theme-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rates-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }

        .rates-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #636e72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #edf2f7;
        }

        .rates-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #edf2f7;
            font-size: 0.95rem;
        }

        .rates-table tr:hover {
            background: #fbfbfb;
        }

        .facility-badge {
            background: #eef2ff;
            color: var(--theme-color);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .price-val {
            font-weight: 600;
            color: #2d3436;
        }

        .price-val.currency::before {
            content: 'Rs. ';
            font-size: 0.8rem;
            color: #b2bec3;
        }

        .edit-btn {
            background: var(--theme-color);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s;
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            margin-bottom: 25px;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--theme-color);
        }

        .close-btn {
            position: absolute;
            right: 25px;
            top: 25px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #b2bec3;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #636e72;
        }

        .form-group input {
            padding: 12px;
            border: 2px solid #edf2f7;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--theme-color);
        }

        .modal-footer {
            margin-top: 35px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-cancel {
            background: #f8f9fa;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-save {
            background: var(--theme-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .section-title {
            grid-column: 1 / -1;
            font-size: 0.9rem;
            font-weight: 700;
            color: #b2bec3;
            text-transform: uppercase;
            margin-top: 15px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #edf2f7;
        }

        /* Toast notifications */
        #toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 1001;
            left: 50%;
            transform: translateX(-50%);
            bottom: 30px;
        }

        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
        @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    </style>
</head>
<body>

<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-tags"></i> Facility Rate Management</h1>
        <a href="/uoc-sports/public/admin-reservations" style="color: #636e72; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
            <i class="fas fa-chevron-left"></i> Back to Reservations
        </a>
    </div>

    <div class="rates-container">
        <table class="rates-table">
            <thead>
                <tr>
                    <th>Facility Name</th>
                    <th>Practice (Working)</th>
                    <th>Practice (Other)</th>
                    <th>Tournament (Full Day)</th>
                    <th>Tournament (Half Day)</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rates)): ?>
                    <?php foreach ($rates as $row): ?>
                        <tr>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-weight: 600;"><?= htmlspecialchars($row['facility_name']) ?></span>
                                    <span class="facility-badge"><?= htmlspecialchars($row['facility_type']) ?></span>
                                </div>
                            </td>
                            <td><span class="price-val currency"><?= number_format($row['practice_working_hours'], 2) ?></span></td>
                            <td><span class="price-val currency"><?= number_format($row['practice_other_hours'], 2) ?></span></td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <small style="color: #b2bec3; font-weight: 600;">WORKING</small>
                                    <span class="price-val currency"><?= number_format($row['tournament_full_day_working'], 2) ?></span>
                                    <small style="color: #b2bec3; font-weight: 600; margin-top: 4px;">OTHER</small>
                                    <span class="price-val currency"><?= number_format($row['tournament_full_day_other'], 2) ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <small style="color: #b2bec3; font-weight: 600;">WORKING</small>
                                    <span class="price-val currency"><?= number_format($row['tournament_half_day_working'], 2) ?></span>
                                    <small style="color: #b2bec3; font-weight: 600; margin-top: 4px;">OTHER</small>
                                    <span class="price-val currency"><?= number_format($row['tournament_half_day_other'], 2) ?></span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <button class="edit-btn" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    <i class="fas fa-edit"></i> Edit Rates
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #b2bec3;">No facility rates configured.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-header">
            <h2 id="modalTitle">Edit Facility Rates</h2>
            <p id="modalFacilityName" style="margin-top: 5px; color: #636e72; font-weight: 500;"></p>
        </div>

        <form id="editRateForm">
            <input type="hidden" name="id" id="edit-id">
            
            <div class="form-grid">
                <div class="section-title">Practice Session Rates</div>
                <div class="form-group">
                    <label>Working Hours (Hourly)</label>
                    <input type="number" step="0.01" name="practice_working_hours" id="edit-pwh" required>
                </div>
                <div class="form-group">
                    <label>Other Hours (Hourly)</label>
                    <input type="number" step="0.01" name="practice_other_hours" id="edit-poh" required>
                </div>

                <div class="section-title">Tournament Rates (Full Day)</div>
                <div class="form-group">
                    <label>Working Days</label>
                    <input type="number" step="0.01" name="tournament_full_day_working" id="edit-tfdw" required>
                </div>
                <div class="form-group">
                    <label>Weekends / Other</label>
                    <input type="number" step="0.01" name="tournament_full_day_other" id="edit-tfdo" required>
                </div>

                <div class="section-title">Tournament Rates (Half Day)</div>
                <div class="form-group">
                    <label>Working Days</label>
                    <input type="number" step="0.01" name="tournament_half_day_working" id="edit-thdw" required>
                </div>
                <div class="form-group">
                    <label>Weekends / Other</label>
                    <input type="number" step="0.01" name="tournament_half_day_other" id="edit-thdo" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="toast"></div>

<script>
    function openEditModal(data) {
        document.getElementById('edit-id').value = data.id;
        document.getElementById('modalFacilityName').textContent = data.facility_name;
        document.getElementById('edit-pwh').value = data.practice_working_hours;
        document.getElementById('edit-poh').value = data.practice_other_hours;
        document.getElementById('edit-tfdw').value = data.tournament_full_day_working;
        document.getElementById('edit-tfdo').value = data.tournament_full_day_other;
        document.getElementById('edit-thdw').value = data.tournament_half_day_working;
        document.getElementById('edit-thdo').value = data.tournament_half_day_other;

        document.getElementById('editModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    document.getElementById('editRateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const saveBtn = this.querySelector('.btn-save');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch('/uoc-sports/public/admin-api/facility/update-rates', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to update rates', 'error');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Changes';
            }
        })
        .catch(err => {
            showToast('Network error occurred', 'error');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        });
    });

    function showToast(message, type) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.style.backgroundColor = type === 'success' ? '#2e7d32' : '#d32f2f';
        toast.className = "show";
        setTimeout(() => toast.className = toast.className.replace("show", ""), 3000);
    }

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) closeModal();
    }
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
</html>
