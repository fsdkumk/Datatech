<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : "";

// Fetch the applications and related registration details of the logged-in user
$sql = "
    SELECT 
        a.appid, 
        r.fullname, 
        r.email, 
        r.phoneno, 
        r.position, 
        a.projecttitle, 
        a.svname, 
        a.pickupdate, 
        a.pickuptime, 
        a.returndate,
        a.itemtype, 
        a.quantity,
        a.status,
        a.returnstatus
    FROM application AS a
    JOIN registration AS r ON a.id = r.id
    WHERE r.username = ? AND a.projecttitle LIKE ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ss", $_SESSION['username'], $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>
    <link rel="stylesheet" href="style/UserStatus.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="title">DATATECH <br> INNOVATION HUB</div>
                <div class="logout"><a href="Logout.php">Log Out</a></div>   
            </div>
            <img src="image/header.png" alt="Header Image">
        </div>

        <nav class="navbar">
            <ul>
                <li><a href="UserItemlist.php">Item List</a></li>
                <li><a href="UserApplicationform.php">Application Form</a></li>
                <li><a href="UserStatus.php" class="active">Application Status</a></li>
                <li><a href="UserAbout.php">About</a></li>
                <li><a href="UserProfile.php">Profile</a></li>
            </ul>
        </nav>

        <div class="content">
            <h2>Application Status</h2>
            <form method="GET" action="UserStatus.php">
                <input type="text" name="search" placeholder="Search by Project Title..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Supervisor</th>
                        <th>Pickup Date</th>
                        <th>Pickup Time</th>
                        <th>Return Date</th>
                        <th>Item Type</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Return Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['projecttitle']); ?></td>
                                <td><?php echo htmlspecialchars($app['svname']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($app['pickupdate'])); ?></td>
                                <td><?php echo date('H:i', strtotime($app['pickuptime'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($app['returndate'])); ?></td>
                                <td><?php echo htmlspecialchars($app['itemtype']); ?></td>
                                <td><?php echo htmlspecialchars($app['quantity']); ?></td>
                                <td>
                                    <?php if ($app["status"] === "Pending"): ?>
                                        <span class="status status-pending">Pending</span>
                                    <?php elseif ($app["status"] === "Approved"): ?>
                                        <span class="status status-approved">Approved</span>
                                    <?php elseif ($app["status"] === "Rejected"): ?>
                                        <span class="status status-rejected">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?php 
                                        if ($app['returnstatus'] === 'Not Completed') {
                                            echo 'returnstatus-notcompleted';
                                        } elseif ($app['returnstatus'] === 'Completed') {
                                            echo 'returnstatus-completed';
                                        } elseif ($app['returnstatus'] === 'Rejected') {
                                            echo 'returnstatus-rejected';
                                        } 
                                    ?>">
                                    <?php echo htmlspecialchars($app['returnstatus']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-container">
                                        <?php if ($app['returnstatus'] === 'Completed'): ?>
                                            <a href="javascript:void(0);" 
                                               class="btn btn-return" 
                                               onclick="alert('The items have already been returned.'); window.location.href = 'UserStatus.php';">Return</a>
                                        <?php elseif ($app['returnstatus'] === 'Not Completed'): ?>
                                            <a href="ReturnUserApplication.php?appid=<?php echo htmlspecialchars($app['appid']); ?>" 
                                               class="btn btn-return">Return</a>
                                        <?php endif; ?>
                                        <a href="EditUserApplication.php?id=<?php echo htmlspecialchars($app['appid']); ?>" 
                                           class="btn btn-update">Update</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>