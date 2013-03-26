<?php 

$content_type = "application/json";
$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
$url = "http://api.crowdflower.com/v1/jobs.json?key=".$api_key;
$uploadDirectory = "Files/";
$file = $_FILES['uploadedfile']['name'];
$file_name = $_FILES['uploadedfile']['tmp_name'];
$maxAutoId = 0;
$template_used = "";

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

/* create the settings' array */
$data = array();
$data["title"] = $_POST["title"]; //"Choose the valid RELATION(s) between the TERMS in the SENTENCE";
$data["judgments_per_unit"] = $_POST["judgments_per_unit"];
$data["max_judgments_per_worker"] = $_POST["max_judgments_per_worker"];
$data["units_per_assignment"] = $_POST["units_per_assignment"];
$data["max_judgments_per_ip"] = $_POST["max_judgments_per_ip"];
//$data["webhook_uri"] = "http://www.few.vu.nl/~oil200/webhook.php";
$data["webhook_uri"] = "webhook.php";
$data["send_judgments_webhook"] = "true";
$data["payment_cents"] = $_POST["payment"];
$data["execution_mode"] = "builder";  // To solve the prblem that CrowdFlower changed the interface
$data["worker_ui_remix"] = "0";

if ($_POST["template"] == "t1" || $_POST["template"] == "t3") {
	$myFile = "instructionsWithExtra";
	$fh = fopen($myFile, 'r');
	$theData = fread($fh, filesize($myFile));
	fclose($fh);
	$data["instructions"] = htmlspecialchars($theData); 
}
else {
	$myFile = "instructionsWithoutExtra";
	$fh = fopen($myFile, 'r');
	$theData = fread($fh, filesize($myFile));
	fclose($fh);
	$data["instructions"] = htmlspecialchars($theData); 
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

/* job id */
$job_id = $array["id"];

/* upload the data to be annotated into the new job */ 
$upload_query = "curl -T \"$file_name\" -H \"Content-Type:text/csv\" \"https://api.crowdflower.com/v1/jobs/$job_id/upload.json?key=$api_key\"";
$response = exec($upload_query);
//print_r(json_decode($response));

/* print the responses from the CrowdFlower server */
//print_r(objectToArray($response));
//print_r($array);

/* To assign the right template based on the user�s choice */
if ($_POST["template"] == "t1") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
    $template_used = "With definitions and with extra questions" ;
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t2") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithDefAndWithoutExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "With definitions but without extra questions" ;
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t3") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "Without definitions but with extra questions" ;
	$response = exec($update_cml_job);
}
else if ($_POST["template"] == "t4") {
	$update_cml_job = "curl -H \"application/json\" -X PUT -D - -d \"key=$api_key&job[cml]=`php cmlWithoutDefAndWithoutExtra.php`\" \"http://api.crowdflower.com/v1/jobs/$job_id.json\"";
	$template_used = "Without definitions and without extra questions" ;
	$response = exec($update_cml_job);
}

$update_job = "curl -X PUT -d \"job[worker_ui_remix]=false&job[execution_mode]=builder\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
$response = exec($update_job);
//print_r(objectToArray($response));
//print_r(json_decode($response));

/* create cURL query for including countries */
$included_countries_query = "curl -X PUT -d \"job[included_countries][]=AU&job[included_countries][]=CA&job[included_countries][]=GB&job[included_countries][]=US&job[included_countries][]=NL\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($included_countries_query);

/* create cURL query for excluding countries */
$excluded_countries_query = "curl -X PUT -d \"job[excluded_countries][]=IN&job[excluded_countries][]=CN&job[excluded_countries][]=ID\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($excluded_countries_query);

/* create cURL query for adding options */
$options_query = "curl -X PUT -d \"job[options][mail_to]=oana.inel@gmail.com&job[options][keywords]=relations-annotation natural-language-processing text-annotation medical-relations&job[options][include_unfinished]=true&job[options][tags]=natural-language-processing\" \"https://api.crowdflower.com/v1/jobs/".$job_id.".json?key=".$api_key."\"";
exec($options_query);

/* add channels to the job */
$set_channels_query = "curl -d \"channels[]=mob\" \"https://api.crowdflower.com/v1/jobs/$job_id/channels?key=$api_key\"";
exec($set_channels_query);

/*order the job */
$order_query = "curl -X POST -d \"debit[units_count]=7&channels[]=mob\" \"https://api.crowdflower.com/v1/jobs/$job_id/orders.json?key=$api_key\"";
$response = exec($order_query);
//print_r(json_decode($response));
//$array = objectToArray($response);
//print_r($array);

/*Store history data*/
//$count = count(file("/tmp/history.csv"));
$count = count(file("history.csv"));
if ($count == 0) {
	echo "aici";
//	$fp_history = fopen("/tmp/history.csv", 'w') or die("adssc");
	$fp_history = fopen("history.csv", 'w') or die("adssc");
	$table_header = array('job_id', 'file_name', 'no_sentences', 'no_judgments', 'max_judgments_per_worker',  'max_judgments_per_ip',  				'sentence_per_assignment', 'payment_per_sentence', 'total_payment_per_sentence', 'total_payment_job');
	fputcsv($fp_history, $table_header);
}
else {
//	$fp_history = fopen("/tmp/history.csv", 'a');
	$fp_history = fopen("history.csv", 'a');
	$row_history = array($count, $file, count(file($file_name)) - 1, $data["judgments_per_unit"], $data["max_judgments_per_worker"], $data["max_judgments_per_ip"], $data["units_per_assignment"], $data["payment_cents"], $data["payment_cents"] * $data["judgments_per_unit"], (count(file($file_name)) - 1) * $data["payment_cents"] * $data["judgments_per_unit"]);
	fputcsv($fp_history, $row_history);

}

/* connect to database watsoncs */
$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
$db = mysql_select_db("watsoncs", $con) or die("Couldn't select database");


/* save data array to the database */
$retrieveAutoid = mysql_query("select Max(auto_id) from cfinput", $con) or die(mysql_error());
if ( $retrieveAutoid != null )
{
	list($maxautoid) = mysql_fetch_row($retrieveAutoid);
	$maxAutoId = $maxautoid;
}

$maxAutoId += 1;
$created_date = date('Y-m-d H:i:s');
$type_of_units = "NA";
$units_per_job = count(file($file_name)) - 1;
$assignments_per_job = $units_per_job / $data["units_per_assignment"];
$judgements_per_assignment = $data["judgments_per_unit"] * $data["units_per_assignment"];
$judgements_per_job = $judgements_per_assignment * $assignments_per_job;
$payment_per_assignment = $data["payment_cents"] * $data["units_per_assignment"];
$payment_per_job = $units_per_job * $data["payment_cents"];
$total_payment_per_unit = $data["payment_cents"] * $data["judgments_per_unit"]; 
$total_payment_per_assignment = $data["units_per_assignment"] * $total_payment_per_unit; 
$total_payment_per_job = $units_per_job * $total_payment_per_unit;
$job_comment = $_POST["job_comment"];
/* when creating a new job, those are saved with default values */
$job_judgements_made = 0;  
$job_completion = 0.0;
$run_time = 0;
$status = "Running";

$insertSQL = "INSERT INTO cfinput  VALUES
('$maxAutoId', '$job_id', '{$data["title"]}', '$created_date', '$file', '$type_of_units', '$template_used',
'{$data["max_judgments_per_worker"]}', '{$data["max_judgments_per_ip"]}', '{$data["units_per_assignment"]}', '$units_per_job', '$assignments_per_job',
'{$data["judgments_per_unit"]}', '$judgements_per_assignment', '$judgements_per_job', 
'{$data["payment_cents"]}', '$payment_per_assignment', '$payment_per_job', '$total_payment_per_unit', '$total_payment_per_assignment', '$total_payment_per_job',
'$job_comment',
'$job_judgements_made', '$job_completion', '$run_time', '$status')";


if (!mysql_query($insertSQL,$con))
{
	die('Error: ' . mysql_error());
}
else
{
	echo "<b>A new job is created and saved.</b>";
	echo "<br />";
	echo "<b>Job ID: $job_id</b>";
	echo "<br />";
}

/* create link for testing the job - crowdflower internal interface */
$link = "http://crowdflower.com/judgments/mob/$job_id";
echo "<a href=$link> Test Job </a>";

/* Link to go back to Home Page */
echo "<br />";
echo "<a href='../index.php'>Back to Home Page</a>";

//curl -X POST -d "debit[units_count]=20&channels[]=mob" "https://api.crowdflower.com/v1/jobs/170725/orders.json?key=c6b735ba497e64428c6c61b488759583298c2cf3"
?>