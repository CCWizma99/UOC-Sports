// Equipment Calendar functionality
class EquipmentCalendar {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.equipmentReservations = {};
        this.categoryName = window.selectedCategory || null;
        this.sportId = window.selectedSportId || null;
        this.tooltip = null;
        this.init();
    }

    async init() {
        this.createTooltip();
        await this.fetchEquipmentReservations();
        this.render();
        this.attachEventListeners();
    }

    attachEventListeners() {
        // Remove old listeners if they exist
        if (this.container) {
            const calendarDays = this.container.querySelectorAll('.calendar-day:not(.other-month)');
            console.log('Attaching event listeners to', calendarDays.length, 'calendar days');
            
            calendarDays.forEach((day, index) => {
                const hasReservations = day.getAttribute('data-reservations');
                if (hasReservations) {
                    console.log(`Day ${index + 1} has reservations data`);
                }
                
                day.addEventListener('mouseenter', (e) => this.handleMouseEnter(e));
                day.addEventListener('mousemove', (e) => this.handleMouseMove(e));
                day.addEventListener('mouseleave', () => this.handleMouseLeave());
            });
            
            console.log('Event listeners attached successfully');
        }
    }

    createTooltip() {
        // Create tooltip element
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'calendar-tooltip';
        this.tooltip.id = 'equipmentCalendarTooltip';
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
        console.log('Equipment calendar tooltip created and appended to body:', this.tooltip);
    }

    async fetchEquipmentReservations() {
        const month = String(this.currentDate.getMonth() + 1).padStart(2, '0');
        const year = this.currentDate.getFullYear();
        
        try {
            let url = `/uoc-sports/public/api/equipment-requests/calendar.php?month=${month}&year=${year}`;
            
            if (this.categoryName) {
                url += '&category_name=' + encodeURIComponent(this.categoryName);
            }
            if (this.sportId) {
                url += '&sport_id=' + this.sportId;
            }
            
            console.log('Fetching equipment reservations from:', url);
            
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.equipmentReservations = result.data;
                console.log('Equipment reservations loaded successfully:', this.equipmentReservations);
                console.log('Number of dates with reservations:', Object.keys(this.equipmentReservations).length);
            } else {
                console.error('Error loading equipment reservations:', result.message);
                this.equipmentReservations = {};
            }
        } catch (error) {
            console.error('Error fetching equipment reservations:', error);
            this.equipmentReservations = {};
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

    showTooltip(event, reservations) {
        if (!reservations || reservations.length === 0) return;
        
        console.log('Showing tooltip with reservations:', reservations);
        
        let tooltipHTML = `
            <div style="font-weight: 600; color: #6b1fa0; margin-bottom: 10px; font-size: 1rem; border-bottom: 2px solid #a855f7; padding-bottom: 8px;">
                ${reservations.length} Reservation${reservations.length > 1 ? 's' : ''}
            </div>
        `;
        
        reservations.forEach((reservation, index) => {
            const statusClass = reservation.status.toLowerCase();
            const statusColor = {
                'accepted': '#10b981',
                'pending': '#f59e0b',
                'completed': '#6b7280',
                'rejected': '#ef4444'
            }[statusClass] || '#9ca3af';
            
            tooltipHTML += `
                <div style="margin-bottom: ${index < reservations.length - 1 ? '12px' : '0'}; padding-bottom: ${index < reservations.length - 1 ? '12px' : '0'}; border-bottom: ${index < reservations.length - 1 ? '1px solid #e5e7eb' : 'none'};">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong style="color: #1f2937; font-size: 0.9rem;">${reservation.category_name}</strong>
                        <span style="background: ${statusColor}; color: white; padding: 2px 8px; border-radius: 8px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">
                            ${reservation.status}
                        </span>
                    </div>
                    <div style="color: #4b5563; font-size: 0.85rem; line-height: 1.6;">
                        <div style="display: flex; align-items: center; margin-bottom: 4px;">
                            <i class="fas fa-clock" style="width: 16px; color: #6b1fa0; margin-right: 6px;"></i>
                            <span>${this.formatTime(reservation.start_time)} - ${this.formatTime(reservation.end_time)}</span>
                        </div>
                        ${reservation.sport_name ? `
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <i class="fas fa-trophy" style="width: 16px; color: #6b1fa0; margin-right: 6px;"></i>
                                <span>${reservation.sport_name}</span>
                            </div>
                        ` : ''}
                        ${reservation.location ? `
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <i class="fas fa-location-dot" style="width: 16px; color: #6b1fa0; margin-right: 6px;"></i>
                                <span>${reservation.location}</span>
                            </div>
                        ` : ''}
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-user" style="width: 16px; color: #6b1fa0; margin-right: 6px;"></i>
                            <span>${reservation.student_name}</span>
                        </div>
                        ${reservation.equipment_items ? `
                            <div style="display: flex; align-items: start; margin-top: 4px;">
                                <i class="fas fa-box" style="width: 16px; color: #6b1fa0; margin-right: 6px; margin-top: 2px;"></i>
                                <span style="font-size: 0.8rem; color: #6b7280;">${reservation.equipment_items}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        this.tooltip.innerHTML = tooltipHTML;
        this.positionTooltip(event);
        this.tooltip.style.opacity = '1';
    }

    hideTooltip() {
        if (this.tooltip) {
            this.tooltip.style.opacity = '0';
        }
    }

    positionTooltip(event) {
        const tooltipRect = this.tooltip.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        let left = event.clientX + 15;
        let top = event.clientY + 15;
        
        // Adjust if tooltip goes off right edge
        if (left + tooltipRect.width > viewportWidth - 20) {
            left = event.clientX - tooltipRect.width - 15;
        }
        
        // Adjust if tooltip goes off bottom edge
        if (top + tooltipRect.height > viewportHeight - 20) {
            top = event.clientY - tooltipRect.height - 15;
        }
        
        // Ensure tooltip doesn't go off left edge
        if (left < 20) {
            left = 20;
        }
        
        // Ensure tooltip doesn't go off top edge
        if (top < 20) {
            top = 20;
        }
        
        this.tooltip.style.left = left + 'px';
        this.tooltip.style.top = top + 'px';
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
                    <button onclick="equipmentCalendar.prevMonth()"><i class="fas fa-chevron-left"></i> Prev</button>
                    <button onclick="equipmentCalendar.nextMonth()">Next <i class="fas fa-chevron-right"></i></button>
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
            const day = prevLastDayDate - x + 1;
            calendarHTML += `<div class="calendar-day other-month">${day}</div>`;
        }
        
        // Current month days
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison
        
        for (let day = 1; day <= lastDayDate; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            const dateKey = this.formatDate(date);
            const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasReservations = this.equipmentReservations[dateKey] && this.equipmentReservations[dateKey].length > 0;
            
            // Determine if date is past or future
            const isPast = date < today;
            const isFuture = date > today;
            
            if (hasReservations) {
                console.log(`Date ${dateKey} has ${this.equipmentReservations[dateKey].length} reservations`);
            }
            
            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (hasReservations) {
                classes += ' has-reservation';
                if (isPast) classes += ' past-date';
                if (isFuture) classes += ' future-date';
            }
            
            const reservationsData = hasReservations ? JSON.stringify(this.equipmentReservations[dateKey]) : '';
            
            calendarHTML += `<div class="${classes}" 
                data-date="${dateKey}" 
                data-reservations='${reservationsData}'
                >${day}</div>`;
        }
        
        this.container.innerHTML = calendarHTML + '</div>';
        this.attachEventListeners();
    }

    handleMouseEnter(event) {
        console.log('Mouse entered calendar day');
        const target = event.currentTarget;
        const reservationsData = target.getAttribute('data-reservations');
        console.log('Reservations data attribute:', reservationsData);
        console.log('Has reservations:', reservationsData && reservationsData !== '');
        
        if (reservationsData && reservationsData !== '') {
            try {
                const reservations = JSON.parse(reservationsData);
                console.log('Parsed reservations:', reservations);
                console.log('Number of reservations:', reservations.length);
                this.showTooltip(event, reservations);
            } catch (e) {
                console.error('Error parsing reservations data:', e);
                console.error('Raw reservations data:', reservationsData);
            }
        } else {
            console.log('No reservations data for this date');
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
        await this.fetchEquipmentReservations();
        this.render();
    }

    async nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        await this.fetchEquipmentReservations();
        this.render();
    }
}

// Make equipment calendar global and initialize when DOM is loaded
let equipmentCalendar;
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing equipment calendar...');
    equipmentCalendar = new EquipmentCalendar('equipmentCalendar');
    window.equipmentCalendar = equipmentCalendar; // Make it globally accessible for debugging
    console.log('Equipment calendar initialized:', equipmentCalendar);
});
