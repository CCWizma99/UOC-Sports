function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    document.querySelector(`.tab-btn[onclick*="${tabName}"]`).classList.add('active');
    document.getElementById(`${tabName}Tab`).classList.add('active');
}

const editModal = document.getElementById('editModal');

function openEditModal() {
    document.getElementById('editName').value = document.getElementById('profileName').textContent;
    document.getElementById('editEmail').value = document.getElementById('email').textContent;
    document.getElementById('editPhone').value = document.getElementById('phone').textContent;
    
    editModal.classList.add('active');
}

function closeEditModal() {
    editModal.classList.remove('active');
}

function saveProfile(event) {
    event.preventDefault(); 
    
    const newName = document.getElementById('editName').value;
    const newEmail = document.getElementById('editEmail').value;
    const newPhone = document.getElementById('editPhone').value;

    document.getElementById('profileName').textContent = newName;
    document.getElementById('email').textContent = newEmail;
    document.getElementById('phone').textContent = newPhone;

    console.log("SENDING DATA TO SERVER FOR DATABASE UPDATE...");
    
    closeEditModal(); 
}

document.getElementById('photoInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    switchTab('teams'); 
});