<?php
include_once '../../includes/dbinfo.php';
$content_type = "application/json";
// $api_key = "c6b735ba497e64428c6c61b488759583298c2cf3"; 
$api_key = "b5e3b32b4d29d45c16dc09274e099f731237e35f";
$url = "http://api.crowdflower.com/v1/jobs.json?key=".$api_key;
$status = $_POST['status'];
$job_id = $_POST['job_id'];


/* useful functions for printing the results from the web server */
function objectToArray($obj) {
	if (is_object($obj)) {
		$obj = get_object_vars($obj);
	}
	if (is_array($obj)) {
		return array_map(__FUNCTION__, $obj);
	}
	else {
		return $obj;
	}
}

function arrayToObject($d) {
	if (is_array($d)) {
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

//get the status of the job
$job_status = "curl -H \"application/json\" \"http://api.crowdflower.com/v1/jobs/$job_id.json?key=$api_key\"";
$response = exec($job_status);
$response_array = objectToArray(json_decode($response));

if($response_array["state"] == "running") {
	$pause_job = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/pause.json?key=$api_key\"";
	$pause_response = exec($pause_job);
	$pause_response_array = json_decode($pause_response);

	if (array_key_exists("success", $pause_response_array)) {
		echo "The job was paused";
    //  update the database
		$updateDB = mysql_query("Update history_table Set status = '$status' Where job_id = '$job_id' ") or mysql_error();
	}
}
else {
	echo "The job cannot be paused";
}
?>
