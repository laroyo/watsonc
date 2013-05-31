<?php

require_once('dataproc.inc'); 

if(isset($_GET['worker_id'])){
  $worker_id = $_GET['worker_id'];
} else if (isset($_GET['test'])){ 
  $worker_id = 17132979; 
}

function addData($name, $data, $xy=NULL){
      
  $i = 0; 
  $res = array(); 

  foreach($data as $d) {
    
    if($xy != NULL){
      $row['x'] = $i++; 
      $row['y'] = $d[$xy['y']] ;       
      $row['label'] = $d[$xy['x']];
    } else {
      //print('second branch'); 
      $row['x'] = $i++; 
      $row['y'] = $d; 
    }
    
    array_push($res, $row); 
  } 

  $obj['key'] = $name;
  $obj['values'] = $res; 
  return $obj;      
}

$worker_sentences = queryList("select distinct(unit_id) from cflower_results where worker_id = $worker_id order by unit_id asc");

$sent_clarity = simpleQuery("select unit_id,clarity from sent_clarity where unit_id in (". implode($worker_sentences,',') .")");
$worker_cosine = queryList("select cos from workerSentenceScore where worker_id = $worker_id and unit_id in (". implode($worker_sentences,',') . ")"); 

$workerSentenceScores = simpleQuery("select worker_id,score from workerSentenceScore where worker_id = $worker_id"); 

$taskCompTimes = simpleQuery("select unit_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) as time from cflower_results where worker_id = $worker_id
order by unit_id asc"); 


$avgCompTimes = simpleQuery("select * from (select unit_id from cflower_results where worker_id = $worker_id) a left join
(select unit_id,avg(UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at)) as time from cflower_results group by unit_id order by unit_id asc) b 
on a.unit_id = b.unit_id");

//$agrSentRelation = simpleQuery("select unit_id,clarity from rel_clarity where unit_id in (". .")"); 

$diffTimes = array(); 
for($i=0; $i < sizeof($avgCompTimes); $i++){
  $diff['time'] = $taskCompTimes[$i]['time'] - $avgCompTimes[$i]['time']; 
  $diff['unit_id'] = $taskCompTimes[$i]['unit_id']; 
  $diffTimes[] = $diff; 
}

$sentTimes = addData("Task comp. time", $taskCompTimes, array("x" => 'unit_id', "y"=>'time')); 
$avgTimes = addData("Avg comp. time", $avgCompTimes, array("x" => 'unit_id', "y"=>'time')); 
$diffTimes = addData("Difference Worker Comp. Time - Avg", $diffTimes, array("x" => 'unit_id', "y"=>'time')); 

$sentClarity =  addData('Sentence Clarity',$sent_clarity, array('x' => 'unit_id', 'y'=> 'clarity')); 
$cosineAgr =  addData('Agr (Cos)',$worker_cosine); 

$wsScore = addData('Worker-Sentence Score', $workerSentenceScores, array('x' => 'worker_id', 'y' => 'score')); 

$maxCos = max($worker_cosine); 
//var_dump($taskCompTimes);

function getMaxArray($array, $key){
  function ret($arr){ 
    return $arr['time']; 
  }
  
  $elems  = array_map('ret', $array); 
  return max($elems); 

}

$maxTime = getMaxArray($taskCompTimes, 'time'); 
?>
<!DOCTYPE html>
<meta charset="utf-8">

<link href="/wcs/css/nv.d3.css" rel="stylesheet" type="text/css">
<style>

body {
  overflow-y:scroll;
}

text {
  font: 12px sans-serif;
}

.lineplot{
  height: 300px;
  width: 500px; 
  margin: 10px;
  min-width: 100px;
  min-height: 100px;
  display:inline-block;
}

</style>
<body>
<h2>Worker Analytics for worker <?php echo($worker_id); ?></h2>

<h3> Sentences: </h3>
  Number of annotated sentences: <? echo(sizeof($worker_sentences)); ?><br>

  <div id="two_graphs" class="lineplot">
    <svg></svg>    
  </div>
  <div id="combined" class="lineplot">
    <svg></svg>
  </div>

<h3> Task completion times</h3>
  
  <div id="times" class="lineplot">
    <svg></svg>
  </div>
  <div id="diff_times" class="lineplot">
    <svg></svg>
  </div>

<script src="/wcs/js/d3.v2.js"></script>
<script src="/wcs/js/nv.d3.js"></script>
<script src="/wcs/js/tooltip.js"></script>
<script src="/wcs/js/utils.js"></script>
<script src="/wcs/js/legend.js"></script>
<script src="/wcs/js/axis.js"></script>
<script> 
<?php

  echo "var two_graphs = [".json_encode($sentClarity). ",". json_encode($cosineAgr) . "];\n";  
  echo "var data = [".json_encode($wsScore). "];\n";  
  echo "var times = [". json_encode($sentTimes). ",". json_encode($avgTimes). "];\n"; 
  echo "var diff_times = [".json_encode($diffTimes). "];\n";  

?> 
</script>
<script src="/wcs/js/lineChart.js"></script>
<script>
  addLineChart('#two_graphs svg', two_graphs); 
  addLineChart('#combined svg', data); 
  addLineChart('#times svg', times, <?php echo($maxTime); ?>);  
  addLineChart('#diff_times svg',diff_times)
</script>