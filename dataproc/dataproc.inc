<?php

/**
 * API (more like a wrapper, actually) for using the dataproc functionaly from php). 
 **/

class DataProc {
  
  private $filteredJudgements; 
  private $spamLabels; 

  function DataProc($job_id=null){

    $user = posix_getuid();
    $userinfo = posix_getpwuid($user);

    //FIXME: define directory structure for storing data of experiments. 
    $res = exec('Rscript workerMetrics.R '. $userinfo['dir'] . '/results.csv '. $userinfo['dir'] . '/workerMetrics.xlsx'); 
    $output = explode('$$',$res);    

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
