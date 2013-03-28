<?php

include('dataproc.inc');

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];  
  print("processing data for job: $job_id\n"); 
} else {
  /* print "Please, specify the Job ID of the job that should be processed on the URL<br>";  */
  /* print "Example: http://eculture2.cs.vu.nl/dataproc/genAnalysisFiles.php?job_id=178569";  */
  /* return;  */
  $job_id = 178934; 
}

$dp  = new DataProc($job_id); 

print('Number of filteredJudgments: ' . $dp->getFilteredJudgements() . "<br>");

print("Workers labelled as spam: <br>");

$res = $dp->getSpamLabels();
foreach($res as $spammer_id){
  print("$spammer_id<br><br>"); 
}


   echo "<a href='http://129.35.251.201/wcs/data'>See generated data files</a>"
   		
?>