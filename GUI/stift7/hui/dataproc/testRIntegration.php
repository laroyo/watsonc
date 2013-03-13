<?php

include('dataproc.inc');
print('postinc'); 
$dp  = new DataProc('176454'); 

print('filteredJudgments: ' . $dp->getFilteredJudgements());
print('\n');
print('spamLabels: ');
$res = $dp->getSpamLabels();
foreach($res as $spammer_id){
  print($spammer_id); 
}


?>