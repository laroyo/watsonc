<?php 
include_once 'includes/dbinfo.php';
include_once 'includes/functions.php';

////
?>
<!doctype html>
<html lang="us">
<head>
<meta charset="utf-8">
<title>Crowd Watson</title>
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
<script src="plugins/jquery-multi-open-accordion/jquery-multi-open-accordion/jquery.multi-accordion-1.5.3.js" type="text/javascript"></script>
<script src="js/huimain.js" type="text/javascript"></script>
<script language="javascript">


function computePayment()
{
	
	var payment_per_sentence = document.getElementById("payment_per_sentence");
	var payment_per_job = document.getElementById("payment_per_job");

	var judgments_per_unit = document.getElementById("judgments_per_unit").value;
	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var payment_per_assignment = document.getElementById("payment").value;
    var total_sentences = document.getElementById("sentences").value;	
     	
	if (judgments_per_unit != "" && units_per_assignment != "" && payment_per_assignment != "" && total_sentences != "") {
	 	payment_per_sentence.value = ((parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) + (parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) * 46.35 / 100 ) / 100 ;
		payment_per_job.value = parseInt(total_sentences) * payment_per_sentence.value;
	}

	computePaymentPerHour();
}

function computeTime() {
	var seconds_per_unit = document.getElementById("seconds_per_unit").value;
	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var seconds_per_assignment = document.getElementById("seconds_per_assignment");
	seconds_per_assignment.value = parseInt(seconds_per_unit) * parseInt(units_per_assignment);

	computePaymentPerHour();
}

function computePaymentPerHour() {
	var payment_per_assignment = document.getElementById("payment").value;
	var seconds_per_assignment = document.getElementById("seconds_per_assignment").value;
        var payment_per_hour = document.getElementById("payment_per_hour");
        if (seconds_per_assignment != "" && payment_per_assignment != "") {
                payment_per_hour.value = ((60 * 60) / parseInt(seconds_per_assignment)) * (parseInt(payment_per_assignment) / 100);
        }
}


</script>




</head>
<body>
	<div id="content">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1" >Home</a></li>
			    <li><a href="#tabs-upload">Upload Raw</a></li>
			    <li><a href="#tabs-ProcessInput">Process Input</a></li>
				<li><a href="#tabs-2" >Jobs</a></li>
				<li><a href="#tabs-3" >History</a></li>
				<li><a href="#tabs-4" >Results</a></li>
			</ul>
			<div id="tabs-1" >
				<h1>Crowd Watson</h1>
				<br> <a href="http://en.wikipedia.org/wiki/Crowdsourcing"><img
					src="graphs/crowdsourcing.jpg" alt="No show" title = "What is the Crowdsourcing?" /></a>
			</div>
			<div id="tabs-upload">
				<div id="accordion">
  <h5>CrowdFlower</h5>
  <div id="tabs-Raw">	
			<form enctype="multipart/form-data" action="services/uploadRaw.php" method="POST">
				<div class="borderframe"  >
					<div class="labelfield">Choose a RAW file to upload:</div>
					<div class="inputfield">
						<input name="rawuploadedfile" type="file" />
					</div>
					<div class="labelfield">seed releation</div>
					<div class="inputfield">
						<input type="text" name="title" class="textboxInput" />
					</div>
					<br />
					<textarea name="freeComment" rows="5" cols="100" ></textarea>
					<br />
					<div class="inputfield">
						<input type="submit" value="Submit"
							title="Click Submit to upload raw file" />
					</div>
					</div>
				</form>
				</div>
  <h5>Games</h5>
  <div>
    <p>Pending</p>
  </div>
</div>
			</div>
			
			
			<div id="tabs-ProcessInput">
			
				<h3>Process Input Files</h3>
				<p>This is to process input files for CrowdFlower</p>
				<div id="preprocessarea" class="borderframe">
				</div>
			</div>
			<div id="tabs-2">
				<h3>This page is to create new jobs on CrowdFlower</h3>
				<br>
			
				<div id="dialog-confirm" title="Select a file from the server">
				<button class="reset" title = "Click to clear all the filter options" >Reset Search</button> <!-- targetted by the "filter_reset" option -->
<br>
  <?php

$result = mysql_query("SELECT b.*, s.original_name
FROM  batches_for_cf  b
INNER JOIN file_storage as s on b.file_id = s.id
ORDER BY  b.created DESC");

echo "<table id='selectfile' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th>File ID</th>";
echo "<th>File Name</th>";
echo "<th>Filter Applied</th>";
echo "<th>Batch Size</th>";
echo "<th>Created By</th>";
echo "<th>Created Date</th>";
echo "<th>Comments</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter



while($row = mysql_fetch_array($result)){

	    extract ( $row );

        echo "<tr>";
        echo "<td><input type='radio' id='radiofile' name ='radiofile'/>$file_id</label></td>";
        echo "<td><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$file_id' class = 'tdlinks' >$original_name</a></td>";
        echo "<td>$filter_named</td>";
        echo "<td>$batch_size</td>";
        echo "<td>$created_by</td>";
        echo "<td>$created</td>";
        echo "<td>$comment</td>";
        echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
</div>
				<form enctype="multipart/form-data" action="/wcs/crowdflower/indexcrowdflower.php" method="POST" 

id="form">
<div class="borderframe"  > 
	<div class="labelfield">Choose a file to upload:</div>
	<div class="inputfield"><input name="uploadedfile" type="button" id="uploadedfile" value = "Choose Server File" />
    <input type="hidden" name="fileid" id="fileid" />
	<input type="hidden" name="sentences" id="sentences" />
	<label for="uploadedfile" style ="font-size: 80%" >No File Chosen</label> 
</div>
	<div class="labelfield">Job title:</div> <div class="inputfield"><input type="text" 

name="title"> <br /></div>

	<div class="labelfield">Judgments per unit:</div><div class="inputfield"><input type="text" 

name="judgments_per_unit" id="judgments_per_unit" oninput="computePayment()" > <br /></div>

	<div class="labelfield">Maxim judgments per worker:</div> <div class="inputfield"><input 

type="text" name="max_judgments_per_worker"> <br /></div>

	<div class="labelfield">Units per assignment (CF: Units per page):</div> <div 

class="inputfield"><input type="text" name="units_per_assignment" oninput="computePayment()" 

id="units_per_assignment"> <br /></div>

	<div class="labelfield">Payment per assignment (CF: Payment per page) (cents):</div><div 

class="inputfield"> <input type="text" name="payment" oninput="computePayment()" id="payment"> <br 

/></div>

	<div class="labelfield">Seconds per unit:</div><div class="inputfield"> <input type="text" 

name="seconds_per_unit" id="seconds_per_unit" oninput="computeTime()"> <br /></div>

	<div class="labelfield" title = "The purposes or notes of creating the job">Comments:</div>  
	<div class="inputfield"><input type="text" name="job_comment" class = 

"commentboxInput"/></div>

	<div class="labelfield">Choose the template: </div>
		<div class="inputfield"><input type="radio" name="template" value="t1" checked> 

Relations with definitions and extra questions required <br /></div>

		<div class="labelfield">&nbsp;</div><div class="inputfield"><input type="radio" 

name="template" value="t2"> Relations with definitions but without extra questions <br /></div>

		<div class="labelfield">&nbsp;</div><div class="inputfield"><input type="radio" 

name="template" value="t3"> Relations without definitions and extra questions required <br /></div>

		<div class="labelfield">&nbsp;</div><div class="inputfield"><input type="radio" 

name="template" value="t4"> Relations without definitions and without extra questions <br /><br></div>


	<div class="labelfield">Choose the channels: </div>
                <div class="inputfield"><input type="radio" name="channels" value="c1" checked>

Amazon Mechanical Turk <br /></div>

                <div class="labelfield">&nbsp;</div><div class="inputfield"><input type="radio" 

name="channels" value="c2"> All channels <br /></div>


	<div class="labelfield">&nbsp;</div><div class="inputfield"><input type="submit" name="action" 

value="Create Job" /><br /> <br /></div>

<div class="labelfield">Payment per sentence (dollar):</div><div class="inputfield"> <input 

type="text" name="payment_per_sentence" id="payment_per_sentence"> <br /></div>
<div class="labelfield">Payment per job (dollar):</div><div class="inputfield"> <input type="text" 

name="payment_per_job" id="payment_per_job"> <br /></div>
<div class="labelfield">Seconds per assignment:</div><div class="inputfield"> <input type="text" 

name="seconds_per_assignment" id="seconds_per_assignment"><br /></div>

<div class="labelfield">Payment per hour:</div><div class="inputfield"> <input type="text" name="payment_per_hour" id="payment_per_hour"> <br /></div>
</div>
</form> 
			</div>
			
			
			<div id="tabs-3">
				<h3>This page is to show the history of jobs created on CrowdFlower</h3>
				<p style ="font-size: 80%" >A sentence is an unit;  An assignment is composed sentences;  A job is composed assignments.</p>
				<p style ="font-size: 80%">All the payments are in cents;  Job Completion is in percentage;  Run Time is in days and hours.</p>
				<br>
				<?php 	

				$result = mysql_query("SELECT * FROM `history_table` WHERE 1");

				/* Update run_time in the database */
				while($item = mysql_fetch_array($result))
              {

	           //$date_diff = round(abs(strtotime(date('Y-m-d H:i:s'))-strtotime($row[3]))/86400);
	           $date2 = date('Y-m-d H:i:s');
	           $date1 = $item["created_date"];
	           $ts1 = strtotime($date1);
	           $ts2 = strtotime($date2);

	           $diff = $ts2 - $ts1;
	           $days = floor($diff/86400);   //24*60*60
	           $hours = round(($diff-$days*60*60*24)/(60*60));
	           if($hours == 24)
	           {
	              $days += 1;
	              $hours = 0;	           
	           }
	           $run_time = $days." days ".$hours." hours";
	           $updateRuntime = mysql_query("Update history_table Set run_time = '$run_time' Where job_id = '{$item["job_id"]}' ");
              	           	 	           
              }
             ?>
				
				
<!--  <button class="search" data-filter-column="10" data-filter-text="2?%">Saved Search</button> (search the Discount column for "2?%") -->
  <button class="reset" title = "Click to clear all the filter options" >Reset Search</button> <!-- targetted by the "filter_reset" option -->
  <br>
<?php

$history = mysql_query("SELECT * FROM  `history_table` ORDER BY created_date DESC");

echo "<table id='historytable' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th title = 'Link to CrowdFlower'>Job ID</th>";
echo "<th>Job Title</th>";
echo "<th>Created Date</th>";
echo "<th>Created By</th>";
echo "<th title = 'Click to open the file'>File Name</th>";
echo "<th>Number of Sentences</th>";
echo "<th>Type of Units</th>";
echo "<th>Template</th>";
echo "<th>Max Judgment Per Worker</th>";
echo "<th>Max Judgment Per Ip</th>";
echo "<th>Units Per Assignment</th>";
echo "<th>Units Per Job</th>";
echo "<th>Judgments Per Unit</th>";
echo "<th title = 'Judgments Per Unit * Units Per Job'>Judgments Per Job</th>";
echo "<th>Seconds Per Unit</th>";
echo "<th>Seconds Per Assignment</th>";
echo "<th>Payment Per Unit</th>";
echo "<th title = 'Payment Per Unit * Units Per Assignment'>Payment Per Assignment</th>";
echo "<th title = 'Payment Per Unit * Judgements Per Unit'>Total Payment Per Unit</th>";
echo "<th title = 'Total Payment Per Unit * Units Per Job'>Total Payment Per Job</th>";
echo "<th>Payment Per Hour</th>";
echo "<th>Channel Used</th>";
echo "<th>Comments</th>";
echo "<th>Job Judgments Made</th>";
echo "<th title = 'Job Judgments Made / Judgments Per Job'>Job Completion</th>";
echo "<th title = 'Days'>Run Time</th>";
echo "<th>Status</th>";
echo "<th>Link</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter



while($row = mysql_fetch_array($history)){

	    extract ( $row );

        echo "<tr>";
     // echo "<td><a href = 'index.php#tabs-4' class = 'tdlinks' >$job_id</a></td>";
        echo "<td><a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >$job_id</a></td>";
        echo "<td>$job_title</td>";
        echo "<td>$created_date</td>";
        echo "<td>$created_by</td>";
        echo "<td style ='font-size: 80%' ><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$cfbatch_id' class = 'filelinks' >$file_name</a></td>";
        echo "<td>$nr_sentences_file</td>";
        echo "<td>$type_of_units</td>";
        echo "<td style ='font-size: 80%' >$template</td>";
        echo "<td>$max_judgments_per_worker</td>";
        echo "<td>$max_judgments_per_ip</td>";
        echo "<td>$units_per_assignment</td>";
        echo "<td>$units_per_job</td>";
        echo "<td>$judgments_per_unit</td>";
        echo "<td>$judgments_per_job</td>";
        echo "<td>$seconds_per_unit</td>";
        echo "<td>$seconds_per_assignment</td>";
        echo "<td>$payment_per_unit</td>";
        echo "<td>$payment_per_assignment</td>";
        echo "<td>$total_payment_per_unit</td>";
        echo "<td>$total_payment_per_job</td>";
        echo "<td>$$payment_per_hour</td>";
        echo "<td>$channels_used</td>";
        echo "<td>$job_comments</td>";
        echo "<td>$job_judgments_made</td>";
        echo "<td>$job_completion</td>";
        echo "<td>$run_time</td>";
        echo "<td title='$job_id'>
        <select class= 'changeStatus'>
        <option value='ChangeStatus'>--Change--</option>
        <option value='Paused'>Pause</option>
        <option value='Running'>Resume</option>
        <option value='Canceled'>Cancel</option>
        <option value='Deleted'>Delete</option></select>
        <dir class = 'cStatus'>$status</dir>
        </td>";
        echo "<td title='$job_id'>
        <a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >
 Link to Results Table
 </a>
  </td>";
    
     echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
			</div>
				<div id="tabs-4">
				<h3>This page is to show results data and to link to various results files</h3>
				</br>
<?php

$results = mysql_query("SELECT * FROM  `results_table` ORDER BY created_date DESC");

echo "<table id='resultstable' class='tablesorter' >";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th title = 'Link to the Origin'>Job ID</th>";
echo "<th title = 'CrowdFlower/Games'>Origin</th>";
echo "<th>Channel (Percentage)</th>";
echo "<th>Average Time</th>";
echo "<th>Actual Time Spent</th>";
echo "<th>Maximum Time</th>";
echo "<th title = 'Click to open the file'>Origin Generated File</th>";
echo "<th># Filtered Sentences</th>";
echo "<th># Filtered Workers</th>";
echo "<th>Statistics Images</th>";
echo "<th>Link</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter

while($row = mysql_fetch_array($results)){

	extract ( $row );

	echo "<tr>";
	echo "<td><a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >$job_id</a></td>";
	echo "<td>$origin</td>";
	echo "<td>$channel_percentage</td>";
	echo "<td>$avg_time</td>";
	echo "<td>$actual_time_spent</td>";
	echo "<td>$max_time</td>";
	echo "<td><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$origin_file_id' class = 'filelinks' >$origin_file_name</a></td>";
	echo "<td><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$filtered_sentences_file_id' class = 'filelinks' >$number_filtered_sentences</a></td>";
	echo "<td><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$filtered_workers_file_id' class = 'filelinks' >$number_filtered_workers</a></td>";
	echo "<td>See Image</td>";
	echo "<td title='$job_id'>
	<a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >
	Link to History Table
	</a>
	</td>";

	echo "</tr>";

	}

	echo "</tbody>";
	echo "</table>";
?>
			
				<br>

				
			</div>
		</div>

	</div>
</body>
</html>