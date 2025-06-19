<?php
include "connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $adminName = $_POST['name'];

    $password = $_POST['password'];



    $stmt = $con->prepare("SELECT * FROM admins WHERE name = ?");

    $stmt->bind_param("s", $adminName);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();





        if (md5($password) == $row['password']) {

            $_SESSION['admin_name'] = $adminName;

            $_SESSION['admin_id'] = $row['id'];

            echo "<p>Login successful. Welcome, $adminName!</p>";

            echo "<script>window.location.href='/adminhome.php'</script>";
            // header("Location: adminhome.php");
            exit();
        } else {

            echo "<p>Invalid password.</p>";
        }
    } else {

        echo "<p>Invalid admin name.</p>";
    }



    $stmt->close();
}



$con->close();

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <style>
        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

            font-family: Arial, sans-serif;

        }



        body {

            display: flex;

            justify-content: center;

            align-items: center;

            min-height: 100vh;

            background-color: #f0f4f8;

        }



        .login-container {

            background-color: #fff;

            width: 350px;

            border-radius: 8px;

            overflow: hidden;

            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

        }



        .header {

            background-color: rgb(20, 64, 105);

            color: #fff;

            padding: 20px;

            text-align: center;

        }



        .header h2 {

            margin-bottom: 8px;

            font-size: 24px;

        }



        .header p {

            font-size: 14px;

        }



        .login-form {

            padding: 20px;

        }



        .login-form label {

            display: block;

            margin-bottom: 5px;

            font-size: 14px;

            color: #333;

        }



        .login-form input[type="text"],

        .login-form input[type="password"] {

            width: 100%;

            padding: 10px;

            margin-bottom: 15px;

            border: 1px solid #ddd;

            border-radius: 5px;

            font-size: 14px;

        }



        .login-form .remember-me {

            display: flex;

            align-items: center;

            margin-bottom: 20px;

        }



        .login-form .remember-me input[type="checkbox"] {

            margin-right: 5px;

        }



        .login-form .actions {

            display: flex;

            justify-content: space-between;

            align-items: center;

        }



        .login-form .actions a {

            text-decoration: none;

            color: black;

            font-size: 14px;

        }



        .login-form .login-btn {

            width: 100%;

            padding: 12px;

            background-color: rgb(20, 64, 105);

            color: #fff;

            border: none;

            border-radius: 5px;

            font-size: 16px;

            cursor: pointer;

            transition: background-color 0.3s;

        }



        .login-form .login-btn:hover {

            background-color: rgb(20, 64, 105);

        }
    </style>

</head>

<body>



    <div class="login-container">

        <div class="header">

            <h2>ADMIN LOGIN</h2>

            <p>Hello there, Sign in and start managing your website</p>

        </div>

        <form class="login-form" action="adminlogin.php" method="POST">

            <label for="name">Username</label>

            <input type="text" id="name" name="name" required>



            <label for="password">Password</label>

            <input type="password" id="password" name="password" required>



            <div class="remember-me">

                <input type="checkbox" id="remember" name="remember">

                <label for="remember">Remember Me</label>

            </div>



            <!-- <div class="actions">

            <a href="#">Forgot Password?</a>

        </div> -->



            <button type="submit" class="login-btn">LOGIN</button>

        </form>

    </div>



</body>

</html>