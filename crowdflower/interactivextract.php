<?php

// Script to interactively extract the results.csv file for a certain task. 
// FIXME: For testing, when the admin is finished, delete this file. 
include_once '../includes/functions.php';
include("extractinfo.php");

date_default_timezone_set("Europe/Amsterdam"); 

if(isset($_GET["job_id"])){
  $job_id = $_GET["job_id"]; 
} else if(isset($argv[1])) {
  $job_id = $argv[1]; 
}
if($job_id > 1 ){
  getResults($job_id); 
  print("OK");
} else {
  print('Error: you must provide a valid job_id'); 
}


?>