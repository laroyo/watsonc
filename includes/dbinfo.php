<?php
$host = 'localhost'; // Host name Normally 'LocalHost'
$user = 'watsoncs'; // MySQL login username
$pass = 'Tre2akEf'; // MySQL login password
$database = 'watsoncs'; // Database name

$con=mysql_connect($host, $user, $pass) or die("Couldn't make connection.");
mysql_select_db($database, $con)  or die("Couldn't select database");

?>
