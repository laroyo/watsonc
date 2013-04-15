<?php
/**
 * Interface to call the R scripts that will generate the Analysis Files. 
 * To be used either as an ajax petition (preferred) or directly from the browser (for testing, mostly). 
 **/

include('dataproc.inc');

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];   
} elseif ($argv[1] > 0) {
  $job_id = $argv[1]; 
} else {
  header('HTTP/1.1 400 Bad Request');    
  print("Error: you must provide a valid Job ID"); 
  print("Example: /genAnalysis.php?job_id=178259");      
}

$sent_res = exec('/usr/bin/Rscript /var/www/html/wcs/dataproc/sentenceMetrics.R '. $job_id); 

if($sent_res == 'OK')
  $worker_res = exec('/usr/bin/Rscript /var/www/html/wcs/dataproc/workerMetrics.R '. $job_id);

#$worker_res = 'OK'; 

if($sent_res == 'JOB_NOT_FOUND') {
  header('HTTP/1.1 400 Bad Request');
  print("Error: job $job_id not found<br>");
  return; 
} 

if($sent_res == 'OK' && isset($worker_res) && $worker_res == 'OK'){
  header('HTTP/1.1 200 OK');      
  print('--> Finished calculating workerMetrics for '. $job_id. "<br>");   
} else { 
  header('HTTP/1.1 500 Internal Server Error');      
  print("Error: internal server error");
}

?>