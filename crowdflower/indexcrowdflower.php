<?php 
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
$content_type = "application/json";
//$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
$api_key = "b5e3b32b4d29d45c16dc09274e099f731237e35f";

$url = "http://api.crowdflower.com/v1/jobs.json?key=".$api_key;
$uploadDirectory = "Files/";
//$file = $_FILES['uploadedfile']['name'];
//$file_name = $_FILES['uploadedfile']['tmp_name'];
$file_id = $_POST["fileid"];
$template_used = "";
$template_info = "";

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

/* wrap the attributes with the prefix "job" */
function prefixDataKeys($data, $prefix) {
      $newdata = array();

      foreach ($data as $key => $value) {
          $newkey = "$prefix" . '[' . $key . ']';
          $newdata[$newkey] = $value;
      }

      return $newdata;
}


/* Get file data from the database based on file_id */ 
$getdata = mysql_query("SELECT b.filter_named, s.original_name, s.storage_path
FROM  batches_for_cf b
INNER JOIN file_storage as s on b.file_id = s.id WHERE s.id = '$file_id' ") or die('Error: ' . mysql_error());

list($filter_applied, $file, $file_name) = mysql_fetch_row($getdata);


/* create the settings' array */
$data = array();
$data["title"] = $_POST["title"]; //"Choose the valid RELATION(s) between the TERMS in the SENTENCE";
$data["judgments_per_unit"] = $_POST["judgments_per_unit"];
$data["max_judgments_per_worker"] = $_POST["max_judgments_per_worker"];
$data["units_per_assignment"] = $_POST["units_per_assignment"];
$data["max_judgments_per_ip"] = $_POST["max_judgments_per_worker"];
//$data["webhook_uri"] = "http://www.few.vu.nl/~oil200/webhook.php";
$data["webhook_uri"] = "http://129.35.251.201/cgi-bin/webhook.php";
$data["send_judgments_webhook"] = "true";
$data["payment_cents"] = $_POST["payment"];
//$data["execution_mode"] = "builder";
//$data["worker_ui_remix"] = "0";
$calibrated_unit_time = $_POST["seconds_per_unit"];

if ($_POST["template"] == "t1" || $_POST["template"] == "t2" || $_POST["template"] == "t1b" || $_POST["template"] == "t2b") {
	$myFile = "instructionsWithExtra";
	$fh = fopen($myFile, 'r');
	$theData = fread($fh, filesize($myFile));
	fclose($fh);
	$data["instructions"] = htmlspecialchars_decode(htmlspecialchars($theData)); 
}
else if ($_POST["template"] == "th") {
	$myFile = "instructionsWithExtraAndHighlight";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $data["instructions"] = htmlspecialchars_decode(htmlspecialchars($theData));
}
else if ($_POST["template"] == "td") {
	$myFile = "instructionsDirection";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $data["instructions"] = htmlspecialchars_decode(htmlspecialchars($theData));
}
else {
	$myFile = "instructionsWithoutExtra";
	$fh = fopen($myFile, 'r');
	$theData = fread($fh, filesize($myFile));
	fclose($fh);
	$data["instructions"] = htmlspecialchars_decode(htmlspecialchars($theData)); 
}

if ($_POST["template"] == "t1b" || $_POST["template"] == "t2b" || $_POST["template"] == "t1ab" || $_POST["template"] == "t2ab") {
	$myJS = "jsRelations";
	$fh = fopen($myJS, 'r');
	$theData = fread($fh, filesize($myJS));
	fclose($fh);
	$data["js"] = htmlspecialchars_decode(htmlspecialchars($theData));
}
if ($_POST["template"] == "td") {
        $myFile = "cssDirection";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $data["css"] = htmlspecialchars_decode(htmlspecialchars($theData));
}
if ($_POST["template"] == "th") {
        $myFile = "jsRelHighlight";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $data["js"] = htmlspecialchars_decode(htmlspecialchars($theData));

	$myFile = "cssRelHighlight";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        $data["css"] = htmlspecialchars_decode(htmlspecialchars($theData));
}



/* create the job with the specified settings */
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(prefixDataKeys($data, "job")));
curl_setopt($ch, CURLOPT_URL, $url);

$response = json_decode(curl_exec($ch));
$info = curl_getinfo($ch);
$array = objectToArray($response);
//print_r(objectToArray($array));
//print_r($info);

/* job id */
$job_id = $array["id"];

/* upload the data to be annotated into the new job */ 
$upload_query = "curl -T \"$file_name\" -H \"Content-Type:text/csv\" \"https://api.crowdflower.com/v1/jobs/$job_id/upload.json?key=$api_key\"";
$response = exec($upload_query);
//print_r(json_decode($response));

/* print the responses from the CrowdFlower server */
//print_r(objectToArray($response));
//print_r($array);

if ($_POST["template"] == "t1") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
 $template_used = "T1" ;
 $template_info = "T1: Relations with (mouse-over) definitions and extra questions required";
 $response = exec($update_cml_job);
}
else if ($_POST["template"] == "t2") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T2" ;
	$template_info = "T2: Relations with (text) definitions and extra questions required";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t1a") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithoutExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T1A" ;
	$template_info = "T1A: Relations with (mouse-over) definitions and without extra questions";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t2a") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndWithoutExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T2A" ;
	$template_info = "T2A: Relations with (text) definitions and extra without questions";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t1b") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithExtraAuto.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
 $template_used = "T1B" ;
 $template_info = "T1B: Relations with (mouse-over) definitions, extra questions required and automatic text field";
 $response = exec($update_cml_job);
}
else if ($_POST["template"] == "t2b") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndExtraAuto.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T2B" ;
	$template_info = "T2B: Relations with (text) definitions and extra questions required and automatic text field";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t1ab") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithoutExtraAuto.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T1AB" ;
	$template_info = "T1AB: Relations with (mouse-over) definitions and without extra questions and automatic text field";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t2ab") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndWithoutExtraAuto.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "T2AB" ;
	$template_info = "T2AB: Relations with (text) definitions and without extra questions and automatic text field ";
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "th") {
        $update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlRelHighlight.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
        $template_used = "TH" ;
        $template_info = "TH: Relations with (mouse-over) definitions, extra questions, automatic text field and words highlighting";
        $response = exec($update_cml_job);
}
else if ($_POST["template"] == "td") {
        $update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlDirection.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
        $template_used = "TD" ;
        $template_info = "TD: Relation direction";
        $response = exec($update_cml_job);
}


//$update_job = "curl -X PUT -d \"job[worker_ui_remix]=false&job[execution_mode]=builder\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
//$response = exec($update_job);
//print_r(objectToArray($response));
//print_r(json_decode($response));

/* create cURL query for including countries */
$included_countries_query = "curl -X PUT -d \"job[included_countries][]=AU&job[included_countries][]=CA&job[included_countries][]=GB&job[included_countries][]=US\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($included_countries_query);

/* create cURL query for excluding countries */
$excluded_countries_query = "curl -X PUT -d \"job[excluded_countries][]=IN&job[excluded_countries][]=CN&job[excluded_countries][]=ID\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($excluded_countries_query);

/* create cURL query for adding options */
$options_query = "curl -X PUT -d \"job[options][calibrated_unit_time]=$calibrated_unit_time&job[options][mail_to]=oana.inel@gmail.com&job[options][keywords]=relations-annotation natural-language-processing text-annotation medical-relations&job[options][include_unfinished]=true&job[options][tags]=natural-language-processing<br>\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($options_query);

/* check what kind of channels need to be added */
$channels = array();
$channel_type = $_POST["channels"];
$channels_used = "";
if ($channel_type == "c1") {
	$channels_used = "amt";
	array_push($channels, "amt");
	array_push($channels, "mob");
}
else if ($channel_type == "c2") {
	$channels_used = "multiple";
	$get_channels_query = "curl \"https://api.crowdflower.com/v1/jobs/$job_id/channels?key=$api_key\"";
	$response = exec($get_channels_query);	
//	print_r(objectToArray(json_decode($response)));
	$array = objectToArray(json_decode($response));
	
//	print_r($array["available_channels"]);
	foreach($array["available_channels"] as $value) {
		array_push($channels, $value);
	}
}
else if ($channel_type == "c3") {
	$channels_used = "last used ones";
	$array_push($channels, "amt");
	$array_push($channels, "neodev");
	$array_push($channels, "prodege");
	$array_push($channels, "crowdguru");
	$array_push($channels, "vivatic");
	$array_push($channels, "zoombucks");
}

$channels_string = "";
foreach($channels as $value) {
	$channels_string .= "channels[]=".$value."&";
}
$channels_string = substr($channels_string, 0, -1);

/* add channels to the job */
$set_channels_query = "curl -d \"$channels_string\" \"https://api.crowdflower.com/v1/jobs/$job_id/channels?key=$api_key\"";
exec($set_channels_query);

/*order the job */
$order_query = "curl -X POST -d \"debit[units_count]=7&$channels_string\" \"https://api.crowdflower.com/v1/jobs/$job_id/orders.json?key=$api_key\"";
//$response = exec($order_query);
//print_r(json_decode($response));
//$array = objectToArray($response);
//print_r($array);

/* create link for testing the job - crowdflower internal interface */
//$link = "http://crowdflower.com/judgments/mob/$job_id";
//echo "<a href=$link> Test Job </a>";

/* save data array to the database */

$nr_sentences_file = $_POST["sentences"];
$units_per_job = count(file($file_name)) - 1;
$units_per_assignment = $data["units_per_assignment"];
$judgments_per_job = $units_per_job * $data["judgments_per_unit"];
$seconds_per_assignment = $_POST["seconds_per_assignment"];
$payment_per_assignment = $_POST["payment"];
$total_payment_per_job = $_POST["payment_per_job"];
$total_payment_per_unit = $_POST["payment_per_sentence"];
$payment_per_unit = $_POST["payment"] / $units_per_assignment;
$job_comments = $_POST["job_comment"];
$payment_per_hour = $_POST["payment_per_hour"];
/* when creating a new job, those are saved with default values */
$origin = "CF";
$job_judgments_made = 0;
$job_completion = 0.0;
$run_time = 0;
$status = "Published";
$status_change = "abled";
$checkbox_check = "disabled";


$insertSQL = "INSERT INTO history_table ( job_id, origin, job_title , created_by , cfbatch_id, file_name, nr_sentences_file, type_of_units, template, template_info,
max_judgments_per_worker, max_judgments_per_ip, units_per_assignment, units_per_job,
judgments_per_unit, judgments_per_job, seconds_per_unit, seconds_per_assignment,
payment_per_unit, payment_per_assignment,total_payment_per_unit,  total_payment_per_job,
payment_per_hour, channels_used, job_comments, 
job_judgments_made, job_completion, run_time, status, status_change, checkbox_check)
VALUES
( '$job_id', '$origin', '{$data["title"]}', '{$_SERVER["REMOTE_USER"]}', '$file_id' , '$file', '$nr_sentences_file', '$filter_applied', '$template_used', '$template_info',
'{$data["max_judgments_per_worker"]}', '{$data["max_judgments_per_ip"]}', '{$data["units_per_assignment"]}', '$units_per_job',
'{$data["judgments_per_unit"]}', '$judgments_per_job', '$calibrated_unit_time', '$seconds_per_assignment',
'$payment_per_unit', '$payment_per_assignment','$total_payment_per_unit',  '$total_payment_per_job',
 '$payment_per_hour', '$channels_used', '$job_comments',
'$job_judgments_made', '$job_completion', '$run_time', '$status', '$status_change', '$checkbox_check')";

if (!mysql_query($insertSQL,$con))
{
	die('Error: ' . mysql_error());
}
else
{
	$getJobs = "SELECT job_id FROM batches_for_cf WHERE file_id = $file_id";
	$jobs_used = getOneFieldFromQuery($getJobs, 'job_id');
	if (strcmp($jobs_used, "no_job") == 0) {
		$updateQuery = mysql_query("UPDATE batches_for_cf SET job_id = $job_id WHERE file_id = $file_id");
	}
	else {
		$updateQuery = mysql_query("UPDATE batches_for_cf SET job_id = '$jobs_used".", ".$job_id."' WHERE file_id = $file_id");
	}
		echo "<b>A new job is created and saved.</b>";
		echo "<br />";
		echo "<b>Job ID: $job_id</b>";
		echo "<br />";
//  header("Location: index.php");
//	echo "<a href='../index.php'>Back to Home Page</a>";
		echo "<a href='../GUI/index.php'>Back to Home Page</a>";
	
	// To freash datatable in GUI
	//echo '<script>parent.window.location.reload(true);</script>';
}

?>
