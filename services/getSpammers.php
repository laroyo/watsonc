<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';

	$job_id    = $_POST['job_id'];
	$sql = "SELECT worker_ids
            		FROM  filtered_workers  
            		Where set_id = '$job_id'";
	$result = mysql_query($sql) or die('Error, query failed');
	list($worker_ids) =  mysql_fetch_array($result);
	
	
 	echo $worker_ids;
 


?>