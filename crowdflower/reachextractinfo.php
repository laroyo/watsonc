<?php


include("extractinfo.php");
$status = $_POST['status'];
$job_id = $_POST['job_id'];
	
getResults($job_id);

?>
