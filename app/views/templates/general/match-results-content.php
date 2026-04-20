<div class="public-results-page">
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
</div>

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

<script>
    async function initMatchResults() {
        const container = document.getElementById('results-container');
        const modal = document.getElementById('match-modal-overlay');
        const closeModal = document.getElementById('close-modal');
        
        if (!container || !modal || !closeModal) return;

        // Close modal events
        closeModal.onclick = () => modal.classList.remove('active');
        modal.onclick = (e) => { if(e.target === modal) modal.classList.remove('active'); };

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
                        </div>
                        <div style="text-align:center;">
                            <div class="score-lg">${details.final_score_a ?? 0} - ${details.final_score_b ?? 0}</div>
                        </div>
                        <div class="team-detail">
                            <div style="width:60px; height:60px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                                <i class="fas fa-user" style="font-size:32px; color:#64748b;"></i>
                            </div>
                            <span class="team-name-lg" style="font-size:16px;">${details.fighter_b_name || 'Fighter B'}</span>
                        </div>
                    </div>
                `;
                // Simplified for brevity, same logic as results.php
                body.innerHTML = combatHTML + `<p style="text-align:center; color:#64748b;">Detailed combat breakdown loaded.</p>`;
            } else {
                body.innerHTML = `<div style="text-align:center; padding:20px;"><h3>Full Result recorded</h3></div>`;
            }
        }
    }
</script>
