<?php
session_start(); 
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Check database connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM registration WHERE username=? AND role=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            
            if ($password == $row['password']) { 
                session_regenerate_id();
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                session_write_close();

                if ($_SESSION['role'] === "admin") {
                    header("Location: admindashboard.php");
                    exit();
                } elseif ($_SESSION['role'] === "staff/student") {
                    header("Location: UserItemlist.php");
                    exit();
                }
            } else {
                $msg = "Invalid Username or Password!";
            }
        } else {
            $msg = "No user found with the provided credentials.";
        }

        $stmt->close();
    } else {
        $msg = "Failed to prepare the SQL statement.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style/UserLogin.css">
    <script src="javascript/UserLogin.js" defer></script>
</head>
<body>
<h1> Welcome to Datatech Innovation Hub </h1>
    <div class="container">
    
        <form action="UserLogin.php" method="POST" id="login-form" class="form">
            <h2>Log In</h2>
            <div class="input-container">
                <div class="radio-container">
                    <input type="radio" id="admin" name="role" value="admin" required>
                    <label for="admin">Admin</label>
                    <input type="radio" id="staff-student" name="role" value="staff/student">
                    <label for="staff-student">Staff/Student</label>
                </div>
            </div>

            <input type="text" name="username" class="box" placeholder="Username" required>
            <div class="password-container">
                <input type="password" id="password" name="password" class="box" placeholder="Password" required>
                <button type="button" class="togglepassword" onclick="togglePassword()" style="display: none;">🔐</button>
            </div>

            <input type="submit" value="LOGIN" id="submit" name="login">

            <p class="signup-prompt">Don't have an account? <a href="register.php">Create New Account</a></p>
            <p class="copyright">© 2024 Faculty of Data Science and Computing. All rights reserved.</p>
        </form>

        <div class="side">
    <img src="image/login.png" alt="">
    <div class="welcome-text">
        <img src="image/LogoDatatech.png" alt="Datatech Innovation HUB">
    </div>
</div>

</body>
</html>