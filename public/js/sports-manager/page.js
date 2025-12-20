function toggleAddParticipantsForm() {
    const form = document.getElementById('addParticipantsForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        // Scroll to the form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        form.style.display = 'none';
    }
}
