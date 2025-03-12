<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['appid'], $_POST['status'])) {
    $appid = $_POST['appid'];
    $newStatus = $_POST['status'];

    // Fetch the user's email, full name, project title, item type, and quantity
    $selectEmailSql = "SELECT r.email, r.fullname, a.projecttitle, a.itemtype, a.quantity FROM application AS a JOIN registration AS r ON a.id = r.id WHERE a.appid = ?";
    $stmtEmail = $conn->prepare($selectEmailSql);
    $stmtEmail->bind_param('i', $appid);
    $stmtEmail->execute();
    $stmtEmail->bind_result($email, $fullname, $projectTitle, $itemType, $quantity);
    $stmtEmail->fetch();
    $stmtEmail->close();

    if ($email) {
        if ($newStatus === "Approved") {
            // Update the quantity left in the item table
            $updateItemSql = "UPDATE item SET quantityleft = GREATEST(quantityleft - ?, 0) WHERE itemtype = ?";
            $stmtUpdateItem = $conn->prepare($updateItemSql);
            $stmtUpdateItem->bind_param('is', $quantity, $itemType);

            if ($stmtUpdateItem->execute()) {
                // Update the application status
                $updateAppSql = "UPDATE application SET status = ? WHERE appid = ?";
                $stmtUpdateApp = $conn->prepare($updateAppSql);
                $stmtUpdateApp->bind_param('si', $newStatus, $appid);
                $stmtUpdateApp->execute();
                $stmtUpdateApp->close();

                // Send approval email using PHPMailer
                $to = $email;
                $subject = "Application Status - Datatech Innovation Hub";
                $message = "Dear $fullname,\n\nYour application for the project '$projectTitle' has been approved.\n\nThank you.\nDatatech Innovation Hub.";

                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->SMTPAuth   = true;
                    $mail->Host       = 'smtp.gmail.com';  
                    $mail->Username   = 'asset.fsdk@umk.edu.my';  
                    $mail->Password   = 'albqkjxvdconverq';  
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // SSL options
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ),
                    );

                    //Recipients
                    $mail->setFrom('asset.fsdk@umk.edu.my', 'Datatech Innovation Hub');
                    $mail->addAddress($to);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = nl2br($message);

                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
            $stmtUpdateItem->close();

        } elseif ($newStatus === "Rejected") {
            // Update the application status and return status
            $updateRejectSql = "UPDATE application SET status = ?, returnstatus = 'Rejected' WHERE appid = ?";
            $stmtUpdateReject = $conn->prepare($updateRejectSql);
            $stmtUpdateReject->bind_param('si', $newStatus, $appid);
            $stmtUpdateReject->execute();
            $stmtUpdateReject->close();

            // Send rejection email using PHPMailer
            $to = $email;
            $subject = "Application Status - Datatech Innovation Hub";
            $message = "Dear $fullname,\n\nYour application for the project '$projectTitle' has been rejected.\n\nThank you.\nDatatech Innovation Hub.";

            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->SMTPAuth   = true;
                $mail->Host       = 'smtp.gmail.com';  
                $mail->Username   = 'asset.fsdk@umk.edu.my';  
                $mail->Password   = 'albqkjxvdconverq';  
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Disable SSL verification
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ),
                );

                //Recipients
                $mail->setFrom('asset.fsdk@umk.edu.my', 'Datatech Innovation Hub');
                $mail->addAddress($to);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = nl2br($message);

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
}

// Handle search for project title
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['filter']) ? $_GET['filter'] : 'All Status';
$sql = "
    SELECT 
        a.appid, 
        r.fullname, 
        r.identityno, 
        r.phoneno,
        a.projecttitle, 
        a.itemtype, 
        a.quantity,
        a.pickupdate, 
        a.returndate,
        a.status
    FROM application AS a
    JOIN registration AS r ON a.id = r.id
";

if ($filterStatus !== 'All Status' && $filterStatus) {
    $sql .= " WHERE a.status = ?";
}
if (!empty($searchTerm)) {
    $sql .= $filterStatus !== 'All Status' ? " AND a.projecttitle LIKE ?" : " WHERE a.projecttitle LIKE ?";
}

$stmt = $conn->prepare($sql);

if ($filterStatus !== 'All Status' && $filterStatus) {
    $stmt->bind_param('s', $filterStatus);
    if (!empty($searchTerm)) {
        $searchTermWildcard = "%$searchTerm%"; // Add wildcards for the query only
        $stmt->bind_param('s', $searchTermWildcard);
    }
} else {
    if (!empty($searchTerm)) {
        $searchTermWildcard = "%$searchTerm%"; // Add wildcards for the query only
        $stmt->bind_param('s', $searchTermWildcard);
    }
}


$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Status</title>
    <link rel="stylesheet" type="text/css" href="style/adminstatus.css">
    <script src="javascript/adminstatus.js" defer></script>
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
        <h2>Application Status</h2>
        <div class="filter-buttons">
            <a href="adminstatus.php" ><button class="all-status">All Status</button></a>
            <a href="adminstatus.php?filter=Pending"><button class="pending">Pending</button></a>
            <a href="adminstatus.php?filter=Approved"><button class="approved">Approved</button></a>
            <a href="adminstatus.php?filter=Rejected"><button class="rejected">Rejected</button></a>
        </div>

        <!-- Search Form -->
        <form class="search-form" method="GET" action="adminstatus.php">
            <input type="text" name="search" placeholder="Search by Project Title..." value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Staff/Matric No</th>
                    <th>Phone No</th>
                    <th>Project Title</th>
                    <th>Item Type</th>
                    <th>Quantity Request</th>
                    <th>Pickup Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["appid"]); ?></td>
                    <td><?php echo htmlspecialchars($row["fullname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["identityno"]); ?></td>
                    <td><?php echo htmlspecialchars($row["phoneno"]); ?></td>
                    <td><?php echo htmlspecialchars($row["projecttitle"]); ?></td>
                    <td><?php echo htmlspecialchars($row["itemtype"]); ?></td>
                    <td><?php echo htmlspecialchars($row["quantity"]); ?></td>
                    <td><?php echo (new DateTime($row["pickupdate"]))->format('d/m/Y'); ?></td>
                    <td><?php echo (new DateTime($row["returndate"]))->format('d/m/Y'); ?></td>
                    <td>
                        <form method="POST" action="adminstatus.php">
                            <input type="hidden" name="appid" value="<?php echo htmlspecialchars($row['appid']); ?>">
                            <select name="status" class="status-dropdown" onchange="this.form.submit(); changeDropdownColor(this);">
                                <option value="Pending" <?php echo ($row["status"] == "Pending" ? 'selected' : ''); ?>>Pending</option>
                                <option value="Approved" <?php echo ($row["status"] == "Approved" ? 'selected' : ''); ?>>Approved</option>
                                <option value="Rejected" <?php echo ($row["status"] == "Rejected" ? 'selected' : ''); ?>>Rejected</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>