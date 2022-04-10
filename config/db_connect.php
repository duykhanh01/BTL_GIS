<?php

//connect to the database
//$dbConn = pg_connect("host=<host> port=<port> dbname=<db_name> user=<db_user> password=<db_pass>");
$conn = pg_connect('host=localhost port=5432 dbname=btl_gis user=postgres password=1');
// check connection
if (!$conn) {
	echo 'Connection error: ' . mysqli_connect_error();
}




// try {
// 	$pdo = new PDO('mysql:host=localhost;port=3306;dbname=example', 'khanh', 'Duykhanh2001');
// 	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
// 	die("Connect Fail");
// 	$e->getMessage();
// }
