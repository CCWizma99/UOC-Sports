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
        <!-- LEFT PANEL: Navigation Buttons -->
        <div class="equipments-grid-left">
            <div class="analytics-link-box">
                <a href="./admin-equipment-analytics" class="btn-analytics">
                    <i class="fas fa-chart-line"></i>
                    View Analytics
                </a>
            </div>
            <div class="form-nav-panel">
                <h2>Equipment Management</h2>
                <div class="form-nav-buttons">
                    <button class="form-nav-btn active" data-form="grn-form" onclick="switchForm('grn-form', this)">
                        <i class="fas fa-box-open"></i>
                        <span>Good Received Note</span>
                    </button>
                    <button class="form-nav-btn" data-form="gin-form" onclick="switchForm('gin-form', this)">
                        <i class="fas fa-share-from-square"></i>
                        <span>Good Issue Note</span>
                    </button>
                    <button class="form-nav-btn" data-form="gcn-form" onclick="switchForm('gcn-form', this)">
                        <i class="fas fa-ban"></i>
                        <span>Good Condemn Note</span>
                    </button>
                    <button class="form-nav-btn" data-form="sport-form" onclick="switchForm('sport-form', this)">
                        <i class="fas fa-trophy"></i>
                        <span>Add Sport</span>
                    </button>
                    <button class="form-nav-btn" data-form="article-form" onclick="switchForm('article-form', this)">
                        <i class="fas fa-baseball-bat-ball"></i>
                        <span>Add Sport Item / Article</span>
                    </button>
                    <button class="form-nav-btn" data-form="supplier-form" onclick="switchForm('supplier-form', this)">
                        <i class="fas fa-truck-field"></i>
                        <span>Add Supplier</span>
                    </button>
                    <a href="./admin-equipment-reports" class="form-nav-btn" style="text-decoration: none;">
                        <i class="fas fa-file-lines"></i>
                        <span>Reports</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Forms -->
        <div class="equipments-grid-right">
            <div class="equipments-forms-container">

                <!-- ═══ 1. Good Received Note ═══ -->
                <section id="grn-form" class="eq-form active">
                    <h2><i class="fas fa-box-open"></i> Good Received Note</h2>
                    <form id="grn-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Sport <span class="required">*</span></label>
                                <select name="sport_id" class="sport-select" data-target="grn" required>
                                    <option value="">Select a sport</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Article (Sport Item) <span class="required">*</span></label>
                                <select name="equipment_id" class="article-select" id="grn-article" required>
                                    <option value="">Select sport first</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-div">
                            <label>Description</label>
                            <textarea name="description" rows="2" placeholder="Brief description of goods received"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Date <span class="required">*</span></label>
                                <div style="display:flex;gap:8px;">
                                    <input type="date" name="date" style="flex:1" required>
                                    <button type="button" class="btn today-btn" onclick="setToday(this)">Today</button>
                                </div>
                            </div>
                            <div class="input-div">
                                <label>P.O. Number</label>
                                <input type="text" name="po_number" placeholder="Purchase Order No.">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Supplier <span class="required">*</span></label>
                                <select name="supplier_id" class="supplier-select" required>
                                    <option value="">Loading suppliers...</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Invoice No.</label>
                                <input type="text" name="invoice_no" placeholder="Invoice Number">
                            </div>
                        </div>

                        <div class="form-row form-row-3">
                            <div class="input-div">
                                <label>Quantity <span class="required">*</span></label>
                                <input type="number" name="quantity" min="1" required>
                            </div>
                            <div class="input-div">
                                <label>Unit <span class="required">*</span></label>
                                <input type="text" name="unit" placeholder="e.g. pcs, pairs, sets" required>
                            </div>
                            <div class="input-div">
                                <label>Unit Price (Rs.) <span class="required">*</span></label>
                                <input type="number" name="unit_price" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Reference</label>
                                <input type="text" name="reference_info" placeholder="File reference for bill attachment">
                            </div>
                            <div class="input-div">
                                <label>Stock ID <span class="required">*</span></label>
                                <select name="stock_id" class="stock-select" id="grn-stock" required>
                                    <option value="">Select sport first</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-check"></i> Submit GRN</button>
                    </form>
                </section>

                <!-- ═══ 2. Good Issue Note ═══ -->
                <section id="gin-form" class="eq-form">
                    <h2><i class="fas fa-share-from-square"></i> Good Issue Note</h2>
                    <form id="gin-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Sport <span class="required">*</span></label>
                                <select name="sport_id" class="sport-select" data-target="gin" required>
                                    <option value="">Select a sport</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Article <span class="required">*</span></label>
                                <select name="equipment_id" class="article-select" id="gin-article" required>
                                    <option value="">Select sport first</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row form-row-3">
                            <div class="input-div">
                                <label>Date <span class="required">*</span></label>
                                <div style="display:flex;gap:8px;">
                                    <input type="date" name="date" style="flex:1" required>
                                    <button type="button" class="btn today-btn" onclick="setToday(this)">Today</button>
                                </div>
                            </div>
                            <div class="input-div">
                                <label>Quantity <span class="required">*</span></label>
                                <input type="number" name="quantity" min="1" required>
                            </div>
                            <div class="input-div">
                                <label>Unit <span class="required">*</span></label>
                                <input type="text" name="unit" placeholder="e.g. pcs, pairs" required>
                            </div>
                        </div>

                        <div class="input-div">
                            <label>Stock ID <span class="required">*</span></label>
                            <select name="stock_id" class="stock-select" id="gin-stock" required>
                                <option value="">Select sport first</option>
                            </select>
                        </div>

                        <div class="form-row form-row-3">
                            <div class="input-div">
                                <label>Sport Manager</label>
                                <select name="sport_manager_id" class="user-spt-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Captain</label>
                                <select name="captain_id" class="user-captain-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Groundsman / Gym Attendant</label>
                                <select name="equipment_manager_id" class="user-eqp-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-check"></i> Submit GIN</button>
                    </form>
                </section>

                <!-- ═══ 3. Good Condemn Note ═══ -->
                <section id="gcn-form" class="eq-form">
                    <h2><i class="fas fa-ban"></i> Good Condemn Note</h2>
                    <form id="gcn-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Sport <span class="required">*</span></label>
                                <select name="sport_id" class="sport-select" data-target="gcn" required>
                                    <option value="">Select a sport</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Article <span class="required">*</span></label>
                                <select name="equipment_id" class="article-select" id="gcn-article" required>
                                    <option value="">Select sport first</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Stock ID <span class="required">*</span></label>
                                <select name="stock_id" class="stock-select" id="gcn-stock" required>
                                    <option value="">Select sport first</option>
                                </select>
                            </div>
                            <div class="input-div">
                                <label>Quantity <span class="required">*</span></label>
                                <input type="number" name="quantity" min="1" required>
                            </div>
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-check"></i> Submit GCN</button>
                    </form>
                </section>

                <!-- ═══ 4. Add Sport ═══ -->
                <section id="sport-form" class="eq-form">
                    <h2><i class="fas fa-trophy"></i> Add Sport</h2>
                    <form id="sport-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="input-div">
                            <label>Sport Name <span class="required">*</span></label>
                            <input type="text" name="sport_name" placeholder="e.g. Badminton, Cricket, Swimming" required>
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Sport</button>
                    </form>
                </section>

                <!-- ═══ 5. Add Sport Item / Article ═══ -->
                <section id="article-form" class="eq-form">
                    <h2><i class="fas fa-baseball-bat-ball"></i> Add Sport Item / Article</h2>
                    <form id="article-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="input-div">
                            <label>Sport <span class="required">*</span></label>
                            <select name="sport_id" class="sport-select" required>
                                <option value="">Select a sport</option>
                            </select>
                        </div>

                        <div class="input-div">
                            <label>Article Name <span class="required">*</span></label>
                            <input type="text" name="article_name" placeholder="e.g. Cricket Bat, Boxing Gloves" required>
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Article</button>
                    </form>
                </section>

                <!-- ═══ 6. Add Supplier ═══ -->
                <section id="supplier-form" class="eq-form">
                    <h2><i class="fas fa-truck-field"></i> Add Supplier</h2>
                    <form id="supplier-form-el" novalidate>
                        <p class="required-note"><span>*</span> Required fields</p>

                        <div class="input-div">
                            <label>Supplier Name <span class="required">*</span></label>
                            <input type="text" name="supplier_name" placeholder="Company or individual name" required>
                        </div>

                        <div class="input-div">
                            <label>Address <span class="required">*</span></label>
                            <textarea name="address" rows="2" placeholder="Full address" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="input-div">
                                <label>Telephone No. 1 <span class="required">*</span></label>
                                <input type="tel" name="telephone_1" placeholder="07X XXXX XXX" required>
                            </div>
                            <div class="input-div">
                                <label>Telephone No. 2</label>
                                <input type="tel" name="telephone_2" placeholder="07X XXXX XXX">
                            </div>
                        </div>

                        <div class="input-div">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="supplier@example.com">
                        </div>

                        <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Supplier</button>
                    </form>
                </section>

            </div>
        </div>
    </div>
</div>

<script>
// ─── Form Switching ───
function switchForm(formId, btn) {
    // Hide all forms
    document.querySelectorAll('.eq-form').forEach(f => f.classList.remove('active'));
    // Deactivate all buttons
    document.querySelectorAll('.form-nav-btn').forEach(b => b.classList.remove('active'));
    // Show selected form
    document.getElementById(formId).classList.add('active');
    // Activate clicked button
    btn.classList.add('active');
}

// ─── Set Today Helper ───
function setToday(btn) {
    const dateInput = btn.parentElement.querySelector('input[type="date"]');
    dateInput.value = new Date().toISOString().split('T')[0];
}

// ─── Data Loading ───
document.addEventListener('DOMContentLoaded', async () => {
    // 1. Load Sports into all sport selects
    try {
        const res = await fetch('admin-equipments/get-sports');
        const data = await res.json();
        if (data.status === 'success') {
            document.querySelectorAll('.sport-select').forEach(sel => {
                sel.innerHTML = '<option value="">Select a sport</option>';
                data.data.forEach(s => {
                    sel.innerHTML += `<option value="${s.sport_id}">${s.sport_name}</option>`;
                });
            });
        }
    } catch(e) { console.error('Failed to load sports', e); }

    // 2. Load Suppliers
    try {
        const res = await fetch('admin-equipments/get-suppliers');
        const data = await res.json();
        if (data.status === 'success') {
            document.querySelectorAll('.supplier-select').forEach(sel => {
                sel.innerHTML = '<option value="">Select a supplier</option>';
                data.data.forEach(s => {
                    sel.innerHTML += `<option value="${s.supplier_id}">${s.supplier_name}</option>`;
                });
            });
        }
    } catch(e) { console.error('Failed to load suppliers', e); }

    // 3. Load Users by Type for GIN form
    const loadUsers = async (type, selector) => {
        try {
            const res = await fetch(`admin-equipments/get-users-by-type?type=${type}`);
            const data = await res.json();
            if (data.status === 'success') {
                document.querySelectorAll(selector).forEach(sel => {
                    sel.innerHTML = '<option value="">-- None --</option>';
                    data.data.forEach(u => {
                        sel.innerHTML += `<option value="${u.user_id}">${u.full_name}</option>`;
                    });
                });
            }
        } catch(e) { console.error(`Failed to load ${type} users`, e); }
    };
    await loadUsers('SPT', '.user-spt-select');
    await loadUsers('CAPTAIN', '.user-captain-select');
    await loadUsers('EQP', '.user-eqp-select');

    // 4. Cascading: Sport → Articles & Stock
    document.querySelectorAll('.sport-select').forEach(sel => {
        sel.addEventListener('change', async function() {
            const sportId = this.value;
            const target = this.dataset.target; // grn, gin, gcn
            
            // Load articles
            const articleSel = this.closest('form')?.querySelector('.article-select');
            if (articleSel) {
                if (!sportId) {
                    articleSel.innerHTML = '<option value="">Select sport first</option>';
                } else {
                    try {
                        const res = await fetch(`admin-equipments/get-equipments?sport_id=${sportId}`);
                        const data = await res.json();
                        articleSel.innerHTML = '<option value="">Select an article</option>';
                        if (data.status === 'success') {
                            data.data.forEach(e => {
                                articleSel.innerHTML += `<option value="${e.equipment_id}">${e.equipment_name}</option>`;
                            });
                        }
                    } catch(e) { articleSel.innerHTML = '<option value="">Error loading</option>'; }
                }
            }

            // Load stock entries
            const stockSel = this.closest('form')?.querySelector('.stock-select');
            if (stockSel) {
                if (!sportId) {
                    stockSel.innerHTML = '<option value="">Select sport first</option>';
                } else {
                    try {
                        const res = await fetch(`admin-equipments/get-stock-entries?sport_id=${sportId}`);
                        const data = await res.json();
                        stockSel.innerHTML = '<option value="">Select a stock entry</option>';
                        if (data.status === 'success') {
                            data.data.forEach(s => {
                                stockSel.innerHTML += `<option value="${s.stock_id}">${s.stock_id} — ${s.equipment_name} (Qty: ${s.quantity})</option>`;
                            });
                        }
                    } catch(e) { stockSel.innerHTML = '<option value="">Error loading</option>'; }
                }
            }
        });
    });

    // ─── Form Submissions ───
    const submitForm = (formElId, url) => {
        document.getElementById(formElId)?.addEventListener('submit', async e => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            try {
                const res = await fetch(url, { method: 'POST', body: formData });
                const result = await res.json();
                if (result.status === 'success') {
                    showNotification(result.message, 'success');
                    form.reset();
                    // Reload sports after adding a new sport
                    if (formElId === 'sport-form-el') reloadSports();
                    // Reload suppliers after adding
                    if (formElId === 'supplier-form-el') reloadSuppliers();
                } else {
                    showNotification(result.message, 'error');
                }
            } catch(err) {
                showNotification('Network error. Please try again.', 'error');
            }
        });
    };

    submitForm('grn-form-el', 'admin-equipments/add-grn');
    submitForm('gin-form-el', 'admin-equipments/add-gin');
    submitForm('gcn-form-el', 'admin-equipments/add-gcn');
    submitForm('sport-form-el', 'admin-equipments/add-sport');
    submitForm('article-form-el', 'admin-equipments/add-article');
    submitForm('supplier-form-el', 'admin-equipments/add-supplier');
});

// ─── Reload helpers ───
async function reloadSports() {
    try {
        const res = await fetch('admin-equipments/get-sports');
        const data = await res.json();
        if (data.status === 'success') {
            document.querySelectorAll('.sport-select').forEach(sel => {
                sel.innerHTML = '<option value="">Select a sport</option>';
                data.data.forEach(s => {
                    sel.innerHTML += `<option value="${s.sport_id}">${s.sport_name}</option>`;
                });
            });
        }
    } catch(e) {}
}

async function reloadSuppliers() {
    try {
        const res = await fetch('admin-equipments/get-suppliers');
        const data = await res.json();
        if (data.status === 'success') {
            document.querySelectorAll('.supplier-select').forEach(sel => {
                sel.innerHTML = '<option value="">Select a supplier</option>';
                data.data.forEach(s => {
                    sel.innerHTML += `<option value="${s.supplier_id}">${s.supplier_name}</option>`;
                });
            });
        }
    } catch(e) {}
}
</script>

<?php require '../app/views/templates/admin/footer.php'; ?>

</body>
<script>
    var currentPage = document.getElementById("sidebar-equipments");
    currentPage.classList.add("active") 
</script>
<script src="/uoc-sports/public/js/sidebar-toggle.js"></script>
</html>
