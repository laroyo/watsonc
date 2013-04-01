<?php 
include_once 'includes/dbinfo.php';
include_once 'includes/functions.php';
?>
<!doctype html>
<html lang="us">
<head>
<meta charset="utf-8">
<title>Watson-Crowdsourcing</title>
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
<script src="js/huimain.js" type="text/javascript"></script>
<script language="javascript">

if (window.File && window.FileReader && window.FileList && window.Blob) {
	//alert("this works");
} else {
  alert('The File APIs are not fully supported in this browser.');
}

function handleFileSelect(evt) {
	//alert("event ok");
	var files = evt.target.files; // FileList object
    	var reader = new FileReader();
	reader.onload = function(theFile) {    
        	str = theFile.target.result;            // load file values
        	var lines = str.split(/[\r\n|\n]+/);    // split data by line
		document.getElementById('sentences').value = lines.length - 2;
	}
	reader.onerror = function() { console.log('Error reading file');}       // error message    
        reader.readAsText(files[0]);
  }
window.addEventListener('DOMContentLoaded', pageCompleteListener, false);

function pageCompleteListener(event) {
	//alert("ok2");
	document.getElementById('uploadedfile').addEventListener('change', handleFileSelect, false);
}

function computePayment()
{
	var payment_per_sentence = document.getElementById("payment_per_sentence");
	var payment_per_job = document.getElementById("payment_per_job")

	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var payment_per_assignment = document.getElementById("payment").value;
	var judgments_per_unit = document.getElementById("judgments_per_unit").value;
	var total_sentences = document.getElementById("sentences").value;
	

	if (units_per_assignment != "" && payment_per_assignment != "" && judgments_per_unit != "" && total_sentences != "") {
		//alert("ok"+units_per_assignment);
 	payment_per_sentence.value = ((parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) + (parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) * 46.35 / 100 ) / 100 ;
	payment_per_job.value = parseInt(total_sentences) * payment_per_sentence.value;
 //	alert(((parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) + (parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) * 46.35 / 100 ) / 100);
	}
}

function computeTime() {
	var seconds_per_unit = document.getElementById("seconds_per_unit").value;
	var units_per_assignment = document.getElementById("units_per_assignment").value;
	var seconds_per_assignment = document.getElementById("seconds_per_assignment");
	seconds_per_assignment.value = parseInt(seconds_per_unit) * parseInt(units_per_assignment);
}

</script>




</head>
<body>
	<div id="content">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1" >Home</a></li>
			    <li><a href="#tabs-Raw">Upload Raw</a></li>
			    <li><a href="#tabs-ProcessInput">Process Input</a></li>
				<li><a href="#tabs-2" >Jobs</a></li>
				<li><a href="#tabs-3" >History</a></li>
				<li><a href="#tabs-4" >Statistics</a></li>
			</ul>
			<div id="tabs-1" >
				<h1>Watson-Crowdsourcing</h1>
				<br> <a href="http://en.wikipedia.org/wiki/Crowdsourcing"><img
					src="graphs/crowdsourcing.jpg" alt="No show" title = "What is the Crowdsourcing?" /></a>
			</div>
			<div id="tabs-Raw">
			
				<h3>Upload Raw Files</h3>
				<form enctype="multipart/form-data" action="services/uploadRaw.php" method="POST">
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
				</form>
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
				<form enctype="multipart/form-data" action="/wcs/crowdflower/indexcrowdflower.php" method="POST" 

id="form">
<div class="borderframe"  > 
	<div class="labelfield">Choose a file to upload:</div>
	 <div class="inputfield"><input name="uploadedfile" type="file" id="uploadedfile"/> <br />
	<input type="hidden" name="sentences" id="sentences" /></div>
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

name="template" value="t4"> Relations without definitions and without extra questions <br /></div>
	<div class="labelfield">&nbsp;</div><div class="inputfield"><input type="submit" name="action" 

value="Create Job" /><br /> <br /></div>

<div class="labelfield">Payment per sentence (dollar):</div><div class="inputfield"> <input 

type="text" name="payment_per_sentence" id="payment_per_sentence"> <br /></div>
<div class="labelfield">Payment per job (dollar):</div><div class="inputfield"> <input type="text" 

name="payment_per_job" id="payment_per_job"> <br /></div>
<div class="labelfield">Seconds per assignment:</div><div class="inputfield"> <input type="text" 

name="seconds_per_assignment" id="seconds_per_assignment"> <br /></div>
</div>
</form> 
			</div>
			
			
			<div id="tabs-3">
				<h3>This page is to show the history of jobs created on CrowdFlower</h3>
				<p style ="font-size: 80%" >A sentence is an unit;  An assignment is composed sentences;  A job is composed assignments.</p>
				<p style ="font-size: 80%">All the payments are in cents;  Job Completion is in percentage;  Run Time is in days.</p>
				<br>
				<?php 				
			
				$result = mysql_query("SELECT * FROM `cfinput` WHERE 1");
				
				/* Update run_time in the database */
				while($item = mysql_fetch_array($result))
              {

	           //$date_diff = round(abs(strtotime(date('Y-m-d H:i:s'))-strtotime($row[3]))/86400);
	           $date2 = date('Y-m-d H:i:s');
	           $date1 = $item["created_date"];
	           $ts1 = strtotime($date1);
	           $ts2 = strtotime($date2);
	
	           $date_diff = ($ts2 - $ts1)/86400;   //24*60*60
	           $temp = mysql_query("Update cfinput Set run_time = '$date_diff' Where job_id = '{$item["job_id"]}' ");
              }
             ?>
				
				
<!--  <button class="search" data-filter-column="10" data-filter-text="2?%">Saved Search</button> (search the Discount column for "2?%") -->
  <button class="reset" title = "Click to clear all the filter options" >Reset Search</button> <!-- targetted by the "filter_reset" option -->
  <br>
<?php

$result = mysql_query("SELECT * FROM `cfinput` WHERE 1");

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
//echo "<th>Assignments Per Job</th>"; 
echo "<th>Units Per Job</th>";
echo "<th>Judgements Per Unit</th>";
echo "<th title = 'Judgements Per Unit * Units Per Assignment'>Judgements Per Assignment</th>";	
echo "<th title = 'Judgements Per Unit * Units Per Job'>Judgements Per Job</th>";
echo "<th>Payment Per Unit</th>";
echo "<th title = 'Payment Per Unit * Units Per Assignment'>Payment Per Assignment</th>";
//echo "<th title = 'Payment Per Unit * Units Per Job'>Payment Per Job</th>";
echo "<th title = 'Payment Per Unit * Judgements Per Unit'>Total Payment Per Unit</th>";
//echo "<th title = 'Total Payment Per Unit * Units Per Assignment'>Total Payment Per Assignment</th>";
echo "<th title = 'Total Payment Per Unit * Units Per Job'>Total Payment Per Job</th>";
echo "<th>Comments</th>";
echo "<th>Job Judgements Made</th>";
echo "<th title = 'Job Judgements Made / Judgements Per Job'>Job Completion</th>";
echo "<th title = 'Days'>Run Time</th>";
echo "<th>Status</th>";
echo "<th>Actions</th>";
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
  //      echo "<td>$assignments_per_job</td>";
        echo "<td>$units_per_job</td>";
        echo "<td>$judgements_per_unit</td>";
        echo "<td>$judgements_per_assignment</td>";
        echo "<td>$judgements_per_job</td>";
        echo "<td>$payment_per_unit</td>";
        echo "<td>$payment_per_assignment</td>";
   //     echo "<td>$payment_per_job</td>";
        echo "<td>$total_payment_per_unit</td>";
  //      echo "<td>$total_payment_per_assignment</td>";
        echo "<td>$total_payment_per_job</td>";
        echo "<td>$job_comments</td>";
        echo "<td>$job_judgements_made</td>";
        echo "<td>$job_completion</td>";
        echo "<td>$run_time</td>";
        echo "<td title='$job_id'>
  <select class= 'changeStatus'>
  <option value='ChangeStatus'>--Change Status--</option>
  <option value='Paused'>Pause</option>
  <option value='Running'>Resume</option>
  <option value='Canceled'>Cancel</option>
  <option value='Deleted'>Delete</option></select>
  <dir class = 'cStatus'>$status</dir>
  </td>";
     echo "<td title='$job_id'>   
  <select class= 'takeAction'>
  <option value='0'>--Take Action--</option>
  <option value='Extract'>Extract</option>
  <option value='Analyze'>Analyze</option> 
  </select> 
   <select class= 'extractedFiles'>
  <option value='0'>--Extracted Files--</option>
  </select>
   <select class= 'analyzedFiles'>
  <option value='0'>--Analyzed Files--</option>
  </select>
  </td>";
    
     echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
			</div>
				<div id="tabs-4">
				<p>Get Files</p>
				<br>
				<div id="statisticsarea"></div>
			</div>
		</div>

	</div>
</body>
</html>