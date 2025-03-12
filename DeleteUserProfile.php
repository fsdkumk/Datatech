<?php
session_start();
include("database.php"); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteAccount') {
    $username = $_SESSION['username'];

    // Delete user from database
    $deleteQuery = "DELETE FROM registration WHERE username='" . mysqli_real_escape_string($conn, $username) . "'";

    if (mysqli_query($conn, $deleteQuery)) {
        // If the account is successfully deleted, destroy the session
        session_destroy();
        echo "success";
    } else {
        // Handle error
        echo "Error: " . mysqli_error($conn);
    }

    exit();
}
?>
