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

    <?php require '../app/views/templates/general/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const container = document.getElementById('results-container');
            
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

                        container.innerHTML += `
                            <div class="result-card">
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
                                </div>
                            </div>
                        `;
                    });
                } else {
                    container.innerHTML = `<p style="color:red; text-align:center; grid-column:1/-1;">Error loading results.</p>`;
                }
            } catch (e) {
                container.innerHTML = `<p style="color:red; text-align:center; grid-column:1/-1;">Could not connect to the server.</p>`;
            }
        });
    </script>
</body>
</html>
