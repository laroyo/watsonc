<?php

require_once('dbinfo.php'); 
 
function getSetId($job_ids,$indiv=FALSE){
  $res = NULL; 
  if($indiv){    
    $query = "SELECT set_id from analysis_sets where members in (". implode($job_ids,','). ")"; 
    $rs = mysql_query($query);
    
    while($row = mysql_fetch_assoc($rs)){
      $res[]  = $row['set_id']; 
    }
    return $res;
  } else {
    $query = "SELECT set_id FROM `analysis_sets` WHERE members = '" . json_encode($job_ids). "'";     
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
 * Returns the pivot table for the annotations of a job. 
 **/
function getPivotTable($job_id){
  
  $worker_res = exec('/usr/bin/Rscript '. BASE_PATH .'/dataproc/getPivotTable.R '. $job_id);  
  $dec = json_decode($worker_res);

  $rels = array('D','S','C','M','L','AW','P','SE','IA','PO','T','CI','OTH','NONE'); 
  
  $matrix = array(); 
  
  for ($i = 0; $i < sizeof($dec->rownames); $i){
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

function getWorkerTaskCompletionTimes($job_id){
  $qres = simpleQuery("select unit_id,worker_id,(UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at)) as time from cflower_results where job_id = $job_id group by unit_id,worker_id order by worker_id asc,unit_id asc");
  $worker_id = null; 
  
  foreach($qres as $row){
    if($row['worker_id'] != $worker_id)      
      $worker_id = $row['worker_id'];

    $res[$worker_id][$row['unit_id']] = $row['time']; 
  }
  return $res; 
}

function getLqc($job_id){

  $query =   "select filter,a.worker_id from filtered_workers a left join (select worker_id,count(*) as count from filtered_workers b where set_id = $job_id 
 group by worker_id) b on a.worker_id = b.worker_id where set_id = 196309 order by count desc, worker_id asc,filter asc";
  
  $lqc = simpleQuery($query); 

  $filtlabels =  array("contribution_filter", "none_other", "rand_text", "rep_response", "rep_text");  
  
  $filters = array(); 
  foreach($lqc as $row){
    if($row['worker_id'] != $worker_id){
      $worker_id  = $row['worker_id'];

      $filters[$worker_id] = array();

      foreach($filtlabels as $key){
	$filters[$worker_id][$key] = False;
      }   
    }
    if($row['filter'] == NULL || $row['filter'] == 'NULL')
      $filters[$worker_id]['contribution_filter']  = True; 
    else
      $filters[$worker_id][$row['filter']]  = True; 
  }
  return $filters;
}

/**
 * Returns the results of $query as an array. Each array entry corresponds to a row. 
 **/
function simpleQuery($query){

  //echo $query; 
  $rs = mysql_query($query); 
 
  while($row = mysql_fetch_assoc($rs)){       
    $res[] = $row;     
  }  
  return $res; 
}

/**
 * Returns the results of $query as an associative array [$key => $value]
 * Both $key and $value should be attributes in the query (usually: columns on the queried table). 
 **/
function queryKeyValue($query, $key, $value){

  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[$row[$key]] = $row[$value]; 
  }  
  return $res; 
}

/**
 * 
 **/
function queryKeyList($query, $key){
  
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $k = $row[$key]; 
    unset($row[$key]); 
    $res[$k] = $row; 
  }  
  return $res; 
}

/**
 * Returns the results of $query as a list. Each entry of the list is a single value (instead of a row). 
 * Used, for instance, to obtain a list of keys (object id's), or a list of values ("all the cosine values for a job")
 * To prevent misuse, the query shouldn't contain more than 1 attribute: select <attribute> from <table>; 
 **/
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
