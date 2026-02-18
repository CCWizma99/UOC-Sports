// Dummy student data
const students = [
   
    {
        id: 'S2021007',
        name: 'Kamal Dissanayake',
        faculty: 'Commerce',
        sport: 'Athletics',
        profilePic: null,
        achievements: [
            { title: '100m Sprint Gold', date: '2024-12-05', position: '1st Place', tournament: 'Track & Field Championship' },
            { title: '200m Silver Medal', date: '2024-11-28', position: '2nd Place', tournament: 'Inter-University Meet' },
            { title: 'Relay Team Record', date: '2024-10-15', position: 'Record Holder', tournament: 'National Games' }
        ]
    },
    {
        id: 'S2021008',
        name: 'Amaya Wijesinghe',
        faculty: 'Medicine',
        sport: 'Swimming',
        profilePic: null,
        achievements: [
            { title: '50m Freestyle Champion', date: '2024-12-20', position: '1st Place', tournament: 'Aquatic Championship' },
            { title: 'Butterfly Stroke Record', date: '2024-11-10', position: 'Record', tournament: 'University Gala' }
        ]
    },
    {
        id: 'S2021009',
        name: 'Thisara Bandara',
        faculty: 'Engineering',
        sport: 'Rugby',
        profilePic: null,
        achievements: [
            { title: 'League Champions', date: '2024-12-18', position: '1st Place', tournament: 'University Rugby League' },
            { title: 'Man of the Match', date: '2024-11-25', position: 'Award', tournament: 'Finals 2024' }
        ]
    },
    {
        id: 'S2021010',
        name: 'Dilani Gunasekara',
        faculty: 'Science',
        sport: 'Table Tennis',
        profilePic: null,
        achievements: [
            { title: 'Singles Gold Medal', date: '2024-12-03', position: '1st Place', tournament: 'Table Tennis Championship' },
            { title: 'Doubles Bronze', date: '2024-10-20', position: '3rd Place', tournament: 'Regional Competition' }
        ]
    },
    {
        id: 'S2021011',
        name: 'Chaminda Rodrigo',
        faculty: 'Arts',
        sport: 'Chess',
        profilePic: null,
        achievements: [
            { title: 'Grand Master Title', date: '2024-12-15', position: 'GM Achievement', tournament: 'National Chess Tournament' },
            { title: 'Rapid Chess Champion', date: '2024-11-08', position: '1st Place', tournament: 'University Chess Open' }
        ]
    },
    {
        id: 'S2021012',
        name: 'Sanduni Mendis',
        faculty: 'Law',
        sport: 'Netball',
        profilePic: null,
        achievements: [
            { title: 'Goal Shooter Excellence', date: '2024-12-22', position: 'Top Scorer', tournament: 'Women\'s Netball League' },
            { title: 'Team Championship', date: '2024-11-30', position: '1st Place', tournament: 'Inter-Faculty Competition' }
        ]
    }
];

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
        <div class="student-card" style="background: white; border: 2px solid rgb(229, 231, 235);; border-radius: 8px; padding: 1.5rem; transition: all 0.3s ease;">
            <!-- Header with Profile and Info -->
            <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem;">
                <!-- Profile Picture -->
                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2b0c4d 0%, #6b1fa0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; font-weight: bold; flex-shrink: 0;">
                    ${student.name.split(' ').map(n => n[0]).join('')}
                </div>
                <div>
                    <h3 style="margin: 0; color: #5e2d91; font-size: 1.1rem;">
                        ${student.name}
                    </h3>
                </div>
            </div>
            
            <!-- Student Details -->
            <div style="margin-bottom: 0.5rem;">
                <p style="margin: 0; color: #374151; line-height: 1.5;">
                    ${student.achievements.length} Achievement${student.achievements.length !== 1 ? 's' : ''} recorded
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                <button onclick="showAchievements('${student.id}')" style="background: #2b0c4d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                    View Achievements
                </button>
            </div>
        </div>
    `).join('');
}

// Show achievements at top of page
function showAchievements(studentId) {
    const student = students.find(s => s.id === studentId);
    if (!student) return;
    
    const displayArea = document.getElementById('achievementsDisplay');
    const nameElement = document.getElementById('achievementsStudentName');
    const contentElement = document.getElementById('achievementsContent');
    
    nameElement.textContent = `${student.name}'s Achievements`;
    
    if (student.achievements.length === 0) {
        contentElement.innerHTML = '<p style="margin: 0; color: #6b7280; text-align: center; padding: 2rem;">No achievements recorded yet</p>';
    } else {
        contentElement.innerHTML = student.achievements.map(achievement => `
            <div style="background: white; border-left: 4px solid #2b0c4d; border-radius: 6px; padding: 1rem; margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem; gap: 0.5rem;">
                    <h5 style="margin: 0; color: #2b0c4d; font-size: 0.95rem; font-weight: 700;">
                        ${achievement.title}
                    </h5>
                    <span style="background: #2b0c4d; color: #ffffff; padding: 0.2rem 0.5rem; border-radius: 0.5rem; font-size: 0.7rem; font-weight: 700; white-space: nowrap;">
                        ${achievement.position}
                    </span>
                </div>
                <p style="margin: 0 0 0.25rem 0; color: #374151; font-size: 0.85rem; font-weight: 500;">
                    ${achievement.tournament}
                </p>
                <p style="margin: 0; color: #6b7280; font-size: 0.75rem;">
                    ${formatDate(achievement.date)}
                </p>
            </div>
        `).join('');
    }
    
    displayArea.style.display = 'block';
    displayArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Close achievements display
function closeAchievementsDisplay() {
    document.getElementById('achievementsDisplay').style.display = 'none';
}

// View student achievements (keeping old function for compatibility)
function viewAchievements(studentId) {
    const student = students.find(s => s.id === studentId);
    if (!student) return;
    
    // Update modal content
    document.getElementById('modalStudentName').textContent = student.name;
    document.getElementById('modalStudentId').textContent = `${student.id} • ${student.faculty} • ${student.sport}`;
    
    // Render achievements
    const achievementsList = document.getElementById('achievementsList');
    if (student.achievements.length === 0) {
        achievementsList.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <p>No achievements recorded yet</p>
            </div>
        `;
    } else {
        achievementsList.innerHTML = student.achievements.map((achievement, index) => `
            <div style="background: linear-gradient(135deg, #f9f7ff 0%, #ffffff 100%); border: 2px solid #e5e7eb; border-left: 4px solid #2b0c4d; border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 4px 15px rgba(43,12,77,0.1)'; this.style.transform='translateX(4px)';" onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #2b0c4d 0%, #6b1fa0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(43,12,77,0.2);">
                        ${index + 1}
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h4 style="margin: 0; color: #2b0c4d; font-size: 1.1rem; font-weight: 700;">
                                ${achievement.title}
                            </h4>
                            <span style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #8b4500; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 8px rgba(255,215,0,0.3);">
                                ${achievement.position}
                            </span>
                        </div>
                        <p style="margin: 0 0 0.5rem 0; color: #374151; font-size: 0.95rem; font-weight: 500;">
                            ${achievement.tournament}
                        </p>
                        <p style="margin: 0; color: #6b7280; font-size: 0.85rem;">
                            ${formatDate(achievement.date)}
                        </p>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    // Show modal
    const modal = document.getElementById('achievementModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
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
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredStudents = students.filter(student => 
            student.name.toLowerCase().includes(searchTerm) ||
            student.id.toLowerCase().includes(searchTerm) ||
            student.faculty.toLowerCase().includes(searchTerm) ||
            student.sport.toLowerCase().includes(searchTerm)
        );
        renderStudents(filteredStudents);
    });
    
    // Initial render
    renderStudents();
    
    // Close modal when clicking outside
    document.getElementById('achievementModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
});
