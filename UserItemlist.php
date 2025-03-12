<?php
session_start();
include("database.php");

// Make sure it correct role staff/student, if not it will back at login
if(!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student"){
    header("location:UserLogin.php");
    exit();
}

// Handle search query
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Fetch items from the database
$sql = "SELECT itemtype, image, quantityleft FROM item WHERE itemtype LIKE '%$searchQuery%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Item List</title>
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <link rel="stylesheet" type="text/css" href="style/UserItemlist.css">
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
            <!-- Search Form -->
            <div class="search-form">
                <form method="GET" action="UserItemlist.php">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search items...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="item-list">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="item-box">
                            <img src="image/<?php echo $row['image']; ?>" alt="<?php echo $row['itemtype']; ?>" class="item-image">
                            <div class="item-info">
                                <h2 class="item-type"><?php echo $row['itemtype']; ?></h2>
                                <p class="quantityleft">Quantity left: <strong><?php echo $row['quantityleft']; ?></strong></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No items found matching your search.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
    </div>

    <script src="scripts/UserItemlist.js"></script>

    <?php $conn->close(); ?>
</body>
</html>