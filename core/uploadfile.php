<?php
session_start();

include('../config/db_connect.php');
if (isset($_POST) && !empty($_FILES['file'])) {
    $id = $_POST['id'];

    $image = $_FILES["file"];
    $imagePath = '';
    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
        $string = randomString(8);
        $imagePath = 'assets/images/' . $string . '/' . $image['name'];
        $imagePathTemp = '../assets/images/' . $string . '/' . $image['name'];

        mkdir(dirname($imagePathTemp));
        move_uploaded_file($image['tmp_name'], $imagePathTemp);
        $sql = "UPDATE users SET avatar='$imagePath' where id = '$id'";
        $res = pg_query($conn, $sql);
        $_SESSION['image'] = $imagePath;
        echo $imagePath;
    } else {
        die('Vui lòng upload file ảnh');
    }
}


function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        # code...
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }
    return $str;
}
