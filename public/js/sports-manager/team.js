// Student rankings data - loaded from backend
let students = [];

// Load student rankings from backend
async function loadStudentRankings() {
    try {
        // Get sport_id from URL if available
        const urlParams = new URLSearchParams(window.location.search);
        const sportId = urlParams.get('sport');
        
        const url = sportId 
            ? `/uoc-sports/public/api/get-student-rankings.php?sport_id=${sportId}`
            : '/uoc-sports/public/api/get-student-rankings.php';
            
        console.log('Fetching rankings from:', url);
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('Rankings response:', data);
        
        if (data.success) {
            students = data.rankings || [];
            console.log('Loaded', students.length, 'students');
            renderStudents();
        } else {
            console.error('Failed to load rankings:', data.message);
            showError('Failed to load student rankings: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading rankings:', error);
        showError('Error connecting to server: ' + error.message);
    }
}

// Show error message
function showError(message) {
    const container = document.getElementById('studentsContainer');
    container.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #ef4444;">
            <i class="fas fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem;">${message}</p>
        </div>
    `;
}

// Render student cards
function renderStudents(studentsToRender = students) {
    const container = document.getElementById('studentsContainer');
    destroyStudentCardCharts();
    
    if (studentsToRender.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #6b7280;">
                <p style="font-size: 1.1rem;">No students found</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = studentsToRender.map(student => `
        <div class="student-card" style="background: white; border: 2px solid rgb(229, 231, 235); border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease;">
            <!-- Header with Profile and Info -->
            <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem;">
                <!-- Profile Picture -->
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2b0c4d 0%, #6b1fa0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; font-weight: bold; flex-shrink: 0;">
                    ${getInitials(student.fname + ' ' + student.lname)}
                </div>
                <div>
                    <h3 style="margin: 0; color: #5e2d91; font-size: 1.1rem;">
                        ${student.fname} ${student.lname}
                    </h3>
                </div>
            </div>
            
            <!-- Student Details -->
            <div style="margin-bottom: 0.5rem;">
                <p style="margin: 0 0 0.25rem 0; color: #374151; line-height: 1.5;">
                    <strong>Points:</strong> ${student.user_points || 0}
                </p>
                <p style="margin: 0; color: #374151; line-height: 1.5;">
                    ${student.total_achievements || 0} Achievement${student.total_achievements !== 1 ? 's' : ''} recorded
                </p>
            </div>

            <div style="background: #f9f7ff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.6rem; margin-top: 0.75rem;">
                <p style="margin: 0 0 0.5rem 0; color: #2b0c4d; font-size: 0.75rem; font-weight: 600;">Event Category Split</p>
                <div style="position: relative; height: 200px; width: 100%;">
                    <canvas id="${getStudentChartCanvasId(student.user_id)}"></canvas>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <button onclick="showAchievements('${student.user_id}')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                    View Achievements
                </button>
            </div>
        </div>
    `).join('');

    loadStudentCardCharts(studentsToRender);
}

// Get initials from name
function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

// Chart instances per student card
const studentCardCharts = new Map();

// Show achievements at top of page
async function showAchievements(userId) {
    const displayArea = document.getElementById('achievementsDisplay');
    const nameElement = document.getElementById('achievementsStudentName');
    const contentElement = document.getElementById('achievementsContent');
    
    // Show loading state
    nameElement.textContent = 'Loading...';
    contentElement.innerHTML = '<p style="text-align: center; padding: 2rem;">Loading achievements...</p>';
    displayArea.style.display = 'block';
    displayArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    try {
        const response = await fetch(`/uoc-sports/public/api/get-student-achievements.php?user_id=${userId}`);
        const data = await response.json();
        
        console.log('Student Achievements Data:', data); // Debug log
        
        if (data.success) {
            const student = students.find(s => s.user_id === userId);
            nameElement.textContent = `${student?.fname || ''} ${student?.lname || ''}'s Achievements`;
            
            if (data.achievements.length === 0) {
                contentElement.innerHTML = '<p style="margin: 0; color: #6b7280; text-align: center; padding: 2rem;">No achievements recorded yet</p>';
            } else {
                // Display achievements list
                contentElement.innerHTML = data.achievements.map(achievement => {
                    // Get year with fallback logic
                    let year = 'N/A';
                    if (achievement.tournament_year) {
                        year = achievement.tournament_year;
                    } else if (achievement.competition_date && achievement.competition_date !== '0000-00-00') {
                        try {
                            const date = new Date(achievement.competition_date);
                            if (!isNaN(date.getTime())) {
                                year = date.getFullYear();
                            }
                        } catch (e) {
                            console.warn('Error parsing date:', e);
                        }
                    }
                    
                    const competitionLabel = achievement.competition_name 
                        ? `${achievement.competition_name} ${year !== 'N/A' ? '(' + year + ')' : ''}` 
                        : '<em style="color: #ef4444;">No Competition Linked</em>';
                    
                    return `
                    <div style="background: white; border-left: 4px solid #2b0c4d; border-radius: 6px; padding: 1rem; margin-bottom: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem; gap: 0.5rem;">
                            <div>
                                <p style="margin: 0 0 0.25rem 0; color: #6b7280; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Competition</p>
                                <h5 style="margin: 0; color: #2b0c4d; font-size: 0.95rem; font-weight: 700;">
                                    ${competitionLabel}
                                </h5>
                            </div>
                            <span style="background: ${getAchievementColor(achievement.achievement)}; color: #ffffff; padding: 0.2rem 0.5rem; border-radius: 0.5rem; font-size: 0.7rem; font-weight: 700; white-space: nowrap;">
                                ${achievement.achievement}
                            </span>
                        </div>
                        <p style="margin: 0; color: #6b7280; font-size: 0.75rem;">
                            Points: ${achievement.points || 0} / 12
                        </p>
                    </div>
                `}).join('');
            }
        } else {
            contentElement.innerHTML = `<p style="color: #ef4444; text-align: center; padding: 2rem;">Failed to load achievements: ${data.message || 'Unknown error'}</p>`;
        }
    } catch (error) {
        console.error('Error loading achievements:', error);
        contentElement.innerHTML = `<p style="color: #ef4444; text-align: center; padding: 2rem;">Error loading achievements: ${error.message}</p>`;
    }
}

function extractEventCategory(achievement) {
    if (achievement.event_category) return achievement.event_category;
    if (achievement.competition_category) return achievement.competition_category;
    if (achievement.category) return achievement.category;

    const competitionName = achievement.competition_name || '';
    if (!competitionName) return 'Other';

    const lowerName = competitionName.toLowerCase();
    if (lowerName.includes('inter university') || lowerName.includes('inter-university')) return 'Inter University';
    if (lowerName.includes('inter faculty') || lowerName.includes('inter-faculty')) return 'Inter Faculty';
    if (lowerName.includes('championship')) return 'Championship';
    if (lowerName.includes('tournament')) return 'Tournament';
    if (lowerName.includes('league')) return 'League';
    if (lowerName.includes('cup')) return 'Cup';
    if (lowerName.includes('open')) return 'Open';
    if (lowerName.includes('national')) return 'National';
    if (lowerName.includes('international')) return 'International';

    return competitionName.split(' ')[0] || 'Other';
}

function getStudentChartCanvasId(userId) {
    const safeUserId = String(userId).replace(/[^a-zA-Z0-9_-]/g, '_');
    return `student-event-chart-${safeUserId}`;
}

// Map specific competition categories to their designated colors
function getCategoryColor(category) {
    const colorMap = {
        'Inter Faculty': '#16a34a',        // green
        'Inter University': '#2563eb',     // blue
        'Freshers': '#f97316',         // orange
        'National': '#8b5cf6',             // violet
        'International': '#ec4899',        // pink
        'Other': '#6b7280'                 // gray
    };
    return colorMap[category] || '#6b7280'; // default to gray if not found
}

function getCategoryPalette(labels) {
    // If labels is a number (backward compatibility), return generic palette
    if (typeof labels === 'number') {
        const colors = [
            '#2563eb', // blue
            '#16a34a', // green
            '#f97316', // orange
            '#eab308', // yellow
            '#7c3aed'  // purple
        ];
        return Array.from({ length: labels }, (_, index) => colors[index % colors.length]);
    }
    
    // If labels is an array, map each label to its specific color
    return labels.map(label => getCategoryColor(label));
}

function createStudentCardPieChart(canvas, achievements, userId) {
    if (!canvas || typeof Chart === 'undefined') return;

    const categoryCounts = achievements.reduce((acc, achievement) => {
        const category = extractEventCategory(achievement);
        acc[category] = (acc[category] || 0) + 1;
        return acc;
    }, {});

    const labels = Object.keys(categoryCounts);
    const values = Object.values(categoryCounts);

    if (labels.length === 0) {
        return;
    }

    const key = String(userId);
    const existingChart = studentCardCharts.get(key);
    if (existingChart) {
        existingChart.destroy();
        studentCardCharts.delete(key);
    }

    const chart = new Chart(canvas, {
        type: 'pie',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: getCategoryPalette(labels),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                    position: 'bottom',
                    labels: {
                        padding: 8,
                        font: {
                            size: 9,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });

    studentCardCharts.set(key, chart);
}

async function loadStudentCardChart(userId) {
    const canvasId = getStudentChartCanvasId(userId);
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(`/uoc-sports/public/api/get-student-achievements.php?user_id=${userId}`);
        const data = await response.json();

        // Card may have been removed by a new filter render before the request completed.
        const currentCanvas = document.getElementById(canvasId);
        if (!currentCanvas) return;

        if (!data.success || !Array.isArray(data.achievements) || data.achievements.length === 0) {
            const container = currentCanvas.parentElement;
            if (container) {
                container.innerHTML = '<p style="margin: 0; color: #6b7280; font-size: 0.72rem; text-align: center; padding-top: 1.2rem;">No chart data</p>';
            }
            return;
        }

        createStudentCardPieChart(currentCanvas, data.achievements, userId);
    } catch (error) {
        console.error('Error loading student chart:', error);
    }
}

function loadStudentCardCharts(studentsToRender) {
    studentsToRender.forEach(student => {
        loadStudentCardChart(student.user_id);
    });
}

function destroyStudentCardCharts() {
    studentCardCharts.forEach(chart => chart.destroy());
    studentCardCharts.clear();
}

// Get achievement color based on type
function getAchievementColor(achievement) {
    const colors = {
        '1st place': '#FFA07A',
        '2nd place': '#4ECDC4',
        '3rd place': '#cd7f32',
        '4th place': '#6b7280',
        'Best performance': '#10b981',
        'Participation': '#45B7D1'
    };
    return colors[achievement] || '#2b0c4d';
}

// Close achievements display
function closeAchievementsDisplay() {
    document.getElementById('achievementsDisplay').style.display = 'none';
}

// View student achievements (keeping old function for compatibility)
async function viewAchievements(userId) {
    const student = students.find(s => s.user_id === userId);
    if (!student) return;
    
    // Update modal content
    document.getElementById('modalStudentName').textContent = `${student.fname} ${student.lname}`;
    document.getElementById('modalStudentId').textContent = `${student.user_id} • Points: ${student.user_points || 0}`;
    
    // Show modal
    const modal = document.getElementById('achievementModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Show loading in achievements list
    const achievementsList = document.getElementById('achievementsList');
    achievementsList.innerHTML = '<p style="text-align: center; padding: 2rem;">Loading achievements...</p>';
    
    try {
        const response = await fetch(`/uoc-sports/public/api/get-student-achievements.php?user_id=${userId}`);
        const data = await response.json();
        
        console.log('Modal Achievements Data:', data); // Debug log
        
        if (data.success) {
            if (data.achievements.length === 0) {
                achievementsList.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <p>No achievements recorded yet</p>
                    </div>
                `;
            } else {
                achievementsList.innerHTML = data.achievements.map((achievement, index) => {
                    const year = achievement.tournament_year || (achievement.competition_date ? new Date(achievement.competition_date).getFullYear() : 'N/A');
                    const competitionLabel = achievement.competition_name 
                        ? `${achievement.competition_name} (${year})` 
                        : '<em style="color: #ef4444;">No Competition Linked</em>';
                    
                    return `
                    <div style="background: linear-gradient(135deg, #f9f7ff 0%, #ffffff 100%); border: 2px solid #e5e7eb; border-left: 4px solid #2b0c4d; border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 4px 15px rgba(43,12,77,0.1)'; this.style.transform='translateX(4px)';" onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                        <div style="display: flex; align-items: start; gap: 1rem;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2b0c4d 0%, #6b1fa0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(43,12,77,0.2);">
                                ${index + 1}
                            </div>
                            <div style="flex: 1;">
                                <div style="margin-bottom: 0.5rem;">
                                    <p style="margin: 0 0 0.25rem 0; color: #6b7280; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Competition Name</p>
                                    <h4 style="margin: 0 0 0.5rem 0; color: #2b0c4d; font-size: 1.1rem; font-weight: 700;">
                                        ${competitionLabel}
                                    </h4>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <span style="background: ${getAchievementColor(achievement.achievement)}; color: #ffffff; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                        ${achievement.achievement}
                                    </span>
                                </div>
                                
                                <p style="margin: 0; color: #6b7280; font-size: 0.85rem;">
                                    Points Earned: ${achievement.points || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                `}).join('');
            }
        } else {
            achievementsList.innerHTML = `<p style="color: #ef4444; text-align: center; padding: 2rem;">Failed to load achievements</p>`;
        }
    } catch (error) {
        console.error('Error loading achievements:', error);
        achievementsList.innerHTML = `<p style="color: #ef4444; text-align: center; padding: 2rem;">Error loading achievements</p>`;
    }
}

// Close modal
function closeModal() {
    const modal = document.getElementById('achievementModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    // Load initial data
    if (typeof initialRankings !== 'undefined' && initialRankings.length > 0) {
        students = initialRankings;
        renderStudents();
    } else {
        loadStudentRankings();
    }
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase(); // Case-insensitive search
        const filteredStudents = students.filter(student => {
            const fullName = `${student.fname} ${student.lname}`.toLowerCase(); // Case-insensitive
            
            return fullName.includes(searchTerm); // Search only by student name
        });
        renderStudents(filteredStudents);
    });
    
    // Close modal when clicking outside
    document.getElementById('achievementModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
});
