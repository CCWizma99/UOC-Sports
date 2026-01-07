/**
 * Admin Notification System
 * Shows centered popup notifications with different types
 * 
 * Usage: showNotification(message, type, duration)
 * Types: 'success', 'error', 'info', 'warning'
 */

function showNotification(message, type = 'info', duration = 5000) {
  // Remove any existing notification
  const existing = document.querySelector('.notification-overlay');
  if (existing) {
    existing.remove();
  }

  // Icon mapping
  const icons = {
    success: '✓',
    error: '✕',
    info: 'ℹ',
    warning: '⚠'
  };

  // Create overlay
  const overlay = document.createElement('div');
  overlay.className = 'notification-overlay';

  // Create notification box
  const box = document.createElement('div');
  box.className = `notification-box ${type}`;

  // Create icon
  const icon = document.createElement('span');
  icon.className = 'notification-icon';
  icon.textContent = icons[type] || icons.info;

  // Create message
  const messageEl = document.createElement('p');
  messageEl.className = 'notification-message';
  messageEl.textContent = message;

  // Assemble
  box.appendChild(icon);
  box.appendChild(messageEl);
  overlay.appendChild(box);
  document.body.appendChild(overlay);

  // Auto dismiss
  setTimeout(() => {
    closeNotification();
  }, duration);

  // Click to dismiss
  overlay.addEventListener('click', closeNotification);
}

function closeNotification() {
  const overlay = document.querySelector('.notification-overlay');
  if (overlay) {
    overlay.classList.add('fade-out');
    const box = overlay.querySelector('.notification-box');
    if (box) {
      box.classList.add('fade-out');
    }

    setTimeout(() => {
      overlay.remove();
    }, 300);
  }
}
