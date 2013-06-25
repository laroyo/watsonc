<?php
include_once '../../includes/dbinfo.php';
include_once '../../includes/functions.php';
if(isset($_GET['id']))
{
	$id    = $_GET['id'];
	$sql = "SELECT `original_name`, `storage_path`, `mime_type` FROM `file_storage` WHERE `id` = $id";
	$result = mysql_query($sql) or die('Error, query failed');
	list($original_name, $storage_path, $mime_type) =  mysql_fetch_array($result);
	
	$image = file_get_contents($storage_path);
	header("Content-type:".$mime_type);	
 	echo $image;
 	exit;
}

?>