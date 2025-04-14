// Global variable to store password data
let passwordData = [];

// Function to initialize password data from the page
function initPasswordData() {
    const dataElement = document.getElementById('password-data');
    if (dataElement) {
        try {
            passwordData = JSON.parse(dataElement.getAttribute('data-credentials'));
        } catch (error) {
            console.error('Error parsing password data:', error);
        }
    }
}




// Open update modal with data
function openUpdateModal(id) {
    const data = passwordData.find(item => parseInt(item.id) === id);
    if (data) {
        document.getElementById('updateId').value = data.id;
        document.getElementById('updateWebsite').value = data.website;
        document.getElementById('updateUsername').value = data.username;
        document.getElementById('updatePassword').value = data.password;
        document.getElementById('updateModal').style.display = 'flex';
    } else {
        alert('Error: Cannot find password data');
    }
}

// Open delete modal
function openDeleteModal(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

// Close any modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Toggle password visibility in forms
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.type === 'password' ? 'text' : 'password';
    input.type = type;
    
    const icon = input.nextElementSibling.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

// Toggle password visibility in cards
function toggleViewPassword(id, password) {
    const passwordElement = document.getElementById('pass' + id);
    const eyeIcon = document.getElementById('eye' + id);
    
    if (passwordElement.textContent === '••••••••') {
        passwordElement.textContent = password;
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordElement.textContent = '••••••••';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}

// Initialize data when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initPasswordData();
});
