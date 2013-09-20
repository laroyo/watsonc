<?php

/**
 * PHP api to some R methods: to reuse some R code, by calling it from PHP.
 * Each method in this library makes a call to a R script, that may return some data in JSON. 
 **/


/**
 * Returns the pivot table for the annotations of a job. 
 **/
function getPivotTable($job_id){
  
  $worker_res = exec('/usr/bin/Rscript '. BASE_PATH .'/dataproc/api/getPivotTable.R '. $job_id);  
  $dec = json_decode($worker_res);

  $rels = array('D','S','C','M','L','AW','P','SE','IA','PO','T','CI','OTH','NONE'); 
  
  $matrix = array(); 
  
  for ($i = 0; $i < sizeof($dec->rownames); $i++){
    $vector = array(); 
    
    foreach ($rels as $rel){
      $vect = $dec->matrix->$rel;
      $vector[$rel] = $vect[$i];
    }
    $matrix[$dec->rownames[$i]] = $vector;
    unset($vector);
  }
  
  return($matrix);
}

/**
 *  Returns the relation(s) majoritarily chosen for each of the sentences of a job
 **/
function getMajRelations($job_id){
  

  $worker_res = exec('/usr/bin/Rscript '. BASE_PATH .'/dataproc/api/getRelationsSentences.R '. $job_id);    
  $dec = json_decode($worker_res,true);
  return($dec);
}