<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Handle form submission
if (isset($_POST["submit"])) {
    $itemtype = $_POST["itemtype"];
    $quantity = $_POST["quantity"];
    $quantityleft = $_POST["quantity"]; 
    $itemstore = $_POST["itemstore"];
    $itemprice = $_POST["itemprice"];
    $itemstatus = $_POST["itemstatus"];
    
    if ($_FILES["image"]["error"] === 4) {
        echo "<script> alert('Image Does Not Exist');</script>";
    } else {
        $fileName = $_FILES["image"]["name"]; // Correct field for the image file name
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        
        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script>alert('Invalid Image Extension');</script>";
        } else if ($fileSize > 1000000) { // Check if the image size is greater than 1MB
            echo "<script>alert('Image Size is too Large');</script>";
        } else {
            $newImageName = uniqid();
            $newImageName .= '.' . $imageExtension;

            move_uploaded_file($tmpName, 'image/' . $newImageName);

            // Insert item details into the database
            $query = "INSERT INTO item (image, itemtype, quantity, quantityleft, itemstore, itemprice, itemstatus) 
                      VALUES ('$newImageName', '$itemtype', '$quantity', '$quantityleft', '$itemstore', '$itemprice', '$itemstatus')";
            mysqli_query($conn, $query);
            
            echo "<script>
                    document.location.href = 'adminItem.php';
                  </script>";
        }
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
    <link rel="stylesheet" type="text/css" href="style/adminadditem.css">
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
        <form action="" method="POST" enctype="multipart/form-data">
        <h2>Add New Item</h2>
        
            <label for="image">Item Image:</label><br>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" required><br><br>
            
            <label for="itemtype">Item Type:</label><br>
            <input type="text" id="itemtype" name="itemtype" required><br><br>
            
            <label for="quantity">Quantity of the Item:</label><br>
            <input type="number" id="quantity" name="quantity" required><br><br>

            <label for="itemstore">Item Store:</label><br>
            <input type="text" id="itemstore" name="itemstore" required><br><br>

            <label for="itemprice">Item Price (per unit):</label><br>
            <input type="text" id="itemprice" name="itemprice" required><br><br>
            
            <label for="itemstatus">Item Status:</label><br>
            <select id="itemstatus" name="itemstatus" required>
                <option value="Available">Available</option>
                <option value="Out of stock">Out of stock</option>
            </select><br><br>
            
            <button type="submit" name="submit" class="submit">submit</button>
        </form>
    </div>
</body>
</html>