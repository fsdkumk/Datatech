<?php
session_start();
include("database.php");

// Ensure the user is an admin before allowing deletion
if (!isset($_SESSION['username']) || $_SESSION['role'] != "admin") {
    header("Location: UserLogin.php");
    exit();
}

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // First, delete related records from the application table
        $deleteApplicationQuery = "DELETE FROM application WHERE id = ?";
        if ($stmtApp = mysqli_prepare($conn, $deleteApplicationQuery)) {
            mysqli_stmt_bind_param($stmtApp, "i", $userId);
            mysqli_stmt_execute($stmtApp);
            mysqli_stmt_close($stmtApp);
        }

        // Then delete from the registration table
        $deleteUserQuery = "DELETE FROM registration WHERE id = ?";
        if ($stmtUser = mysqli_prepare($conn, $deleteUserQuery)) {
            mysqli_stmt_bind_param($stmtUser, "i", $userId);
            mysqli_stmt_execute($stmtUser);
            mysqli_stmt_close($stmtUser);
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect with success message
        header("Location: adminuserlist.php?message=UserDeleted");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction if anything failed
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Redirect back if no ID is provided
    header("Location: adminuserlist.php");
    exit();
}
?>
