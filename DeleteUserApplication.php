<?php
session_start();
include("database.php");

if (!isset($_SESSION['username']) || $_SESSION['role'] != "staff/student") {
    header("location:UserLogin.php");
    exit();
}

// Get application ID from URL
if (isset($_GET['id'])) {
    $appid = intval($_GET['id']);

    // Delete query
    $sql = "DELETE FROM application WHERE appid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appid);
    
    if ($stmt->execute()) {
        header("Location: UserStatus.php"); // Redirect after deletion
        exit();
    } else {
        echo "Error deleting application: " . $stmt->error;
    }
} else {
    echo "Invalid application ID.";
    exit();
}

$conn->close();
?>
