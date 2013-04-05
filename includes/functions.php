<?php 
include_once 'dbinfo.php';
$filesdir = "/var/www/files/";
function dieError($msg) {
	error_log($msg);
	die($msg);
}
function getFolder() {
	global $filesdir;
	date_default_timezone_set('UTC');
	//and create it if it is not there
	$checkyear    =    date("Y");
	$checkmonth    =    date("m");
	$checkday    =    date("d");
	// Checking for this year's folder
	if(!is_dir($filesdir.$checkyear)) {
		mkdir($filesdir.$checkyear,0755);
	}
	// Checking for this month's folder
	if(!is_dir($filesdir.$checkyear."/".$checkmonth)) {
		mkdir($filesdir.$checkyear."/".$checkmonth,0755);
	}
	// Checking for today's folder
	if(!is_dir($filesdir.$checkyear."/".$checkmonth."/".$checkday)) {
		mkdir($filesdir.$checkyear."/".$checkmonth."/".$checkday,0755);
	}
	return $filesdir.$checkyear."/".$checkmonth."/".$checkday."/";
}
function storeFile($filefieldname) {
	$storage_path = getFolder().uniqid()."_".basename( $_FILES[$filefieldname]['name']);
	$original_name = $_FILES[$filefieldname]['name'];
	$mime_type = $_FILES[$filefieldname]['type'];
	$filesize = $_FILES[$filefieldname]['size'];
	if(move_uploaded_file($_FILES[$filefieldname]['tmp_name'], $storage_path)) {
		$query="INSERT INTO `file_storage`(`original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`) 
		VALUES ('".$original_name."','".$storage_path."','".$mime_type."',".$filesize.",'".$_SERVER['REMOTE_USER']."')";
		mysql_query($query) or dieError("function: storeFile<br/>".$query."<br/>".mysql_error());
		return mysql_insert_id();
	} else{
		return null;
	}
}
function getOneFieldFromQuery($query,$field) {
	
	$data = mysql_query($query) or dieError("getOneFieldFromQuery<br/>".$query."<br/>".mysql_error());
	$row = mysql_fetch_assoc($data);
	return $row[$field];
}
function getLines($id) {
	$query="SELECT `storage_path` FROM `file_storage` WHERE `id` = ".$id;
	$filename = getOneFieldFromQuery($query, 'storage_path');
	$linecount = 0;
	$handle = fopen($filename, "r");
	if ($handle) {
		while(!feof($handle)){
			$line = fgets($handle);
			$linecount++;
		}
		fclose($handle);
	} else  {
		dieError("function: getLines cannot open:".$filename);
	}
	
	return $linecount;
}
?>
