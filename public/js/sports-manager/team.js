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
            
            <!-- Action Buttons -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <button onclick="showAchievements('${student.user_id}')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                    View Achievements
                </button>
            </div>
        </div>
    `).join('');
}

// Get initials from name
function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
}

// Chart instance
let performanceChart = null;

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
                // Hide chart if no data
                if (performanceChart) {
                    performanceChart.destroy();
                    performanceChart = null;
                }
            } else {
                // Create performance chart
                createPerformanceChart(data.achievements);
                
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

// Create performance analysis chart (Dot Chart)
function createPerformanceChart(achievements) {
    const ctx = document.getElementById('performanceChart');
    
    if (!ctx) return;
    
    // Destroy existing chart if any
    if (performanceChart) {
        performanceChart.destroy();
    }
    
    try {
        // Extract category from competition name (e.g., "National Championship" -> "Championship")
        function extractCategory(competitionName) {
            if (!competitionName) return 'Unknown';
            
            const lowerName = competitionName.toLowerCase();
            
            // Check for specific competition types first
            if (lowerName.includes('inter university') || lowerName.includes('inter-university')) {
                return 'Inter University';
            }
            if (lowerName.includes('inter faculty') || lowerName.includes('inter-faculty')) {
                return 'Inter Faculty';
            }
            
            // Common competition categories
            const categories = {
                'championship': 'Championship',
                'competition': 'Competition',
                'tournament': 'Tournament',
                'league': 'League',
                'cup': 'Cup',
                'open': 'Open',
                'series': 'Series',
                'match': 'Match',
                'national': 'National',
                'international': 'International'
            };
            
            for (const [key, value] of Object.entries(categories)) {
                if (lowerName.includes(key)) {
                    return value;
                }
            }
            
            // If no category found, use first word of competition name
            return competitionName.split(' ')[0];
        }
        
        // Get year from achievement data with fallback
        function getYear(achievement) {
            // Try tournament_year first
            if (achievement.tournament_year) {
                return achievement.tournament_year;
            }
            // Try competition_date
            if (achievement.competition_date && achievement.competition_date !== '0000-00-00') {
                try {
                    const date = new Date(achievement.competition_date);
                    if (!isNaN(date.getTime())) {
                        return date.getFullYear();
                    }
                } catch (e) {
                    console.warn('Error parsing date:', e);
                }
            }
            // Default to current year
            return new Date().getFullYear();
        }
        
        // Color palette for categories (fixed colors)
        const categoryColors = {
            'Inter University': '#9B59B6',      // Purple - for inter university
            'Inter Faculty': '#45B7D1',         // Red - for inter faculty
            'Championship': '#FF6B6B',          // Light Red
            'Competition': '#4ECDC4',           // Teal
            'Tournament': '#3498DB',            // Blue
            'League': '#FFA07A',                // Coral
            'Cup': '#98D8C8',                   // Mint
            'Open': '#F7DC6F',                  // Yellow
            'Series': '#BB8FCE',                // Light Purple
            'Match': '#85C1E2',                 // Light Blue
            'National': '#2ECC71',              // Green
            'International': '#E74C3C',         // Bright Blue
            'Unknown': '#95A5A6'                // Gray
        };
        
        // Group achievements by category
        const categoryMap = new Map();
        
        achievements.forEach((achievement, idx) => {
            const category = extractCategory(achievement.competition_name);
            const year = getYear(achievement);
            const label = `${category}\n${year}`;
            
            if (!categoryMap.has(category)) {
                categoryMap.set(category, {
                    label: category,
                    data: [],
                    backgroundColor: categoryColors[category] || '#' + Math.floor(Math.random()*16777215).toString(16),
                    borderColor: categoryColors[category] || '#' + Math.floor(Math.random()*16777215).toString(16),
                    pointRadius: 8,
                    pointHoverRadius: 12,
                    pointStyle: 'circle',
                    borderWidth: 2
                });
            }
            
            categoryMap.get(category).data.push({
                x: label,  // Use the label directly for category axis
                y: achievement.points || 0,
                competitionName: achievement.competition_name,
                achievement: achievement.achievement,
                year: year
            });
        });
        
        // Sort achievements by year (oldest to newest) for proper timeline display
        const sortedAchievements = [...achievements].sort((a, b) => {
            const yearA = getYear(a);
            const yearB = getYear(b);
            return yearA - yearB;  // Ascending order: past records on left, recent on right
        });
        
        // Rebuild category datasets with sorted achievements
        categoryMap.clear();
        sortedAchievements.forEach((achievement, idx) => {
            const category = extractCategory(achievement.competition_name);
            const year = getYear(achievement);
            const label = `${category}\n${year}`;
            
            if (!categoryMap.has(category)) {
                categoryMap.set(category, {
                    label: category,
                    data: [],
                    backgroundColor: categoryColors[category] || '#' + Math.floor(Math.random()*16777215).toString(16),
                    borderColor: categoryColors[category] || '#' + Math.floor(Math.random()*16777215).toString(16),
                    pointRadius: 8,
                    pointHoverRadius: 12,
                    pointStyle: 'circle',
                    borderWidth: 2
                });
            }
            
            categoryMap.get(category).data.push({
                x: label,
                y: achievement.points || 0,
                competitionName: achievement.competition_name,
                achievement: achievement.achievement,
                year: year
            });
        });
        
        // Convert map to datasets array
        const datasets = Array.from(categoryMap.values());
        
        // Create labels for x-axis using sorted achievements
        const labels = sortedAchievements.map((a, idx) => {
            const category = extractCategory(a.competition_name);
            const year = getYear(a);
            return `${category}\n${year}`;
        });
        
        performanceChart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const dataPoint = context[0].raw;
                                return dataPoint.competitionName || 'Competition';
                            },
                            label: function(context) {
                                const dataPoint = context.raw;
                                return [
                                    `Category: ${context.dataset.label}`,
                                    `Year: ${dataPoint.year}`,
                                    `Achievement: ${dataPoint.achievement}`,
                                    `Points: ${dataPoint.y}`
                                ];
                            }
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'category',
                        labels: labels,
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Competition Timeline',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: false,
                            font: {
                                size: 9
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawOnChartArea: true,
                            offset: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 12,
                        title: {
                            display: true,
                            text: 'Points Achieved',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            stepSize: 2
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating performance chart:', error);
        // Show error message in chart container
        const chartContainer = ctx.parentElement;
        if (chartContainer) {
            chartContainer.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 2rem;">Error creating chart. Please check console for details.</p>';
        }
    }
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
    // Destroy chart when closing
    if (performanceChart) {
        performanceChart.destroy();
        performanceChart = null;
    }
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
