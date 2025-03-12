<?php
session_start();
include("database.php");

// Check if the user is logged in as staff/student
if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

// Retrieve user data from the database
$userData = [];
if (isset($_SESSION['username'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $sql = "SELECT * FROM registration WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
}

// Retrieve item types from the database
$itemTypes = [];
$itemTypesQuery = "SELECT DISTINCT itemType FROM item";
$itemTypesResult = $conn->query($itemTypesQuery);

if ($itemTypesResult->num_rows > 0) {
    while ($row = $itemTypesResult->fetch_assoc()) {
        $itemTypes[] = $row['itemType'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $userData['id'];  // Foreign key from the registration table
    $projectTitle = $conn->real_escape_string($_POST['projectTitle']);
    $svname = $conn->real_escape_string($_POST['svname']);
    $pickupDate = $conn->real_escape_string($_POST['pickupdate'][0]);  // Still taking the first item for pickup date and time
    $pickupTime = $conn->real_escape_string($_POST['pickuptime'][0]);
    $returnDate = $conn->real_escape_string($_POST['returndate'][0]);
    
    // Get the concatenated item types and quantities
    $combinedItems = $conn->real_escape_string($_POST['combinedItems']);  // Comma-separated items
    $combinedQuantities = $conn->real_escape_string($_POST['combinedQuantities']);  // Comma-separated quantities
    $status = "Pending";  // Default status
    $returnStatus = "Not Completed";  // Default return status

    // Insert project details into the application table
    $insertProjectQuery = "INSERT INTO application (id, projecttitle, svname, pickupdate, pickuptime, returndate, itemtype, quantity, status, returnstatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertProjectQuery);

    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
    } else {
        // Bind parameters (i = integer, s = string)
        $stmt->bind_param("isssssssss", $userId, $projectTitle, $svname, $pickupDate, $pickupTime, $returnDate, $combinedItems, $combinedQuantities, $status, $returnStatus);

        if ($stmt->execute()) {
            // Redirect or show a success message
            echo "<script> window.location.href = 'UserStatus.php';</script>";
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        $stmt->close(); // Close the statement
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
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

        <!-- Application Form -->
        <div class="content">
            <form id="applicationForm" method="post" action="UserApplicationform.php">
                <!-- User Information Section -->
                <div class="form-section">
                    <h2>User Information</h2>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['fullname'] ?? ''); ?>" readonly>

                    <label for="staffNo">Staff/Matric No:</label>
                    <input type="text" id="staffNo" name="staffNo" value="<?php echo htmlspecialchars($userData['identityno'] ?? ''); ?>" readonly>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" readonly>

                    <label for="phone">Phone No:</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phoneno'] ?? ''); ?>" readonly>

                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($userData['position'] ?? ''); ?>" readonly>

                    <label for="projectTitle">Project Title:</label>
                    <input type="text" id="projectTitle" name="projectTitle" placeholder="Enter your project title" required>

                    <label for="svname">Supervisor Name:</label>
                    <input type="text" id="svname" name="svname" placeholder="Enter your supervisor name">
                </div>

                <!-- Item Information Section -->
                <div class="form-section">
                    <h2>Item Information <button type="button" id="addItem">+ Add Item</button></h2>
                    <div id="itemContainer">
                        <div class="item-row">
                            <label for="pickupdate[]">Pickup Date:</label>
                            <input type="date" name="pickupdate[]" required>

                            <label for="pickuptime[]">Pickup Time:</label>
                            <input type="time" name="pickuptime[]" required>

                            <label for="returndate[]">Return Date:</label>
                            <input type="date" name="returndate[]" required>

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

                <!-- Rules and Regulations Section -->
                <div class="form-section">
                    <h2>Rules and Regulations</h2>
                    <p>I agree and promise that:</p>
                    <ul>
                        <li>I will take good care of this equipment.</li>
                        <li>I will be responsible for any damage or loss caused by my own negligence and I am ready to accept any action and claim if necessary.</li>
                        <li>I will be ready to pay any fees if I return the item late. </li>
                        <li>I will return this equipment on the agreed date.</li>
                    </ul>
                    <label>
                        <input type="checkbox" name="agree" required> I agree to the terms and conditions.
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="form-section">
                    <button type="submit" class="submit-button">Submit</button>
                </div>
            </form>
        </div>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </footer>
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

    this.appendChild(hiddenItemInput);
    this.appendChild(hiddenQuantityInput);
});

    </script>
</body>
</html>