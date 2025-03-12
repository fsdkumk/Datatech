<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>
    <link rel="stylesheet" href="style/UserAbout.css">
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

        <!-- Content Section -->
        <div class="content">
            <h2>About Datatech Innovation Hub System</h2>
            <p>This system is developed to facilitate the monitoring of internal assets at the University of Malaysia Kelantan. 
                With this system in place, the university can enhance the overall management of its internal assets more efficiently and effectively.</p>

            <p>The system is the result of the dedicated efforts and expertise of the Computer and Informatics Center of the University of Malaysia Kelantan. 
                Developed in-house, this system has been specifically designed to tailor to the university's asset monitoring needs and processes. 
                The decision to develop this system internally reflects the university's proactive commitment to improving their asset management performance.</p>

            <p>One of the main advantages of this system is its ability to integrate various information related to the university's internal assets into a single, 
                user-friendly platform. With this system, asset management staff can easily access data related to assets, their locations, and status. 
                This facilitates better decision-making regarding asset management and future planning.</p>

            <p>Additionally, the system allows for real-time asset monitoring. This feature enables the university to quickly detect changes in asset status and 
                take appropriate action if there are urgent issues or needs. This helps to reduce the risk of asset loss or damage that could negatively impact 
                the university's operations.</p>

        </div>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
