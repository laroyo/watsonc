<?php

$content_type = "application/json";
$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
$job_id = "177697";
$worker_id = "16357142";
$reason = "Your answers were not correct!";
$url = "http://api.crowdflower.com/v1/jobs.json?key=".$api_key;

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

/* wrap the attributes with the prefix "worker" */
function prefixDataKeys($data, $prefix) {
      $newdata = array();

      foreach ($data as $key => $value) {
          $newkey = "$prefix" . '[' . $key . ']';
          $newdata[$newkey] = $value;
      }

      return $newdata;
}


$data['reason'] = $reason;
$url = "https://api.crowdflower.com/v1/jobs/$job_id/workers/$worker_id/flag?key=$api_key";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(prefixDataKeys($data, "worker")));
curl_setopt($ch, CURLOPT_URL, $url);

$response = json_decode(curl_exec($ch));
$info = curl_getinfo($ch);
print_r($info);
$array = objectToArray($response);
print_r($array);
print_r($response);


/*

$update_cml_job = "curl -H \"application/json\" -X PUT -d \"key=$api_key?worker[reason]=$reason\" \"http://api.crowdflower.com/v1/jobs/$job_id/workers/$worker_id/flag\"";
$response = exec($update_cml_job);


$array = objectToArray($response);
print_r($array);
print_r($response);


*/
?>

