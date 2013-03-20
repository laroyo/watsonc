<?php

include('dataproc.inc');

if(isset($argv[1])){
  $job_id = $argv[1];  
}

if(isset($job_id)){
  print("processing data for job: $job_id\n"); 
} 


$dp  = new DataProc($job_id); 

print('Number of filteredJudgments: ' . $dp->getFilteredJudgements() . "\n");

print("Workers labelled as spam: \n");

$res = $dp->getSpamLabels();
foreach($res as $spammer_id){
  print("$spammer_id\n"); 
}


?>