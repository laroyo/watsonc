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
//print_r($response_array);

if($response_array["state"] == "unordered" || $response_array["state"] == "canceled") {
	$delete_job = "curl -H \"application/json\" -X DELETE \"http://api.crowdflower.com/v1/jobs/$job_id.json?key=$api_key\"";
	$delete_response = exec($delete_job);
	$delete_response_array = objectToArray(json_decode($delete_response));
	print_r($delete_response_array);

	if (array_key_exists("message", $delete_response_array)) {
		echo "The job was deleted";
    //  update the database
	//  $updateDB = mysql_query("Update history_table Set status = '$status' Where job_id = '$job_id' ") or mysql_error();	
	//  get the final run time (fixed)	
		$getruntime = updateRuntime($job_id);
	//  $updateDB = mysql_query("Update history_table Set run_time = '$getruntime', status = '$status', status_change = 'disabled', checkbox_check = 'abled' Where job_id = '$job_id' ") or mysql_error();
		$updateDB = mysql_query("Update history_table Set run_time = '$getruntime', status = '$status', status_change = 'disabled' Where job_id = '$job_id' ") or mysql_error();	
	}
}
else {
	echo "The job cannot be deleted";
}
?>
