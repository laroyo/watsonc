<?php
class DataProc {
  
  private $filteredJudgements; 
  private $spamLabels; 
//  private static $dataproc_path = 'C:\\Users\\gsc\\watsonc\\dataproc'; 
  private static $dataproc_path = 'C:\\Users\\IBM_ADMIN\\Desktop\\IBM\\stift7\\hui\\dataproc';

  function DataProc($job_id=null){

   $res = exec('Rscript '. self::$dataproc_path . '\\workerMetrics.R'); 	     

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
