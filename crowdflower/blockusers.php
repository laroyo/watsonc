<html>
<head>
</head>
<body>

<?php

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

function blockSpamWorkers($job_id, $spam_workers) {
	$content_type = "application/json";
	$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
	$reason = "Your answers were not correct!";
	$data = array('flag' => $reason, 'persist' => 'true'); 

	foreach($spam_workers as $worker_id) {
		$url = "https://api.crowdflower.com/v1/jobs/$job_id/workers/$worker_id/flag?key=$api_key";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		curl_setopt($ch, CURLOPT_URL, $url);

		$response = json_decode(curl_exec($ch));
		$info = curl_getinfo($ch);
		print_r($info);
		$array = objectToArray($response);
		print_r($array);	
		print_r($response);
	}
}

$job_id = $_POST["job_id"];
$spam_workers = $_POST["spam_workers"]; 
blockSpamWorkers($job_id, $spam_workers);
?>


</body>
</html>
