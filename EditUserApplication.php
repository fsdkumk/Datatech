<?php
session_start();
include("database.php");

// Check if the user is logged in and is a staff/student
if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

// Get the application ID from the URL
$appid = $_GET['id'];

// Retrieve item types from the database
$itemTypes = [];
$itemTypesQuery = "SELECT DISTINCT itemType FROM item";
$itemTypesResult = $conn->query($itemTypesQuery);

if ($itemTypesResult->num_rows > 0) {
    while ($row = $itemTypesResult->fetch_assoc()) {
        $itemTypes[] = $row['itemType'];
    }
}

// Fetch the current application data
$sql = "SELECT * FROM application WHERE appid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appid);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

// Handle form submission to update the application
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projecttitle = $_POST['projecttitle'];
    $svname = $_POST['svname'];
    $pickupdate = $_POST['pickupdate'];
    $pickuptime = $_POST['pickuptime'];
    $returndate = $_POST['returndate'];

    // Get the concatenated item types and quantities
    $combinedItems = $conn->real_escape_string($_POST['combinedItems']);  // Comma-separated items
    $combinedQuantities = $conn->real_escape_string($_POST['combinedQuantities']);  // Comma-separated quantities
    $status = "Pending";  // Default status
    
    // Update the application in the database
    $update_sql = "UPDATE application SET projecttitle = ?, svname = ?, pickupdate = ?, pickuptime = ?, returndate = ?, itemtype = ?, quantity = ? WHERE appid = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    // Bind the correct variables (use $combinedItems and $combinedQuantities)
    $update_stmt->bind_param("sssssssi", $projecttitle, $svname, $pickupdate, $pickuptime, $returndate, $combinedItems, $combinedQuantities, $appid);

    if ($update_stmt->execute()) {
        // Redirect back to the status page after update
        header("Location: UserStatus.php");
        exit();
    } else {
        echo "Error updating application: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application Form</title>
    <link rel="stylesheet" href="style/UserApplicationform.css">
    <!-- Include Select2 CSS for searchable dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        <div class="content">
            <div class="form-section">
                <h2>Edit Application</h2>
                <form id="applicationForm" action="EditUserApplication.php?id=<?php echo $appid; ?>" method="POST">
                    <div>
                        <label for="projecttitle">Project Title:</label>
                        <input type="text" id="projecttitle" name="projecttitle" value="<?php echo htmlspecialchars($application['projecttitle']); ?>" required>
                    </div>
                    <div>
                        <label for="svname">Supervisor Name:</label>
                        <input type="text" id="svname" name="svname" value="<?php echo htmlspecialchars($application['svname']); ?>" required>
                    </div>
            </div>

            <div class="form-section">
                <h2>Item Information <button type="button" id="addItem">+ Add Item</button></h2>
                <div>
                    <label for="pickupdate">Pickup Date:</label>
                    <input type="date" id="pickupdate" name="pickupdate" value="<?php echo htmlspecialchars($application['pickupdate']); ?>" required>
                </div>
                <div>
                    <label for="pickuptime">Pickup Time:</label>
                    <input type="time" id="pickuptime" name="pickuptime" value="<?php echo htmlspecialchars($application['pickuptime']); ?>" required>
                </div>
                <div>
                    <label for="returndate">Return Date:</label>
                    <input type="date" id="returndate" name="returndate" value="<?php echo htmlspecialchars($application['returndate']); ?>" required>
                </div>
                <div id="itemContainer">
                    <div class="item-row">
                        <label for="itemType[]">Item Type:</label>
                        <select name="itemType[]" class="item-select" required>
                            <option value="" disabled selected>Select Item Type</option>
                            <?php foreach ($itemTypes as $itemType): ?>
                                <option value="<?php echo htmlspecialchars($itemType); ?>"><?php echo htmlspecialchars($itemType); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="quantity[]">Quantity:</label>
                        <input type="number" name="quantity[]" min="1" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <button type="submit" class="submit-button">Update Application</button>
            </div>
        </form>
    </div>

    <!-- Include jQuery and Select2 JS for searchable dropdowns -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 for item types
        $(document).ready(function() {
            $('.item-select').select2();
        });

        document.getElementById('addItem').addEventListener('click', function() {
            const itemContainer = document.getElementById('itemContainer');
            const newItemRow = document.createElement('div');
            newItemRow.classList.add('item-row');
            newItemRow.innerHTML = `
                <label for="itemType[]">Item Type:</label>
                <select name="itemType[]" class="item-select" required>
                    <option value="" disabled selected>Select Item Type</option>
                    <?php foreach ($itemTypes as $itemType): ?>
                        <option value="<?php echo htmlspecialchars($itemType); ?>"><?php echo htmlspecialchars($itemType); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity[]">Quantity:</label>
                <input type="number" name="quantity[]" min="1" required>
            `;
            itemContainer.appendChild(newItemRow);

            // Re-initialize Select2 for the newly added select box
            $('.item-select').select2();
        });

        // When the form is submitted, combine all item types and quantities into comma-separated strings
        document.getElementById('applicationForm').addEventListener('submit', function(e) {
            const itemTypes = document.querySelectorAll('select[name="itemType[]"]');
            const quantities = document.querySelectorAll('input[name="quantity[]"]');
            
            let combinedItems = [];
            let combinedQuantities = [];

            itemTypes.forEach((itemType) => {
                combinedItems.push(itemType.value);
            });

            quantities.forEach((quantity) => {
                combinedQuantities.push(quantity.value);
            });

            // Create hidden inputs to store the combined items and quantities
            const hiddenItemInput = document.createElement('input');
            hiddenItemInput.type = 'hidden';
            hiddenItemInput.name = 'combinedItems';
            hiddenItemInput.value = combinedItems.join(', '); // comma-separated string

            const hiddenQuantityInput = document.createElement('input');
            hiddenQuantityInput.type = 'hidden';
            hiddenQuantityInput.name = 'combinedQuantities';
            hiddenQuantityInput.value = combinedQuantities.join(', '); // comma-separated string

            // Append hidden inputs to the form
            this.appendChild(hiddenItemInput);
            this.appendChild(hiddenQuantityInput);
        });
    </script>
</body>
</html>
