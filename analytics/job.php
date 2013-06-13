<?php

require_once('dataproc.inc'); 

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];    
} 

if(isset($_GET['set_id'])){
  $set_id = $_GET['set_id'];    
} 

if (isset($_POST['job_ids'])){

  $job_ids = array_map('intval', $_POST['job_ids']); 
  sort($job_ids); 

  /* $set_id  = getSetId($job_ids);  */
  //FIXME: extend, to be able to analyze several grouped jobs. 
  $job_id = $job_ids[0];
} 

if (isset($_GET['test'])){  
  $job_id = 179229; 
}

$matrix = getPivotTable($job_id); 
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
<h2>Job Analytics for job <?php echo($job_id); ?></h2>

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


<script src="/wcs/js/d3.v2.js"></script>
<script src="/wcs/js/nv.d3.js"></script>
<script src="/wcs/js/tooltip.js"></script>
<script src="/wcs/js/utils.js"></script>
<script src="/wcs/js/legend.js"></script>
<script src="/wcs/js/axis.js"></script>
<script src="/wcs/js/multiBar.js"></script>
<script src="/wcs/js/multiBarChart.js"></script>
<script src="/wcs/js/job_analytics.js"></script>
<!-- <script src="/wcs/js/compTimes.js"></script> !-->
