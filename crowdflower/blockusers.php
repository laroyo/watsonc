<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
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

function blockSpamWorkers($job_id, $reason, $spam_workers) {
	$content_type = "application/json";
	$api_key = "b5e3b32b4d29d45c16dc09274e099f731237e35f";
//	$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
	$data = array('flag' => $reason, 'persist' => 'true'); 
	$responseString = "";
	foreach($spam_workers as $worker_id) {
		$url = "https://api.crowdflower.com/v1/jobs/$job_id/workers/$worker_id/flag?key=$api_key";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		curl_setopt($ch, CURLOPT_URL, $url);

		$response = json_decode(curl_exec($ch));
		$info = curl_getinfo($ch);
		$array = objectToArray($response);
		$keys = array_keys($array);
		if ($keys[0] == "error") {
			$responseString .= "An error occurred when blocking contributor $worker_id! ";
		}
		else if ($keys[0] == "warning") {
			$responseString .= "Contributor $worker_id has already been flagged! ";
		}
		else if ($keys[0] == "success") { 
			$responseString .= "Contributor $worker_id flagged! ";  
			
			// update blocked_workers table
			$insertSQL = "INSERT INTO blocked_workers (worker_id, job_id, reason, created_by) 
				      VALUES ( '$worker_id', '$job_id', '$reason', '{$_SERVER["REMOTE_USER"]}')";
			if (!mysql_query($insertSQL, $con))
			{
				die('Error: ' . mysql_error());
			}
		}
	}
	return $responseString;
}

$job_id = $_POST["jobId"];
$reason = $_POST["reason"];
$spam_workers = $_POST["workerId"];
$scriptResponse = blockSpamWorkers($job_id, $reason, $spam_workers);
echo $scriptResponse;
?>
