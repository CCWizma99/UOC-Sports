<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal | UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/student/student-portal.css); /* Reuse existing styles */
        @import url(/uoc-sports/public/css/student/sub-nav.css);
        @import url(/uoc-sports/public/css/general/footer.css);

        /* Reuse existing styles from student-portal.css */


        .portal-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .portal-card h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            color: #5e2d91;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .student-portal-wrapper {
            display: flex;
            flex-direction: column;
            max-width: 100%;
            margin: 0;
            padding: 20px 40px; /* Increased padding */
            height: calc(100vh - 120px);
            min-height: 520px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .portal-card.welcome-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 30px 45px; /* Significantly increased padding */
            margin-bottom: 0;
            overflow: hidden;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }

        .portal-card h2 {
            margin: 0 0 2px 0;
            font-size: 1.3rem;
            color: #2d1a47;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .portal-card p {
            margin: 0 0 12px 0;
            font-size: 0.85rem;
            color: #6a528a;
        }

        /* Asymmetric Summary Grid - 3-Stack (L) vs 1-Big (R) */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            grid-template-rows: repeat(3, 1fr);
            gap: 24px; /* Doubled gap for more breathing room */
            flex: 1;
            min-height: 0;
            margin-top: 10px;
        }

        /* Summary Cards - Unified System Theme (Purple) */
        .summary-card.sports,
        .summary-card.sessions,
        .summary-card.equipment,
        .summary-card.facilities { 
            --accent: #5e2d91; 
            --bg: #f9f6ff; 
            --border: #e9e4f5; 
        }

        /* Standard Cards on the Left */
        .summary-card {
            background: var(--bg);
            padding: 12px 18px;
            border-radius: 16px;
            border: 1px solid var(--border);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            box-sizing: border-box;
            overflow: hidden;
            height: 100%;
            position: relative;
            border-left: 4px solid var(--accent);
        }

        /* Large Sessions Card - Span 3 Rows on Right */
        .summary-card.sessions {
            grid-column: 2;
            grid-row: 1 / span 3;
            padding: 22px;
            background: linear-gradient(to bottom, var(--bg), #ffffff);
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            filter: brightness(0.98);
            border-color: var(--accent);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
            width: 100%;
        }

        .sessions .card-header {
            margin-bottom: 20px;
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-card i.main-icon {
            font-size: 1rem;
            color: var(--accent);
            background: var(--bg);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .summary-card h3 {
            font-size: 0.95rem;
            color: #444;
            margin: 0;
            font-weight: 700;
        }

        .sessions h3 { font-size: 1.2rem; }

        .summary-card .count {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent);
            background: var(--bg);
            padding: 2px 10px;
            border-radius: 20px;
        }

        .card-summary {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            width: 100%;
            text-align: left;
            border-top: 1px solid #f8f7fa;
            padding-top: 8px;
            flex: 1;
            overflow: hidden;
            display: flex;
            gap: 15px;
        }

        .sessions .card-summary {
            border-top: none;
            padding-top: 0;
            gap: 25px;
            align-items: flex-start;
        }

        /* Calendar Navigation - Reference Image Style */
        .calendar-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #ffffff;
            color: #1a1a1a;
            border-bottom: 2px solid #f0f0f5;
        }

        .calendar-nav span {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .calendar-nav-buttons {
            display: flex;
            gap: 10px;
        }

        .cal-nav-btn {
            background: #5e2d91; /* System Purple */
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px; /* Square with slight roundness */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }

        .cal-nav-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            flex: 1;
        }

        .calendar-table th {
            padding: 10px 5px;
            font-size: 0.85rem;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: none;
            background: #ffffff;
            border-bottom: none;
            opacity: 0.8;
        }

        .calendar-table td {
            padding: 0;
            height: 52px;
            text-align: center;
            border: none;
            position: relative;
            transition: all 0.2s ease;
            font-size: 1rem;
            color: #b0b0b0; /* Softer gray for non-active numbers */
            font-weight: 500;
            cursor: pointer;
        }

        .calendar-table td:hover:not(.empty) {
            color: #1a1a1a;
        }

        .calendar-day-num {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            border-radius: 10px;
            transition: all 0.2s ease;
            position: relative;
            z-index: 2;
        }

        /* Today Highlight - Reference Match */
        .calendar-table td.today .calendar-day-num {
            color: #ffffff;
            background: #5e2d91;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(94, 45, 145, 0.2);
        }
        
        /* Session Highlight */
        .calendar-table td.has-session .calendar-day-num {
            color: #ffffff;
            background: #2e7d32; 
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        .session-indicator {
            display: none; /* Removed in favor of full cell highlight */
        }

        .calendar-table td.empty {
            background: #ffffff;
        }

        .sessions .card-summary {
            border-top: none;
            padding-top: 0;
            gap: 15px;
            align-items: stretch;
            flex-direction: column;
        }
        
        .sessions-scroll-container {
            max-height: 140px;
            overflow-y: auto;
            border-top: 2px solid #f0f0f5;
            padding-top: 15px;
        }

        .card-summary ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .card-summary li {
            padding: 3px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-summary li i {
            font-size: 0.7rem;
            margin-bottom: 0;
            color: #999;
        }

        @media (max-width: 600px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .student-portal-wrapper {
                height: auto;
                min-height: 100vh;
            }
        }
    </style>
</head>
<body class="mesh-sporty">
    <?php
        $userModel = new User();
        $fname = (isset($data['user'])) ? $data['user']['fname'] : 'Student';
        
        require '../app/views/templates/general/header.php';
        require '../app/views/templates/student/sub_header.php';
    ?>

    <div class="student-portal-wrapper">
        <!-- Welcome Message -->
        <div class="portal-card welcome-card">
            <h2><i class="fas fa-user-graduate"></i> Welcome Back, <?php echo htmlspecialchars($fname); ?>!</h2>
            <p>From here you can manage your sports activities and reservations.</p>
            
            <!-- Summary Cards Inside Welcome Container -->
            <div class="summary-grid">
                <!-- Stacked Left Column -->
                <a href="/uoc-sports/public/student/sports" class="summary-card sports">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i class="fas fa-running main-icon"></i>
                            <h3>Enrolled Sports</h3>
                        </div>
                        <div class="count" id="countSports">-</div>
                    </div>
                    <div class="card-summary" id="listSports"></div>
                </a>
                
                <a href="/uoc-sports/public/student/equipment" class="summary-card equipment">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i class="fas fa-table-tennis main-icon"></i>
                            <h3>Reserved Equipment</h3>
                        </div>
                        <div class="count" id="countEquipment">-</div>
                    </div>
                    <div class="card-summary" id="listEquipment"></div>
                </a>

                <a href="/uoc-sports/public/student/facilities" class="summary-card facilities">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i class="fas fa-building main-icon"></i>
                            <h3>Active Reservations</h3>
                        </div>
                        <div class="count" id="countFacilities">-</div>
                    </div>
                    <div class="card-summary" id="listFacilities"></div>
                </a>

                <!-- Large Right Column Card -->
                <a href="/uoc-sports/public/student/sports" class="summary-card sessions">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i class="fas fa-calendar-alt main-icon"></i>
                            <h3>Upcoming Sessions</h3>
                        </div>
                        <div class="count" id="countSessions">-</div>
                    </div>
                    <div class="card-summary">
                        <div id="miniCalendar" class="mini-calendar"></div>
                        <div class="sessions-scroll-container">
                            <div id="listSessions" class="summary-list-container"></div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Placeholder for upcoming sessions list if needed -->

    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        fetchStats();
        renderMiniCalendar(); // Initial render for current month
    });

        let currentDisplayDate = new Date();
        let globalSessions = [];

        function renderMiniCalendar(sessions = globalSessions) {
            const container = document.getElementById('miniCalendar');
            if (!container) return;

            const year = currentDisplayDate.getFullYear();
            const month = currentDisplayDate.getMonth();
            const today = new Date();
            const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
            
            // Adjust for Monday start to match reference image (Reference shows Su at the end)
            // Reference image headers: Mo, Tu, We, Th, Fr, Sa, Su
            let firstDay = new Date(year, month, 1).getDay(); // 0=Su, 1=Mo...
            firstDay = (firstDay === 0) ? 6 : firstDay - 1; // 0=Mo, 6=Su
            
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Map sessions to dates
            const sessionDates = {};
            if (sessions) {
                sessions.forEach(s => {
                    const d = new Date(s.session_date);
                    if (d.getMonth() === month && d.getFullYear() === year) {
                        const dayOfMonth = d.getDate();
                        if (!sessionDates[dayOfMonth]) sessionDates[dayOfMonth] = [];
                        sessionDates[dayOfMonth].push(s);
                    }
                });
            }

            const monthName = currentDisplayDate.toLocaleString('default', { month: 'long' });
            
            let html = `
                <div class="calendar-nav">
                    <span>${monthName} ${year}</span>
                    <div class="calendar-nav-buttons">
                        <button class="cal-nav-btn" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                        <button class="cal-nav-btn" onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th><th>Su</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let date = 1;
            for (let i = 0; i < 6; i++) {
                html += '<tr>';
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        html += '<td class="empty"></td>';
                    } else if (date > daysInMonth) {
                        html += '<td class="empty"></td>';
                    } else {
                        const hasSession = sessionDates[date];
                        const isToday = isCurrentMonth && date === today.getDate();
                        const classes = [];
                        if (hasSession) classes.push('has-session');
                        if (isToday) classes.push('today');

                        html += `
                            <td class="${classes.join(' ')}">
                                <span class="calendar-day-num">${date}</span>
                            </td>
                        `;
                        date++;
                    }
                }
                html += '</tr>';
                if (date > daysInMonth) break;
            }

            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function changeMonth(offset) {
            currentDisplayDate.setMonth(currentDisplayDate.getMonth() + offset);
            renderMiniCalendar();
        }

    async function fetchStats() {
        try {
            const response = await fetch('/uoc-sports/public/student/dashboard-stats');
            if (!response.ok) return;
            const result = await response.json();

            if (result.status === 'success') {
                const s = result.stats;
                
                // 1. Sports
                document.getElementById('countSports').textContent = s.sports_count;
                renderList('listSports', s.sports_list, 'fas fa-check-circle');

                // 2. Sessions
                document.getElementById('countSessions').textContent = s.sessions_count;
                const sessionDisplayList = s.sessions_list.map(sess => `${sess.sport_name}: ${formatDate(sess.session_date)}`);
                renderList('listSessions', sessionDisplayList, 'fas fa-clock');
                
                // Highlight sessions on mini-calendar
                if (result.upcoming) {
                    globalSessions = result.upcoming;
                    renderMiniCalendar(globalSessions);
                }

                // 3. Equipment
                document.getElementById('countEquipment').textContent = s.equipment_count;
                const equipmentDisplayList = s.equipment_list.map(eq => eq.equipment_name);
                renderList('listEquipment', equipmentDisplayList, 'fas fa-tools');

                // 4. Facilities
                document.getElementById('countFacilities').textContent = s.facilities_count;
                const facilityDisplayList = s.facilities_list.map(f => `${f.facility_name} (${f.slot.toLowerCase()})`);
                renderList('listFacilities', facilityDisplayList, 'fas fa-map-marker-alt');
            }
        } catch (error) {
            console.error('Error fetching dashboard stats:', error);
        }
    }

    function renderList(containerId, items, iconClass) {
        const container = document.getElementById(containerId);
        if (!items || items.length === 0) {
            container.innerHTML = '<span style="color: #bbb; font-style: italic;">No active items</span>';
            return;
        }
        
        let html = '<ul>';
        items.forEach(item => {
            html += `<li><i class="${iconClass}"></i> ${item}</li>`;
        });
        html += '</ul>';
        container.innerHTML = html;
    }

    function formatDate(dateString) {
        const options = { month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
    </script>
</body>
</html>
