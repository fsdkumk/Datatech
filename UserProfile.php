<?php
session_start();
include("database.php"); // Database connection file

// Redirect if not logged in or not a staff/student
if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

// Fetch user data from the database
$username = $_SESSION['username'];
$query = "SELECT * FROM registration WHERE username='$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style/UserProfile.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="title">DATATECH <br> INNOVATION HUB</div>
                <div class="login"><a href="Logout.php">Log Out</a></div>
            </div>
            <img src="image/header.png" alt="Header Image">
        </div>

        <!-- Navigation Bar -->
        <nav class="navbar">
            <ul>
                <li><a href="UserItemlist.php">Item List</a></li>
                <li><a href="UserApplicationform.php">Application Form</a></li>
                <li><a href="UserStatus.php">Application Status</a></li>
                <li><a href="UserAbout.php">About</a></li>
                <li><a href="UserProfile.php">Profile</a></li>
            </ul>
        </nav>

        <!-- User Profile Section -->
        <div class="profile-box">
            <h2>User Profile</h2>
            <div class="profile-info">
                <p><strong>Username:</strong> <?= $user['username'] ?></p>
                <p><strong>Fullname:</strong> <?= $user['fullname'] ?></p>
                <p><strong>Staff/Matric No:</strong> <?= $user['identityno'] ?></p>
                <p><strong>Email:</strong> <?= $user['email'] ?></p>
                <p><strong>Phone Number:</strong> <?= $user['phoneno'] ?></p>
                <p><strong>Position:</strong> <?= $user['position'] ?></p>
            </div>
            <div class="buttons">
                <a href="EditUserProfile.php" class="edit-btn">Edit Profile</a>
                <a href="#" class="delete-btn">Delete Profile</a>
            </div>
        </div>

        <!-- AJAX Script for Deleting Profile -->
        <script>
        document.querySelector('.delete-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const confirmDelete = confirm("Are you sure you want to delete your account?");
            if (confirmDelete) {
                // Create an AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "DeleteUserProfile.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // If the server returns success, redirect to the login page
                        if (xhr.responseText === "success") {
                            alert("Your account has been deleted.");
                            window.location.href = "UserLogin.php";
                        } else {
                            alert("Error deleting account: " + xhr.responseText);
                        }
                    }
                };

                // Send the delete request with the necessary data
                xhr.send("action=deleteAccount");
            }
        });
        </script>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
