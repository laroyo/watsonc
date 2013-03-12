<?php

include('dataproc.php');

$dp  = new DataProc(); 

print('filteredJudgments: ' . $dp->getFilteredJudgements());
print('spamLabels: ');
$res = $dp->getSpamLabels();
foreach($res as $spammer_id){
  print($spammer_id); 
}


?>