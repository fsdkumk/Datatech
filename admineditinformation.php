<?php
session_start();
include("database.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Retrieve admin information
$adminId = 1; // Assuming ID 1 is for admin
$sql = "SELECT * FROM registration WHERE id = $adminId";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Handle form submission to update admin info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phoneno = $_POST['phoneno'];
    $role = $_POST['position'];

    $updateSql = "UPDATE registration SET 
                  fullname = ?, 
                  password = ?, 
                  email = ?, 
                  phoneno = ?, 
                  position = ? 
                  WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssssi", $fullname, $password, $email, $phoneno, $role, $adminId);

    if ($stmt->execute()) {
        // Redirect back to adminsetting.php
        header("Location: adminsetting.php");
        exit();
    } else {
        echo "<script>alert('Error updating information.');</script>";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style/admineditinformation.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
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

<div class="content">
    <div class="form-container">
        <h2>Edit Admin Information</h2>
        <form method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($row['fullname']); ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($row['password']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>

            <label for="phoneno">Phone Number:</label>
            <input type="text" id="phoneno" name="phoneno" value="<?php echo htmlspecialchars($row['phoneno']); ?>" required>

            <label for="position">Position:</label>
            <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($row['position']); ?>" readonly>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>
</body>
</html>
