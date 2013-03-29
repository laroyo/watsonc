<?php

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


function getResults($job_id) {
	$api_key = "b5e3b32b4d29d45c16dc09274e099f731237e35f";
	//$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";

	/* get all the units from a job */
	$units_list_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/units.json?key=$api_key\"";
	$result_exec = exec($units_list_query);
	$result = objectToArray(json_decode($result_exec));
	/* save the units id */
	$units_id = array_keys($result);

	/* print results into files */

		$user = posix_getuid();
		$userinfo = posix_getpwuid($user);
		$t = time();
		
		if (!is_dir("/var/www/files/". date('Y/m/d',$t))) {
		  mkdir("/var/www/files/". date('Y/m/d',$t));
		}

                $fp_results = fopen("/var/www/files/". date('Y/m/d',$t) ."/". $job_id ."_file_results.csv",'w');
                $fp_overview = fopen("/var/www/files/". date('Y/m/d',$t) ."/".$job_id ."_overview.csv",'w');


	$table_header_results = array('unit_id', 'worker_id', 'worker_trust', 'external_type', 'step_1_select_the_valid_relations',  'step_2b_if_you_selected_none_in_step_1_explain_why',  'step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1', 'started_at', 'created_at', 'term1', 'term2', 'sentence');
	$table_header_overview = array('job_id', 'unit_id', 'created_at', 'updated_at', 'agreement', 'term1', 'term2', 'sentence');

	fputcsv($fp_results, $table_header_results);
	fputcsv($fp_overview, $table_header_overview);

	/* get all the information about an unit */
	for ($i = 0; $i < count($units_id); $i ++) {
		$unit_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/units/$units_id[$i].json?key=$api_key\"";
		$result_exec = exec($unit_query);
		$result = objectToArray(json_decode($result_exec));
		$results = $result["results"];
		$judgments = $results["judgments"];
		$row_overview = array($result["job_id"], $result["id"], formatDateAndTime($result["created_at"]), formatDateAndTime($result["updated_at"]), $result["agreement"], $result["data"]["term1"], $result["data"]["term2"], $result["data"]["sentence"]);

		fputcsv($fp_overview, $row_overview);

		for ($j = 0; $j < count($judgments); $j ++) {
			$row_result = array($judgments[$j]["unit_id"], $judgments[$j]["worker_id"], $judgments[$j]["worker_trust"], $judgments[$j]["external_type"]);
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
			fputcsv($fp_results, $row_result);
		}
	}
	fclose($fp_results);
	fclose($fp_overview);
}
?>
