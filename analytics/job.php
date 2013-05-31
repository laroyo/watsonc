<?php

require_once('dataproc/dataproc.inc'); 

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];  
  $matrix = getPivotTable($job_id); 
} else {
  $job_id = 179229; 
}

?>
<!DOCTYPE html>
<meta charset="utf-8">

<link href="css/nv.d3.css" rel="stylesheet" type="text/css">
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

<script src="js/d3.v2.js"></script>
<script src="js/nv.d3.js"></script>
<script src="js/tooltip.js"></script>
<script src="js/utils.js"></script>
<script src="js/legend.js"></script>
<script src="js/axis.js"></script>
<script src="js/multiBar.js"></script>
<script src="js/multiBarChart.js"></script>
<script src="js/job_analytics.js"></script>
<script src="js/compTimes.js"></script>