const UI = {
    // Show a toast notification
    showToast: function(message, type = 'info', title = '') {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        if (!title) {
            switch(type) {
                case 'success': title = 'Success'; break;
                case 'error': title = 'Error'; break;
                case 'info': title = 'Information'; break;
                case 'warning': title = 'Warning'; break;
                default: title = 'Note';
            }
        }

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
        setTimeout(() => toast.classList.add('show'), 10);

        const timer = setTimeout(() => this.removeToast(toast), 5000);
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timer);
            this.removeToast(toast);
        });
    },

    removeToast: function(toast) {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    },

    // Show a custom confirmation modal
    // Returns a Promise for easier async/await usage
    confirm: function(message, onConfirm = null, onCancel = null, isDanger = false) {
        return new Promise((resolve) => {
            // Ensure namespaced modal base structure exists
            let overlay = document.querySelector('.ui-confirm-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'ui-confirm-overlay';
                overlay.id = 'globalConfirmModal';
                overlay.innerHTML = `
                    <div class="ui-confirm-content">
                        <div class="ui-confirm-icon"><i class="fa-solid fa-circle-question"></i></div>
                        <h3 class="ui-confirm-title">Confirm Action</h3>
                        <p class="ui-confirm-message" id="confirmModalMessage"></p>
                        <div class="ui-confirm-actions">
                            <button class="ui-confirm-btn ui-confirm-btn-cancel" id="confirmBtnCancel">Cancel</button>
                            <button class="ui-confirm-btn ui-confirm-btn-confirm" id="confirmBtnConfirm">Confirm</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(overlay);
            }

            const messageEl = overlay.querySelector('#confirmModalMessage');
            const confirmBtn = overlay.querySelector('#confirmBtnConfirm');
            const cancelBtn = overlay.querySelector('#confirmBtnCancel');

            messageEl.textContent = message;

            // Reset classes
            confirmBtn.className = 'ui-confirm-btn ui-confirm-btn-confirm';
            
            // Apply danger theme if requested or inferred from message
            if (isDanger || message.toLowerCase().includes('delete') || message.toLowerCase().includes('remove')) {
                confirmBtn.classList.add('danger');
            }

            const cleanup = () => {
                overlay.classList.remove('show');
                // Remove listeners to prevent memory leaks by using cloneNode
                const newConfirmBtn = confirmBtn.cloneNode(true);
                const newCancelBtn = cancelBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
            };

            const handleConfirm = () => {
                cleanup();
                if (onConfirm) onConfirm();
                resolve(true);
            };

            const handleCancel = () => {
                cleanup();
                if (onCancel) onCancel();
                resolve(false);
            };

            overlay.querySelector('#confirmBtnConfirm').addEventListener('click', handleConfirm);
            overlay.querySelector('#confirmBtnCancel').addEventListener('click', handleCancel);
            
            // Show the modal
            overlay.classList.add('show');
        });
    },

    // Helper for inline HTML confirmation
    handleConfirm: function(event, message, actionUrl, isDanger = false) {
        if (event) event.preventDefault();
        this.confirm(message, () => {
            window.location.href = actionUrl;
        }, null, isDanger);
        return false;
    }
};
