// JavaScript for handling button clicks
function handleRightClick() {
    // Show submit button when 'Right' is clicked
    document.getElementById("submitBtn").classList.remove("hidden");
    document.getElementById("itemInfoSection").classList.add("hidden");
}

function handleRightClick() {
    $.post('ReturnUserApplication.php', { ajax: 'updateStatus', status: 'Completed' }, function () {
        alert("Thank you for your responsibility!");
        window.location.href = 'UserStatus.php';
    });
}

function handleWrongClick() {
    // Show item information section when 'Wrong' is clicked
    document.getElementById("itemInfoSection").classList.remove("hidden");
    document.getElementById("submitBtn").classList.add("hidden");
}


$(document).ready(function() {
    $('#applicationForm').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            url: 'ReturnUserApplication.php', 
            type: 'POST',
            data: new FormData(this), // Use FormData to handle file uploads
            contentType: false,
            processData: false,
            success: function(response) {
                try {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert("Thank You for your concern!"); // Show popup message
                        window.location.href = 'UserStatus.php'; // Redirect to UserStatus.php
                    } else {
                        alert("Error: " + res.message); // Handle error message
                    }
                } catch (error) {
                    console.error("Parsing error:", error);
                    alert("An unexpected response was received.");
                }
            },
            error: function() {
                alert("An error occurred. Please try again."); // Handle AJAX error
            }
        });
    });
});