<?php

require_once('../includes/dbinfo.php');

function getPivotTable($job_id){

  //echo('/usr/bin/Rscript /home/gsc/watson/dataproc/getPivotTable.R '. $job_id);
  $worker_res = exec('/usr/bin/Rscript /var/www/html/wcs/dataproc/getPivotTable.R '. $job_id);
  
  //echo "Json decode: $worker_res<br>";
  $dec = json_decode($worker_res);
  //var_dump($worker_res); 
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

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id']; 
  //$job_ids = getJobsInSet($set_id);  
  //var_dump($matrix);
} else {
  $job_id = 179229; 
}

?>
<!DOCTYPE html>
<meta charset="utf-8">

<link href="../css/nv.d3.css" rel="stylesheet" type="text/css">
<style>

body {
  overflow-y:scroll;
}

text {
  font: 12px sans-serif;
}

#chart1 {
  height: 500px;
  width: 650px; 
  margin: 10px;
  min-width: 100px;
  min-height: 100px;
/*
  Minimum height and width is a good idea to prevent negative SVG dimensions...
  For example width should be =< margin.left + margin.right + 1,
  of course 1 pixel for the entire chart would not be very useful, BUT should not have errors
*/
}

#overview {
  height: 300px;
  margin: 10px;
  min-width: 100px;
  min-height: 100px;
}

</style>
<body>
<h2>Job Analytics for job <?= $job_id ?></h2>

<h3>Sentence labels</h3>
  <div id="chart1">
    <svg></svg>
  </div>

<h3> Worker metrics</h3>
  <div id="overview">
    <svg></svg>
  </div>
<?php
  $matrix = getPivotTable($job_id); 
  //echo "end matrix ". end(array_keys($matrix));
  echo "<script>var sents = [";
  foreach($matrix as $sentence_id => $relations){
    echo '{"sentence_id": '. $sentence_id. ',';
    foreach($relations as $key => $value){
      echo '"'.$key.'":' . $value ;
      if($key != 'NONE')
	echo ","; 
    }
    echo '}';
    
    if($sentence_id != end(array_keys($matrix)))
      echo ",";
  }
  echo "];</script>";
?>
<script src="../js/d3.v2.js"></script>
<script src="../js/nv.d3.js"></script>
<script src="../js/tooltip.js"></script>
<script src="../js/utils.js"></script>
<script src="../js/legend.js"></script>
<script src="../js/axis.js"></script>
<script src="../js/multiBar.js"></script>
<script src="../js/multiBarChart.js"></script>
<script src="../js/job_analytics2.js"></script>
<script src="../js/compTimes.js"></script>