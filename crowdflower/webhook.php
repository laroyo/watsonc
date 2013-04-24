<?php
include_once '/var/www/html/wcs/includes/dbinfo.php';
include_once '/var/www/html/wcs/includes/functions.php';
include_once '/var/www/html/wcs/crowdflower/extractinfo.php';
/*
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
*/
function getSignal() {
	$signal = $_POST["signal"];
	$payload = $_POST["payload"];

//	$fh = fopen("/var/www/cgi-bin/test", "a") or die("no such file");
//	fwrite($fh, $signal);
//	fwrite($fh, "ok");
	if($signal == "new_judgments") {
		//here we should update in the DB the number of judgments for a job
		//update the job with the following id (increment by 1 the number of judgments made and then recompute the job completion percentage):
		$array = objectToArray(json_decode($payload));
		$job_id = $array[0]["job_id"];
//		fwrite($fh, $job_id);
		$updatejudgments = mysql_query("Update history_table Set job_judgments_made = job_judgments_made + 1 Where job_id = '$job_id' ") or die(mysql_error());
		$getData =  mysql_query("select judgments_per_job, job_judgments_made from history_table where job_id = '$job_id' ") or die(mysql_error());	
		list($judgments_per_job, $job_judgments_made) = mysql_fetch_row($getData);
		$job_completion = $job_judgments_made / $judgments_per_job;
		$updateCompletion = mysql_query("Update history_table Set job_completion = $job_completion Where job_id = '$job_id' ") or die(mysql_error());

	}

	if($signal == "job_complete") {
		$array = objectToArray(json_decode($payload));
		$job_id = $array["id"];
//		fwrite($fh, $job_id);
		getResults($job_id);
   // change status to Finished and update run_time to final
	
      $getruntime = updateRuntime($job_id);
	  $updateruntime = mysql_query("Update history_table Set run_time = '$getruntime', status = 'Finished', status_change = 'disabled' Where job_id = '$job_id' ") or die(mysql_error());
		
	}
//fclose($fh);
}

getSignal();

?>
