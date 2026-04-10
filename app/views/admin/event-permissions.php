<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission History | UOC Sports E-Portal</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/admin/header.css);
        @import url(/uoc-sports/public/css/admin/link-bar.css);
        @import url(/uoc-sports/public/css/admin/sidebar.css);
        @import url(/uoc-sports/public/css/admin/footer.css);
        
        @import url(/uoc-sports/public/css/admin/players-page.css);
        @import url(/uoc-sports/public/css/admin/ui-improvements.css);

        .history-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h1 {
            font-size: 24px;
            color: #1e1e2e;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            color: #4b5563;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #f9fafb;
            border-color: #d1d5db;
            transform: translateX(-3px);
        }
    </style>
</head>
<body>
<?php 
require '../app/views/templates/admin/header.php';
require '../app/views/templates/admin/link-bar.php';
require '../app/views/templates/admin/sidebar.php';
?>

<div class="main-content-wrapper">
    <div class="history-container">
        <div class="page-header">
            <h1><i class="fas fa-history" style="color:#5e2d91;"></i> Permission History</h1>
            <a href="./admin-players" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Player Records
            </a>
        </div>

        <!-- ===== GRANTED PERMISSIONS LIST ===== -->
        <section id="permissions-list-section" class="insight-card">
            <div class="card-header">
                <h2><i class="fas fa-list-check" style="color:#5e2d91;margin-right:8px;"></i>Full History of Granted Access</h2>
            </div>
            <div class="card-content">
                <div id="permissions-loading" style="padding:16px;color:#8b5cf6;font-size:13px;">
                    <i class="fas fa-spinner fa-spin"></i> Loading history...
                </div>
                <div id="permissions-table-wrap" style="display:none;margin-top:12px;"></div>
                <div id="permissions-empty" style="display:none;padding:20px;text-align:center;color:#9ca3af;font-size:13px;">
                    No permission records found.
                </div>
            </div>
        </section>
    </div>
</div>

<script>
async function loadGrantedPermissions() {
    try {
        const res  = await fetch('admin-tournament/granted-permissions');
        const data = await res.json();
        const loading = document.getElementById('permissions-loading');
        const wrap    = document.getElementById('permissions-table-wrap');
        const empty   = document.getElementById('permissions-empty');

        loading.style.display = 'none';

        if (data.status === 'success' && data.data && data.data.length > 0) {
            let html = `<table class="perm-table">
                <thead><tr>
                    <th>Tournament</th>
                    <th>Sport</th>
                    <th>Captain</th>
                    <th>Granted At</th>
                    <th>By</th>
                    <th>Status</th>
                </tr></thead><tbody>`;

            data.data.forEach(p => {
                const grantedAt   = new Date(p.granted_at).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'});
                const statusClass = p.status === 'ACTIVE' ? 'perm-active' : 'perm-revoked';

                html += `<tr>
                    <td>${p.tournament_name}</td>
                    <td><span class="t-meta-sport">${p.sport_name}</span></td>
                    <td>${p.captain_name}<br><small style="color:#9ca3af;">${p.captain_email}</small></td>
                    <td>${grantedAt}</td>
                    <td>${p.granted_by_name || 'System'}</td>
                    <td><span class="perm-status-badge ${statusClass}">${p.status}</span></td>
                </tr>`;
            });

            html += '</tbody></table>';
            wrap.innerHTML = html;
            wrap.style.display = 'block';
        } else {
            empty.style.display = 'block';
        }
    } catch(e) {
        console.error('Error loading permissions:', e);
        document.getElementById('permissions-loading').innerHTML = '<p style="color:#dc2626;font-size:13px;">Failed to load.</p>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadGrantedPermissions();
});
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
