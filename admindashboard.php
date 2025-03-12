<?php
session_start();
include("database.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Get the number of registered users
$userQuery = "SELECT COUNT(id) AS user_count FROM registration";
$userResult = $conn->query($userQuery);
$userCount = $userResult->fetch_assoc()['user_count'];

// Get the total number of applications
$appQuery = "SELECT COUNT(appid) AS app_count FROM application";
$appResult = $conn->query($appQuery);
$appCount = $appResult->fetch_assoc()['app_count'];

// Get the total number of items
$itemQuery = "SELECT COUNT(no) AS item_count FROM item";
$itemResult = $conn->query($itemQuery);
$itemCount = $itemResult->fetch_assoc()['item_count'];

// Get application statuses for the pie chart
$statusQuery = "
    SELECT 
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved_count,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM application
";
$statusResult = $conn->query($statusQuery);
$statusData = $statusResult->fetch_assoc();
$pendingCount = $statusData['pending_count'];
$approvedCount = $statusData['approved_count'];
$rejectedCount = $statusData['rejected_count'];

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style/admindashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <script src="javascript/admindashboard.js" defer></script>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="header-left">
            <img src="image/LogoFSDK.png" alt="Logo" class="logo">
            <h1>DATATECH INNOVATION HUB </h1>
        </div>
        <div class="header-right">
            <div class="dropdown">
                <button class="dropbtn">Administration</button>
                <div class="dropdown-content">
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="admindashboard.php"><img src="image/icondashboard.png" alt="Dashboard Icon" class="nav-icon">Dashboard</a>
        <a href="adminItem.php"><img src="image/iconitem.png" alt="Item Icon" class="nav-icon">Item</a>
        <a href="adminstatus.php"><img src="image/iconapplication.png" alt="Application Icon" class="nav-icon">Application Status</a>
        <a href="adminuserlist.php"><img src="image/iconuser 2.png" alt="User Icon" class="nav-icon">User</a>
        <a href="admininventory.php"><img src="image/iconinventory.png" alt="Inventory Icon" class="nav-icon">Inventory</a>
        <a href="adminsetting.php"><img src="image/iconsetting.png" alt="Setting Icon" class="nav-icon">Setting</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Admin Dashboard</h2>

        <!-- Boxes to show the counts -->
        <div class="dashboard-boxes">
            <div class="dashboard-box">
                <h3>Users Registered</h3>
                <p><?php echo $userCount; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Total Applications</h3>
                <p><?php echo $appCount; ?></p>
            </div>
            <div class="dashboard-box">
                <h3>Total Items</h3>
                <p><?php echo $itemCount; ?></p>
            </div>
        </div>

        <!-- Pie Chart for Application Status -->
        <div class="chart-container">
            <h3>Status of Applications</h3>
            <canvas id="statusChart"></canvas>
        </div>
        
        <!-- Include the Chart.js library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
        // Wait for the DOM to load before executing the script
        document.addEventListener("DOMContentLoaded", function() {
            // Pass PHP variables to JavaScript for the chart
            var pendingCount = <?php echo $pendingCount; ?>;
            var approvedCount = <?php echo $approvedCount; ?>;
            var rejectedCount = <?php echo $rejectedCount; ?>;

        // Call the function to generate the pie chart with the passed data
        createStatusChart(pendingCount, approvedCount, rejectedCount);
        });
        </script>

<!-- External JavaScript file -->
<script src="admindashboard.js"></script>


</body>
</html>
