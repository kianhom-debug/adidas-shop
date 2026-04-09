<?php
session_start();
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // check password match
    if ($password != $confirm) {
        echo "Password not match!";
    } else {

        // hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // check email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            echo "Email already exists!";
        } else {

            // insert user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'member')");
            $stmt->execute([$name, $email, $hash]);

            echo "Register success!";
        }
    }
}
?><!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<form method="POST">
    Name: <input type="text" name="name"><br><br>
    Email: <input type="email" name="email"><br><br>
    Password: <input type="password" name="password"><br><br>
    Confirm Password: <input type="password" name="confirm_password"><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>