<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
include_once '../dataproc/dataproc.inc';

$job_id    = $_POST['job_id'];
	/* $sql = "SELECT worker_ids */
        /*     		FROM  filtered_workers   */
        /*     		Where set_id = '$job_id'"; */
	/* $result = mysql_query($sql) or die('Error, query failed'); */
	/* list($worker_ids) =  mysql_fetch_array($result); */

$abbr = array('contribution_filter' => 'CF', 'none_other' => 'NO', 'rep_text' => 'RT', 'rep_response' => 'RT', 'rand_text' => 'RND','no_relation' => 'NR'); 


$channels = queryKeyValue("select worker_id,external_type from cflower_results where job_id =  $job_id group by worker_id order by worker_id asc",'worker_id','external_type');

$worker_id = null;

$lqc = getLqc($job_id);
$avgWorkerMetrics = getAvgWorkerMetricsSet($job_id);

$set_id = $job_id; 
$workerMetrics = queryKeyList("select worker_id,cos,annotSentence,agreement from worker_metrics where set_id = $set_id and filter is null order by worker_id asc", 'worker_id');

$workerSentScore = queryKeyList("select * from workerSentenceScore where worker_id in (". implode(',',array_keys($lqc)) .")",'worker_id'); 

$cos = array(); 
$score = array(); 
foreach($workerSentScore as $worker_id => $row){    
  if(!isset($cos["" .$worker_id]))
    $cos["".$worker_id] = 0; 

  if(!isset($score["" .$worker_id]))
    $score["".$worker_id] = 0; 

  $cos["" .$worker_id] += $row['cos'];
  $score["".$worker_id] += $row['score'];
}

$avgTimes = queryKeyValue("select unit_id,avg(UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at)) as time from cflower_results where job_id = $job_id group by unit_id order by unit_id asc",'unit_id', 'time');

foreach($lqc as $worker_id => $filters){

  $workerTimes = getWorkerTaskCompletionTimes($job_id);
  $avgTime = array_sum(array_values($workerTimes[$worker_id])) / sizeof($workerTimes[$worker_id]);
  
  $obj = array();

  $sum = 0;
  foreach($workerTimes[$worker_id] as $unit_id => $time)
    $sum += ($avgTimes[$unit_id] - $time);
  
  $diff = $sum / sizeof($workerTimes[$worker_id]);
     
  $obj["worker_id"] = $worker_id; 
  $obj["agr"] = sprintf("%.2f", $workerMetrics[$worker_id]['agreement']);
  $obj["diffAgr"] = sprintf("%.2f", $workerMetrics[$worker_id]['agreement'] - $avgWorkerMetrics['agr']);
       
  $obj["cos"] = sprintf("%.2f", $cos[$worker_id] / sizeof($workerSentScore[$worker_id]));
  $obj["score"] = sprintf("%.2f", $score[$worker_id] / sizeof($workerSentScore[$worker_id]));
  
  $obj["annotSent"] =  sprintf("%.2f", $workerMetrics[$worker_id]['annotSentence']);
  $obj["diffAnnot"] =  sprintf("%.2f", $workerMetrics[$worker_id]['annotSentence'] - $avgWorkerMetrics['annotSent']);
  
  $obj["avgTime"]  = sprintf("%.2f", $avgTime);
  $obj["diffTime"]  = sprintf("%.2f", $diff);
  
  
  $filt = ''; 
  $sel_filters = array();
  foreach($filters as $key => $value){
    if($value)
      $sel_filters[] = $abbr[$key]; 
  }

  $obj["filters"] = $sel_filters;
  $obj["channel"]  = $channels[$worker_id];

  $res[] = $obj; 
}


echo json_encode($res);
 
?>