<?php
$host = 'localhost'; // Host name Normally 'LocalHost'
$user = 'root'; // MySQL login username
$pass = 'william1'; // MySQL login password
$database = 'watsoncs'; // Database name

$con=mysql_connect($host, $user, $pass) or die("Couldn't make connection.");
mysql_select_db($database, $con)  or die("Couldn't select database");

?>
