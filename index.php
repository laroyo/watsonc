<?php 
include_once 'includes/dbinfo.php';
include_once 'includes/functions.php';


?>
<!doctype html>
<html lang="us">
<head>
<meta charset="utf-8">
<title>Crowd-Watson</title>
<!-- Style sheets  -->
<link href="plugins/jquery-ui/css/pepper-grinder/jquery-ui-1.10.2.custom.css" rel="stylesheet">
<link href="plugins/Mottie-tablesorter/css/theme.default.css" rel="stylesheet" type="text/css" />	
<link href="plugins/multiselect/css/jquery.multiselect.css" rel="stylesheet" type="text/css" />	

<link href="css/huimain.css" rel="stylesheet">
<!-- js libraries  -->
<script src="plugins/jquery-ui/js/jquery-1.9.1.js"></script>
<script src="plugins/jquery-ui/js/jquery-ui-1.10.2.custom.js"></script>
<script src="plugins/Mottie-tablesorter/js/jquery.tablesorter.js" type="text/javascript"></script>
<script src="plugins/Mottie-tablesorter/js/jquery.tablesorter.widgets.js" type="text/javascript"></script>
<script src="plugins/multiselect/js/jquery.multiselect.js" type="text/javascript"></script>
<script src="plugins/galleria/galleria-1.2.9.min.js" type="text/javascript"></script>
<script src="plugins/waypoints.js" type="text/javascript"></script>

<script src="js/huimain.js" type="text/javascript"></script>
<script language="javascript">


function getselectedjobid()
{
	 
	 var selectedjobid = document.getElementById(spamblockjobid).value;
	 return selectedjobid;
}



$(document).ready(function() {
	
	 Galleria.loadTheme('plugins/galleria/themes/classic/galleria.classic.min.js');
     Galleria.run('#galleria');
	
 });


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
	<div class="wrapper" >
		<div id="tabs">
		<h3 class="ui-tab-title"><a href ="http://www.slideshare.net/laroyo/websci2013-harnessing-disagreement-in-crowdsourcing" target="_blank" class = 'titlelinks' title = "WebSci2013 Harnessing Disagreement in Crowdsourcing" >Crowd-Watson</a></h3>
			<ul>
				<li><a href="#tabs-1" >Home</a></li>
			   <!-- <li><a href="#tabs-2">Configurate Raw</a></li> -->
			    <li><a href="#tabs-3">Input</a></li>
				<li><a href="#tabs-4" >Jobs</a></li>
				<li><a href="#tabs-5" >History</a></li>
				<li><a href="#tabs-6" >Results</a></li>
				<li><a href="#tabs-7" >About</a></li>
			</ul>
			
			
			
			<div id="tabs-1" class = "generaltab" >
			</br>
			<div class="generalborderframe"  >
				<h1 align="center" >Module Introduction</h1>
				</br></br> 
			</div>
			</br>
			   <div id="galleria">
	            <img src="graphs/crowd-truth/CT01.jpg">
	            <img src="graphs/crowd-truth/CT02.jpg">
	            <img src="graphs/crowd-truth/CT03.jpg">
	            <img src="graphs/crowd-truth/CT04.jpg">
	            <img src="graphs/crowd-truth/CT05.jpg">
	            <img src="graphs/crowd-truth/CT06.jpg">
	            <img src="graphs/crowd-truth/CT07.jpg">
	            <img src="graphs/crowd-truth/CT08.jpg">
	            <img src="graphs/crowd-truth/CT09.jpg">
	            <img src="graphs/crowd-truth/CT10.jpg">
	            <img src="graphs/crowd-truth/CT11.jpg">
	            <img src="graphs/crowd-truth/CT12.jpg">
	        </div>
			</br>
			</div>
			<!-- 
			
			<div id="tabs-2" class = "generaltab" >
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
					<div class="labelfield" >Comments:</div>
					<div class="inputfield"><input type="text" name="raw_comment" class = 
                               "commentboxInput"/></div>
                    <div class="labelfield">&nbsp;</div>
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
			 -->
			
			
			<div id="tabs-3" class = "generaltab" >
			
	  <div id="accordion">	
	 
  <h5>CrowdFlower</h5>
<div id="tabs-ProcessInput">
				<div id="preprocessarea" class="borderframe">
				</div>
			</div>
			<h5>Games</h5>
			<div id="tabs-Raw">	
			<form enctype="multipart/form-data" action="services/uploadRaw.php" method="POST">
				<div class="borderframe"  >
					<div class="labelfield">Choose a RAW file to upload:</div>
					<div class="inputfield">
						<input name="rawuploadedfile" type="file" />
					</div>
					<div class="labelfield">Seed releation</div>
					<div class="inputfield">
						<input type="text" name="title" class="textboxInput" />
					</div>
					<div class="labelfield" >Comments:</div>
					<div class="inputfield"><input type="text" name="raw_comment" class = 
                               "commentboxInput"/></div>
                    <div class="labelfield">&nbsp;</div>
					<div class="inputfield">
						<input type="submit" value="Submit"
							title="Click Submit to upload raw file" />
					</div>
					</div>
				</form>
				</div>
			</div>
			</br>
  </div>
  
  
  
			<div id="tabs-4" class = "generaltab" >
			<div id="accordion">

  <h5>CrowdFlower</h5>
<div>
				
			
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
        echo "<td><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$file_id' class = 'filelinks' >$original_name</a></td>";
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
						<form enctype="multipart/form-data"
							action="/wcs/crowdflower/indexcrowdflower.php" method="POST"
							id="form">
							<div class="borderframe">
								<div class="labelfield">Choose a file to upload:</div>
								<div class="inputfield">
									<input name="uploadedfile" type="button" id="uploadedfile"
										value="Choose Server File" /> <input type="hidden"
										name="fileid" id="fileid" /> <input type="hidden"
										name="sentences" id="sentences" /> <label for="uploadedfile"
										style="font-size: 80%">No File Chosen</label>
								</div>
								<div class="labelfield">Job title:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="title"> <br />
								</div>

								<div class="labelfield">Judgments per unit:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="judgments_per_unit"
										id="judgments_per_unit" oninput="computePayment()"> <br />
								</div>

								<div class="labelfield">Maxim judgments per worker:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="max_judgments_per_worker"> <br />
								</div>

								<div class="labelfield">Units per assignment (CF: Units per
									page):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="units_per_assignment"
										oninput="computePayment()" id="units_per_assignment"> <br />
								</div>

								<div class="labelfield">Payment per assignment (CF: Payment per
									page) (cents):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment" oninput="computePayment()"
										id="payment"> <br />
								</div>

								<div class="labelfield">Seconds per unit:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="seconds_per_unit"
										id="seconds_per_unit" oninput="computeTime()"> <br />
								</div>

								<div class="labelfield"
									title="The purposes or notes of creating the job">Comments:</div>
								<div class="inputfield">
									<input type="text" name="job_comment" class="commentboxInput" />
								</div>

								<div class="labelfield">Choose the template:</div>
								<div class="inputfield">
									<input type="radio" name="template" value="t1" checked>

									Relations with definitions and extra questions required <br />
								</div>

								<div class="labelfield">&nbsp;</div>
								<div class="inputfield">
									<input type="radio" name="template" value="t2"> Relations with
									definitions but without extra questions <br />
								</div>

								<div class="labelfield">&nbsp;</div>
								<div class="inputfield">
									<input type="radio" name="template" value="t3"> Relations
									without definitions and extra questions required <br />
								</div>

								<div class="labelfield">&nbsp;</div>
								<div class="inputfield">
									<input type="radio" name="template" value="t4"> Relations
									without definitions and without extra questions <br /> <br>
								</div>


								<div class="labelfield">Choose the channels:</div>
								<div class="inputfield">
									<input type="radio" name="channels" value="c1" checked> Amazon
									Mechanical Turk <br />
								</div>

								<div class="labelfield">&nbsp;</div>
								<div class="inputfield">
									<input type="radio" name="channels" value="c2"> All channels <br />
								</div>


								<div class="labelfield">&nbsp;</div>
								<div class="inputfield">
									<input type="submit" name="action" value="Create Job" /><br />
									<br />
								</div>

								<div class="labelfield">Payment per sentence (dollar):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment_per_sentence"
										id="payment_per_sentence"> <br />
								</div>
								<div class="labelfield">Payment per job (dollar):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment_per_job" id="payment_per_job">
									<br />
								</div>
								<div class="labelfield">Seconds per assignment:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="seconds_per_assignment"
										id="seconds_per_assignment"><br />
								</div>

								<div class="labelfield">Payment per hour:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment_per_hour"
										id="payment_per_hour"> <br />
								</div>
							</div>
						</form>
					</div>
			<h5>Games</h5>
  <div>
    <p>Pending</p>
  </div>
  </div>
  </br>
  </div>
			
		
			
			<div id="tabs-5" class = "historytab" >
<div>
				
				<?php 	

				$result = mysql_query("SELECT * FROM `history_table` WHERE 1");

				/* Update run_time in the database */
				while($item = mysql_fetch_array($result))
              {

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
	           $run_time = $days."d ".$hours."h";
	           $updateRuntime = mysql_query("Update history_table Set run_time = '$run_time' Where job_id = '{$item["job_id"]}' and status != 'Finished' ");
              	           	 	           
              }
             ?>
				
			
             
             
             
            <div id = "dialog-blockspammers" title = "Block Spammers" >
            <!-- Load data from Database --!>
            </br>
            Selected Job ID: <input type="text" id = "spamblockjobid" disabled /> 
            </br>
     Spammers Found: <input type="text" id = "spammerfound" disabled /> 
             
            </div>
<!--  <button class="search" data-filter-column="10" data-filter-text="2?%">Saved Filters</button> (search the Discount column for "2?%") -->
  <button class="reset" title = "Click to clear all the filter options" >Reset Filters</button> <!-- targetted by the "filter_reset" option -->
  <button class="passjobid" id = "passjobid"  title = "Click to analyze selected JOB IDs" >Analyze</button> 
<select id="hidecolumns" name="hidecolumns" multiple="multiple" title = "Click to hide/show columns">
<!--<option value="cJobId">Job ID (Batch File)</option>-->
<option value="cOrigin">Origin</option>
<!--<option value="cJobTitle">Job Title</option>-->
<option value="cCreatedDate">Created Date</option>
<option value="cCreatedBy">Created By</option>
<option value="cNumberOfSentences">Number of Sentences</option>
<option value="cTypeofUnits">Type of Units</option>
<option value="cTemplate">Template</option>
<option value="cMaxJudgmentPerWorker">Max Judgment Per Worker</option>
<option value="cUnitsPerAssignment">Units Per Assignment</option>
<option value="cUnitsPerJob">Units Per Job</option>
<option value="cJudgmentsPerUnit">Judgments Per Unit</option>
<option value="cJudgmentsPerJob">Judgments Per Job</option>
<option value="cSecondsPerUnit">Seconds Per Unit</option>
<option value="cSecondsPerAssignment">Seconds Per Assignment</option>
<option value="cPaymentPerUnit">Payment Per Unit</option>
<option value="cPaymentPerAssignment">Payment Per Assignment</option>
<option value="cTotalPaymentPerUnit">Total Payment Per Unit</option>
<option value="cTotalPaymentPerJob">Total Payment Per Job</option>
<option value="cPaymentPerHour">Payment Per Hour</option>
<option value="cChannelUsed">Channel Used</option>
<option value="cChannelsPercentage">Channels Percentage</option>
<option value="cComments" >Comments</option>
<option value="cJobJudgmentsMade">Job Judgments Made</option>
<option value="cJobCompletion">Job Completion</option>
<option value="cRunTime">Run Time</option>
<option value="cAverageTimePerJob">Average Time Per Job</option>
<option value="cMinTimePerJob">Min Time Per Job</option>
<option value="cMaxTimePerJob">Max Time Per Job</option>
<option value="cNumberFilteredSentences">Number Filtered Sentences</option>
<option value="cTotalNumberofWorkers">Total Number of Workers</option>
<option value="cNumberFilteredWorkers">Number Filtered Workers</option>
<option value="cStatus">Status (Results File)</option>
<option value="cActions">Actions</option>
<!--<option value="cJobIDLinktoOrigin">Job ID (Origin)</option>-->
</select>
<input type="hidden" id = "testjobidarray" value = "To test job_ids array"/> 
  <br>
<?php
$history = mysql_query("SELECT * FROM  `history_table` ORDER BY created_date DESC");
echo "<div id='historytableContainer'>";
echo "<table id='historytable' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
//echo "<th  ><input type='checkbox' id='checkboxjob' name ='job_ids[]'/>All</th>";
echo "<th class = 'filter-false sorter-false' ></th>";
echo "<th title = 'Job ID - Click to download Batch File' class='cJobId'>JobID</th>";
echo "<th title = 'Origin' class='cOrigin'>Orig</th>";
//echo "<th title = 'Job Title' class='cJobTitle'>JT</th>";
echo "<th title = 'Created Date' class='cCreatedDate'>Date</th>";
echo "<th title = 'Created By' class='cCreatedBy'>Creater</th>";
echo "<th title = 'Number of Sentences'  class='cNumberOfSentences' >#S</th>";
echo "<th title = 'Type of Units' class='cTypeofUnits'>TypeU</th>";
echo "<th title = 'Template' class='cTemplate'>Tmpl</th>";
echo "<th title = 'Max Judgment Per Worker' class='cMaxJudgmentPerWorker'>Max J/W</th>";
echo "<th title = 'Units Per Assignment' class='cUnitsPerAssignment'>U/A</th>";
echo "<th title = 'Units Per Job' class='cUnitsPerJob'>U/Job</th>";
echo "<th title = 'Judgments Per Unit' class='cJudgmentsPerUnit'>J/U</th>";
echo "<th title = 'Judgments Per Job' class='cJudgmentsPerJob'>J/Job</th>";
echo "<th title = 'Seconds Per Unit' class='cSecondsPerUnit'>s/U</th>";
echo "<th title = 'Seconds Per Assignment' class='cSecondsPerAssignment'>s/A</th>";
echo "<th title = 'Payment Per Unit' class='cPaymentPerUnit'>P/U</th>";
echo "<th title = 'Payment Per Assignment' class='cPaymentPerAssignment'>P/A</th>";
echo "<th title = 'Total Payment Per Unit' class='cTotalPaymentPerUnit'>TotalP/U</th>";
echo "<th title = 'Total Payment Per Job' class='cTotalPaymentPerJob'>TotalP/Job</th>";
echo "<th title = 'Payment Per Hour' class='cPaymentPerHour'>P/H</th>";
echo "<th title = 'Channel Used' class='cChannelUsed'>Chnl</th>";
echo "<th title = 'Channels Percentage' class='cChannelsPercentage'>Chnl%</th>";
echo "<th title = 'Comments'  class='cComments'>Cmt</th>";
echo "<th title = 'Job Judgments Made' class='cJobJudgmentsMade'>JobJ</th>";
echo "<th title = 'Job Completion' class='cJobCompletion'>JobC</th>";
echo "<th title = 'Run Time' class='cRunTime'>RT</th>";
echo "<th title = 'Average Time Per Job' class='cAverageTimePerJob'>Ave T/Job</th>";
echo "<th title = 'Min Time Per Job' class='cMinTimePerJob'>Min T/J</th>";
echo "<th title = 'Max Time Per Job' class='cMaxTimePerJob'>Max T/J</th>";
echo "<th title = 'Number Filtered Sentences' class='cNumberFilteredSentences'>FS</th>";
echo "<th title = 'Total Number of Workers' class='cTotalNumberofWorkers'>TotalW</th>";
echo "<th title = 'Number Filtered Workers' class='cNumberFilteredWorkers'>FW</th>";
echo "<th title = 'Status - Click to download Results File' class='cStatus'>Status</th>";
echo "<th title = 'Actions' class='cActions'>Actions</th>";
//echo "<th title = 'Job ID - Link to Origin' class='cJobIDLinktoOrigin'>JI</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter
while($row = mysql_fetch_array($history)){
	    extract ( $row );
        echo "<tr>";
        echo "<td style ='font-size: 80%' ><input type='checkbox' $checkbox_check id='checkboxjob' name ='job_ids' value = '$job_id'/></td>";
        echo "<td style ='font-size: 80%' class='cJobId' title = '$job_title' ><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$cfbatch_id' class = 'filelinks' >$job_id</a></td>";
		echo "<td style ='font-size: 80%' class='cOrigin'>$origin</td>";
  //    echo "<td style ='font-size: 80%' class='cJobTitle' title = '$job_title' >".substr($job_title,0, 6)."</td>";
	    echo "<td style ='font-size: 80%' class='cCreatedDate' title = '$created_date' >".substr($created_date,2, 8)."</td>";
	    echo "<td style ='font-size: 80%' class='cCreatedBy'>$created_by</td>";
	    echo "<td style ='font-size: 80%' class='cNumberOfSentences'>$nr_sentences_file</td>";
	    echo "<td style ='font-size: 80%' class='cTypeofUnits' title = '$type_of_units' >".substr($type_of_units,0, 6)."</td>";
	    echo "<td style ='font-size: 80%' class='cTemplate' title = '$template_info' >$template</td>";
        echo "<td style ='font-size: 80%' class='cMaxJudgmentPerWorker'>$max_judgments_per_worker</td>";
        echo "<td style ='font-size: 80%' class='cUnitsPerAssignment'>$units_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cUnitsPerJob'>$units_per_job</td>";
        echo "<td style ='font-size: 80%' class='cJudgmentsPerUnit'>$judgments_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cJudgmentsPerJob'>$judgments_per_job</td>";
        echo "<td style ='font-size: 80%' class='cSecondsPerUnit'>$seconds_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cSecondsPerAssignment'>$seconds_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cPaymentPerUnit'>$payment_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cPaymentPerAssignment'>$payment_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cTotalPaymentPerUnit'>$total_payment_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cTotalPaymentPerJob'>$total_payment_per_job</td>";
        echo "<td style ='font-size: 80%' class='cPaymentPerHour'>$payment_per_hour</td>";
        echo "<td style ='font-size: 80%' class='cChannelUsed'>$channels_used</td>";
		echo "<td style ='font-size: 80%' class='cChannelsPercentage cssChildRow' title = '$channels_percentage' >".substr($channels_percentage,0, 6)."</td>";
        echo "<td style ='font-size: 80%' class='cComments'>$job_comments</td>";
        echo "<td style ='font-size: 80%' class='cJobJudgmentsMade'>$job_judgments_made</td>";
        echo "<td style ='font-size: 80%' class='cJobCompletion'>$job_completion</td>";
        echo "<td style ='font-size: 80%' class='cRunTime' title = '$run_time' >".substr($run_time,0, 6)."</td>";
		echo "<td style ='font-size: 80%' class='cAverageTimePerJob'>$avg_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cMinTimePerJob'>$min_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cMaxTimePerJob'>$max_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cNumberFilteredSentences'>$no_filtered_sentences</td>";
		echo "<td style ='font-size: 80%' class='cTotalNumberofWorkers' >$no_workers</td>";
		echo "<td style ='font-size: 80%' class='cNumberFilteredWorkers'><button class = 'blockspammers' title = '$job_id' $checkbox_check >$no_filtered_workers</button></td>";
        echo "<td style ='font-size: 80%' class='cStatus' title = '$job_id' ><a href = 'http://crowd-watson.nl/wcs/services/getFile.php?id=$resultsfile_id' class = '$checkbox_check' >$status</a></td>";
        echo "<td style ='font-size: 80%' class='cActions' title = '$job_id' >
        <select $status_change class= 'cActions changeStatus'>
        <option value='ChangeStatus'>-Change-</option>
        <option value='Paused'>Pause</option>
        <option value='Running'>Resume</option>
        <option value='Canceled'>Cancel</option>
        <option value='Deleted'>Delete</option></select>     
        </td>";
   //     echo "<td style ='font-size: 80%' class='cJobIDLinktoOrigin'><a href = 'https://crowdflower.com/jobs/$job_id'   target='_blank'   class = 'tdlinks' >$job_id</a></td>";
     echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
?>
			</div>


			</br>
  </div>
			
			
			
				<div id="tabs-6" class = "generaltab" >
				<div id="accordion">

  <h5>CrowdFlower</h5>
<div>		
				<h4>So far, the record is stored manually for testing!</h4>
				<div id="dialog-image" style="display: none;">
                <img id="statisticsimage" src=""/>
                </div>
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
	echo "<td><a href = 'http://crowd-watson.nl/wcs/services/showImage.php?id=$statistics_image_id' id = 'showimage' class = 'filelinks' value = '$statistics_image' >$statistics_image
	</a>
	</td>";
	echo "<td title='$job_id'>
	<a href = 'https://crowdflower.com/jobs' target='_blank' class = 'tdlinks' >
	Link to History
	</a>
	</td>";

	echo "</tr>";

	}

	echo "</tbody>";
	echo "</table>";
?>		
			</div>
			<h5>Games</h5>
  <div>
    <p>Pending</p>
  </div>
  </div>
  </br>
  </div>
  
  
  <div id="tabs-7" class = "generaltab" >
  </br>
	<div class="generalborderframe"  >
	
	<a href="http://sciencetoprofitsblog.com/2012/02/15/second-opinions-ibm-watson-crowdsourcing/" target="_blank" ><img
	src="graphs/icon-watson.jpg" alt="No show" title = "Crowdsourcing for Watson" style="width: 12%; height: 12%"  /></a>
	
	
	<a href="http://www.ibm.com/us/en/" target="_blank" ><img
	src="graphs/IBM.jpg" alt="No show" title = "IBM"  style="width: 12%; height: 12%" /></a>
	
	<a href="http://www.vu.nl/en/index.asp" target="_blank" ><img
		src="graphs/VU.jpg" alt="No show" title = "VU University Amsterdam"  style="width: 21%; height: 21%" /></a>
		</br>
		</br>
	<p>This project is a collaboration between the VU University Amsterdam and IBM Research, NY.
	</br>
	</br>
	Principle investigators:
		</br>
	
	<a href="http://www.cs.vu.nl/~laroyo/" target="_blank" title = "Professor in the Computer Science department of VU University Amsterdam" class = "filelinks" >Lora Aroyo</a>, VU University Amsterdam	
	</br>
	<a href="http://researcher.watson.ibm.com/researcher/view.php?person=us-welty" target="_blank" title = "Research Scientist at the IBM T.J. Watson Research Center in New York" class = "filelinks" >Chris Welty</a>, IBM Research, NY
	</br>
	</br>

	Project members:
		</br>
	<a href="http://nl.linkedin.com/in/dumitracheanca/" target="_blank" class = "filelinks" >Anca Dumitrache</a>
	</br>
	<a href="https://plus.google.com/u/0/106755161022646926513/posts" target="_blank" class = "filelinks" >Guillermo Soberon Casado</a>
	</br>
	<a href="http://nl.linkedin.com/pub/hui-lin/53/92/b5/" target="_blank" class = "filelinks" >Hui Lin</a>
	</br>
	<a href="http://nl.linkedin.com/pub/oana-inel/5a/99/711/" target="_blank" class = "filelinks" >Oana Inel</a>
	</br>
	<a href="http://nl.linkedin.com/pub/manfred-overmeen/0/445/567/" title = "Senior IT Specialist at IBM Nederland BV" target="_blank" class = "filelinks" >Manfred Overmeen</a>, IBM Nederland BV	
	</br>
	<a href="http://nl.linkedin.com/in/rsips/" target="_blank" title = "University Relations Manager at IBM Nederland BV" class = "filelinks" >Robert-Jan Sips</a>, IBM Nederland BV
	</br>
	</br>
	Want to join?
			</br>
			If you are interested in a MSc, MSc or a PhD project in this context, send you CV and motivation to <a href="mailto:lora.aroyo@vu.nl">lora.aroyo@vu.nl</a>
			</br>
			</br>
			Here are some example projects currently running:</p>
			</br>
	<img src="graphs/crowd-watson/CW23v2.JPG" class = "center" style="width: 65%; height: 65%" >
	</br>
 </div>
 </br>
</div>
  
  </div>
 
  <div class="push"></div>
  </div> 
  <div class="footer" >
	<h5 align = "center" class = "copyrightfooter" >Copyright © 2013 <a href = 'http://crowd-watson.nl/wcs/' target="_blank" class = 'filelinks' title = "http://crowd-watson.nl/wcs/" >Crowd-Watson</a>. All rights reserved.</h5>

<h5 align = "center" class = "copyrightfooter" >| <a href = '#tabs-7'  id = "tofive" class = 'filelinks' >About</a> | <a href = 'http://mailman.few.vu.nl/mailman/listinfo/crowd-watson-all' target="_blank" class = 'filelinks' title = "Crowd-watson-all" >Contact</a> | Terms & Conditions | Privacy Policy |</h5>
	</div>
		
		
		
</body>
</html>
