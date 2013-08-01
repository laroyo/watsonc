<?php

require_once('dataproc.inc'); 

if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];    
  $job_ids = array(179229);
} 

if(isset($_GET['set_id'])){
  $set_id = $_GET['set_id'];    
  $job_ids = getJobsInSet($set_id);
} 

if (isset($_POST['job_ids'])){

  $job_ids = array_map('intval', $_POST['job_ids']); 
  sort($job_ids); 

  /* $set_id  = getSetId($job_ids);  */
  //FIXME: extend, to be able to analyze several grouped jobs. 
  $job_id = $job_ids[0];
} 

if (isset($_GET['test'])){  
  $job_id = 196304; 
  $job_ids = array(196304);
}

//FIXME: Move this to a library or somewhere where it could be easily reused. 
$abbr = array('C' =>'[CAUSES]', 
	       'S' => '[SYMPTOM]',
	       'L'=>'[LOCATION]',
	       'P'=>'[PREVENTS]',
	       'D'=>'[DIAGNOSE_BY_TEST_OR_DRUG]',
	       'M'=>'[MANIFESTATION]',
	       'AW'=>'[ASSOCIATED_WITH]',
	       'PO'=>'[PART_OF]',
	       'OTH'=>'[OTHER]',
	       'T'=>'[TREATS]',
	       'NONE'=>'[NONE]',
	       'IA'=>'[IS_A]',
	       'SE'=>'[SIDE_EFFECT]',
	       'CI'=>'[CONTRAINDICATES]',
	       'D'=>'[DIAGNOSED_BY_TEST_OR_DRUG]');

$matrix = getPivotTable($job_id); 
?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
<link href="/wcs/analytics/css/nv.d3.css" rel="stylesheet" type="text/css">
<link href="/wcs/analytics/css/bootstrap.css" rel="stylesheet" type="text/css">
<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
     .hiddenRow{
        padding: 0 !important;
     }
</style>
<link href="/wcs/analytics/css/bootstrap-responsive.css" rel="stylesheet">
<!-- body {
  overflow-y:scroll;
}

text {
  font: 12px sans-serif;
} -->
<style>
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

.piechart {
    height:250px;
     width:250px;
}


#overview {
  height: 300px;
  margin: 10px;
  min-width: 100px;
  min-height: 100px;
}

</style>
</head>
<body data-spy="scroll" data-target=".sidebar-nav">
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
    <div class="container">
    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    </button>
    <a class="brand" href="#">Crowd Watson</a>
    <div class="nav-collapse collapse">
    <p class="navbar-text pull-right">
    Logged in as <a href="#" class="navbar-link">Username</a>
    </p>
   <ul class="nav">
    <li class="active"><a href="#">Home</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#contact">Contact</a></li>
    </ul>
    </div><!--/.nav-collapse -->
    </div>
  </div>
</div>
  <div class="container">
    <div class="row">
    <div class="span3">
       <div class="well sidebar-nav affix" data-spy="affix" data-offset-top="50">
       <ul class="nav nav-list" >
              <li class="nav-header">Workers</li>
              <li><a href="#workersSection">Workers distribution</a></li>
              <li><a href="#filteredworkers">Low quality candidates</a></li>
              <li><a href="#completionTimes">Task completion times</a></li>
              <li><a href="#filtersDistribution">Filters distribution</a></li>
              <li class="nav-header">Job Overview</li>
              <li class="active"><a href="#annotationDistribution">Annotation Distribution</a></li>
              <li><a href="#relationDistribution">Annotated relations</a></li>
              <li class="nav-header">Sentences</li>
              <li><a href="#">TBC</a></li>
              <li><a href="#">TBC</a></li>
              <li><a href="#">TBC</a></li>
              <li class="nav-header">Relations</li>
              <li><a href="#usedrelations">TBC</a></li>
              <li><a href="#">TBC</a></li>
              <li><a href="#">TBC</a></li>
            </ul>
         </div><!--- well -->
          <div class='well well-small'>
           <strong>Worker Metrics  </strong>
           <span class='pull-right'><button class="btn btn-success btn-mini" type="button" onclick='alert("TBC (ToBeCompleted)");'>Download</button></span><br>
          </div>
          <div class='well well-small'>
           <strong>Sentence Metrics  </strong>
           <span class='pull-right'><button class="btn btn-success btn-mini" type="button" onclick='alert("TBC (ToBeCompleted)");'>Download</button></span>
          </div>
        </div><!--/span-->

    <div class='span9'>

    <!-- Annotations
    ================================================== -->    

    <section id='annotationDistribution'><h3> Annotation Distribution</h3>
    
    <!-- <div id="chart1" style='display:none'> -->
    <div id="chart1"> 
    <svg></svg>
    </div>
    </section>

    <!-- Relation
    ================================================== -->    
    <section id='relationDistribution'><h3> Annotated relations</h3>
    <p> Relations majoritary annotated for the sentences in the job</p>
     <table class="table table-condensed" style="border-collapse:collapse;">
    <!--   <table class="table table-condensed" style="border-collapse:collapse; display:none;"> -->
    <thead>
    <tr>
      <th></th><th>Relation</th><th>Number of sentences</th><th>Sentence Clarity</th>
    </tr>
    </thead>
    <tbody>    

    <?php
    $majRelations = getMajRelations($job_id);
    $relations = array_keys($majRelations['aggr']);
    for($i = 0; $i < sizeof($majRelations['aggr']); $i++){
      $relation = $relations[$i]; 
      $count = $majRelations['aggr'][$relation]; 

      if($count > 1){
	echo "<tr><td><span onclick='relTableToggle(\"".$relation."\")'> + </span></td>";	
	echo "<td>" . $abbr[$relation] . "</td><td>" . $count . "</td><td>0.5 (Avg)</td></tr>\n";      
	
	
	foreach($majRelations['rels'][$relation] as $sent){
	  echo "<tr class='".$relation."-row' style='display:none'><td></td>";
	  echo "<td colspan='2'><a href='#'> Sentence ".$sent['unit_id'] . "</a></td><td>".$sent['clarity']."</td></tr>\n";
	}  
      } else {
	$clarity = $majRelations['rels'][$relation][0]['clarity'];
	$unit_id = $majRelations['rels'][$relation][0]['unit_id'];
	
      
	if($i == sizeof($majRelations['aggr']) - 1 ){
	  $acum_relations .= $abbr[$relation]; 
	  $acum_clarity .= $clarity; 	  
	  $acum_rows .= "<tr class='onerel-row' style='display: none'><td></td><td colspan='2'>
            <a href='#'>Sentence " . $unit_id . "</a> - ". $abbr[$relation]. " </td><td> ".$clarity."</td></tr>\n";	  
	  

	  echo "<tr><td><span onclick='myToggle(\"onerel\")'>".  ( ($acum_relations != $relation)  ? "+"  : "-" ) . "</span></td>";
	  echo "<td>$acum_relations </td><td> 1</td><td>$acum_clarity</td></tr>\n"; 

	  echo $acum_rows; 

	} else {
	  $acum_relations .=  $abbr[$relation] . ", "; 
	  $acum_clarity .= $clarity . ", ";
	  $acum_rows .= "<tr class='onerel-row' style='display: none'><td></td><td colspan='2'><a href='#'>Sentence " . $unit_id . "</a> - ". 
	    $abbr[$relation]. " </td><td>".$clarity."</td></tr>\n";
	}
      }
    }

    ?>
    </table>
    </section>

    <!-- Workers
    ================================================== -->
    <?php

    $numSpammers = queryOne("select count(distinct(worker_id)) as count from filtered_workers where set_id = $job_id");
    $numWorkers = queryOne("select count(distinct(worker_id)) as count from cflower_results where job_id = $job_id");
    $numSentences = queryOne("select count(distinct(unit_id)) as count from cflower_results where job_id = $job_id");
        
    $worker_filters = queryGroup("select worker_id, filter from filtered_workers where set_id = $job_id order by worker_id asc", 'worker_id', 'filter');

    $spamPerFilter = queryGroup("select worker_id, filter from filtered_workers where set_id = $job_id order by filter asc,worker_id asc", 'filter', 'worker_id');

    $spamPerChannel = queryGroup("select fw.worker_id as worker_id,external_type from filtered_workers fw left join cflower_results cf ". 
				 "on cf.worker_id = fw.worker_id where set_id = $job_id group by worker_id", 'external_type', 'worker_id');

    $worker_channels = queryGroup("select worker_id,external_type as channel from cflower_results where job_id = $job_id 
       group by worker_id order by channel asc",'channel','worker_id');
    
    foreach($spamPerChannel as $channel => $spammers){

      $row = array();
      $row[] = $channel;
      $row[] = sizeof($spammers);

      $row2 = array();
      $row2[] = $channel; 
      $row2[] = (float)sprintf('%01.2f',sizeof($spammers) / sizeof($worker_channels[$channel]));       
     
      $row3 = array();
      $row3['label'] =$channel;
      $row3['value'] = (float)sprintf('%01.2f',sizeof($spammers) /$numSpammers);       

      
      $acum[]  = $row; 
      $ratio[] = $row2; 
      $spamSources[] = $row3;
    }

   foreach($spamPerFilter as $filter => $spammers){
  
      $row = array();
      $row[] = $filter;
      $row[] = sizeof($spammers);

      /* $row2 = array(); */
      /* $row2[] = $filter;  */
      /* $row2[] = (float)sprintf('%01.2f',sizeof($spammers) / sizeof($worker_channels[$channel]));        */
     
      $row3 = array();
      $row3['label'] = $filter;
      $row3['value'] = (float)sprintf('%01.2f',sizeof($spammers) /$numSpammers);       

      
      $acum[]  = $row; 
      //$ratio[] = $row2; 
      $filterProportion[] = $row3;
    }

function comp ($row_a, $row_b){
  return strcmp($row_a[1],$row_b[1]);
}

uasort($acum, 'comp');
$acum = array_reverse($acum);


$compTimes = queryGroup("select unit_id,worker_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) as time from cflower_results where job_id = $job_id 
   order by unit_id asc,time asc limit 80",'unit_id');

function jsonScatterPoint($x, $y, $size){
  return "{'x' : ".sprintf('%01.2f',$x) .",'y' : $y,'size' : $size}"; 	    
}

$min_unit = min(array_keys($compTimes)); 
echo "<script>"; 
echo "var compTimes = ["; 

foreach($compTimes as $unit_id => $list){
  echo "{'key' : $unit_id, \n";
  echo " 'values': [\n";

  $keys = array_keys($list);
  $max = 0;
  
  foreach($list as $elem)
    $max = ($elem['time'] > $max) ? $elem['time'] : $max; 
  
  for($i = 0; $i < sizeof($list); $i++){

    $elem = $list[$keys[$i]];
    
    if($i == 0){
      $prev['time'] = ((int)$elem['time']);
      $prev['count'] = 1;      
    } else if($i == (sizeof($list) -1)){
      
      if($prev['time'] == $elem['time']) 	
	$prev['count'] += 1;		
            
      echo jsonScatterPoint($prev['time'] * 100 / $max,$unit_id, $prev['count']). "\n"; 
			    
      if($prev['time'] != $elem['time'])
	//The last element (if not equal to the previous one). 
	echo ", " . jsonScatterPoint(((int)$elem['time']) * 100 / $max,$unit_id, 1) . "\n"; 
      
    } else {
      if($prev['time'] == $elem['time']) 
	$prev['count'] += 1;
      else {		
	echo jsonScatterPoint($prev['time'] * (100 / $max),$unit_id, $prev['count']) . ",\n"; 
	$prev['time'] = ((int)$elem['time']); 
	$prev['count'] = 1; 
      }
    }
  }  
  echo "]},\n"; // </values>  
}
echo "]\n";

?>
   var  spamPerChannel = [
     {'key':  'Num Spammers',
     'bar' : true,
      'values' : <?php echo json_encode($acum); ?>
     },
     {'key':  'Spammer ratio',
      'values' : <?php echo json_encode($ratio); ?>
     }].map(function(series) {
	 series.values = series.values.map(function(d) {return {x: d[0], y: d[1] } });
	 return series;
     }); 
  
     var distSpamChannel = [{'key' : 'legend', 'values' : <?php  echo json_encode($spamSources);?> }];
 
     var distSpamFilter = [{'key' : 'legend', 'values' : <?php  echo json_encode($filterProportion);?> }];
     
</script>	
    <section id='workersSection'>

       <h3> Workers </h3>

      <div class='row'>
      <div class='span4'>
        <div id="workersPerChannel" class='piechart'>
      <svg></svg>
      </div>            
      </div> <!-- /span -->

      
 
      <div class='span4'>
      
      <table class="table table-condensed">
       <tbody>

       <?php 

       function printJobsInSet($job_ids){
            for($i = 0; $i < sizeof($job_ids); $i++) {
	      $job_id = $job_ids[$i];
	      echo "<a href='/wcs/analytics/job.php?job_id=$job_id'> $job_id </a>";
	      if($i < sizeof($job_ids) - 1)
		echo ",";
	    }	 
       }  

       if(sizeof($job_ids) == 1)  { ?>
	 <tr><td>Jobs in the set: </td><td> <?php echo($job_ids[0]) ?> </td></tr>  <?php
       } else if(sizeof($job_ids) <= 3)  { ?>
	 <tr><td colspan='2'><span class='pull-left'>Jobs in the set: </span>
         <span class='pull-right'><?php printJobsInSet($job_ids) ?> </span></td></tr> 
       <?php 
       } else { ?>
	 <tr><td>Jobs in the set: </td><td> <?php echo(sizeof($job_ids)); ?> </td></tr>
	 <tr><td colspan='2' style='text-align: center;'><?php echo(implode(',' , $job_ids)) ?></td></tr>	   	     
       <?php } ?>
       <tr><td> Number of sentences in the set:  </td><td> <?php echo($numSentences); ?> </td></tr>
      <tr><td> Number of workers in the set: </td><td><?php echo($numWorkers); ?> </td></td>
      <tr><td> Number of LQ candidates in the set: </td><td><?php echo($numSpammers ." (". sprintf('%01.2f', ($numSpammers / $numWorkers) * 100) ."%)"); ?></td</tr>
      <tr><td> Channels used</td><td></td></tr>   


      </tbody>
      </table>
      <p> This is placeholder text, to fill in the available space on the right of the pie chart. Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod.</p>
      

    </div> <!-- /span -->
    </div> <!-- /row -->
    </section>
    <section id='filteredworkers'>
    <h4> Low quality candidates </h4>      
    <div class='row'>

      <div class='span4'>
      <div id="spamPerChannel">
      <svg style='height: 300px'></svg>
      </div>            
      </div>
      <div class='span4'>
      <div id="distSpamChannel">
      <svg class='piechart'></svg>
      </div>            

      
      </div> <!-- span -->
      </div> <!-- row -->
      </section>

      <section id ="completionTimes">
      <h4> Task completion times</h4>

       <div class='row'>
       <div class='span8'>
       <div id="compTimes">
       <svg style='height: 800 px;'></svg>
       </div>            
       <p> Percentile distribution of the task completion times. Each point represents the time a single worker has taken to annotate the sentence in the
       y-axis. Points with bigger radius represent a percentile whith more than one worker. </p>
       </div> <!-- /span -->
       </div> <!-- /row -->
      </section>

      <section id ="filtersDistribution">
      <h4> Filters </h4>
      <div id="vennFilters">
      </div>     
      </section>
	 
    </div><!--/span-->
    </div> <!--/row-->
      <hr>
      <footer>
        <p>&copy; Crowd Watson 2013</p>
      </footer>

</div><!--/.container-->    
<script>       	    
<?php
    
$matrix = getPivotTable($job_id); 
echo "var sents = [";
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
echo "];"; 

$relations = getMajRelations($job_id);

$channelDistribution = simpleQuery("select external_type as channel,count(*) as count from cflower_results where job_id = $job_id group by external_type", 
				     'channel','count');

$numElements = 0;

foreach($channelDistribution as $row)
  $numElements += $row['count'];

foreach($channelDistribution as &$row) 
  $row['ratio'] =  sprintf('%01.2f',((float)$row['count'] / $numElements) * 100); 


$values = array();

foreach($channelDistribution as $r){
  array_push($values, array('label' => $r['channel'], 'value' => $r['ratio']));
}

$res = array(array('key' => 'legend', 'values' => $values));
echo("\n var channels =   " . json_encode($res). ";");    ?>
</script>

<script src="/wcs/analytics/js/jquery-1.9.1.js"></script>
<script src="/wcs/analytics/js/d3.v2.js"></script>
<script src="/wcs/analytics/js/nv.d3.js"></script>

<script src="/wcs/analytics/js/tooltip.js"></script>
<script src="/wcs/analytics/js/utils.js"></script>
<script src="/wcs/analytics/js/legend.js"></script>
<script src="/wcs/analytics/js/axis.js"></script>
<script src="/wcs/analytics/js/multiBar.js"></script>
<script src="/wcs/analytics/js/multiBarChart.js"></script>
<script src="/wcs/analytics/js/job_analytics.js"></script> 
<script src="/wcs/analytics/js/pieChart.js"></script>
<script src="/wcs/analytics/js/bootstrap.js"></script>
<script src="/wcs/analytics/js/main.js"></script>
<script src="/wcs/analytics/js/linePlusBar.js"></script>
<script src="/wcs/analytics/js/scatter.js"></script>
<script src="/wcs/analytics/js/venn/venn.js"></script>
<script src="/wcs/analytics/js/mds/mds.js"></script>
<script src="/wcs/analytics/js/numeric/numeric-1.2.6.js"></script>
<!-- <script src="/wcs/analytics/js/compTimes.js"></script> !-->

<script>
  addPieChart(channels,'workersPerChannel','Distribution of workers per channel');
//addPieChart(data_spam,'disSpamChannel');
  addLinePlusBar(spamPerChannel,'spamPerChannel', 'LQ candidates per channel + Ratio LQ / Workers');
  addPieChart(distSpamChannel,'distSpamChannel', '% of LQ candidates per channel');
  addScatterPlot(compTimes, 'compTimes');

// define sets and set set intersections
/* var sets = [{label: "A", size: 10}, {label: "B", size: 10}], */
/*     overlaps = [{sets: [0,1], size: 2}]; */

var sets = [{'label': "valid_words", size: 7},{'label': "disagr", size: 11},{'label': "rep_resp", size: 9},{'label': "rep_text", size: 5},{'label': "none_other", size: 10}],
  overlaps = [{sets: [0,1], size: 1}, 
	      {sets: [0,2], size: 1}, 
	      {sets: [0,3], size: 0}, 
	      {sets: [0,4], size: 0}, 
	      {sets: [1,2], size: 1}, 
	      {sets: [1,3], size: 0}, 
	      {sets: [1,4], size: 0}, 
	      {sets: [2,3], size: 0}, 
	      {sets: [2,4], size: 0}, 
	      {sets: [3,4], size: 0}];



// get positions for each set
sets = venn.venn(sets, overlaps);

// draw the diagram in the 'simple_example' div
venn.drawD3Diagram(d3.select("#vennFilters"), sets, 300, 300);

</script>
</body>
</html>