const UI = {
    showToast: function(message, type = 'info', title = '') {
        // Ensure container exists
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Title defaults based on type
        if (!title) {
            switch(type) {
                case 'success': title = 'Success'; break;
                case 'error': title = 'Error'; break;
                case 'info': title = 'Information'; break;
                case 'warning': title = 'Warning'; break;
            }
        }

        // Icon based on type
        let icon = 'info-circle';
        switch(type) {
            case 'success': icon = 'check-circle'; break;
            case 'error': icon = 'circle-exclamation'; break;
            case 'warning': icon = 'triangle-exclamation'; break;
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fa-solid fa-${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <div class="toast-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        `;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);

        // Auto remove after 5s
        const timer = setTimeout(() => this.removeToast(toast), 5000);

        // Manual close
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timer);
            this.removeToast(toast);
        });
    },

    removeToast: function(toast) {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }
};
