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
            overflow-y: auto;
        }

        .portal-card.welcome-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 30px 45px; /* Significantly increased padding */
            margin-bottom: 0;
            overflow-y: auto;
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
            padding: 16px 22px;
            background: linear-gradient(to bottom, var(--bg), #ffffff);
            overflow-y: auto;
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
            margin-bottom: 8px;
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

        /* --- Practice Sessions Calendar Styles (Sport Manager Exact Matches) --- */
        .calendar-section-compact {
            width: 100%;
            background: linear-gradient(to bottom, #faf5ff 0%, #ffffff 100%);
            border-radius: 12px;
            border: 2px solid #e9d5ff;
            box-shadow: 0 2px 8px rgba(168, 85, 247, 0.08);
            margin-top: 0;
        }

        #calendar {
            width: 100%;
            background-color: white;
            border-radius: 12px;
            padding: 0.4rem;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .calendar-header h4 {
            font-size: 1.25rem;
            color: #6b21a8;
            font-weight: 700;
            margin: 0;
        }

        .calendar-nav {
            display: flex;
            gap: 0.5rem;
        }

        .calendar-nav button {
            background: #a855f7;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(168, 85, 247, 0.2);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .calendar-nav button:hover {
            background: #9333ea;
            box-shadow: 0 4px 8px rgba(147, 51, 234, 0.3);
            transform: translateY(-1px);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.2rem;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #6b21a8;
            background: #f3e8ff;
            padding: 0.5rem;
            font-size: 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.3rem;
            border: 1px solid #e9d5ff;
        }

        .calendar-day {
            background-color: #fefefe;
            border: 2px solid #e9d5ff;
            border-radius: 6px;
            padding: 0.3rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 500;
            color: #4b5563;
        }

        .calendar-day:hover {
            border-color: #a855f7;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.25);
            z-index: 10;
            background: #faf5ff;
        }

        /* Exact Stat/Session Colors */
        .calendar-day.today {
            background: #f3e8ff;
            border-color: #a855f7;
            color: #7e22ce;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(168, 85, 247, 0.2);
        }
            
        .calendar-day.has-reservation.past-date {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
            font-weight: 600;
        }

        .calendar-day.has-reservation.future-date {
            background: #f0fdf4;
            border-color: #86efac;
            color: #16a34a;
            font-weight: 600;
        }

        .calendar-day.today.has-reservation {
            background: #e0e7ff;
            border-color: #818cf8;
            color: #4338ca;
            font-weight: 700;
            box-shadow: 0 3px 8px rgba(129, 140, 248, 0.25);
        }

        /* Session indicator dots */
        .calendar-day.has-reservation::after {
            content: '●';
            position: absolute;
            bottom: 3px;
            font-size: 0.65rem;
            color: #a855f7;
        }

        .calendar-day.has-reservation.past-date::after {
            color: #f87171;
        }

        .calendar-day.has-reservation.future-date::after {
            color: #4ade80;
        }

        .calendar-day.today.has-reservation::after {
            color: #818cf8;
        }

        .calendar-day.other-month {
            opacity: 0.4;
            pointer-events: none;
        }

        /* Calendar Tooltip Styles */
        .calendar-tooltip {
            position: fixed;
            background: white;
            border: 3px solid #a855f7;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 8px 24px rgba(168, 85, 247, 0.3);
            z-index: 10000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            max-width: 350px;
            min-width: 280px;
            font-size: 0.875rem;
            display: block;
        }

        .sessions .card-summary {
            border-top: none;
            padding-top: 0;
            gap: 15px;
            align-items: stretch;
            flex-direction: column;
            overflow: visible;
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

                <!-- Large Right Column Card: Practice Sessions Calendar -->
                <div class="summary-card sessions" style="min-height: auto; height: auto; max-height: none;">
                    <div class="card-header">
                        <div class="card-header-left">
                            <i class="fas fa-calendar-alt main-icon"></i>
                            <h3>Practice Sessions</h3>
                        </div>
                    </div>
                    <div class="card-summary" style="flex-direction: column; padding: 0.5rem; height: auto;">
                        <div class="calendar-section-compact" style="overflow: visible;">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder for upcoming sessions list if needed -->

    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>

    <script src="/uoc-sports/public/js/sports-manager/calendar.js"></script>
    <script>
    window.selectedSportId = null; // Show all sports by default on dashboard
    document.addEventListener("DOMContentLoaded", () => {
        fetchStats();
    });

    async function fetchStats() {
        try {
            const response = await fetch('/uoc-sports/public/student/dashboard-stats');
            if (!response.ok) return;
            const result = await response.json();

            if (result.status === 'success') {
                const s = result.stats;

                // 1. Enrolled Sports
                setCount('countSports', s.sports_count);
                renderList('listSports', s.sports_list, 'fas fa-check-circle');

                // 2. Reserved Equipment
                setCount('countEquipment', s.equipment_count);
                const equipmentDisplayList = (s.equipment_list || []).map(eq => eq.equipment_name);
                renderList('listEquipment', equipmentDisplayList, 'fas fa-tools');

                // 3. Active Facility Reservations
                setCount('countFacilities', s.facilities_count);
                const facilityDisplayList = (s.facilities_list || []).map(f => {
                    const slot = f.slot ? f.slot.charAt(0).toUpperCase() + f.slot.slice(1).toLowerCase() : '';
                    const date = formatDate(f.date);
                    return `${f.facility_name} &mdash; ${date}${slot ? ' (' + slot + ')' : ''}`;
                });
                renderList('listFacilities', facilityDisplayList, 'fas fa-map-marker-alt');

                // 4. Handle upcoming sessions if needed
                if (result.upcoming && Array.isArray(result.upcoming)) {
                    // Could store globalSessions = result.upcoming; 
                    // calendar.js takes care of displaying them.
                }
            }
        } catch (error) {
            console.error('Error fetching dashboard stats:', error);
        }
    }

    // Safely set a count badge (guards against missing elements)
    function setCount(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = (value !== undefined && value !== null) ? value : '0';
    }

    function renderList(containerId, items, iconClass) {
        const container = document.getElementById(containerId);
        if (!container) return;
        if (!items || items.length === 0) {
            container.innerHTML = '<span style="color: #bbb; font-style: italic;">None</span>';
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
