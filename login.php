<?php
// Include the database connection file
require_once 'connection.php';

// Start session to store logged-in user information
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if the password is at least 8 characters long
    if (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        // Check if phone number exists in the database
        $sql = "SELECT * FROM users WHERE phone = '$phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // User found, now check the password
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                
                // Password is correct, start the session and set user info
                // FIXED: Changed 'user_id' to 'userId' to match core.php perfectly
                $_SESSION['userId'] = $row['id']; 
                $_SESSION['phone'] = $row['phone'];

                // Redirect to the dashboard or home page
                header("Location: dashboard.php");
                exit();
            } else {
                // Incorrect password
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            // Phone number not found
            $error_message = "Phone number not found. Please register.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style_login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="login.php" method="POST">
            <h1>Login</h1>
            
            <?php
            // Display error message if login failed
            if (isset($error_message)) {
                echo "<p class='error'>$error_message</p>";
            }
            ?>

            <div class="input-box">
                <input type="text" name="phone" placeholder="Phone Number" required>
                <i class='bx bxs-phone'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't Have An Account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>
