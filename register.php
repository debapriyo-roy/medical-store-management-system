<?php
// Include the database connection file
include('connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query to insert user into the database
        $sql = "INSERT INTO users (first_name, last_name, gender, phone, password) VALUES ('$first_name', '$last_name', '$gender', '$phone', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style_login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="register.php" method="POST">
            <h1>Register</h1>
            
            <?php
            // Display error message if registration failed
            if (isset($error_message)) {
                echo "<p class='error'>$error_message</p>";
            }
            ?>

            <div class="input-box">
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>
            <div class="input-box">
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
            <div class="input-box">
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="input-box">
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" class="btn">Register</button>

            <div class="register-link">
                <p>Already Have An Account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
