// Calendar functionality
class Calendar {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.practiceSessions = {};
        this.sportId = window.selectedSportId || null;
        this.tooltip = null;
        this.init();
    }

    async init() {
        this.createTooltip();
        await this.fetchPracticeSessions();
        this.render();
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Remove old listeners if they exist
        if (this.container) {
            const calendarDays = this.container.querySelectorAll('.calendar-day:not(.other-month)');

            
            calendarDays.forEach((day, index) => {
                day.addEventListener('mouseenter', (e) => this.handleMouseEnter(e));
                day.addEventListener('mousemove', (e) => this.handleMouseMove(e));
                day.addEventListener('mouseleave', () => this.handleMouseLeave());
            });


        }
    }

    createTooltip() {
        // Create tooltip element
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'calendar-tooltip';
        this.tooltip.id = 'calendarTooltip';
        this.tooltip.style.cssText = `
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
        `;
        document.body.appendChild(this.tooltip);

    }

    async fetchPracticeSessions() {
        const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
        const year = this.currentDate.getFullYear();
        
        try {
            const url = `/uoc-sports/public/api/practice-sessions/calendar.php?month=${month}&year=${year}${this.sportId ? '&sport_id=' + this.sportId : ''}`;

            
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.practiceSessions = result.data;
            } else {

                console.error('Error loading practice sessions:', result.message);
                this.practiceSessions = {};
            }
        } catch (error) {
            console.error('Error fetching practice sessions:', error);
            this.practiceSessions = {};
        }
    }

    formatDate(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    formatTime(timeString) {
        // Convert 24-hour time to 12-hour format
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    showTooltip(event, sessions) {


        
        if (!sessions || sessions.length === 0) {

            return;
        }

        if (!this.tooltip) {
            console.error('Tooltip element does not exist!');
            return;
        }

        // Header with session count
        let tooltipContent = `
            <div style="font-weight: 700; color: #2b0c4d; margin-bottom: 8px; border-bottom: 2px solid #a855f7; padding-bottom: 6px; font-size: 0.9rem;">
                Practice Sessions (${sessions.length})
            </div>
        `;
        
        sessions.forEach((session, index) => {

            
            // Add separator between sessions
            if (index > 0) {
                tooltipContent += '<div style="border-top: 1px dashed #d1d5db; margin: 10px 0;"></div>';
            }
            
            tooltipContent += `
                <div style="margin-bottom: 6px; padding: 6px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #a855f7;">
                    <div style="color: #6b1fa0; font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;">
                        <i class="fas fa-clock" style="margin-right: 6px; color: #a855f7;"></i>
                        ${this.formatTime(session.start_time)} - ${this.formatTime(session.end_time)}
                    </div>
                    <div style="color: #4b5563; margin-bottom: 3px; font-size: 0.8rem;">
                        <i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #10b981; width: 14px;"></i>
                        <strong>Location:</strong> ${session.location || 'TBD'}
                    </div>
                    ${session.facility ? `
                        <div style="color: #6b7280; margin-bottom: 3px; font-size: 0.75rem;">
                            <i class="fas fa-building" style="margin-right: 6px; color: #3b82f6; width: 14px;"></i>
                            <strong>Facility:</strong> ${session.facility}
                        </div>
                    ` : ''}
                    ${session.sport_name ? `
                        <div style="color: #6b7280; font-size: 0.75rem; margin-top: 3px;">
                            <i class="fas fa-trophy" style="margin-right: 6px; color: #f59e0b; width: 14px;"></i>
                            <strong>Sport:</strong> ${session.sport_name}
                        </div>
                    ` : ''}
                    ${session.notes ? `
                        <div style="color: #6b7280; font-size: 0.7rem; margin-top: 4px; padding-top: 4px; border-top: 1px solid #e5e7eb; font-style: italic;">
                            <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                            ${session.notes}
                        </div>
                    ` : ''}
                </div>
            `;
        });

        this.tooltip.innerHTML = tooltipContent;
        this.tooltip.style.opacity = '1';
        this.tooltip.style.visibility = 'visible';
        this.positionTooltip(event);
    }


    hideTooltip() {
        if (this.tooltip) {
            this.tooltip.style.opacity = '0';
            this.tooltip.style.visibility = 'hidden';

        }
    }

    positionTooltip(event) {
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const padding = 10;
        
        let left = event.clientX + padding;
        let top = event.clientY + padding;
        
        // Adjust if tooltip goes off-screen
        if (left + tooltipRect.width > window.innerWidth) {
            left = event.clientX - tooltipRect.width - padding;
        }
        
        if (top + tooltipRect.height > window.innerHeight) {
            top = event.clientY - tooltipRect.height - padding;
        }
        
        this.tooltip.style.left = `${left}px`;
        this.tooltip.style.top = `${top}px`;
    }

    render() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const prevLastDay = new Date(year, month, 0);
        
        const firstDayIndex = firstDay.getDay();
        const lastDayDate = lastDay.getDate();
        const prevLastDayDate = prevLastDay.getDate();
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        
        let calendarHTML = `
            <div class="calendar-header">
                <h4>${monthNames[month]} ${year}</h4>
                <div class="calendar-nav">
                    <button onclick="calendar.prevMonth()"><i class="fas fa-chevron-left"></i> Prev</button>
                    <button onclick="calendar.nextMonth()">Next <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid">
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
        `;
        
        // Previous month days
        for (let x = firstDayIndex; x > 0; x--) {
            calendarHTML += `<div class="calendar-day other-month">${prevLastDayDate - x + 1}</div>`;
        }
        
        // Current month days
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison
        
        for (let day = 1; day <= lastDayDate; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            const dateKey = this.formatDate(date);
            const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasSessions = this.practiceSessions[dateKey] && this.practiceSessions[dateKey].length > 0;
            
            // Determine if date is past or future

            
            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (hasSessions) {
                classes += ' has-reservation';
                if (isPast) classes += ' past-date';
                if (isFuture) classes += ' future-date';
            }
            
            const sessionsData = hasSessions ? JSON.stringify(this.practiceSessions[dateKey]) : '';
            
            calendarHTML += `<div class="${classes}" 
                data-date="${dateKey}" 
                data-sessions='${sessionsData}'
                >${day}</div>`;
        }
        
        this.container.innerHTML = calendarHTML + '</div>';
        this.attachEventListeners();
    }

    handleMouseEnter(event) {

        const target = event.currentTarget;
        const sessionsData = target.getAttribute('data-sessions');


        
        if (sessionsData && sessionsData !== '') {
            try {
                const sessions = JSON.parse(sessionsData);


                this.showTooltip(event, sessions);
            } catch (e) {
                console.error('Error parsing sessions data:', e);
                console.error('Raw sessions data:', sessionsData);
            }
        } else {

        }
    }

    handleMouseMove(event) {
        if (this.tooltip.style.opacity === '1') {
            this.positionTooltip(event);
        }
    }

    handleMouseLeave() {
        this.hideTooltip();
    }

    async prevMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        await this.fetchPracticeSessions();
        this.render();
    }

    async nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        await this.fetchPracticeSessions();
        this.render();
    }
}

// Make calendar global and initialize when DOM is loaded
let calendar;
document.addEventListener('DOMContentLoaded', function() {

    calendar = new Calendar('calendar');
    window.calendar = calendar; // Make it globally accessible for debugging

});

