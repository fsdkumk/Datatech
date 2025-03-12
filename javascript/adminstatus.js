// script/adminstatus.js

// Function to change the dropdown color based on the selected value
function changeDropdownColor(selectElement) {
    let selectedValue = selectElement.value;

    // Reset the color for the dropdown
    selectElement.classList.remove('status-pending', 'status-approved', 'status-rejected');

    // Apply the color based on the selected option
    if (selectedValue === 'Pending') {
        selectElement.classList.add('status-pending');
    } else if (selectedValue === 'Approved') {
        selectElement.classList.add('status-approved');
    } else if (selectedValue === 'Rejected') {
        selectElement.classList.add('status-rejected');
    }
}

// Initialize color when the page loads based on the selected value
document.querySelectorAll('.status-dropdown').forEach(function(selectElement) {
    changeDropdownColor(selectElement);
});

// JavaScript to handle status updates
function updateStatus(appId, newStatus) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "updateStatus.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // Send the application ID and new status to the server
    xhr.send("appid=" + appId + "&status=" + newStatus);
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Update the button text after successful update
            const button = document.getElementById("status-button-" + appId);
            button.textContent = newStatus;

            // Update the button color based on status
            if (newStatus === 'Approved') {
                button.classList.remove("status-pending", "status-rejected");
                button.classList.add("status-approved");
            } else if (newStatus === 'Rejected') {
                button.classList.remove("status-pending", "status-approved");
                button.classList.add("status-rejected");
            }
        }
    };
}
