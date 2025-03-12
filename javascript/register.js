// Function to show the password toggle button
function showTogglePassword() {
    const toggleButton = document.querySelector('.toggle-password');
    toggleButton.style.display = 'block';
}

// Function to hide the password toggle button
function hideTogglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.querySelector('.toggle-password');

    if (passwordField.value === '') {
        toggleButton.style.display = 'none';
    }
}

// Function to toggle the password visibility
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.querySelector('.toggle-password');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.textContent = '🔐'; // Icon to indicate hiding the password
    } else {
        passwordField.type = 'password';
        toggleButton.textContent = '👁️'; // Icon to indicate showing the password
    }
}

// Function to show the position label when an option is selected
function showPositionLabel() {
    const positionLabel = document.getElementById('position-label');
    positionLabel.style.display = 'block';
}

// Add event listeners to password field
const passwordField = document.getElementById('password');
passwordField.addEventListener('focus', showTogglePassword);
passwordField.addEventListener('blur', hideTogglePassword);

// Add event listener to the position select field
const positionSelect = document.getElementById('position');
positionSelect.addEventListener('change', showPositionLabel);
