<body data-spy="scroll" data-target=".sidebar-nav">
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

    $relations = array_keys($maj_relations['aggr']);
    for($i = 0; $i < sizeof($maj_relations['aggr']); $i++){
      $relation = $relations[$i]; 
      $count = $maj_relations['aggr'][$relation]; 

      if($count > 1){
	echo "<tr><td><span onclick='relTableToggle(\"".$relation."\")'> + </span></td>";	
	echo "<td>" . $abbr[$relation] . "</td><td>" . $count . "</td><td>0.5 (Avg)</td></tr>\n";      
	
	
	foreach($maj_relations['rels'][$relation] as $sent){
	  echo "<tr class='".$relation."-row' style='display:none'><td></td>";
	  echo "<td colspan='2'><a href='sentence.php?sentence_id=".$sent['unit_id']."'> Sentence ".$sent['unit_id'] . "</a></td><td>".$sent['clarity']."</td></tr>\n";
	}  
      } else {
	$clarity = $maj_relations['rels'][$relation][0]['clarity'];
	$unit_id = $maj_relations['rels'][$relation][0]['unit_id'];
	
      
	if($i == sizeof($maj_relations['aggr']) - 1 ){
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
	  $acum_rows .= "<tr class='onerel-row' style='display: none'><td></td><td colspan='2'><a href='sentence.php?sentence_id=".$unit_id."'>Sentence " . $unit_id . "</a> - ". 
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
    
    foreach($spam_per_channel as $channel => $spammers){

      $row = array();
      $row[] = $channel;
      $row[] = sizeof($spammers);

      $row2 = array();
      $row2[] = $channel; 
      $row2[] = (float)sprintf('%01.2f',sizeof($spammers) / sizeof($worker_channels[$channel]));       
     
      $row3 = array();
      $row3['label'] =$channel;
      $row3['value'] = (float)sprintf('%01.2f',sizeof($spammers) /$num_spammers);       

      
      $acum[]  = $row; 
      $ratio[] = $row2; 
      $spamSources[] = $row3;
    }

   foreach($spam_per_filter as $filter => $spammers){
  
      $row = array();
      $row[] = $filter;
      $row[] = sizeof($spammers);

      /* $row2 = array(); */
      /* $row2[] = $filter;  */
      /* $row2[] = (float)sprintf('%01.2f',sizeof($spammers) / sizeof($worker_channels[$channel]));        */
     
      $row3 = array();
      $row3['label'] = $filter;
      $row3['value'] = (float)sprintf('%01.2f',sizeof($spammers) /$num_spammers);       

      
      $acum[]  = $row; 
      //$ratio[] = $row2; 
      $filterProportion[] = $row3;
    }

function comp ($row_a, $row_b){
  return strcmp($row_a[1],$row_b[1]);
}

uasort($acum, 'comp');
$acum = array_reverse($acum);


function jsonScatterPoint($x, $y, $size){
  return "{'x' : ".sprintf('%01.2f',$x) .",'y' : $y,'size' : $size}"; 	    
}

$min_unit = min(array_keys($comp_times)); 
?>
<script>
<?php
echo "var compTimes = ["; 

foreach($comp_times as $unit_id => $list){
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
	      echo "<a href='job.php?job_id=$job_id'> $job_id </a>";
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
       <tr><td> Number of sentences in the set:  </td><td> <?php echo($num_sentences); ?> </td></tr>
      <tr><td> Number of workers in the set: </td><td><?php echo($num_workers); ?> </td></td>
      <tr><td> Number of LQ candidates in the set: </td><td><?php echo($num_spammers ." (". sprintf('%01.2f', ($num_spammers / $num_workers) * 100) ."%)"); ?></td</tr>
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

    <!-- Filters
    ================================================== -->
    <script src="js/jquery-1.9.1.js"></script>

    <script>

       $.arrayIntersect = function(a, b)
       {
	 return $.grep(a, function(i)
	  {
	    return $.inArray(i, b) > -1;
	  });
       };
       function loadSelect(id, data, exclude = null){
	 $('#'+id).empty();
	 $('#'+id).append(new Option('', ''));

	 var filters = Object.keys(data)
	 for(var k in filters){
	   if(filters[k] != exclude){
	     $('#'+id).append(new Option(filters[k], filters[k]));
	   } 
	 }
       }
       function loadVennDiagram(def){	 
	 var f1 = def == true ? $('#filter1').children()[1].value : $('#filter1').val(); 
	 var f2 = def == true ? $('#filter1').children()[2].value : $('#filter2').val(); 	          

	 alert(f1);
	 alert(f2);
	 if(f1 && f2){
	   
	   var intersect = $.arrayIntersect(filteredWorkers[f1], filteredWorkers[f2]);
	   
	   var sets = [{'label': f1, size: filteredWorkers[f1].length},{'label': f2, size: filteredWorkers[f2].length}],
	     intersects = [{sets: [0,1], size: intersect.length}];
	   
	   // get positions for each set
	   sets = venn.venn(sets, intersects);
	   $("#vennFilters").empty();
	   //if(def)
	   venn.drawD3Diagram(d3.select("#vennFilters"), sets, 300, 300);	     
	   d3.selectAll('circle').on("mouseover", alert(' -> mouseover')); 
	     /* else  */
	   /*   venn.updateD3Diagram(d3.select("#vennFilters"), sets);	    */
	 } 
       }	 


   </script>
   <section id ="filtersDistribution">
	 <h4> Filters </h4>

	 <div class='row'> Select two filters to display the overlap between both: </div>
	 <div class='row'>
	 Filter 1: 
	 <select id='filter1' onchange='loadSelect("filter2",filteredWorkers, this.value)'> 
	 </select>

	 <script>
	 <?php echo 'var filteredWorkers = ' . json_encode($spam_per_filter) . ";"; ?>      
	 loadSelect('filter1', filteredWorkers); 
	 </script>

	 Filter 2: 
         <select id = 'filter2' onchange='loadVennDiagram()'> 
         </select>
	 </div>
	 <!-- //alert($.arrayIntersect(filteredWorkers['disag_filters'], filteredWorkers['valid_words'])); -->
		    
	 <div id="vennFilters">
      </div>     
      </section>
	 
    </div><!--/span-->
    </div> <!--/row-->

</div><!--/.container-->    
<script>       	    

<?php
    
echo "var sents = [";
  foreach($pivot_table as $sentence_id => $relations){
    echo '{"sentence_id": '. $sentence_id. ',';
    foreach($relations as $key => $value){
      echo '"'.$key.'":' . $value ;
      if($key != 'NONE')
       echo ","; 
    }
    echo '}';
    
    if($sentence_id != end(array_keys($pivot_table)))
      echo ",";
  }
echo "];"; 

$channelDistribution = simpleQuery("select external_type as channel,count(*) as count from cflower_results where job_id = $job_id group by external_type", 
				     'channel','count');

/* formatting and json'ing of channel distribution data */
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


<script src="js/d3.v2.js"></script>
<script src="js/nv.d3.js"></script>

<script src="js/tooltip.js"></script>
<script src="js/utils.js"></script>
<script src="js/legend.js"></script>
<script src="js/axis.js"></script>
<script src="js/multiBar.js"></script>
<script src="js/multiBarChart.js"></script>
<script src="js/annotDistribution.js"></script> 
<script src="js/pieChart.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/main.js"></script>
<script src="js/linePlusBar.js"></script>
<script src="js/scatter.js"></script>
<script src="js/venn/venn.js"></script>
<script src="js/mds/mds.js"></script>
<script src="js/numeric/numeric-1.2.6.js"></script>
<!-- <script src="js/compTimes.js"></script> !-->

<script>
  addPieChart(channels,'workersPerChannel','Distribution of workers per channel');
//addPieChart(data_spam,'disSpamChannel');
  addLinePlusBar(spamPerChannel,'spamPerChannel', 'LQ candidates per channel + Ratio LQ / Workers');
  addPieChart(distSpamChannel,'distSpamChannel', '% of LQ candidates per channel');
  addScatterPlot(compTimes, 'compTimes');

  //Load the default venn diagram for filters. 
  loadVennDiagram(true);

// define sets and set set intersections
/* var sets = [{label: "A", size: 10}, {label: "B", size: 10}], */
/*     overlaps = [{sets: [0,1], size: 2}]; */
</script>

      <hr>
      <footer>
        <p>&copy; Crowd Watson 2013</p>
      </footer>
</body>
</html>