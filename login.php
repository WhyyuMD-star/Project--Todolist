<?php
session_start();
require "koneksi.php";

// Initialize and establish database connection here

if (isset($_POST['loginbtn'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Sanitize and prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        // Check if username exists in the database
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $passwordsekarang = $row['password'];

            // Verify password using password_verify()
            if (password_verify($password, $passwordsekarang)) {
                $_SESSION['email'] = $row['email'];
                header("Location: ../todo/index.php");
                exit(); // Always exit after a header redirect
            } else {
                $login_error = "Password salah";
            }
        } else {
            $login_error = "Akun tidak tersedia";
        }
    } else {
        // Handle database query error
        $login_error = "Database error: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <form action="" method="post">
            <h1>Login</h1>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user' id="username"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <a class='bx bxs-lock-alt' id="password"></a>
            </div>

            <div class="remember-forget">
                <label>
                    <input type="checkbox">Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn" name="loginbtn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>

    <?php
    if (isset($login_error)) {
        echo '<div class="alert"><p>' . $login_error . '</p></div>';
    }
    ?>

</body>

</html>