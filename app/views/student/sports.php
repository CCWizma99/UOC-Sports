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

        .main-portal-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 92%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            height: calc(100vh - 220px);
            min-height: 500px;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0,0,0,0.02);
            position: relative;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            height: 100%;
            flex: 1;
            overflow: hidden;
        }

        .portal-card {
            background: #fdfbff;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            border: 1px solid #f3f0f7;
        }

        .sports-grid {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }

        /* Custom Scrollbar for compact lists */
        .sports-grid::-webkit-scrollbar {
            width: 6px;
        }
        .sports-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .sports-grid::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        .sports-grid::-webkit-scrollbar-thumb:hover {
            background: #5e2d91;
        }

        section {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .section-header {
            margin-bottom: 15px;
            border-bottom: 2px solid #f3f0f7;
            padding-bottom: 10px;
            flex-shrink: 0;
        }

        .section-header h2 {
            color: #5e2d91;
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }

        .sport-card {
            gap: 8px;
            padding: 8px;
        }

        .sport-card .sport-info h4 {
            font-size: 0.85rem;
        }

        .sport-card .sport-info p {
            font-size: 0.75rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 15px;
            flex-shrink: 0;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .search-box input {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border: 2px solid #f3f0f7;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: #5e2d91;
            box-shadow: 0 0 0 4px rgba(94, 45, 145, 0.05);
        }

        .sports-grid-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-content: flex-start;
            gap: 8px;
            padding-right: 0px;
            overflow-y: hidden;
            flex: 1;
        }

        .sports-grid-images::-webkit-scrollbar {
            width: 6px;
        }
        .sports-grid-images::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .sports-grid-images::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .sport-image-card {
            flex: 0 1 calc(25% - 9px);
            min-width: 120px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #f0f0f5;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .sport-image-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(94, 45, 145, 0.15);
            border-color: #d1bced;
        }

        .sport-image-card .sport-img {
            width: 100%;
            height: 75px;
            background-size: cover;
            background-position: center;
            transition: all 0.3s;
        }

        .sport-info-box {
            padding: 8px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .sport-info-box .sport-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #444;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .sport-info-box .join-btn {
            background: #f0ebf7;
            color: #5e2d91;
            border: none;
            padding: 4px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .sport-image-card:hover .join-btn {
            background: #5e2d91;
            color: white;
        }

        @media (max-width: 1024px) {
            .main-portal-card {
                width: 95%;
                height: auto;
                padding: 1rem;
            }
            .grid-layout {
                grid-template-columns: 1fr;
                height: auto;
                overflow: visible;
            }
            .portal-card {
                height: 500px;
            }
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php require APP_ROOT . '/app/views/templates/general/header.php'; ?>
    <?php require APP_ROOT . '/app/views/templates/student/sub_header.php'; ?>

    <div class="page-container">
        <div class="main-portal-card">
            <div class="grid-layout">
                <!-- Available Sports -->
                <div class="portal-card">
                    <section id="available-sports-section">
                        <div class="section-header">
                            <h2><i class="fas fa-plus-circle"></i> Available Sports</h2>
                        </div>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="sport-search" placeholder="Search for sports..." autocomplete="off">
                        </div>
                        <div id="available-sports-list" class="sports-grid">
                            <p>Loading...</p>
                        </div>
                    </section>
                </div>

                <!-- Enrolled Sports -->
                <div class="portal-card">
                    <section id="enrolled-sports-section">
                        <div class="section-header">
                            <h2><i class="fas fa-medal"></i> My Enrolled Sports</h2>
                        </div>
                        <div id="enrolled-sports-list" class="sports-grid">
                            <p>Loading...</p>
                        </div>
                    </section>
                </div>
            </div>
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

        const sportImages = {
            'basketball': 'https://images.unsplash.com/photo-1519861531473-9200262188bf?w=500&q=80',
            'football': 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=500&q=80',
            'tennis': 'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?w=500&q=80',
            'swimming': 'https://images.unsplash.com/photo-1519315901367-f34f92742302?w=500&q=80',
            'volleyball': 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=500&q=80',
            'cricket': 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=500&q=80',
            'badminton': 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=500&q=80',
            'table tennis': 'https://images.unsplash.com/photo-1609710228159-0fa9bd7c0822?w=500&q=80',
            'rugby': 'https://images.unsplash.com/photo-1588693766782-595b2d7cbab5?w=500&q=80',
            'athletics': 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=500&q=80',
            'default': 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=500&q=80'
        };

        function getSportImage(name) {
            const lowerName = name.toLowerCase();
            for (let key in sportImages) {
                if (lowerName.includes(key)) return sportImages[key];
            }
            return sportImages['default'];
        }

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
                    if (searchInput.value.trim().length === 0) {
                        renderSuggestedSports(availableSports.slice(0, 6));
                    }
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

        function renderSuggestedSports(sports) {
            availableList.className = 'sports-grid-images';
            availableList.innerHTML = '';
            if (sports.length === 0) {
                availableList.innerHTML = '<p class="no-sports">No sports available to suggest.</p>';
                return;
            }
            sports.forEach(sport => {
                const img = getSportImage(sport.sport_name);
                const div = document.createElement('div');
                div.className = 'sport-image-card';
                div.innerHTML = `
                    <div class="sport-img" style="background-image: url('${img}')"></div>
                    <div class="sport-info-box">
                        <span class="sport-name" title="${sport.sport_name}">${sport.sport_name}</span>
                        <button class="join-btn" onclick="enrollInSport('${sport.sport_id}', '${sport.sport_name}')">Join <i class="fas fa-arrow-right"></i></button>
                    </div>
                `;
                availableList.appendChild(div);
            });
        }

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            if (!sportsLoaded && query.length > 0) loadAvailableSports(); 
            
            if (query.length === 0) {
                if (availableSports.length > 0) {
                    renderSuggestedSports(availableSports.slice(0, 6));
                } else {
                    availableList.innerHTML = '<p class="no-sports" style="text-align:center; color:#666; padding:20px;">Use the search bar to find sports to join.</p>';
                }
                return;
            }
            
            availableList.className = 'sports-grid';
            const filtered = availableSports
                .filter(s => {
                    const name = (s.sport_name || '').toLowerCase();
                    return name.includes(query);
                })
                .sort((a, b) => {
                    const nameA = (a.sport_name || '').toLowerCase();
                    const nameB = (b.sport_name || '').toLowerCase();
                    const startsA = nameA.startsWith(query);
                    const startsB = nameB.startsWith(query);
                    
                    if (startsA && !startsB) return -1;
                    if (!startsA && startsB) return 1;
                    return nameA.localeCompare(nameB);
                });
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
