<?php
$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
$db = mysql_select_db("test", $con) or die("Couldn't select database");

$sql =  mysql_query("SELECT * FROM `files` LIMIT 0, 30 ");
$files =  array();
while($row = mysql_fetch_assoc($sql)){
	array_push($files,$row);
}
echo json_encode($files);
?>





