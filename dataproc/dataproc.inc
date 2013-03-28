<?php

/**
 * API (more like a wrapper, actually) for using the dataproc functionaly from php). 
 **/

class DataProc {
  
  private $filteredJudgements; 
  private $spamLabels; 
  
  function DataProc($job_id=null){

    //FIXME: it should always receive a Job_Id. 
    if($job_id == null){
      $job_id = 1234; 
    }

    //FIXME: define directory structure for storing data of experiments. 
    $res = exec('/usr/bin/Rscript --vanilla /var/www/html/wcs/dataproc/workerMetrics.R '. $job_id . ' /var/www/html/wcs/data/file_results.csv /var/www/html/wcs/data/'); 
    $output = explode('$$',$res);    

    $this->filteredJudgements = (int)trim($output[0]); 
    $this->spamLabels = explode(' ',$output[1]);        
  }  function getSpamLabels(){    
    return $this->spamLabels;
  }
  
  function getFilteredJudgements(){
    return $this->filteredJudgements;        
  }

}

?>
