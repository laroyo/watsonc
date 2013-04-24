<?php

include_once('../includes/functions.php'); 
include_once '../includes/dbinfo.php';

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

/* transforms the time and date in a more readable format */
function formatDateAndTime($input) {
	list($date, $time) = explode('T', $input);
	list($year, $month, $day) = explode('-', $date);
	list($exact_time, $nothing) = explode('+', $time);
	$output = $month . "/" . $day . "/" . $year . " " . $exact_time;
	return $output;
}

/* parse the response for extracting the relation between terms */
function extractChoice($input) {
	$pattern = "/\[[A-Za-z\_\:]*\]/";
	preg_match($pattern, $input, $matches);
	$choice = str_replace(":", "", $matches[0]);
	return $choice; 
}

function format_interval(DateInterval $interval) {
    $result = "";
    if ($interval->y > 0) { $result .= $interval->format("%y years "); }
    if ($interval->m > 0) { $result .= $interval->format("%m months "); }
    if ($interval->d > 0) { $result .= $interval->format("%d days "); }
    if ($interval->h > 0) { $result .= $interval->format("%h hours "); }
    if ($interval->i > 0) { $result .= $interval->format("%i minutes "); }
    if ($interval->s > 0) { $result .= $interval->format("%s seconds "); }

    return $result;
}

function to_seconds(DateInterval $interval) { 
        return ($interval->y * 365 * 24 * 60 * 60) + 
               ($interval->m * 30 * 24 * 60 * 60) + 
               ($interval->d * 24 * 60 * 60) + 
               ($interval->h * 60 * 60) + 
               ($interval->i * 60) + 
               $interval->s; 
} 


function getResults($job_id) {
	date_default_timezone_set('UTC');
	$api_key = "b5e3b32b4d29d45c16dc09274e099f731237e35f";
//	$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
	$origin = "CF";

	/* get all the units from a job */
	$units_list_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/units.json?key=$api_key\"";
	$result_exec = exec($units_list_query);
	$result = objectToArray(json_decode($result_exec));
	/* save the units id */
	$units_id = array_keys($result);


	$job_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id.json?key=$api_key\"";
	$result_exec = exec($job_query);
	$result = objectToArray(json_decode($result_exec));
//	$startTimeJob = new DateTime(formatDateAndTime($result["created_at"]));
//	$endTimeJob = new DateTime(formatDateAndTime($result["completed_at"]));
//	$run_time = format_interval($startTimeJob->diff($endTimeJob));

	/* get all the channels that were used for the job */
	$get_channels_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/channels?key=$api_key\"";
	$response = exec($get_channels_query);	
	$array = objectToArray(json_decode($response));
	$allChannels = array();
	foreach($array["available_channels"] as $value) {
		$allChannels[$value] = 0;
	}

	/* save all the times */
	$startingTimeArray = array();
	$endingTimeArray = array();
	$timeDifference = array();

	/* print results into files */

	$table_header_results = array('unit_id', 'worker_id', 'worker_trust', 'external_type', 'step_1_select_the_valid_relations',  'step_2b_if_you_selected_none_in_step_1_explain_why',  'step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1', 'started_at', 'created_at', 'term1', 'term2', 'sentence');
	$table_header_overview = array('job_id', 'unit_id', 'created_at', 'updated_at', 'agreement', 'term1', 'term2', 'sentence');
	
	$results_content = array($table_header_results);
	$overview_content = array($table_header_overview); 

	/* get all the information about an unit */
	for ($i = 0; $i < count($units_id); $i ++) {
		$unit_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/units/$units_id[$i].json?key=$api_key\"";
		$result_exec = exec($unit_query);
		$result = objectToArray(json_decode($result_exec));
		$results = $result["results"];
		$judgments = $results["judgments"];
		$row_overview = array($result["job_id"], $result["id"], formatDateAndTime($result["created_at"]), formatDateAndTime($result["updated_at"]), $result["agreement"], $result["data"]["term1"], $result["data"]["term2"], $result["data"]["sentence"]);

		//fputcsv($fp_overview, $row_overview);
		array_push($overview_content,$row_overview); 

		for ($j = 0; $j < count($judgments); $j ++) {
			$row_result = array($judgments[$j]["unit_id"], $judgments[$j]["worker_id"], $judgments[$j]["worker_trust"], $judgments[$j]["external_type"]);
			$allChannels[$judgments[$j]["external_type"]] ++;
			$choices = "";
			if (isset($judgments[0]["data"]["select_the_valid_relations"])) {
				for ($k = 0; $k < count($judgments[$j]["data"]["select_the_valid_relations"]); $k ++) {
					$choices .= extractChoice($judgments[$j]["data"]["select_the_valid_relations"][$k]) . "\n";
				}
			}
			if (isset($judgments[0]["data"]["step_1_select_the_valid_relations"])) {
				for ($k = 0; $k < count($judgments[$j]["data"]["step_1_select_the_valid_relations"]); $k ++) {
					$choices .= extractChoice($judgments[$j]["data"]["step_1_select_the_valid_relations"][$k]) . "\n";
				}
			}
			
				$choices = substr($choices, 0, -1);
				array_push($row_result, $choices, $judgments[$j]["data"]["step_2b_if_you_selected_none_in_step_1_explain_why"], $judgments[$j]["data"]["step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"], formatDateAndTime($judgments[$j]["started_at"]), formatDateAndTime($judgments[$j]["created_at"]), $judgments[$j]["unit_data"]["term1"], $judgments[$j]["unit_data"]["term2"], $judgments[$j]["unit_data"]["sentence"]);
			//fputcsv($fp_results, $row_result);
			array_push($results_content, $row_result);
			
			$first_date = new DateTime(formatDateAndTime($judgments[$j]["started_at"]));
			$second_date = new DateTime(formatDateAndTime($judgments[$j]["created_at"]));
			array_push($startingTimeArray, $first_date);
			array_push($endingTimeArray, $second_date);
			
			$difference = to_seconds($first_date->diff($second_date));
			//echo $difference;
			array_push($timeDifference, $difference); 
		}
	}

//	print_r($allChannels);
	$noJudgments = 0;
	foreach($allChannels as $value) {
		$noJudgments += $value;
	}
	$percentageChannels = array();
	$channel_percentage = "";
	foreach($allChannels as $key => $value) {
		if ($value > 0) {
			$percentageChannels[$key] = (100 * $value) / $noJudgments;
			$percentageChannels[$key] .= "%";
			$channel_percentage .= $key."-".$percentageChannels[$key].", ";
		}
	}
	$channel_percentage = substr($channel_percentage, 0, strlen($channel_percentage) - 2);
//	print_r($channel_percentage);

	//print_r($timeDifference);
	$max_time = gmdate('H:i:s', max($timeDifference));
	$min_time = gmdate('H:i:s', min($timeDifference));
	$avg_time = gmdate('H:i:s', array_sum($timeDifference) / $noJudgments);	

	$updatehistorytable = mysql_query("Update history_table Set origin='$origin', channels_percentage='$channel_percentage', min_time_unitworker='$min_time', max_time_unitworker='$max_time', avg_time_unitworker='$avg_time' Where job_id = '$job_id' ") or die(mysql_error());

	$results_file_info = array(
	    'name' => $job_id. '_file_results.csv',
	    'job_id' => $job_id,
	    'mime_type' => 'text/csv',
	    'file_type' => 'CFlowerResultFiles'
        );
	$overview_file_info = array(
	    'name' => $job_id. '_file_results.csv',
	    'job_id' => $job_id,
	    'mime_type' => 'text/csv',
	    'file_type' => 'CFlowerResultFiles'
	); 
	
	//FIXME: Specify a correct user as creator of the files (instead of 'script')	
	storeContentInFile($results_file_info,$results_content,'script'); 
	//storeContentInFile($overview_file_info,$results_content,'script'); 
	/* fclose($fp_results); */
	/* fclose($fp_overview); */
}
//getResults("185404");
?>
