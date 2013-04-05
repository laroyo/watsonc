<?php 
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
$fileid = storeFile('rawuploadedfile');
$lines = getLines($fileid) ;
$filetitle =  $_POST['title'];
$filefreeComment =  $_POST['freeComment'];

$query="INSERT INTO `raw_file`( `seedrelationname`, `fileid`, `lines`, `comment`, `createdby`) 
VALUES ('".$filetitle."',".$fileid.",".$lines.",'".$filefreeComment."','".$_SERVER['REMOTE_USER']."')";
mysql_query($query) or dieError("uploadRaw 2:<br/>".$query."<br/>".mysql_error());
header('Location: ../index.php'); //back home
?>