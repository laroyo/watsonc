<!doctype html>
<html lang="us">
<head>
<meta charset="utf-8">
<title>Hui App</title>
<!-- Style sheets  -->
<link href="css/huimain.css" rel="stylesheet">
<link href="plugins/jquery-ui/css/dark-hive/jquery-ui-1.10.1.custom.css"
	rel="stylesheet">
<link href="plugins/tablesorter/css/theme.default.css" rel="stylesheet" type="text/css" />	
<!-- js libraries  -->
<script src="plugins/jquery-ui/js/jquery-1.9.1.js"></script>
<script src="plugins/jquery-ui/js/jquery-ui-1.10.1.custom.js"></script>
<script src="plugins/tablesorter/js/jquery.tablesorter.min.js" type="text/javascript"></script>
<script src="plugins/tablesorter/js/jquery.tablesorter.widgets.min.js" type="text/javascript"></script>
<script src="js/huimain.js"></script>
</head>
<body>
	<div id="content">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1" >Home</a></li>
				<li><a href="#tabs-2" >Jobs</a></li>
				<li><a href="#tabs-3" >History</a></li>
				<li><a href="#tabs-4" >Statistics</a></li>
				<li><a href="#tabs-5" >Examples</a></li>
			</ul>
			<div id="tabs-1" >
				<h1>Watson-Crowdsourcing</h1>
				<br> <a href="http://en.wikipedia.org/wiki/Crowdsourcing"><img
					src="graphs/crowdsourcing.jpg" alt="No show" title = "What is the Crowdsourcing?" /></a>
			</div>
			<div id="tabs-2">
				<h3>This page is to create new jobs on CrowdFlower</h3>
				<br>
				<div id="jobarea"></div>
			</div>
			<div id="tabs-3">
				<h3>This page is to show the history of jobs created on CrowdFlower</h3>
				<p style ="font-size: 80%" >A sentence is an unit;  An assignment is composed sentences;  A job is composed assignments.</p>
				<p style ="font-size: 80%">All the payments are in cents;  Job Completion is in percentage;  Run Time is in days.</p>
				<br>
				<?php 				
				$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
				$db = mysql_select_db("watsoncs", $con) or die("Couldn't select database");
				$result = mysql_query("SELECT * FROM  `cfinput` LIMIT 0 , 30");
				
				/* Update run_time in the database */
				while($item = mysql_fetch_array($result))
              {

	           //$date_diff = round(abs(strtotime(date('Y-m-d H:i:s'))-strtotime($row[3]))/86400);
	           $date2 = date('Y-m-d H:i:s');
	           $date1 = $item["created_date"];
	           $ts1 = strtotime($date1);
	           $ts2 = strtotime($date2);
	
	           $date_diff = $ts2 - $ts1;
	           $temp = mysql_query("Update 'cfinput' Set 'run_time' = '$date_diff' Where 'job_id' = '{$item["job_id"]}' ");
              }
             ?>
				
				<div id="historyarea">
<!--  <button class="search" data-filter-column="10" data-filter-text="2?%">Saved Search</button> (search the Discount column for "2?%") -->
  <button class="reset" title = "Click to clear all the filter options" >Reset Search</button> <!-- targetted by the "filter_reset" option -->
  <br>
<?php



$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
$db = mysql_select_db("watsoncs", $con) or die("Couldn't select database");

$result = mysql_query("SELECT * FROM  `cfinput` LIMIT 0 , 30");


echo "<table id='historytable' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th title = 'Link to CrowdFlower'>Job ID</th>";
echo "<th>Job Title</th>";
echo "<th>Created Date</th>";
echo "<th>File Name</th>";
echo "<th>Type of Units</th>";
echo "<th>Template</th>";
echo "<th>Max Judgement Per Worker</th>";
echo "<th>Max Judgement Per Ip</th>";
echo "<th>Units Per Assignment</th>";
echo "<th>Assignments Per Job</th>"; 
echo "<th>Units Per Job</th>";
echo "<th>Judgements Per Unit</th>";
echo "<th title = 'Judgements Per Unit * Units Per Assignment'>Judgements Per Assignment</th>";	
echo "<th title = 'Judgements Per Unit * Units Per Job'>Judgements Per Job</th>";
echo "<th>Payment Per Unit</th>";
echo "<th title = 'Payment Per Unit * Units Per Assignment'>Payment Per Assignment</th>";
echo "<th title = 'Payment Per Unit * Units Per Job'>Payment Per Job</th>";
echo "<th title = 'Payment Per Unit * Judgements Per Unit'>Total Payment Per Unit</th>";
echo "<th title = 'Total Payment Per Unit * Units Per Assignment'>Total Payment Per Assignment</th>";
echo "<th title = 'Total Payment Per Unit * Units Per Job'>Total Payment Per Job</th>";
echo "<th>Comments</th>";
echo "<th>Job Judgements Made</th>";
echo "<th title = 'Job Judgements Made / Judgements Per Job'>Job Completion</th>";
echo "<th>Run Time</th>";
echo "<th>Status</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter



while($row = mysql_fetch_array($result)){

	    extract ( $row );
	    
        echo "<tr>";
     // echo "<td><a href = 'index.php#tabs-4' class = 'tdlinks' >$job_id</a></td>";
        echo "<td><a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >$job_id</a></td>";
        echo "<td>$job_title</td>";
        echo "<td>$created_date</td>";
        echo "<td>$file_name</td>";
        echo "<td>$type_of_units</td>";
        echo "<td>$template</td>";
        echo "<td>$max_judgements_per_worker</td>";
        echo "<td>$max_judgements_per_ip</td>";
        echo "<td>$units_per_assignment</td>";
        echo "<td>$assignments_per_job</td>";
        echo "<td>$units_per_job</td>";
        echo "<td>$judgements_per_unit</td>";
        echo "<td>$judgements_per_assignment</td>";
        echo "<td>$judgements_per_job</td>";
        echo "<td>$payment_per_unit</td>";
        echo "<td>$payment_per_assignment</td>";
        echo "<td>$payment_per_job</td>";
        echo "<td>$total_payment_per_unit</td>";
        echo "<td>$total_payment_per_assignment</td>";
        echo "<td>$total_payment_per_job</td>";
        echo "<td>$job_comments</td>";
        echo "<td>$job_judgements_made</td>";
        echo "<td>$job_completion</td>";
        echo "<td>$run_time</td>";
        echo "<td>$status</td>";
        echo "</tr>";
}
echo "</tbody>";
echo "</table>";
?></div>
			</div>
				<div id="tabs-4">
				<p>Get Files</p>
				<br>
				<div id="filearea"></div>
			</div>
			<div id="tabs-5">
				<p>Future Use</p>
				<a href="jquery.html">jquery ui examples</a><br> 
				<a href="http://jqueryui.com/themeroller/">jquery ui themeroller</a><br>
				<a href="http://jqueryui.com/themeroller/">jquery ui demos</a><br>
				<a href="http://api.jquery.com/">jquery api's</a><br> 
				<a href="http://blueimp.github.com/jQuery-File-Upload//">file upload</a><br>
			</div>
		</div>

	</div>
</body>
</html>