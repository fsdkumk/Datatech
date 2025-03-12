// script/UserLogin.js

function showTogglePassword() {
    const toggleButton = document.querySelector('.togglepassword');
    toggleButton.style.display = 'block';
}

function hideTogglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.querySelector('.togglepassword');

    if (passwordField.value === '') {
        toggleButton.style.display = 'none';
    }
}

function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.querySelector('.togglepassword');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.textContent = '👁️'; // lock (hide password)
    } else {
        passwordField.type = 'password';
        toggleButton.textContent = '🔐'; // Eye icon (show password)
    }
}

// Add event listeners on the password field
const passwordField = document.getElementById('password');
passwordField.addEventListener('focus', showTogglePassword);
passwordField.addEventListener('blur', hideTogglePassword);

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');

    form.addEventListener('submit', (event) => {
        // Remove event.preventDefault(); to allow form submission
        // event.preventDefault(); // Comment this out if you want the form to submit normally
        
        const role = document.querySelector('input[name="role"]:checked').value;
        const username = document.querySelector('input[name="username"]').value;
        const password = document.querySelector('input[name="password"]').value;

        // Perform client-side validation if necessary
        if (!username || !password) {
            alert('Please fill in all fields.');
            event.preventDefault(); // Prevent submission if validation fails
            return;
        }

        // If you want to manually handle the redirection without server-side processing, you can do it here:
        if (role === 'Admin') {
            window.location.href = 'AdminDashboard.php';
        } else if (role === 'Staff/Student') {
            window.location.href = 'UserItemlist.php';
        }
    });
});
