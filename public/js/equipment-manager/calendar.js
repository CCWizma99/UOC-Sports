// Calendar functionality
class Calendar {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.reservations = this.getSampleReservations();
        this.init();
    }

    init() {
        this.render();
    }

    getSampleReservations() {
        // Sample reservation data - in production, this would come from API
        const today = new Date();
        const reservations = {};
        
        // Add some sample reservations for various dates
        for (let i = -5; i < 15; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);
            const dateKey = this.formatDate(date);
            
            if (Math.random() > 0.5) { // Random reservations
                reservations[dateKey] = [
                    {
                        time: '09:00 AM - 11:00 AM',
                        equipment: 'Basketball Court A',
                        user: 'Sarah Johnson',
                        status: 'approved'
                    },
                    {
                        time: '02:00 PM - 04:00 PM',
                        equipment: 'Tennis Rackets (x4)',
                        user: 'Mike Chen',
                        status: 'pending'
                    }
                ];
            }
        }
        
        return reservations;
    }

    formatDate(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
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
        for (let day = 1; day <= lastDayDate; day++) {
            const date = new Date(year, month, day);
            const dateKey = this.formatDate(date);
            const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasReservation = this.reservations[dateKey] && this.reservations[dateKey].length > 0;
            
            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (hasReservation) classes += ' has-reservation';
            
            calendarHTML += `<div class="${classes}" onclick="calendar.showReservations('${dateKey}')">${day}</div>`;
        }
        
        this.container.innerHTML = calendarHTML + '</div>';
    }

    prevMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        this.render();
    }

    nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        this.render();
    }

    showReservations(dateKey) {
        const modal = document.getElementById('reservationModal');
        const modalReservations = document.getElementById('modalReservations');
        const selectedDate = document.getElementById('selectedDate');
        
        const date = new Date(dateKey);
        selectedDate.textContent = date.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        const reservations = this.reservations[dateKey] || [];
        
        if (reservations.length === 0) {
            modalReservations.innerHTML = '<p style="text-align: center; color: #6b7280;">No reservations for this date.</p>';
        } else {
            modalReservations.innerHTML = reservations.map(res => `
                <div class="reservation-item">
                    <div class="reservation-header">
                        <span class="reservation-time">${res.time}</span>
                        <span class="status-badge ${res.status}">${res.status}</span>
                    </div>
                    <p class="reservation-equipment">${res.equipment}</p>
                    <p class="reservation-user">User: ${res.user}</p>
                </div>
            `).join('');
        }
        
        modal.style.display = 'block';
    }
}

// Initialize calendar when DOM is loaded
let calendar;
document.addEventListener('DOMContentLoaded', function() {
    calendar = new Calendar('calendar');
    
    // Modal close functionality
    const modal = document.getElementById('reservationModal');
    const closeBtn = document.querySelector('.close');
    
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
});
