<?php
require_once('includes/envars.php'); 

if(!isset($_POST['postback']) && !isset($_GET['set_id'])){ 
?>
<form method ='POST' target=''>
  <input type ="hidden" name='postback' value='1'/> 
  <input type="checkbox" name="job_ids[]" value="179366"/> 179366<br/>
  <input type="checkbox" name="job_ids[]" value="179229"/> 179229<br/>
  <input type="checkbox" name="job_ids[]" value="178597"/> 178597<br/>
  <input type="checkbox" name="job_ids[]" value="178569"/> 178569<br/>
  <input type='submit'>
</form>
<?php
    return; 
}
function workerMetricsRow($row, $job_id=NULL,$set_id=NULL){
  echo "<tr>\n"; 

  if($job_id)
    echo "<td> $job_id </td>"; 
  else
    echo "<td></td>"; 
  if($job_id == 'aggregated'){
    echo "<td><a href='".IMAGES_ROUTE."/numSent_histogram_$set_id.jpg' target='_blank'>". sprintf("%.2f", $row['numSents']) . "</a></td>\n"; 
    echo "<td><a href='".IMAGES_ROUTE."/cos_histogram_$set_id.jpg' target='_blank'>". sprintf("%.2f", $row['cos']) . "</a></td>\n"; 
    echo "<td><a href='".IMAGES_ROUTE."/agr_histogram_$set_id.jpg' target='_blank'>". sprintf("%.2f", $row['agr']) . "</a></td>\n"; 
    echo "<td><a href='".IMAGES_ROUTE."/annotSent_histogram_$set_id.jpg' target='_blank'>". sprintf("%.2f", $row['annotSent']) . "</a></td></tr>\n"; 
  } else {
    echo "<td>". sprintf("%.2f", $row['numSents']) . "</td>\n"; 
    echo "<td>". sprintf("%.2f", $row['cos']) . "</td>\n"; 
    echo "<td>". sprintf("%.2f", $row['agr']) . "</td>\n"; 
    echo "<td>". sprintf("%.2f", $row['annotSent']) . "</td></tr>\n"; 
  }

}
function workerMetricsTable($workerMetrics,$set_id){
  
  echo "<table>"; 
  echo "<tr><td></td><td>NumSent</td><td> Cos </td><td> Agreement </td><td> Annotations per sentence </td></tr>"; 
  foreach($workerMetrics as $label => $row){
    workerMetricsRow($row, $label,$set_id); 
  }
  echo "</table>"; 
}


require_once('dataproc/dataproc.inc'); 

//Check if the statistics for the requested set has been previously calculated. 
if(isset($_GET['set_id'])){
  $set_id = $_GET['set_id']; 
  $job_ids = getJobsInSet($set_id);  
  
  /* print($job_ids);  */
  /* print(implode($job_ids));  */
} else {
  
  $job_ids = array_map('intval', $_POST['job_ids']); 
  sort($job_ids); 
  
  $set_id  = getSetId($job_ids); 

  echo "set_id $set_id <br>"; 
}

 if(!$set_id){
   echo "Computing new values.... "; 
   echo "Invoking script: " .'/usr/bin/Rscript '. $_SERVER['DOCUMENT_ROOT'] . '/dataproc/set_analytics.R ' . implode(' ',$job_ids); 
   $set_id = exec('/usr/bin/Rscript '. $_SERVER['DOCUMENT_ROOT'] . '/dataproc/set_analytics.R ' . implode(' ',$job_ids));
   if(! $set_id){
     echo "Error: the statistics for the requested set cannot be computed"; 
     return; 
   }
 } 

echo "<h3> Analytics for set:  $set_id </h3>"; 
echo "<h4> (Jobs:" . implode($job_ids,',') . ")</h4>";

$judgmentsPerJob = getJudgmentsPerJob($job_ids); 
//var_dump($judPerJob); 

$filteredSentences = getFilteredSentences($set_id); 

if(sizeof($filteredSentences) == 0){
  print('<br>No sentences were filtered'); 
  return; 
}

echo "<br><h4>Sentence Metrics </h4>"; 
echo "<a href='$images_path/set_heatmap_$set_id.jpg' target='_blank'> Heat map </a><br>"; 
echo '<table>'; 


foreach($filteredSentences as $sentence){
  print("<tr><td>" . $sentence['filter'] . "</td><td>". sizeof(json_decode($sentence['unit_ids'])) ."</td></tr>");
}
echo "</table>";

echo "Filtered sentences (per filter)<br>"; 
echo "<table>";
$workerMetrics['aggregated'] = getAvgWorkerMetricsSet($set_id);

if(sizeof($job_ids) > 1){
  //print('job_ids '. implode($job_ids). "<br>"); 

  foreach($job_ids as $job_id) {
    //print('retrieve : ' . $job_id); 
    $workerMetrics[$job_id] = getAvgWorkerMetricsJob($job_id); 
  }
}

echo "<hr>"; 
//echo "Worker Metrics"; 
//var_dump($workerMetrics); 

$crossjob_workers = getCrossJobWorkers($job_ids);

echo "<br><h4>Worker Metrics: </h4>"; 
echo "(Without applying any filter)"; 

workerMetricsTable($workerMetrics,$set_id); 

//var_dump($workerMetrics); 
echo "<br> Number of cross-job workers: ". sizeof($crossjob_workers) . "<br>"; 
if(sizeof($crossjob_workers) > 0){
  echo "<br>Cross-job workers: " . implode(array_keys($crossjob_workers),', '). "<br>"; 
  //var_dump($crossjob_workers); 
  
  $cjstats = getCrossJobStats($crossjob_workers); 
  //var_dump($res); 
  echo "Number of jobs in which the cross - job workers have completed tasks: <br>"; 
  echo "<br> Min: ". $cjstats['min'] . " Mean:  " . $cjstats['mean'] . " Max: ". $cjstats['max']. "<br>"; 
  //var_dump($res['mean']); 
}

echo "<b> NEW: </b><a href='".IMAGES_ROUTE."/workerLabels_$set_id.jpg'>Worker Labels distribution</a> (Most active workers only)"; 
echo "<hr>"; 
print('job_ids');
print(implode($job_ids,' '));

$time_stats = getCompletionTimeStats($job_ids);
echo "<br><h4>Time stats: </h4>";
echo "<a href='".IMAGES_ROUTE."/set_histogram_$set_id.jpg' target='_blank'>Distribution of worker times (Plot)</a> (WITH outliers)<br/>";
echo "<a href='".IMAGES_ROUTE."/set_line_histogram_$set_id.jpg' target='_blank'>Distribution of worker times (Histogram)</a> (After filtering outliers)<br/>";
$set_time_stats = getSetCompletionTimeStats($time_stats, $judgmentsPerJob);
?>
<br><table>
<tr><td></td><td>Min</td><td>Avg</td><td>Max</td></tr>
<tr><td>Set</td><td><?= $set_time_stats['min_time_unitworker']?></td><td><? printf("%.2f",$set_time_stats['avg_time_unitworker']); ?></td><td><?= $set_time_stats['max_time_unitworker']?></td></tr>
<?php
foreach($time_stats as $job_id => $row){
  echo("<tr><td>Job $job_id</td><td> " .$row['min_time_unitworker']. "</td><td>".$row["avg_time_unitworker"]. "</td><td>".$row['max_time_unitworker']. "</td></tr>");
}
echo '</table>';
echo "<hr>";


