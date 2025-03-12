<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Handle search form submission
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch items from database based on search input
$i = 1;
$query = "SELECT no, image, itemtype, quantity, quantityleft, itemstore, itemprice, itemstatus 
          FROM item 
          WHERE itemtype LIKE '%$search%' 
          ORDER BY no ASC";
$rows = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/png" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Item Organizer</title>
    <link rel="stylesheet" type="text/css" href="style/adminItem.css">
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
        <h2>Item Organizer
            <button class="add-item-btn" onclick="window.location.href='adminadditem.php'">+ Add Item</button>
        </h2>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="adminItem.php">
           <input type="text" name="search" placeholder="Search for item type..." value="<?php echo htmlspecialchars($search); ?>">
           <button type="submit">Search</button>
        </form>
        
        <!-- Displaying Items in Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item Picture</th>
                    <th>Item Type</th>
                    <th>Quantity</th>
                    <th>Quantity Left</th>
                    <th>Item Store</th>
                    <th>Item Price</th>
                    <th>Item Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = mysqli_fetch_assoc($rows)) { ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <?php 
                                $imagePath = 'image/' . $row['image']; 
                            ?>
                            <img src="<?php echo $imagePath; ?>" alt="Item Picture" width="200">
                        </td>
                        <td><?php echo $row["itemtype"]; ?></td>
                        <td><?php echo $row["quantity"]; ?></td>
                        <td><?php echo $row["quantityleft"]; ?></td>
                        <td><?php echo $row["itemstore"]; ?></td>
                        <td><?php echo isset($row["itemprice"]) ? 'RM'. $row["itemprice"] : 'N/A'; ?></td>
                        <td><?php echo $row["itemstatus"]; ?></td>
                        <td>
                            <a href="EditadminItem.php?id=<?php echo htmlspecialchars($row['no']); ?>" class="btn btn-update">Update</a>
                            <a href="DeleteadminItem.php?id=<?php echo htmlspecialchars($row['no']); ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                        </td>
                    </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
