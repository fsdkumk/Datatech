<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Fetch item details based on ID
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    $query = "SELECT * FROM item WHERE no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if (!$item) {
        echo "Item not found.";
        exit();
    }
}

// Handle the form submission for updating item details
if (isset($_POST['updateItem'])) {
    $itemType = $_POST['itemtype'];
    $quantity = $_POST['quantity'];
    $quantityLeft = $_POST['quantityleft'];
    $itemStore = $_POST['itemstore'];
    $itemPrice = $_POST['itemprice'];
    $itemStatus = $_POST['itemstatus'];

    $imageName = $item['image']; // Keep the existing image if no new image is uploaded
    if (!empty($_FILES['itemimage']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['itemimage']['name']);
        $targetFilePath = $targetDir . $imageName;

        // Validate and upload the image
        if (move_uploaded_file($_FILES['itemimage']['tmp_name'], $targetFilePath)) {
            echo "Image uploaded successfully.";
        } else {
            echo "Failed to upload image.";
        }
    }

    // Update the item in the database
    $updateQuery = "UPDATE item SET itemtype = ?, quantity = ?, quantityleft = ?, itemstore = ?, itemprice = ?, itemstatus = ?, image = ? WHERE no = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("siissssi", $itemType, $quantity, $quantityLeft, $itemStore, $itemPrice, $itemStatus, $imageName, $itemId);

    if ($stmt->execute()) {
        echo "Item updated successfully!";
        header("location: adminItem.php");
        exit();
    } else {
        echo "Error updating item: " . $conn->error;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Item Organizer</title>
    <link rel="stylesheet" type="text/css" href="style/EditadminItem.css">
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
    </div>

    <!-- Main Content -->
    <div class="content">
        <form action="" method="POST" enctype="multipart/form-data">
            <h2>Edit Item</h2>

            <!-- Image Upload -->
            <label for="itemimage">Item Image:</label>
            <?php if (!empty($item['image'])): ?>
                <div>
                    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" style="max-width: 200px; max-height: 200px;">
                </div>
            <?php endif; ?>
            <input type="file" id="itemimage" name="itemimage" accept="image/*"><br>

            <label for="itemtype">Item Type:</label>
            <input type="text" id="itemtype" name="itemtype" value="<?php echo htmlspecialchars($item['itemtype']); ?>" required><br>

            <label for="quantity">Quantity of the Item:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" required><br>

            <label for="quantityleft">Quantity Left:</label>
            <input type="number" id="quantityleft" name="quantityleft" value="<?php echo htmlspecialchars($item['quantityleft']); ?>" required><br>

            <label for="itemstore">Item Store:</label>
            <input type="text" id="itemstore" name="itemstore" value="<?php echo htmlspecialchars($item['itemstore']); ?>" required><br>

            <label for="itemprice">Item Price:</label>
            <input type="text" id="itemprice" name="itemprice" value="<?php echo isset($item['itemprice']) ? htmlspecialchars(number_format((float)$item['itemprice'], 2)) : ''; ?>" required><br>

            <label for="itemstatus">Item Status:</label>
            <select id="itemstatus" name="itemstatus" required>
                <option value="Available" <?php echo $item['itemstatus'] === 'Available' ? 'selected' : ''; ?>>Available</option>
                <option value="Out of stock" <?php echo $item['itemstatus'] === 'Out of stock' ? 'selected' : ''; ?>>Out of stock</option>
            </select><br><br>

            <button type="submit" name="updateItem">Update Item</button>
        </form>
    </div>
</body>
</html>
