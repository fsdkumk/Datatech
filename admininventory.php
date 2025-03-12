<?php
session_start();
include("database.php"); // Include your database connection

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Retrieve inventory data with filtering
$filterCondition = '';
if (isset($_POST['filter'])) {
    if ($_POST['filter'] == 'damage') {
        $filterCondition = " WHERE itemcondition='Damage'";
    } elseif ($_POST['filter'] == 'lost') {
        $filterCondition = " WHERE itemcondition='Lost'";
    } elseif ($_POST['filter'] == 'good') {
        $filterCondition = " WHERE itemcondition='Good'";
    }
}

$query = "SELECT * FROM inventory" . $filterCondition;
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory</title>
    <link rel="stylesheet" type="text/css" href="style/admininventory.css">
    
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
        <h2>Inventory</h2>
        <!-- Filter Buttons -->
        <form method="post" action="admininventory.php">
            <button type="submit" name="filter" value="allitems" class="btn-allitems">All Items</button>
            <button type="submit" name="filter" value="damage" class="btn-damage">Damaged Items</button>
            <button type="submit" name="filter" value="lost" class="btn-lost">Lost Items</button>
            <button type="submit" name="filter" value="good" class="btn-good">Good Items</button>
        </form>

        <!-- Inventory Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Item Type</th>
                    <th>Quantity</th>
                    <th>Item Condition</th>
                    <th>Proof of Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $conditionClass = '';
                        $proof = '';
                        if ($row['itemcondition'] == 'Damage' || $row['itemcondition'] == 'Lost') {
                            $conditionClass = strtolower($row['itemcondition']);
                            $proof = "<button class='view-proof-btn' data-file='uploads/" . $row['proofofpayment'] . "'>View Proof</button>";
                        } elseif ($row['itemcondition'] == 'Good') {
                            $conditionClass = 'good';
                            $proof = "<span class='good-condition'>Good</span>";
                        }

                        echo "<tr>";
                        echo "<td>" . $row['itemtype'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td><span class='$conditionClass'>" . $row['itemcondition'] . "</span></td>";
                        echo "<td>" . $proof . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No items found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="proofModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="proofImage" src="" alt="Proof of Payment" style="max-width: 100%; max-height: 80vh;">
        </div>
    </div>

    <script>
        // Modal JavaScript
        const modal = document.getElementById("proofModal");
        const proofImage = document.getElementById("proofImage");
        const closeModal = document.querySelector(".close");

        document.querySelectorAll(".view-proof-btn").forEach(button => {
            button.addEventListener("click", function () {
                const file = this.getAttribute("data-file");
                proofImage.src = file;
                modal.style.display = "flex";
            });
        });

        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
