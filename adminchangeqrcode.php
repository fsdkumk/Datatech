<?php
session_start();
include("database.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Handle file upload to update the QR code
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['new_qrcode'])) {
    $file = $_FILES['new_qrcode'];
    $message = '';

    // Check for errors
    if ($file['error'] == 0) {
        // Validate file type (e.g., only allow image files)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($file['type'], $allowedTypes)) {
            // Validate file size (max 2MB)
            if ($file['size'] <= 2 * 1024 * 1024) {
                // Set the upload directory and ensure it exists
                $uploadDir = 'uploads/qrcodes/';
                
                // Check if the directory exists, if not create it
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create directory with proper permissions
                }

                // Get the current QR code image from the database
                $sql = "SELECT qrimage FROM qrcode WHERE qrid = 1"; 
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentImagePath = $row['qrimage'];
                    
                    // Delete the old image from the server if it exists
                    if (file_exists($currentImagePath)) {
                        unlink($currentImagePath);
                    }
                }

                // Set the new image file path
                $newImagePath = $uploadDir . basename($file['name']);
                
                // Move the uploaded file to the designated folder
                if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
                    $updateSql = "UPDATE qrcode SET qrimage = ? WHERE qrid = 1"; 
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("s", $newImagePath);
                    if ($updateStmt->execute()) {
                        
                        header("Location: adminsetting.php");
                        exit();
                    } else {
                        $message = "Error updating QR Code in the database.";
                    }
                } else {
                    $message = "Error uploading the file.";
                }
            } else {
                $message = "File size exceeds the 2MB limit.";
            }
        } else {
            $message = "Only image files (JPEG, PNG, GIF) are allowed.";
        }
    } else {
        $message = "Error with file upload.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change QR Code</title>
    <link rel="stylesheet" type="text/css" href="style/adminsetting.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="header-left">
            <img src="image/LogoFSDK.png" alt="Logo" class="logo">
            <h1>DATATECH INNOVATION HUB</h1>
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
        <div class="dashboard-box">
            <h3>Change The QR Code</h3>
            <?php if (isset($message)): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form action="adminchangeqrcode.php" method="POST" enctype="multipart/form-data">
                <label for="new_qrcode">Select QR Code Image:</label>
                <input type="file" name="new_qrcode" id="new_qrcode" required>
                <button type="submit">Upload QR Code</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php

$conn->close();
?>