<?php

require_once('includes/dbinfo.php'); 

function getSetId($job_ids,$indiv=FALSE){
  $res = NULL; 
  if($indiv){    
    $query = "SELECT set_id from analysis_sets where members in (". implode($job_ids,','). ")"; 
    //print($query); 
    $rs = mysql_query($query);
    
    while($row = mysql_fetch_assoc($rs)){
      //var_dump($row); 
      $res[]  = $row['set_id']; 
    }
    return $res;
  } else {
    $query = "SELECT set_id FROM `analysis_sets` WHERE members = '" . json_encode($job_ids). "'";     
    //print("---> " . $query . "\n"); 
    $res = mysql_query($query);
    $row = mysql_fetch_assoc($res); 

    if($row)       
      return($row['set_id']);         
    else 
      return NULL;        
  }
}

function getJobsInSet($set_id){
  $res = mysql_query("SELECT members FROM `analysis_sets` WHERE set_id = '$set_id'");
  $row = mysql_fetch_row($res); 
  
  return(json_decode($row[0]));   
}

function getFilteredSentences($set_id){ 
  $res = NULL; 
  $rs = mysql_query("SELECT * from filtered_sentences where set_id = $set_id"); 
  if($rs)
     while($row = mysql_fetch_assoc($rs)){
       $res[]  = $row; 
     }    
  return $res; 
}

/**
 * The average for the workerMetrics of the members of the set. 
 **/
function getAvgWorkerMetricsSet($set_id, $filter=NULL){
  //FIXME: the average calculation should take into account that not all the jobs in a set will have the same number of elements. 
  if($filter == 'ALL'){
    $rs = mysql_query("select distinct(filter) as f from worker_metrics where set_id = $set_id"); 
    while($row = mysql_fetch_assoc($rs)){      
      $filter =  $row['f']; 
      $query = "select avg(numSents) as numSents,avg(cos) as cos,avg(agreement) as agr,avg(annotSentence) as annotSent from worker_metrics where set_id = $set_id and filter = '". $filter ."'";
      $rs = mysql_query($query); 
      $res[$filter] = mysql_fetch_assoc($rs);           
    }
    return $res; 
  } else {    
    
    if($filter == NULL){
      $query = "select avg(numSents) as numSents,avg(cos) as cos,avg(agreement) as agr,avg(annotSentence) as annotSent from worker_metrics where set_id = $set_id and filter is null";             
    } else {
      $query = "select avg(numSents) as numSents,avg(cos) as cos,avg(agreement) as agr,avg(annotSentence) as annotSent from worker_metrics where set_id = $set_id and filter = '". $filter ."'";      
    }    
    //print($query); 
    $rs = mysql_query($query); 
    return mysql_fetch_assoc($rs);     
  }
}

/**
 * Get the worker metrics for ONE individual job. 
 **/
function getAvgWorkerMetricsJob($job_id, $filter=NULL){
  $set_id = getSetId(array($job_id)); 
  //print('--->set_id : '. $set_id . "<br>\n"); 
  //print("getting worker metrics for job $set_id"); 
  return getAvgWorkerMetricsSet($set_id, $filter); 
}

/**
 * List of workers which have made judgments in several jobs of the set. 
 * [worker_id => [job_ids]]
 **/
function getCrossJobWorkers($job_ids){  

  $query = "select a.worker_id,job_id from cflower_results a right join ". 
    "(select worker_id from cflower_results where job_id in (".implode($job_ids,',') .") group by worker_id having count(distinct(job_id))>1) b ". 
    " on a.worker_id = b.worker_id where job_id in (". implode($job_ids,','). ") group by worker_id,job_id";
  //print($query); 

  $rs = mysql_query($query); 
  
  while($row = mysql_fetch_assoc($rs)) {
    $res[$row['worker_id']][] = $row['job_id']; 
  }
  return $res;       
}

/**
 * Returns task completion time stats (stored in the history table), for a list of jobs. 
 **/

function getCompletionTimeStats($job_ids){
  $query = "select job_id,min_time_unitworker,max_time_unitworker,avg_time_unitworker from history_table where job_id in (". implode($job_ids, ','). ")";     
  //$query = "select job_id,worker_id,started_at,created_at from cflower_results where job_id = $job_id"); 
  //print($query); 
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){    
    $job_id = $row['job_id']; 
    unset($row['job_id']); 
    $res[$job_id] = $row; 
  }
  return $res;      
}

/**
 * Returns task completion time stats for one set (the average of the jobs that form the set). 
 **/
function getSetCompletionTimeStats($time_stats, $judgmentsPerJob){

  $res['min_time_unitworker'] = PHP_INT_MAX; 
  $res['max_time_unitworker'] = 0; 
  $mean = 0; 
  $numJudgmentsSet = 0; 
  
  foreach($time_stats as $job_id => $stats){
    
    if($stats['min_time_unitworker'] < $res['min_time_unitworker']){
      /* print('min ');  */
      /* print($stats['min_timeunit_worker'] . "<Br>");  */
      $res['min_time_unitworker'] = $stats['min_time_unitworker']; 
    }
    
    if($stats['max_time_unitworker'] > $res['max_time_unitworker']){
      //print('max: '. $stats['max_time_unitworker']); 
      $res['max_time_unitworker'] = $stats['max_time_unitworker']; 
    }
    
    $mean = $mean + $stats['avg_time_unitworker'] * $judgmentsPerJob[$job_id];         
    $numJudgmentsSet += $judgmentsPerJob[$job_id]; 
  }
  $avg = ($mean / $numJudgmentsSet); 
  $res['avg_time_unitworker'] = $avg; 
  return $res;   
}

/**
 * Get the number of judgment per job, for a list of job_ids. (Stored on the history table). 
 **/
function getJudgmentsPerJob($job_ids){
  $query = "select job_id,judgments_per_job from history_table where job_id in (". implode($job_ids,','). ")"; 
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[$row['job_id']] = $row['judgments_per_job']; 
  }
  return $res; 
}


/**
 * Calculates the min,avg,max number of jobs completed for crossjob workers. 
 **/

function getCrossJobStats($crossjob_workers){  
  $numJobs = array_map(sizeof, $crossjob_workers);   
  $res['min'] = min($numJobs); 
  $res['mean'] = (array_sum($numJobs) / count($numJobs)); 
  $res['max'] = max($numJobs); 
  return $res; 
}

function getWorkerSentenceScore($worker_id){
  
  $query = "select unit_id,score from workerSentenceScore where worker_id = $worker_id";
  
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[]  = $row; 
    //$res[$row['unit_id']] = $row['score']; 
  }
  return $res;   
}

function getTaskCompletionTimes(){
  $query = "select unit_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) from cflower_results"; 
  
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[]  = $row;    
  }
  return $res;   
}

function getAvgTaskCompletionTimes($worker_id){

  $avgTaskCompTimes = simpleQuery("select unit_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) as time from cflower_results where unit_id in worker_units"); 
  simpleQuery("select unit_id,created_at from cflower_results where unit_id in (select unit_id from cflower_results where worker_id = $worker_id",con); 
}

function queryKeyValue($query, $key, $value){

  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[$row[$key]] = $row[$value]; 
  }  
  return $res; 
}

function simpleQuery($query){

  //echo $query; 
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){       
    $res[] = $row;     
  }  
  return $res; 
}

function queryList($query){
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){       
    
    if(sizeof(array_keys($row)) > 1){
      throw new Exception("The result set contains more than 1 field"); 
    } else {
      $keys = array_keys($row);       
      $res[] = $row[$keys[0]];     
    }    
  }  
  return $res; 
}



?>
