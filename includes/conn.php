<?php
// $conn = new mysqli('192.168.10.112', 'dbmp_admin', 'DBMP_@dm1n', 'palaystat');
$conn = new mysqli('localhost', 'root', '', 'palaystat');
if($conn -> connect_errno > 0){
die('Unable to connect to database [' . $conn->connect_error . ']');
}
mysqli_set_charset($conn,"utf8");

$GLOBALS['htp'] = "http";
?>