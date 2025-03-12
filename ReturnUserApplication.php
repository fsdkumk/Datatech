<?php
session_start();
include("database.php"); // Database connection file

// Redirect if not logged in or not a staff/student
if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("Location: UserLogin.php");
    exit();
}

// Fetch the user's ID from the registration table
$username = $_SESSION['username'];
$userIdQuery = "SELECT id FROM registration WHERE username = ?";
$userIdStmt = $conn->prepare($userIdQuery);
$userIdStmt->bind_param("s", $username);
$userIdStmt->execute();
$userIdResult = $userIdStmt->get_result();

if ($userIdResult->num_rows > 0) {
    $userIdRow = $userIdResult->fetch_assoc();
    $userId = $userIdRow['id'];
} else {
    echo "<script>alert('User not found.'); window.location.href = 'Logout.php';</script>";
    exit();
}

// Fetch the specific appid the user wants to return
$appIdQuery = "SELECT appid FROM application WHERE id = ? AND returnstatus = 'Not Completed' ORDER BY appid ASC LIMIT 1";
$appIdStmt = $conn->prepare($appIdQuery);
$appIdStmt->bind_param("i", $userId);
$appIdStmt->execute();
$appIdResult = $appIdStmt->get_result();

if ($appIdResult->num_rows > 0) {
    $appIdRow = $appIdResult->fetch_assoc();
    $appId = $appIdRow['appid'];
} else {
    echo "<script>alert('The items have already been returned.'); window.location.href = 'UserStatus.php';</script>";
    exit();
}

// Retrieve item types and prices from the database
$itemTypes = [];
$itemPrices = [];
$itemTypesQuery = "SELECT itemType, itemPrice FROM item";
$itemTypesResult = $conn->query($itemTypesQuery);

if ($itemTypesResult && $itemTypesResult->num_rows > 0) {
    while ($row = $itemTypesResult->fetch_assoc()) {
        $itemTypes[] = $row['itemType'];
        $itemPrices[$row['itemType']] = $row['itemPrice'];
    }
}

// Fetch QR code data using prepared statements
$qrcodeSql = "SELECT * FROM qrcode";
$qrcodeStmt = $conn->prepare($qrcodeSql);
$qrcodeStmt->execute();
$qrcodeResult = $qrcodeStmt->get_result();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['ajax'])) {
    $items = $_POST['items'] ?? [];

    if (empty($items)) {
        echo "<script>alert('No items submitted!'); window.history.back();</script>";
        exit();
    }

    foreach ($items as $itemIndex => $item) {
        $itemType = $item['itemType'] ?? null;
        $quantity = $item['quantity'] ?? null;
        $itemCondition = $item['itemCondition'] ?? null;
    
        // Validate each item data
        if (!$itemType || !$quantity || !$itemCondition) {
            echo "<script>alert('All fields are required for each item!'); window.history.back();</script>";
            exit();
        }
    
        // Handle file upload for proof of payment if available
        $proofOfPayment = null;
        $proofOfPaymentTemp = null;
        if ($itemCondition == 'Good') {
            // If the condition is "Good", set proofOfPayment to the string "items condition is good"
            $proofOfPayment = "items condition is good";
        } elseif (isset($_FILES['proofOfPayment']['name'][$itemIndex]) && $_FILES['proofOfPayment']['error'][$itemIndex] === UPLOAD_ERR_OK) {
            $proofOfPayment = $_FILES['proofOfPayment']['name'][$itemIndex];
            $proofOfPaymentTemp = $_FILES['proofOfPayment']['tmp_name'][$itemIndex];
    
            // Move the uploaded file to the desired location
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadPath = $uploadDir . basename($proofOfPayment);
            if ($proofOfPaymentTemp && !move_uploaded_file($proofOfPaymentTemp, $uploadPath)) {
                echo "<script>alert('Failed to upload proof of payment.'); window.history.back();</script>";
                exit();
            }
        }
    
        // Insert data into the inventory table, including appid
        $insertQuery = "INSERT INTO inventory (id, appid, itemtype, quantity, itemcondition, proofofpayment) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iisiss", $userId, $appId, $itemType, $quantity, $itemCondition, $proofOfPayment);
    
        if ($insertStmt->execute()) {
            // Update the quantityleft in the item table based on condition
            if ($itemCondition === "Damage" || $itemCondition === "Lost") {
                $updateQuery = "UPDATE item SET quantityleft = quantityleft - ? WHERE itemType = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("is", $quantity, $itemType);
                $updateStmt->execute();
                $updateStmt->close();
            } elseif ($itemCondition === "Good") {
                $updateQuery = "UPDATE item SET quantityleft = quantityleft + ? WHERE itemType = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("is", $quantity, $itemType);
                $updateStmt->execute();
                $updateStmt->close();
            }
        } else {
            echo "<script>alert('Error inserting data into inventory: " . $insertStmt->error . "');</script>";
        }
    
        $insertStmt->close();
    }
    
    // Update returnstatus to Completed
    $updateStatusQuery = "UPDATE application SET returnstatus = 'Completed' WHERE appid = ?";
    $updateStatusStmt = $conn->prepare($updateStatusQuery);
    $updateStatusStmt->bind_param("i", $appId);
    $updateStatusStmt->execute();
    $updateStatusStmt->close();

    echo "<script>
        alert('Thank you for your responsibility!');
        window.location.href = 'UserStatus.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/png" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style/ReturnUserApplication.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="title">DATATECH <br> INNOVATION HUB</div>
                <div class="login"><a href="Logout.php">Log Out</a></div>
            </div>
            <img src="image/header.png" alt="Header Image">
        </div>

        <!-- Navigation Bar -->
        <nav class="navbar">
            <ul>
                <li><a href="UserItemlist.php">Item List</a></li>
                <li><a href="UserApplicationform.php">Application Form</a></li>
                <li><a href="UserStatus.php">Application Status</a></li>
                <li><a href="UserAbout.php">About</a></li>
                <li><a href="UserProfile.php">Profile</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="content">
            <form id="applicationForm" method="post" action="ReturnUserApplication.php" enctype="multipart/form-data">
                <div id="itemsContainer">
                    <div class="form-section item-section">
                        <h2>Item Information</h2>

                        <div class="qr-code-container">
                        <?php while ($qrcodeRow = $qrcodeResult->fetch_assoc()) : ?>
                             <img src="<?php echo htmlspecialchars($qrcodeRow['qrimage']); ?>" alt="QR Code" class="qrcode-image">
                        <?php endwhile; ?>

                            <p class="qr-code-text">SCAN THE QR CODE TO PAY</p>
                            <!-- Add Item Button below QR Code Text -->
                            <button type="button" class=additem id="addItemButton">+ Add Another Item</button>
                        </div>

                        <label for="itemType">Item Type:</label>
                        <select name="items[0][itemType]" class="item-select" required>
                            <option value="" disabled selected>Select Item Type</option>
                            <?php foreach ($itemTypes as $itemType): ?>
                                <option value="<?= htmlspecialchars($itemType) ?>"><?= htmlspecialchars($itemType) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="itemPrice">Item Price (RM):</label>
                        <input type="text" class="itemPrice" name="items[0][itemPrice]" readonly>

                        <label for="quantity">Quantity:</label>
                        <input type="number" name="items[0][quantity]" min="1" required>

                        <label for="itemCondition">Condition:</label>
                        <select id="itemCondition" name="items[0][itemCondition]" required>
                            <option value="" disabled selected>Select Condition</option>
                            <option value="Good">Good</option>
                            <option value="Damage">Damage</option>
                            <option value="Lost">Lost</option>
                        </select>

                        <label for="proofOfPayment">Proof of Payment :</label>
                        <input type="file" name="proofOfPayment[0]" accept="image/*" id="proofOfPayment0" class="proof-of-payment"> 

                    </div> 
                </div>

                <!-- Submit Button placed separately -->
                <div class="form-submit">
                    <input type="submit" class="submitBtn" value="Submit Application">
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
    let itemIndex = 1;
    const itemPrices = <?= json_encode($itemPrices) ?>;

    $('#addItemButton').click(function () {
        const newItemSection = `
            <div class="form-section item-section">
                <h2>Item Information</h2>
                <label for="itemType">Item Type:</label>
                <select name="items[${itemIndex}][itemType]" class="item-select" required>
                    <option value="" disabled selected>Select Item Type</option>
                    <?php foreach ($itemTypes as $itemType): ?>
                        <option value="<?= htmlspecialchars($itemType) ?>"><?= htmlspecialchars($itemType) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="itemPrice">Item Price (RM):</label>
                <input type="text" class="itemPrice" name="items[${itemIndex}][itemPrice]" readonly>

                <label for="quantity">Quantity:</label>
                <input type="number" name="items[${itemIndex}][quantity]" min="1" required>

                <label for="itemCondition">Condition:</label>
                <select id="itemCondition" name="items[${itemIndex}][itemCondition]" required>
                    <option value="" disabled selected>Select Condition</option>
                    <option value="Good">Good</option>
                    <option value="Damage">Damage</option>
                    <option value="Lost">Lost</option>
                </select>

                <label for="proofOfPayment">Proof of Payment:</label>
                <input type="file" name="proofOfPayment[${itemIndex}]" accept="image/*" id="proofOfPayment${itemIndex}">
                <img src="" class="proofImage" style="display: none; max-width: 150px;" id="proofImage${itemIndex}">
            </div>`;
        $('#itemsContainer').append(newItemSection);
        itemIndex++;
    });

    // Update the item price based on the selected item type
    $(document).on('change', '.item-select', function () {
        const selectedItemType = $(this).val();
        const itemPriceInput = $(this).closest('.form-section').find('.itemPrice');

        if (selectedItemType && itemPrices[selectedItemType]) {
            itemPriceInput.val(itemPrices[selectedItemType]);
        }
    });

    $(document).on('change', '.item-select, #itemCondition', function () {
    const itemSection = $(this).closest('.item-section');
    const itemCondition = itemSection.find('#itemCondition').val();
    const proofOfPaymentField = itemSection.find('.proof-of-payment');

    if (itemCondition === 'Good') {
        proofOfPaymentField.closest('label').hide();
        proofOfPaymentField.prop('required', false);
    } else {
        proofOfPaymentField.closest('label').show();
        proofOfPaymentField.prop('required', true);
    }
});

});
    </script>
    <script src="js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>
</html>