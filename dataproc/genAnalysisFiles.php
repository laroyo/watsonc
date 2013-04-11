<?php

include('dataproc.inc');

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];  
  print("processing data for job: $job_id\n"); 
} else {
  $job_id = 178934; 
}

exec('/usr/bin/Rscript /var/www/html/wcs/dataproc/sentenceMetrics.R '. $job_id); 
exec('/usr/bin/Rscript /var/www/html/wcs/dataproc/workerMetrics.R '. $job_id); 
   		
?>