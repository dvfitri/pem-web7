<?php
session_start();
require './../config/db.php';

if (isset($_POST['submit'])) {

    global $db_connect;

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Confirming the password
    if ($confirm != $password) {
        echo "Password tidak sesuai dengan konfirmasi password";
        die;
    }

    // Checking if the email is already in use
    $usedEmail = mysqli_query($db_connect, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($usedEmail) > 0) {
        echo "Email sudah digunakan";
        die;
    }

    // Using password_hash to securely store the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    $created_at = date('Y-m-d H:i:s', time());

    // Using prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($db_connect, "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $password, $created_at);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Retrieving user data
    $getuserdata = mysqli_query($db_connect, "SELECT name, role FROM users WHERE email = '$email'");
    $sessionData = mysqli_fetch_assoc($getuserdata);
    $_SESSION['name'] = $sessionData['name'];
    $_SESSION['role'] = $sessionData['role'];

    header('Location:./../profile.php');
}