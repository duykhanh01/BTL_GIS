<?php

include('../config/db_connect.php');
session_start();
if (isset($_POST['change-pass'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM users where email = '$email'";
    $res = mysqli_query($conn, $sql);
    $pass = $_POST['password'];
    $newpass = $_POST['newpassword'];
    $cpass = $_POST['cpassword'];
    $pass_saved = $user['password'];
    if (!password_verify($pass, $pass_saved)) $errors[] = 'Mật khẩu hiện tại không chính xác';

    if ($newpass != $cpass) $errors[] = 'Mật khẩu không khớp';
    if (empty($errors)) {
        $pass_hash = password_hash($newpass, PASSWORD_DEFAULT);
        $sql = "UPDATE users set password = '$pass_hash' where email = '$email'";
    }
}
