<?php

session_start();

include('config/db_connect.php');

if (!$_SESSION['email']) {
	header("Location: login.php");
}



?>

<!DOCTYPE html>
<html lang="en">

<?php include('templates/header.php'); ?>





<?php include('templates/footer.php'); ?>


</html>