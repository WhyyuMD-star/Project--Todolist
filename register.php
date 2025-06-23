<?php
    session_start();
    require "koneksi.php";
?>


<?php
require 'koneksi.php';

if(isset($_POST['registerbtn'])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM user";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);


    if ($email == $row['email']) {
        $registerError = "Email telah digunakan";
    } else if ($password == $row['password']) {
        $registerError = "Password telah digunakan";
    } else if ($username == $row['username']) {
        $registerError = "Username telah digunakan";
    } else {
        $epassword = password_hash($password, PASSWORD_BCRYPT);
        
        $query_sql = "INSERT INTO user (email, username, password) 
                    VALUES ('$email', '$username', '$epassword')";
    
        if (mysqli_query($conn, $query_sql)) {
            header("location: login.php");
        } else {
            echo "Pendaftaran Gagal : " . mysqli_error($conn);
        }
    }
    
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="shine">
        <form action="" method="post">
            
            <h1>Register</h1>
            
            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
                <c class='bx bx-envelope'></c>
            </div>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <a class='bx bxs-lock-alt'></a>
            </div>

            <div class="agg">
                <label><input type="checkbox">Agree</label>
            </div>

            <button type="submit" class="btn" name="registerbtn">Register</button>

            <div class="login-link">
                <p>Have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>    

    <?php
        if (isset($registerError)) {
            echo '<div class="alert"><p>' . $registerError . '</p></div>';
        }
    ?>
</body>
</html>