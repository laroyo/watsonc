<?php
$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
$db = mysql_select_db("watsoncs", $con) or die("Couldn't select database");

$result =  mysql_query("SELECT * FROM  `csvinput` LIMIT 0 , 30");
$rows =  array();
while($row = mysql_fetch_array($result)){
	$rows[] = $row;
}
echo json_encode($rows);

?>