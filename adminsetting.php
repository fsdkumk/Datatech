<?php
session_start();
include("database.php");

// Redirect if not logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Fetch admin data using prepared statements
$sql = "SELECT * FROM registration WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the row as an associative array
    $row = $result->fetch_assoc();
} else {
    $row = null; // Handle the case where no data is found
}

// Fetch QR code data using prepared statements
$qrcodeSql = "SELECT * FROM qrcode";
$qrcodeResult = $conn->query($qrcodeSql);

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
                $sql = "SELECT qrimage FROM qrcode WHERE qrid = 1"; // Assuming the existing QR code has an ID of 1
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
                
                if (move_uploaded_file($file['tmp_name'], $newImagePath)) {
                    $updateSql = "UPDATE qrcode SET qrimage = ? WHERE qrid = 1"; 
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("s", $newImagePath);
                    if ($updateStmt->execute()) {
                        $message = "QR Code updated successfully!";
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
    <title>Admin Setting</title>
    <link rel="stylesheet" type="text/css" href="style/adminsetting.css">
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
        
        <div class="dashboard-box">
            <h3>Admin Information
                <button class="edit-btn" onclick="window.location.href='admineditinformation.php'">Edit</button>
            </h3>
            <?php if ($row): ?>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($row['fullname']); ?></p>
                <p><strong>Password:</strong> <?php echo htmlspecialchars($row['password']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($row['phoneno']); ?></p>
                <p><strong>Position:</strong> <?php echo htmlspecialchars($row['position']); ?></p>
            <?php else: ?>
                <p>No data found for Admin ID 1.</p>
            <?php endif; ?>
        </div>

        <!-- QR Code Information -->
        <div class="dashboard-box">
            <h3>QR Code Payment
            <button class="change-btn" onclick="window.location.href='adminchangeqrcode.php'">Change</button>
            </h3>
            <?php if ($qrcodeResult->num_rows > 0): ?>
                <table>
                    <tbody>
                        <p>This QrCode is displayed on the user page</p>
                        <?php while ($qrcodeRow = $qrcodeResult->fetch_assoc()): ?>
                            <tr>
                                <td>
                                <img src="<?php echo htmlspecialchars($qrcodeRow['qrimage']); ?>" alt="QR Code" class="qrcode-image">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No QR Code data found.</p>
            <?php endif; ?>
        </div>
    </div>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
