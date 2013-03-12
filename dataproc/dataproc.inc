<?php

class DataProc {
  
  private $filteredJudgements; 
  private $spamLabels; 

  function DataProc($job_id=null){
    $res = exec('Rscript workerMetrics.R'); 
    $output = explode('$$',$res); 
    print('output');
    for ($i = 0; $i < sizeof($output); $i++){
      print($output[$i]); 
    }    
    $this->filteredJudgements = (int)trim($output[0]); 
    $this->spamLabels = explode(' ',$output[1]);        
  }

  function getSpamLabels(){    
    return $this->spamLabels;
  }
  
  function getFilteredJudgements(){
    return $this->filteredJudgements;        
  }

}

?>
