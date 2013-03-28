<?php

include("connectDb.php");
include("extractinfo.php");

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

function getSignal() {
	$signal = $_POST["signal"];
	$payload = $_POST["payload"];

	if($signal == "new_judgments") {
		//here we should update in the DB the number of judgments for a job
		//update the job with the following id (increment by 1 the number of judgments made and then recompute the job completion percentage):
		$array = objectToArray(json_decode($payload));
		$job_id = $array[0]["job_id"];
	}

	if($signal == "job_complete") {
		$array = objectToArray(json_decode($payload));
		$job_id = $array[0]["job_id"];
		getResults($job_id);
	}
}

getSignal();

?>

