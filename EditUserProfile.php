<?php
session_start();
include("database.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if not logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

$original_username = $_SESSION['username']; // Store the original username

// Fetch user data
$query = "SELECT * FROM registration WHERE username='" . mysqli_real_escape_string($conn, $original_username) . "'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $identityno = mysqli_real_escape_string($conn, $_POST['identityno']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneno = mysqli_real_escape_string($conn, $_POST['phoneno']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);

    // Update query with original username in WHERE clause
    $updateQuery = "UPDATE registration SET 
                    username='$new_username', 
                    fullname='$fullname', 
                    password='$password', 
                    identityno='$identityno', 
                    email='$email', 
                    phoneno='$phoneno', 
                    position='$position' 
                    WHERE username='$original_username'";
    
    if (mysqli_query($conn, $updateQuery)) {
        // Update the session username to the new username
        $_SESSION['username'] = $new_username;
        echo "<script>window.location.href='UserProfile.php';</script>";
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style/EditUserProfile.css">
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
        <div class="content">
            <form id="EditProfileUser" method="post" action="">
                <div class="form-section">
                    <h2>Edit Profile</h2>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= $user['username'] ?>" required>

                    <label for="fullname">Fullname</label>
                    <input type="text" id="fullname" name="fullname" value="<?= $user['fullname'] ?>" required>

                    <label for="password">Password</label>
                    <input type="text" id="password" name="password" value="<?= $user['password'] ?>" required>
                
                    <label for="identityno">Staff / Matric No</label>
                    <input type="text" id="identityno" name="identityno" value="<?= $user['identityno'] ?>" required>
                
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>

                    <label for="phoneno">Phone Number</label>
                    <input type="tel" id="phoneno" name="phoneno" value="<?= $user['phoneno'] ?>" required>

                    <label for="position">Position</label>
                    <select id="position" class="position-select" name="position" required>
                        <option value="Academic Staff" <?= $user['position'] == 'Academic Staff' ? 'selected' : '' ?>>Academic Staff</option>
                        <option value="Admin Staff" <?= $user['position'] == 'Admin Staff' ? 'selected' : '' ?>>Admin Staff</option>
                        <option value="UMK Student" <?= $user['position'] == 'UMK Student' ? 'selected' : '' ?>>UMK Student</option>
                    </select>

                    <div class="form-section">
                        <button type="submit" class="submit-button">Submit</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>