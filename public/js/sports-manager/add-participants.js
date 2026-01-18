// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName + 'Tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');

    // Clear the other input method
    if (tabName === 'upload') {
        clearSelectedStudents();
    } else {
        clearUploadedFile();
    }
}

// Multi-select dropdown functionality
const multiSelectDisplay = document.getElementById('multiSelectDisplay');
const multiSelectDropdown = document.getElementById('multiSelectDropdown');
const selectedItemsContainer = document.getElementById('selectedItems');
const placeholderText = document.getElementById('placeholderText');
const studentSearchInput = document.getElementById('studentSearch');
const selectedStudentsInput = document.getElementById('selectedStudents');

let selectedStudents = [];

// Sample student data - replace with actual data from backend
const students = [
    { id: 'S001', name: 'John Doe', faculty: 'Engineering' },
    { id: 'S002', name: 'Jane Smith', faculty: 'Science' },
    { id: 'S003', name: 'Bob Johnson', faculty: 'Arts' },
    { id: 'S004', name: 'Alice Williams', faculty: 'Medicine' },
    { id: 'S005', name: 'Charlie Brown', faculty: 'Engineering' },
    { id: 'S006', name: 'Diana Prince', faculty: 'Law' },
    { id: 'S007', name: 'Ethan Hunt', faculty: 'Business' },
    { id: 'S008', name: 'Fiona Green', faculty: 'Science' },
];

// Toggle dropdown
multiSelectDisplay.addEventListener('click', (e) => {
    if (!e.target.classList.contains('remove-btn')) {
        multiSelectDropdown.classList.toggle('active');
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.multi-select-container')) {
        multiSelectDropdown.classList.remove('active');
    }
});

// Render student list
function renderStudentList(filterText = '') {
    const studentList = document.getElementById('studentList');
    studentList.innerHTML = '';

    const filteredStudents = students.filter(student => 
        student.name.toLowerCase().includes(filterText.toLowerCase()) ||
        student.id.toLowerCase().includes(filterText.toLowerCase())
    );

    filteredStudents.forEach(student => {
        const isSelected = selectedStudents.some(s => s.id === student.id);
        
        const studentItem = document.createElement('div');
        studentItem.className = `student-item ${isSelected ? 'selected' : ''}`;
        studentItem.innerHTML = `
            <input type="checkbox" class="student-checkbox" ${isSelected ? 'checked' : ''} 
                   data-student-id="${student.id}">
            <div class="student-info">
                <div class="student-name">${student.name}</div>
                <div class="student-id">${student.id} - ${student.faculty}</div>
            </div>
        `;

        studentItem.addEventListener('click', (e) => {
            if (e.target.type !== 'checkbox') {
                const checkbox = studentItem.querySelector('.student-checkbox');
                checkbox.checked = !checkbox.checked;
            }
            toggleStudent(student);
        });

        studentList.appendChild(studentItem);
    });
}

// Toggle student selection
function toggleStudent(student) {
    const index = selectedStudents.findIndex(s => s.id === student.id);
    
    if (index > -1) {
        selectedStudents.splice(index, 1);
    } else {
        selectedStudents.push(student);
    }
    
    updateSelectedDisplay();
    renderStudentList(studentSearchInput.value);
}

// Update selected students display
function updateSelectedDisplay() {
    selectedItemsContainer.innerHTML = '';
    
    if (selectedStudents.length === 0) {
        placeholderText.style.display = 'block';
    } else {
        placeholderText.style.display = 'none';
        
        selectedStudents.forEach(student => {
            const selectedItem = document.createElement('div');
            selectedItem.className = 'selected-item';
            selectedItem.innerHTML = `
                ${student.name} (${student.id})
                <span class="remove-btn" data-student-id="${student.id}">&times;</span>
            `;
            
            selectedItem.querySelector('.remove-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                toggleStudent(student);
            });
            
            selectedItemsContainer.appendChild(selectedItem);
        });
    }
    
    // Update hidden input with selected student IDs
    selectedStudentsInput.value = selectedStudents.map(s => s.id).join(',');
}

// Search functionality
studentSearchInput.addEventListener('input', (e) => {
    renderStudentList(e.target.value);
});

// Clear selected students
function clearSelectedStudents() {
    selectedStudents = [];
    updateSelectedDisplay();
    renderStudentList();
}

// File upload functionality
const fileUploadArea = document.getElementById('fileUploadArea');
const fileInput = document.getElementById('participantsFile');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const removeFileBtn = document.getElementById('removeFileBtn');

// Click to upload
fileUploadArea.addEventListener('click', () => {
    fileInput.click();
});

// Drag and drop
fileUploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileUploadArea.classList.add('drag-over');
});

fileUploadArea.addEventListener('dragleave', () => {
    fileUploadArea.classList.remove('drag-over');
});

fileUploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    fileUploadArea.classList.remove('drag-over');
    
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    }
});

// File selection
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

// Handle file selection
function handleFileSelect(file) {
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            alert('Please upload a valid file (JPG, PNG, or PDF)');
            fileInput.value = '';
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            fileInput.value = '';
            return;
        }

        fileName.textContent = file.name;
        fileInfo.classList.add('active');
    }
}

// Remove file
removeFileBtn.addEventListener('click', () => {
    clearUploadedFile();
});

function clearUploadedFile() {
    fileInput.value = '';
    fileInfo.classList.remove('active');
}

// Form submission
document.getElementById('addParticipantsForm').addEventListener('submit', (e) => {
    const uploadTabActive = document.getElementById('uploadTab').classList.contains('active');
    const selectTabActive = document.getElementById('selectTab').classList.contains('active');
    
    if (uploadTabActive) {
        // Validate file upload
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please upload a participant list file');
            return;
        }
    } else if (selectTabActive) {
        // Validate student selection
        if (selectedStudents.length === 0) {
            e.preventDefault();
            alert('Please select at least one participant');
            return;
        }
    }
    
    // Form will submit naturally with either file or selected student IDs
});

// Initialize
renderStudentList();

// Highlight active page in subnav
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = document.getElementById("sub-competitions");
    if (currentPage) currentPage.classList.add("active");
});
