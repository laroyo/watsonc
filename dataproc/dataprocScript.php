<?php

include('dataproc.inc');

$dp  = new DataProc(); 

print('Number of filteredJudgments: ' . $dp->getFilteredJudgements() . "\n");

print("Workers labelled as spam: \n");

$res = $dp->getSpamLabels();
foreach($res as $spammer_id){
  print("$spammer_id\n"); 
}


?>