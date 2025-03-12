<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("location:UserLogin.php");
    exit();
}

// Delete the item based on ID
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    // Prepare the delete query
    $deleteQuery = "DELETE FROM item WHERE no = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $itemId);

    if ($stmt->execute()) {
        echo "Item deleted successfully!";
        header("location: adminItem.php"); // Redirect back to the item page
        exit();
    } else {
        echo "Error deleting item: " . $conn->error;
    }
} else {
    echo "Invalid item ID.";
}

mysqli_close($conn);
?>
