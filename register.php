<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <link rel="shortcut icon" type="datatech-icon" href="image/Logo Datatech.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style/register.css">
    <script src="javascript/register.js" defer></script>
</head>
<body>

    <!-- Title Section -->
    <h1>DATATECH INNOVATION HUB</h1>

    <!-- Registration Section -->
    <form action="register.php" method="post">

<?php
if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $fullname = $_POST["fullname"];
    $password = $_POST["password"]; 
    $identityno = $_POST["identityno"];
    $email = $_POST["email"];
    $phoneno = $_POST["phoneno"];
    $position = $_POST["position"];
    $role = "staff/student"; 

    $errors = array();

    // Validation checks
    if (empty($username) || empty($fullname) ||empty($password) || empty($identityno) || empty($email) || empty($phoneno) || empty($position)) {
        array_push($errors, "All fields are required");
    }
    
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) ||   // Uppercase letter
        !preg_match('/[a-z]/', $password) ||   // Lowercase letter
        !preg_match('/[0-9]/', $password) ||   // Numeric character
        !preg_match('/[\W]/', $password)) {    // Special character
        array_push($errors, "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one special character, and one numeric character.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }

    require_once "database.php";
    $stmt = mysqli_stmt_init($conn);

    // Check if the username, identity number, email, or phone number already exists
    $sql = "SELECT * FROM registration WHERE username = ? OR identityno = ? OR email = ? OR phoneno = ?";
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $username, $identityno, $email, $phoneno);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            array_push($errors, "A user with the same username, identity number, email, or phone number already exists!");
        }
    }

    if (count($errors) == 0) {
        // Insert the new user into the database
        $sql = "INSERT INTO registration (username, fullname, password, identityno, email, phoneno, position, role) VALUES (?,?, ?, ?, ?, ?, ?, ?)";
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $username, $fullname, $password, $identityno, $email, $phoneno, $position, $role);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>You are registered successfully.</div>";
            header("Location: UserLogin.php"); // Redirect to UserLogin.php
            exit(); // Ensure no further code is executed
        } else {
            die("Something went wrong during registration.");
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

        <h2>Register Account</h2>

        <div class="input-container">
            <input type="text" id="username" name="username" placeholder=" " required>
            <label for="username">Username</label>
        </div>

        <div class="input-container">
            <input type="text" id="fullname" name="fullname" placeholder=" " required>
            <label for="fullname">Fullname</label>
        </div>

        <div class="input-container">
            <input type="password" id="password" name="password" placeholder=" " required>
            <label for="password">Password</label>
            <button type="button" class="toggle-password" onclick="togglePassword()" style="display: none;">🔐</button>
        </div>

        <div class="input-container">
            <input type="text" id="identityno" name="identityno" placeholder=" " required>
            <label for="identityno">Staff / Matric No</label>
        </div>

        <div class="input-container">
            <input type="email" id="email" name="email" placeholder=" " required>
            <label for="email">Email</label>
        </div>

        <div class="input-container">
            <input type="text" id="phoneno" name="phoneno" placeholder=" " required>
            <label for="phoneno">Phone Number</label>
        </div>

        <div class="input-container">
            <select id="position" name="position" required>
                 <option value="" disabled selected class="disabled-option">Position</option>
                 <option value="Academic Staff">Academic Staff</option>
                 <option value="Admin Staff">Admin Staff</option>
                 <option value="UMK Student">UMK Student</option>
            </select>

            <label id="position-label" for="position" style="display: none;">Position</label>
        </div>

        <input type="hidden" id="role" name="role" value="staff/student">

        <button type="submit" class="register-btn" name="register">Register</button>
        <p class="copyright">© 2024 Faculty of Data Science and Computing. All rights reserved.</p>
    </form>

</body>
</html>