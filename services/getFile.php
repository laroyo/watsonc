<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
if(isset($_GET['id']))
{
	$id    = $_GET['id'];
	//$sql = "SELECT `original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`, `created` FROM `file_storage` WHERE `id` = 1 LIMIT 0, 30 ";
	$sql = "SELECT `original_name`, `storage_path`, `mime_type` FROM `file_storage` WHERE `id` = $id";
	$result = mysql_query($sql) or die('Error, query failed');
	list($original_name, $storage_path, $mime_type) =  mysql_fetch_array($result);
	if (empty($content) ) {
		$handle = fopen( $storage_path, "r");
		$content = stream_get_contents($handle);
	}
	header("Content-type:".$mime_type);
	header("Content-Disposition: attachment; filename=$original_name");
	echo $content;
	exit;
}
?>