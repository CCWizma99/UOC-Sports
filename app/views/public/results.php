<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Results | UOC Sports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(/uoc-sports/public/css/global.css);
        @import url(/uoc-sports/public/css/general/header.css);
        @import url(/uoc-sports/public/css/general/footer.css);
        .public-results-page {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .hero-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .hero-header h1 {
            font-size: 42px;
            color: #1e1e2e;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #1e1e2e, #5e2d91);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-header p {
            font-size: 18px;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Results Grid */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }

        .result-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(94, 45, 145, 0.1);
        }

        .card-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #faf9fc, #f3f0f7);
            border-bottom: 1px solid rgba(94, 45, 145, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .tournament-badge {
            font-size: 12px;
            font-weight: 700;
            color: #5e2d91;
            background: rgba(94, 45, 145, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sport-icon {
            color: #8b5cf6;
            font-size: 20px;
        }

        .card-body {
            padding: 24px;
        }

        .match-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e1e2e;
            margin: 0 0 12px 0;
            line-height: 1.3;
        }

        .match-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }
        .match-meta i { margin-right: 6px; color: #9ca3af; }

        /* Winner Banner */
        .winner-banner {
            background: #fdfbf7;
            border: 1px solid #fef3c7;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .winner-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #d97706;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 4px;
        }

        .winner-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e1e2e;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .winner-name i { color: #f59e0b; } /* Gold trophy */

        /* Draw / No Result State */
        .draw-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .draw-banner .winner-label { color: #475569; }
        .draw-banner .winner-name { color: #334155; }
        .draw-banner .winner-name i { color: #94a3b8; }
        
        /* Loading skeleton */
        .skeleton { animation: pulse 1.5s infinite; background: #e2e8f0; border-radius: 4px; }
        @keyframes pulse { 0% { opacity: 0.6; } 50% { opacity: 1; } 100% { opacity: 0.6; } }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-container {
            background: white;
            width: 90%;
            max-width: 700px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active .modal-container {
            transform: scale(1);
        }

        .modal-header {
            padding: 30px;
            background: linear-gradient(135deg, #5e2d91, #8b5cf6);
            color: white;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .close-modal:hover { background: rgba(255, 255, 255, 0.3); }

        .modal-body {
            padding: 40px;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Scoreboard Detail UI */
        .scoreboard-detail {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 20px;
        }

        .team-detail {
            flex: 1;
            text-align: center;
        }

        .team-detail img {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 20px;
            margin-bottom: 12px;
            padding: 10px;
            object-fit: contain;
        }

        .team-name-lg {
            font-size: 20px;
            font-weight: 800;
            color: #1e1e2e;
            display: block;
        }

        .score-lg {
            font-size: 56px;
            font-weight: 900;
            color: #5e2d91;
            line-height: 1;
        }

        .score-separator {
            font-size: 24px;
            color: #94a3b8;
            font-weight: 300;
        }

        /* Set Breakdown Table */
        .sets-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .sets-table th {
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            padding: 10px 16px;
            letter-spacing: 1px;
        }

        .set-row {
            background: #f8fafc;
            border-radius: 12px;
        }

        .set-row td {
            padding: 16px;
            font-weight: 600;
        }

        .set-row td:first-child { border-radius: 12px 0 0 12px; color: #64748b; font-size: 14px; }
        .set-row td:last-child { border-radius: 0 12px 12px 0; }

        .set-score {
            display: flex;
            justify-content: center;
            gap: 12px;
            font-size: 18px;
        }

        .winner-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body class="mesh-pattern">

    <?php require '../app/views/templates/general/header.php'; ?>

    <main class="public-results-page">
        <div class="hero-header">
            <h1>Official Match Results</h1>
            <p>Explore the latest verified outcomes of university tournaments and events.</p>
        </div>

        <div class="results-grid" id="results-container">
            <!-- Skeletons while loading -->
            <div class="result-card" style="height: 250px;"><div class="skeleton" style="width:100%;height:100%;"></div></div>
            <div class="result-card" style="height: 250px;"><div class="skeleton" style="width:100%;height:100%;"></div></div>
            <div class="result-card" style="height: 250px;"><div class="skeleton" style="width:100%;height:100%;"></div></div>
        </div>
    </main>

    <!-- Match Detail Modal -->
    <div class="modal-overlay" id="match-modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <button class="close-modal" id="close-modal"><i class="fas fa-times"></i></button>
                <div id="modal-header-content">
                    <span id="modal-tournament" class="tournament-badge" style="background: rgba(255,255,255,0.2); color:white; margin-bottom:10px; display:inline-block;">Tournament</span>
                    <h2 id="modal-match-name" style="margin:0; font-size: 24px;">Match Name</h2>
                    <p id="modal-match-date" style="margin:5px 0 0 0; opacity:0.8; font-size: 14px;">Date</p>
                </div>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamic Content Loads Here -->
                <div class="skeleton" style="height: 200px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <?php require '../app/views/templates/general/footer.php'; ?>

    <?php require '../app/views/templates/general/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const container = document.getElementById('results-container');
            const modal = document.getElementById('match-modal-overlay');
            const closeModal = document.getElementById('close-modal');
            
            // Close modal events
            closeModal.addEventListener('click', () => modal.classList.remove('active'));
            modal.addEventListener('click', (e) => { if(e.target === modal) modal.classList.remove('active'); });

            try {
                const res = await fetch('/uoc-sports/public/public/match-results-api');
                const data = await res.json();
                
                if (data.status === 'success') {
                    if(data.data.length === 0) {
                        container.innerHTML = `
                            <div style="grid-column: 1/-1; text-align:center; padding: 60px; background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                                <i class="fas fa-calendar-times" style="font-size:48px; color: #cbd5e1; margin-bottom:16px;"></i>
                                <h3 style="color:#334155; font-size:20px; margin:0 0 8px 0;">No Results Available</h3>
                                <p style="color:#64748b; margin:0;">Check back later as new match results are published.</p>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = '';
                    data.data.forEach(match => {
                        let bannerHTML = '';
                        
                        if (match.result_status === 'DRAW' || match.result_status === 'CANCELLED' || match.result_status === 'NO_RESULT') {
                            bannerHTML = `
                                <div class="draw-banner">
                                    <span class="winner-label">Outcome</span>
                                    <div class="winner-name">
                                        <i class="fas fa-handshake"></i> ${match.result_status.replace('_', ' ')}
                                    </div>
                                </div>
                            `;
                        } else if (match.winner_display_name && match.winner_display_name !== 'DRAW') {
                            bannerHTML = `
                                <div class="winner-banner">
                                    <span class="winner-label">Match Winner</span>
                                    <div class="winner-name">
                                        <i class="fas fa-trophy"></i> ${match.winner_display_name}
                                    </div>
                                </div>
                            `;
                        }

                        const dateStr = new Date(match.match_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                        const card = document.createElement('div');
                        card.className = 'result-card';
                        card.style.cursor = 'pointer';
                        card.innerHTML = `
                            <div class="card-header">
                                <span class="tournament-badge">${match.tournament_name}</span>
                                <div class="sport-icon"><i class="fas fa-medal"></i></div>
                            </div>
                            <div class="card-body">
                                <h3 class="match-title">${match.match_name}</h3>
                                <div class="match-meta">
                                    <span><i class="far fa-calendar-alt"></i> ${dateStr}</span>
                                    <span><i class="fas fa-running"></i> ${match.sport_name}</span>
                                </div>
                                ${bannerHTML}
                                <div style="margin-top:20px; text-align:center; color:#5e2d91; font-size:12px; font-weight:700;">
                                    VIEW FULL BREAKDOWN <i class="fas fa-chevron-right" style="font-size:10px; margin-left:4px;"></i>
                                </div>
                            </div>
                        `;
                        
                        card.onclick = () => showMatchDetails(match);
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = `<p style="color:red; text-align:center; grid-column:1/-1;">Error loading results.</p>`;
                }
            } catch (e) {
                container.innerHTML = `<p style="color:red; text-align:center; grid-column:1/-1;">Could not connect to the server.</p>`;
            }

            async function showMatchDetails(match) {
                modal.classList.add('active');
                const body = document.getElementById('modal-body');
                const headerTournament = document.getElementById('modal-tournament');
                const headerName = document.getElementById('modal-match-name');
                const headerDate = document.getElementById('modal-match-date');

                headerTournament.innerText = match.tournament_name;
                headerName.innerText = match.match_name;
                headerDate.innerText = new Date(match.match_date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                
                body.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin" style="font-size:32px; color:#5e2d91;"></i><p style="color:#64748b; margin-top:10px;">Fetching match highlights...</p></div>';

                try {
                    const res = await fetch(`/uoc-sports/public/public/match-details/${match.match_id}`);
                    const json = await res.json();

                    if (json.status === 'success') {
                        renderDetails(json.data, match.sport_category);
                    } else {
                        body.innerHTML = `<p style="color:red; text-align:center;">Failed to load details: ${json.message}</p>`;
                    }
                } catch (e) {
                    body.innerHTML = `<p style="color:red; text-align:center;">Network error while fetching details.</p>`;
                }
            }

            function renderDetails(data, category) {
                const body = document.getElementById('modal-body');
                const details = data.details || {};
                
                if (category === 'BALL_COURT') {
                    const periods = details.period_scores || [];
                    const isVolleyball = details.sport_subtype === 'VOLLEYBALL';
                    const periodLabel = isVolleyball ? 'Set' : 'Period';

                    let scoreboardHTML = `
                        <div class="scoreboard-detail">
                            <div class="team-detail">
                                <i class="fas fa-university" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <span class="team-name-lg">${details.team_a_name || 'Team A'}</span>
                            </div>
                            <div style="text-align:center;">
                                <div class="score-lg">${details.final_score_a ?? 0} - ${details.final_score_b ?? 0}</div>
                                <div style="color:#64748b; font-size:12px; font-weight:700; text-transform:uppercase; margin-top:10px;">FINAL SCORE</div>
                            </div>
                            <div class="team-detail">
                                <i class="fas fa-shield-alt" style="font-size:48px; color:#cbd5e1; margin-bottom:15px;"></i>
                                <span class="team-name-lg">${details.team_b_name || 'Team B'}</span>
                            </div>
                        </div>

                        <div style="margin-top:30px;">
                            <h4 style="color:#1e1e2e; margin-bottom:15px; font-size:16px;">Score Breakdown</h4>
                            <table class="sets-table">
                                <thead>
                                    <tr>
                                        <th>${periodLabel}</th>
                                        <th style="text-align:center;">Scoreline</th>
                                        <th style="text-align:right;">Set Winner</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    periods.forEach((p, idx) => {
                        const winner = p.a > p.b ? details.team_a_name : details.team_b_name;
                        scoreboardHTML += `
                            <tr class="set-row">
                                <td>${periodLabel} ${idx + 1}</td>
                                <td style="text-align:center;">
                                    <span class="set-score">
                                        <span style="${p.a > p.b ? 'color:#1e1e2e; font-weight:800;' : 'color:#94a3b8;'}">${p.a}</span>
                                        <span style="color:#e2e8f0;">:</span>
                                        <span style="${p.b > p.a ? 'color:#1e1e2e; font-weight:800;' : 'color:#94a3b8;'}">${p.b}</span>
                                    </span>
                                </td>
                                <td style="text-align:right; font-size:13px; color:#334155;">
                                    <span class="winner-dot" style="${p.a === p.b ? 'background:#94a3b8;' : ''}"></span>${winner}
                                </td>
                            </tr>
                        `;
                    });

                    scoreboardHTML += `</tbody></table></div>`;
                    
                    if (details.notes) {
                        scoreboardHTML += `<div style="margin-top:20px; padding:15px; background:#f1f5f9; border-radius:12px; font-size:13px; color:#475569;">
                            <strong>Notes:</strong> ${details.notes}
                        </div>`;
                    }

                    body.innerHTML = scoreboardHTML;
                } else if (category === 'COMBAT') {
                    const rounds = details.round_scores || [];
                    
                    let combatHTML = `
                        <div class="scoreboard-detail">
                            <div class="team-detail">
                                <div style="width:60px; height:60px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                                    <i class="fas fa-user-ninja" style="font-size:32px; color:#5e2d91;"></i>
                                </div>
                                <span class="team-name-lg" style="font-size:16px;">${details.fighter_a_name || 'Fighter A'}</span>
                                <span style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Fighter A</span>
                            </div>
                            <div style="text-align:center;">
                                <div class="score-lg">${details.final_score_a ?? 0} - ${details.final_score_b ?? 0}</div>
                                <div style="color:#5e2d91; font-size:12px; font-weight:800; text-transform:uppercase; margin-top:5px;">${details.result_type || 'POINTS'}</div>
                            </div>
                            <div class="team-detail">
                                <div style="width:60px; height:60px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                                    <i class="fas fa-user" style="font-size:32px; color:#64748b;"></i>
                                </div>
                                <span class="team-name-lg" style="font-size:16px;">${details.fighter_b_name || 'Fighter B'}</span>
                                <span style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Fighter B</span>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; margin-top:20px;">
                            <div style="background:#f8fafc; padding:12px; border-radius:12px; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Weight Category</div>
                                <div style="font-size:16px; color:#1e1e2e; font-weight:700;">${details.weight_category || '-'}</div>
                            </div>
                            <div style="background:#f8fafc; padding:12px; border-radius:12px; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Match Status</div>
                                <div style="font-size:16px; color:#5e2d91; font-weight:700;">${data.result_status || 'COMPLETED'}</div>
                            </div>
                        </div>

                        <div style="margin-top:25px;">
                            <h4 style="color:#1e1e2e; margin-bottom:12px; font-size:15px; display:flex; align-items:center; gap:8px;">
                                <i class="fas fa-list-ol" style="color:#5e2d91;"></i> Round Breakdown
                            </h4>
                            <table class="sets-table">
                                <thead>
                                    <tr>
                                        <th>Round</th>
                                        <th style="text-align:center;">Points Gain</th>
                                        <th style="text-align:right;">Stats</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    rounds.forEach((r, idx) => {
                        combatHTML += `
                            <tr class="set-row">
                                <td style="font-weight:700; color:#1e1e2e;">Round ${idx + 1}</td>
                                <td style="text-align:center;">
                                    <span class="set-score" style="background:#f1f5f9; padding:4px 12px; border-radius:20px;">
                                        <span style="${r.a > r.b ? 'color:#5e2d91; font-weight:800;' : 'color:#64748b;'}">${r.a}</span>
                                        <span style="color:#cbd5e1; margin:0 4px;">-</span>
                                        <span style="${r.b > r.a ? 'color:#1e1e2e; font-weight:800;' : 'color:#64748b;'}">${r.b}</span>
                                    </span>
                                </td>
                                <td style="text-align:right; font-size:12px; color:#64748b;">
                                    ${r.a > r.b ? '<span style="color:#10b981; font-weight:700;">WIN</span>' : (r.a < r.b ? '<span style="color:#ef4444; font-weight:700;">LOSS</span>' : 'DRAW')}
                                </td>
                            </tr>
                        `;
                    });

                    combatHTML += `</tbody></table></div>`;

                    // Combat Stats Section (Warnings/Knockdowns)
                    combatHTML += `
                        <div style="margin-top:25px; display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div>
                                <h5 style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:8px;">Fighter A (UOC) Stats</h5>
                                <div style="display:flex; gap:10px;">
                                    <div class="stat-mini" title="Warnings"><i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i> ${details.warnings_a || 0}</div>
                                    <div class="stat-mini" title="Knockdowns"><i class="fas fa-fist-raised" style="color:#ef4444;"></i> ${details.knockdowns_a || 0}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <h5 style="font-size:11px; color:#64748b; text-transform:uppercase; margin-bottom:8px;">Fighter B Stats</h5>
                                <div style="display:flex; gap:10px; justify-content:flex-end;">
                                    <div class="stat-mini" title="Warnings"><i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i> ${details.warnings_b || 0}</div>
                                    <div class="stat-mini" title="Knockdowns"><i class="fas fa-fist-raised" style="color:#ef4444;"></i> ${details.knockdowns_b || 0}</div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (details.notes) {
                        combatHTML += `<div style="margin-top:20px; padding:15px; border-left:4px solid #5e2d91; background:#f8fafc; border-radius:0 8px 8px 0; font-size:13px; color:#475569;">
                            <strong>Match Summary:</strong> ${details.notes}
                        </div>`;
                    }

                    body.innerHTML = combatHTML;
                } else {
                    // Fallback for other categories
                    body.innerHTML = `
                        <div style="text-align:center; padding:20px;">
                            <h3 style="color:#1e1e2e;">Full Result Recorded</h3>
                            <p style="color:#64748b;">Detailed period-by-period breakdown is only available for court sports currently.</p>
                            <div class="winner-banner" style="display:inline-block; margin-top:20px;">
                                <span class="winner-label">Match Winner</span>
                                <div class="winner-name"><i class="fas fa-trophy"></i> ${data.winner_display_name || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                }
            }
        });
    </script>
</body>
</html>
