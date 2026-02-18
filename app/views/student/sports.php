<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-portal.css); /* Reuse existing styles */
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        .mesh-sporty {
            background: 
                linear-gradient(rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(94, 45, 145, 0.05) 1px, transparent 1px),
                linear-gradient(135deg, #faf9fc 0%, #f3f1f7 100%);
            background-size: 40px 40px, 40px 40px, 100% 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .page-container {
            flex: 1;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px 40px;
        }

        .portal-card {
            min-height: 75vh;
        }

        .section-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .section-header h2 {
            color: #5e2d91;
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            height: 100%;
        }

        @media (max-width: 1024px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
            .page-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

    <div class="page-container">
        <div class="grid-layout">
            <!-- Enrolled Sports -->
            <div class="portal-card">
                <section id="enrolled-sports-section">
                    <h2><i class="fas fa-medal"></i> My Enrolled Sports</h2>
                    <div id="enrolled-sports-list" class="sports-grid">
                        <p>Loading...</p>
                    </div>
                </section>
            </div>

            <!-- Available Sports -->
            <div class="portal-card">
                <section id="available-sports-section">
                    <h2><i class="fas fa-plus-circle"></i> Available Sports</h2>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="sport-search" placeholder="Search for sports..." autocomplete="off">
                    </div>
                    <div id="available-sports-list" class="sports-grid">
                        <p>Loading...</p>
                    </div>
                </section>
            </div>
        </div>
        
        <div id="enroll-message" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none;"></div>
    </div>

    <?php require APP_ROOT . '/app/views/templates/general/footer.php'; ?>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-question-circle"></i>
                <h3 id="confirmModalTitle">Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeConfirmModal()">Cancel</button>
                <button class="btn btn-primary" id="confirmModalBtn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Encouragement Modal -->
    <div id="encouragementModal" class="modal">
        <div class="modal-content encouragement">
            <div class="modal-header success">
                <i class="fas fa-trophy"></i>
                <h3>Welcome to the Team!</h3>
            </div>
            <div class="modal-body">
                <p><strong>Congratulations on enrolling in <span id="enrolledSportName"></span>!</strong></p>
                <p>Stay committed. Stay consistent. Make UOC proud!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeEncouragementModal()">I'm Ready!</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const enrolledList = document.getElementById("enrolled-sports-list");
        const availableList = document.getElementById("available-sports-list");
        const searchInput = document.getElementById("sport-search");
        const msg = document.getElementById("enroll-message");
        const confirmModal = document.getElementById("confirmModal");
        const encouragementModal = document.getElementById("encouragementModal");
        
        let availableSports = [];
        let enrolledSports = [];
        let sportsLoaded = false;

        window.closeConfirmModal = () => { confirmModal.style.display = 'none'; };
        window.closeEncouragementModal = () => { encouragementModal.style.display = 'none'; };

        function showConfirmModal(title, message, onConfirm) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = message;
            confirmModal.style.display = 'flex';
            document.getElementById('confirmModalBtn').onclick = () => {
                closeConfirmModal();
                onConfirm();
            };
        }

        function showEncouragementModal(sportName) {
            document.getElementById('enrolledSportName').textContent = sportName;
            encouragementModal.style.display = 'flex';
        }

        window.onclick = (event) => {
            if (event.target === confirmModal) closeConfirmModal();
            if (event.target === encouragementModal) closeEncouragementModal();
        };

        async function loadEnrolledSports() {
            try {
                const res = await fetch('/uoc-sports/public/student/enrolled-sports');
                const data = await res.json();
                if (data.status === 'success') {
                    enrolledSports = data.data;
                    renderEnrolledSports();
                }
            } catch (error) { console.error(error); }
        }

        async function loadAvailableSports() {
            try {
                const res = await fetch('/uoc-sports/public/student/available-sports');
                const data = await res.json();
                if (data.status === 'success') {
                    availableSports = data.data;
                    sportsLoaded = true;
                    // Don't show all initially
                    availableList.innerHTML = '<p class="no-sports" style="text-align:center; color:#666; padding:20px;">Use the search bar to find sports to join.</p>';
                }
            } catch (error) { console.error(error); }
        }

        function renderEnrolledSports() {
            enrolledList.innerHTML = '';
            if (enrolledSports.length === 0) {
                enrolledList.innerHTML = '<p class="no-sports">No enrolled sports yet.</p>';
                return;
            }
            enrolledSports.forEach(sport => {
                const div = document.createElement('div');
                div.className = 'sport-card enrolled';
                div.innerHTML = `
                    <div class="sport-icon"><i class="fas fa-medal"></i></div>
                    <div class="sport-info">
                        <h4>${sport.sport_name}</h4>
                        <p>Joined: ${new Date(sport.joined_date).toLocaleDateString()}</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="unenrollSport('${sport.sport_id}', '${sport.sport_name}')"><i class="fas fa-times"></i></button>
                `;
                enrolledList.appendChild(div);
            });
        }

        function renderAvailableSports(sports) {
            availableList.innerHTML = '';
            if (sports.length === 0) {
                availableList.innerHTML = '<p class="no-sports">No sports found matching your search.</p>';
                return;
            }
            sports.forEach(sport => {
                const div = document.createElement('div');
                div.className = 'sport-card available';
                div.innerHTML = `
                    <div class="sport-icon"><i class="fas fa-running"></i></div>
                    <div class="sport-info"><h4>${sport.sport_name}</h4></div>
                    <button class="btn btn-primary btn-sm" onclick="enrollInSport('${sport.sport_id}', '${sport.sport_name}')"><i class="fas fa-plus"></i></button>
                `;
                availableList.appendChild(div);
            });
        }

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            if (!sportsLoaded && query.length > 0) loadAvailableSports(); 
            
            if (query.length === 0) {
                availableList.innerHTML = '<p class="no-sports" style="text-align:center; color:#666; padding:20px;">Use the search bar to find sports to join.</p>';
                return;
            }
            
            const filtered = availableSports.filter(s => s.sport_name.toLowerCase().startsWith(query));
            renderAvailableSports(filtered);
        });

        window.enrollInSport = async (sportId, sportName) => {
            showConfirmModal('Enroll', `Join ${sportName}?`, async () => {
                const fd = new FormData();
                fd.append('sport_id', sportId);
                const res = await fetch('/uoc-sports/public/student/enroll-sport', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.status === 'success') {
                    showEncouragementModal(sportName);
                    loadEnrolledSports();
                    loadAvailableSports();
                } else {
                    alert(result.message);
                }
            });
        };

        window.unenrollSport = async (sportId, sportName) => {
            showConfirmModal('Unenroll', `Leave ${sportName}?`, async () => {
                const fd = new FormData();
                fd.append('sport_id', sportId);
                const res = await fetch('/uoc-sports/public/student/unenroll-sport', { method: 'POST', body: fd });
                const result = await res.json();
                if (result.status === 'success') {
                    loadEnrolledSports();
                    loadAvailableSports();
                } else {
                    alert(result.message);
                }
            });
        };

        loadEnrolledSports();
        loadAvailableSports();
    });
    </script>
</body>
</html>
