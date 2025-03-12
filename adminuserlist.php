<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: UserLogin.php");
    exit();
}

// Check if a search term is provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch users from the database
if (!empty($searchTerm)) {
    $sql = "SELECT * FROM registration WHERE identityno LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm =  $searchTerm ; 
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM registration";
    $result = mysqli_query($conn, $sql);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Userlist</title>
    <link rel="stylesheet" type="text/css" href="style/adminuserlist.css">
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
        <h2>Registered Users
        <a href="adminuserlist.php" ><button class="all-user">All Users</button></a>
        </h2>

        <!-- Buttons for searching and showing all users -->
        <form method="GET" action="adminuserlist.php" class="search-form">
            <input type="text" name="search" placeholder="Search by Matric/Staff No..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- User List Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Matric/Staff No</th>
                    <th>Email</th>
                    <th>Phone No</th>
                    <th>Position</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['password']) . "</td>";
            echo "<td>" . htmlspecialchars($row['identityno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phoneno']) . "</td>";
            echo "<td>" . htmlspecialchars($row['position']) . "</td>";
            echo "<td>
                <a href='DeleteadminUser.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-delete btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No users found</td></tr>";
    }

    mysqli_close($conn);
    ?>
    
            </tbody>
        </table>
    </div>
</body>
</html>
