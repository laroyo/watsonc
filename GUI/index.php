<?php 
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
?>

<!doctype html>
<html lang="us">
<head>
<meta charset="utf-8">
<title>Crowd-Watson</title>
<!-- Style sheets  -->
<link href="/wcs/GUI/plugins/jquery-ui/css/pepper-grinder/jquery-ui-1.10.2.custom.css" rel="stylesheet">
<link href="/wcs/GUI/plugins/Mottie-tablesorter/css/theme.default.css" rel="stylesheet" type="text/css" />	
<link href="/wcs/GUI/plugins/multiselect/css/jquery.multiselect.css" rel="stylesheet" type="text/css" />	
<link href="/wcs/GUI/css/huimain.css" rel="stylesheet">
<!-- js libraries  -->
<script src="/wcs/GUI/plugins/jquery-ui/js/jquery-1.9.1.js"></script>
<script src="/wcs/GUI/plugins/jquery-ui/js/jquery-ui-1.10.2.custom.js"></script>
<script src="/wcs/GUI/plugins/Mottie-tablesorter/js/jquery.tablesorter.js" type="text/javascript"></script>
<script src="/wcs/GUI/plugins/Mottie-tablesorter/js/jquery.tablesorter.widgets.js" type="text/javascript"></script>
<script src="/wcs/GUI/plugins/multiselect/js/jquery.multiselect.js" type="text/javascript"></script>
<script src="/wcs/GUI/plugins/galleria/galleria-1.2.9.min.js" type="text/javascript"></script>
<script src="/wcs/GUI/js/huimain.js" type="text/javascript"></script>

</head>
<body>
	<div class="wrapper" >
		<div id="tabs">
		<h3 class="ui-tab-title"><img src='/wcs/GUI/img/icon-watson.jpg' width="14" height="14" /> <a href ="http://www.slideshare.net/laroyo/websci2013-harnessing-disagreement-in-crowdsourcing" target="_blank" class = 'titlelinks' title = "WebSci2013 Harnessing Disagreement in Crowdsourcing" >Crowd-Watson</a></h3>
			<ul>
				<li><a href="#tabs-1" ><span class="ui-icon ui-icon-home" style="display:inline-block"></span>Home</a></li>
			   <!-- <li><a href="#tabs-2">Configurate Raw</a></li> -->
			    <li><a href="#tabs-3"><span class="ui-icon ui-icon-refresh" style="display:inline-block"></span>Input</a></li>
				<li><a href="#tabs-4" ><span class="ui-icon ui-icon-cart" style="display:inline-block"></span>Jobs</a></li>
				<li><a href="#tabs-5" ><span class="ui-icon ui-icon-clock" style="display:inline-block"></span>History</a></li>
			   <!-- <li><a href="#tabs-6" >Results</a></li> -->
				<li><a href="#tabs-7" ><span class="ui-icon ui-icon-contact" style="display:inline-block"></span>About</a></li>
			</ul>
		
			<div id="tabs-1" class = "generaltab" >
			</br>
			   <div id="galleria">
	            <img src="/wcs/GUI/img/crowd-truth/CT01.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT02.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT03.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT04.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT05.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT06.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT07.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT08.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT09.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT10.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT11.jpg">
	            <img src="/wcs/GUI/img/crowd-truth/CT12.jpg">
	        </div>
			</br>
			<div class="generalborderframe"  >
			<!--<h1 align="center">Module Introduction</h1>-->
				<h1 align="center">Crowdsourcing for Watson</h1>
				</br>
				<h3 style = "color: blue;" ><span class="ui-icon ui-icon-refresh" style="display:inline-block"></span>Input</h3>
				<strong>The Input tab contains the following input fields (CF):</strong></br>
• 	Upload of the text files that need to be processed (an entire folder will be uploaded on the
server)</br>
• 	Select the filters that should be applied on the files; three types of filters are available:</br>
◦ 	Special cases filtering:</br>
▪ 	Special case – semicolon between the two terms</br>
▪ 	Special case – one term between brackets</br>
▪ 	No semicolon between the two terms</br>
▪ 	No term between brackets</br>
▪ 	No special cases (sentences without semicolon between the two terms and without terms
    between brackets)</br>
◦  	Relation mentioned filtering:</br>
▪ 	Relation mentioned between the two terms</br>
▪ 	Relation mentioned but not between the two terms</br>
▪ 	No relation mentioned</br>
◦ 	Sentence length:</br>
▪ 	Short sentences</br>
▪ 	Long sentences</br>
• 	Select how many sentences should be chosen from each relation file </br>
<strong>Input for Games:</strong> </br>
• 	Upload the raw files that need to be processed </br>
◦ 	The file will be saved on the server</br>
◦ 	The file will be registered at the database
				</br>
				</br>
				</br>
				<h3 style = "color: blue;" ><span class="ui-icon ui-icon-cart" style="display:inline-block"></span>Jobs</h3>
				<strong>The Jobs tab contains the following input fields (CF):</strong> </br>
					•	Upload of the data file (job file).</br>
					•	Job settings needed by CF: job title, number of judgments per unit, number of units per assignment, maximum number of judgments that one worker can make, payment per assignment, seconds per unit. </br>
					•	The maximum number of judgments from the same IP address is always set to be equal to the maximum number of judgments that one worker can make. </br>
					•	A comment field for adding extra information related to the job/file.</br>
					•	For the job instructions and the assignment visualization four templates are available:</br>
					◦	Extra questions required + definition and example added after each relation</br>
					◦	Extra questions required + definition and example on mouse hover over one relation</br>
					◦	No extra question required + definition and example added after each relation</br>
					◦	No extra question required + definition and example on mouse hover over one relation</br>
					•	Channels for publishing the job:</br>
					◦	Amt (Amazon Mechanical Turk and CrowdFlower Internal Interface)</br>
					◦	All – all the channels available at that moment (first we execute a GET request to CF for retrieving all the available channels; next, we set the channels to that list)</br>
					
					<strong>Additional information about the job is computed based on the user input:</strong></br>
					•	After the job file is selected, we compute the number of units (number of lines – 1)</br>
					•	When the fields: judgments per unit, units per assignment and payment per assignment are added, we compute the total payment for one sentence (unit) </br>
					•	When the fields: judgments per unit, units per assignment and payment per assignment are added and the number of units in the file is known, we compute the total payment per job </br>
					•	When the field seconds per unit is added, we compute the seconds per assignment</br>
					•	After the value of the field seconds per assignment is added, the payment per hour is computed </br>
					•	After creating a job and setting the price, CF includes a markup of 33% in the cost of the job; thus, the total payment per one unit and the total payment per job are computed using this additional percentage </br>
					<strong>All the information about the job are stored in a history table.</strong>
				</br>
				</br>
				</br>
				<h3 style = "color: blue;" ><span class="ui-icon ui-icon-clock" style="display:inline-block"></span>History</h3>
				<strong>The History tab contains the following functions:</strong> </br>
				• 	Indicate integrated data</br>
				• 	Track parameters, statuses and results of jobs </br>
				• 	Enable to change status of jobs and synchronize the changes in the database and origin </br>
				• 	Indicate the completion of each job </br>
				• 	Enable to download both batch files (through Job ID link) and results files (jobs with Finished Status) </br>
				• 	Enable to block spammers after clicking on Filtered Workers data of a particular finished job: </br>
				◦	Check Worker ID(s) and fill in the reason to flag spammers relative the selected job</br>
				◦	Click on a particular Worker ID to visulize the Worker Analytics in a new Web page </br>
				◦	The Worker Analytics page generates charts for Annotated Sentences and Task Completion Times relative to the selected worker</br>
				• 	Enable to select Job ID(s) to generate or view statistical results dynamically in the pop-up Results pages, after clicking on the Analyze button: </br>
				◦	Data "Structuring" (refine and preprocess data, evolve structures to ease analysis, other transformations) </br>
				◦	Filtering, labeling and classification: to separate (filter or select) certain data, according to defined function or criteria. This may be used for operations such as spam detection, outlier removal, and the like. </br>
				◦	Analysis: draw statistics from the data (TBC) </br>
				• 	Enable to sort and filter many types of data including linked data in a cell without page refreshes, based on different columns 
				</br>
				</br>
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
			<!--	<div id="preprocessarea" class="borderframe"> --!>
<div id="dialog-confirm-preprocessing" title="Select a folder from the server">
	<button class="reset" title = "Click to clear all the filter options" >Reset Filters</button> <!-- targetted by the "filter_reset" option -->
<br>
  <?php

$result = mysql_query("SELECT s.storage_path, s.original_name, r.comment
FROM  file_storage s
INNER JOIN raw_file as r on s.id = r.fileid
WHERE s.storage_path like \"%TextFiles%\"");

echo "<table id='selectfolder' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th>Folder Name</th>";
echo "<th>Content Files</th>";
echo "<th>Comment</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter

$indexFolder = 0;
$outputData = array();
while($row = mysql_fetch_array($result)){

	    extract ( $row );

	$folderName = substr($storage_path, 0, strrpos($storage_path, "/"));
	if (array_key_exists($folderName, $outputData)) {
		$outputData[$folderName][0] .= "\n".$original_name; 
	}
	else {
		$outputData[$folderName] = array();
		$outputData[$folderName][0] = $original_name;
		$outputData[$folderName][1] = $comment;
	}
//        echo "<tr>";
//        echo "<td><input type='radio' id='radiofilepreprocessing' name ='radiofilepreprocessing'/>$folderName</label></td>";
//        echo "<td>$original_name</td>";
//        echo "<td>$comment</td>";
//        echo "</tr>";
}

foreach ($outputData as $folder => $content) {
	echo "<tr>";
        echo "<td><input type='radio' id='radiofilepreprocessing' name ='radiofilepreprocessing'/>$folder</label></td>";
        echo "<td>$content[0]</td>";
        echo "<td>$content[1]</td>";
        echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
</div>
<form enctype="multipart/form-data" action="/wcs/preprocessing/indexpreprocessing.php" method="POST" id="form" name="form">
	<div class="labelfield">Select your input data files from your local machine: </div><input type="file" multiple name="uploadedfilepreproc[]" webkitdirectory="" id="uploadedfilepreproc"  /> <br /> or <br />
	<div class="labelfield"> Select your input data files from your server machine: </div>
	<div class="inputfield">
		<input name="uploadedfilepreprocessing" type="button" id="uploadedfilepreprocessing" value="Choose Server Folder" /> 
		<input type="hidden" name="foldername" id="foldername" /> 
		<input type="hidden" name="filenames" id="filenames" /> 
		<label for="uploadedfilepreprocessing" style="font-size: 75%">No Folder Chosen</label>
	</div>
	Provide description of your data: <br /> <input type="text" name="files_comment" id="files_comment" class="commentboxInput"/> <br />

	Select data filters to be applied: <br />
	
	<input type="checkbox" name="filters[]" id="filters1" value="specialcases" checked onchange="checkForFilters1()"> Filters for Special Cases <br />
		<input type="radio" name="specialcases" id="specialcases" value="withSemicolon"> Select sentences with SEMICOLON <br />
		<input type="radio" name="specialcases" id="specialcases" value="withTermBetweenBr"> Select sentences with AT LEAST ONE OF THE TERMS BETWEEN BRACKETS <br />
		<input type="radio" name="specialcases" id="specialcases" value="noSemicolon"> Filter out sentences with SEMICOLON <br />
		<input type="radio" name="specialcases" id="specialcases" value="noTermBetweenBr"> Filter out sentences with AT LEAST ONE OF THE TERMS BETWEEN BRACKETS <br />
		<input type="radio" name="specialcases" id="specialcases" value="noSpecialCase" checked> Filter out sentences with SPECIAL CASES <br /><br />

	<input type="checkbox" name="filters[]" id="filters2" value="relations" checked onchange="checkForFilters2()"> Filters for Relation Mentions <br />
		<input type="radio" name="relation" id="relation" value="withRelationsBetween"> Select sentences with a relation mentioned between the two terms <br />
		<input type="radio" name="relation" id="relation" value="withRelationsOutside"> Select sentences with a relation mentioned outside the two terms <br />
		<input type="radio" name="relation" id="relation" value="noRelation" checked> Filter out sentences with any relation mentioned in the sentence <br /><br />

	<input type="checkbox" name="filters[]" id="filters3" value="length" checked onchange="checkForFilters3()"> Filters for Sentence Length <br />
		<input type="radio" name="length" id="length" value="long"> Select only LONG sentences <br />
		<input type="radio" name="length" id="length" value="shortAndAverage" checked> Select only SHORT sentences <br /><br />

	Indicate the number of sentences per relation name to be included in the sentence batch: <br />
		<input type="text" id="noscause" name="noscause" size="2">  Cause <br />
		<input type="text" id="noscontra" name="noscontra" size="2">  Contraindicate <br />
		<input type="text" id="nosdiagnose" name="nosdiagnose" size="2">  Diagnose <br />  
		<input type="text" id="noslocation" name="noslocation" size="2">  Location <br />
		<input type="text" id="nosprevent" name="nosprevent" size="2">  Prevent <br />
		<input type="text" id="nossymptom" name="nossymptom" size="2">  Symptom <br />
		<input type="text" id="nostreat" name="nostreat" size="2">  Treat <br /> 

<!--
	Create the job file: <br />
	Number of sentences from each relation: <input type="text" id="nosentences" name="nosentences" size="3"> 
--!>
<br />
	<input type="submit" name="action" value="Create & Save Batch"/>
</form>
	</div>
			<h5>Games</h5>
			<div id="tabs-Raw">	
			<form enctype="multipart/form-data" action="/wcs/services/uploadRaw.php" method="POST">
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
				<button class="reset" title = "Click to clear all the filter options" >Reset Filters</button> <!-- targetted by the "filter_reset" option -->
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
echo "<th>Job Id</th>";
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
	echo "<td>$job_id</td>";
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
								<div class="labelfield">Select batch for your job:</div>
								<div class="inputfield">
									<input name="uploadedfile" type="button" id="uploadedfile"
										value="Choose Server File" /> <input type="hidden"
										name="fileid" id="fileid" /> <input type="hidden"
										name="sentences" id="sentences" /> <label for="uploadedfile"
										style="font-size: 75%">No File Chosen</label>
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
									page) (in cents):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment" oninput="computePayment()"
										id="payment"> <br />
								</div>

								<div class="labelfield">Seconds per unit:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="seconds_per_unit"
										id="seconds_per_unit" oninput="computeTime()">
								</div>
								 
								<div class="labelfield"
									title="The purposes or notes of creating the job">Provide description of your job:</div>
								<div class="inputfield">
									<input type="text" name="job_comment" class="commentboxInput" /><br />
								</div>
								<br />
								<div class="labelfield">Choose the template:</div>
								<div class="inputfield">&nbsp;<br /></div>
								<div class = "combinedfield">
									<input type="radio" name="template" value="t1" checked> T1: Relations with (mouse-over) definitions and extra questions required<br />
								</div>

							
								<div class = "combinedfield">
									<input type="radio" name="template" value="t2">

									T2: Relations with (text) definitions and extra questions required  <br />
								</div>

								
								<div class = "combinedfield">
									<input type="radio" name="template" value="t1a"> T1A: Relations with (mouse-over) definitions and without extra questions  <br /> 
								</div>

								
								<div class = "combinedfield">
									<input type="radio" name="template" value="t2a"> T2A: Relations with (text) definitions and without extra questions <br />
								</div>
								
								
								<div class = "combinedfield">
								<input type="radio" name="template" value="t1b" >

									T1B: Relations with (mouse-over) definitions, extra questions required and automatic text field<br />
								</div>

								
								<div class = "combinedfield">
									<input type="radio" name="template" value="t2b"> T2B: Relations with (text) definitions and extra questions required and automatic text field  <br />
								</div>

								
								<div class = "combinedfield">
									<input type="radio" name="template" value="t1ab"> T1AB: Relations with (mouse-over) definitions and without extra questions and automatic text field  <br />
								</div>

								
								<div class = "combinedfield">
									<input type="radio" name="template" value="t2ab"> T2AB: Relations with (text) definitions and extra without questions and automatic text field  <br /> <br /> 
								</div>
								
								<div class="labelfield">Choose worker channels:</div>
								<div class="inputfield">&nbsp;</br></div>
								<div class = "combinedfield">
									<input type="radio" name="channels" value="c1" checked> Amazon
									Mechanical Turk <br />
								</div>

							
								<div class = "combinedfield">
									<input type="radio" name="channels" value="c2"> Multiple channels <br />
								</div>
								 <div class = "combinedfield">
                                                                        <input type="radio" name="channels" value="c3" title="amt, crowdguru, prodege, neodev, vivatic, zoombucks"> Last used channels (mouseover) <br />
                                                                </div>


								<br />
								
								<div class = "combinedfield">
									<input type="submit" name="action" value="Create & Publish Job" /><br />
									<br />
								</div>

								<div class="labelfield">Payment per sentence (in dollars):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment_per_sentence"
										id="payment_per_sentence"> <br />
								</div>
								<div class="labelfield">Payment per job (in dollars):</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="payment_per_job" id="payment_per_job">
									<br />
								</div>
								<div class="labelfield">Seconds per assignment:</div>
								<div class="inputfield">
									<input type="text" class = "textboxInput" name="seconds_per_assignment"
										id="seconds_per_assignment"><br />
								</div>

								<div class="labelfield">Payment per hour (in dollars):</div>
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
$timezone = date_default_timezone_get();
date_default_timezone_set($timezone); 	
	function format_interval_ht(DateInterval $interval) {
    		$result = "";
	    	if ($interval->y) { $result .= ""; }
    		if ($interval->m) { $result .= ""; }
    		if ($interval->d) { $result .= $interval->format("%d d "); }
    		if ($interval->h) { $result .= $interval->format("%h h "); }
    		if ($interval->i) { $result .= $interval->format("%i m "); }
    		if ($interval->s) { $result .= ""; }
    		return $result;
	}

$result = mysql_query("SELECT * FROM `history_table` WHERE 1");
/* Update run_time in the database */
while($item = mysql_fetch_array($result)) {
	if ($item["status"] == "Running") {
		$date2 = date('Y-m-d H:i:s');
	        $date1 = $item["created_date"];
		
		$first_date = new DateTime($date1);
		$second_date = new DateTime($date2);

		$difference = $first_date->diff($second_date);
		$runningtime = format_interval_ht($difference);
	     //      $ts1 = strtotime($date1);
	     //      $ts2 = strtotime($date2);

	     //      $diff = $ts2 - $ts1;
	     //      $days = floor($diff/86400);   //24*60*60
	     //      $hours = round(($diff-$days*60*60*24)/(60*60));
	     //      if($hours == 24)
	     //      {
	     //         $days += 1;
	     //         $hours = 0;	           
	     //      }
	     //      $run_time = $days."d ".$hours."h";
	           $updateRuntime = mysql_query("Update history_table Set run_time = '$runningtime' Where job_id = '{$item["job_id"]}' and status != 'Finished' and status != 'Deleted' ");
	}
}
?>
				
				
            <div id = "dialog-blockspammers" title = "Block Spammers" >
            <!-- Load data from Database --!>
            </br>
            <form id="myform" class="myform" method="post" name="myform">
        <strong>Reason: </strong><input type="text" name="reason" id="reason" size="100"> <br/>
	    <strong>Selected Job ID:   </strong><input type="text" name="spamblockjobid" id = "spamblockjobid" readonly style = "border: none; background: transparent; color: red; font-weight: bold;" />
            </br>				  
            <strong>Spammers Found: </strong><div >
	    <table id ="spammerfound" border='1' style='width: 100%' >
            </table>
	   </form>
	   <div style="display:none;" id="answer" name="answer">  </div>
	   </div>
 </div>
             
  <button class="reset" title = "Click to clear all the filter options" >Reset Column Filters</button> <!-- targetted by the "filter_reset" option -->
  <button class="passjobid" id = "passjobid"  title = "Click to analyze selected JOB IDs" >Analyze</button> 
<select id="hidecolumns" name="hidecolumns" multiple="multiple" title = "Click to hide/show columns">
<!--<option value="cJobId">Job ID (Batch File)</option>-->
<option value="cOrigin">Origin</option>
<!--<option value="cJobTitle">Job Title</option>-->
<option value="cCreatedDate">Created Date</option>
<option value="cCreatedBy">Created By</option>
<option value="cNumberofSentences">Number of Sentences</option>
<option value="cTypeofUnits">Type of Units</option>
<option value="cTemplate">Template</option>
<option value="cMaxJudgmentsPerWorker">Max Judgments Per Worker</option>
<option value="cUnitsPerAssignment">Units Per Assignment</option>
<option value="cUnitsPerJob">Units Per Job</option>
<option value="cJudgmentsPerUnit">Judgments Per Unit</option>
<option value="cJudgmentsPerJob">Judgments Per Job</option>
<option value="cSecondsPerUnit">Seconds Per Unit</option>
<option value="cSecondsPerAssignment">Seconds Per Assignment</option>
<option value="cPaymentsPerUnit">Payments Per Unit</option>
<option value="cPaymentsPerAssignment">Payments Per Assignment</option>
<option value="cTotalPaymentsPerUnit">Total Payments Per Unit</option>
<option value="cTotalPaymentsPerJob">Total Payments Per Job</option>
<option value="cPaymentsPerHour">Payments Per Hour</option>
<option value="cChannelsUsed">Channels Used</option>
<!--<option value="cChannelsPercentage">Channels Percentage</option>-->
<option value="cComments" >Comments</option>
<option value="cJobJudgmentsMade">Job Judgments Made</option>
<option value="cJobCompletion">Job Completion</option>
<option value="cRunTime">Run Time</option>
<option value="cAverageTimePerJob">Average Time Per Job</option>
<option value="cMinTimePerJob">Min Time Per Job</option>
<option value="cMaxTimePerJob">Max Time Per Job</option>
<option value="cNumberofFilteredSentences">Number of Filtered Sentences</option>
<option value="cTotalNumberofWorkers">Total Number of Workers</option>
<option value="cNumberofFilteredWorkers">Number of Filtered Workers</option>
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
echo "<th title = 'Number of Sentences'  class='cNumberofSentences' >#S</th>";
echo "<th title = 'Type of Units' class='cTypeofUnits'>TypeU</th>";
echo "<th title = 'Template' class='cTemplate'>Tmpl</th>";
echo "<th title = 'Max Judgments Per Worker' class='cMaxJudgmentsPerWorker'>Max J/W</th>";
echo "<th title = 'Units Per Assignment' class='cUnitsPerAssignment'>U/A</th>";
echo "<th title = 'Units Per Job' class='cUnitsPerJob'>U/Job</th>";
echo "<th title = 'Judgments Per Unit' class='cJudgmentsPerUnit'>J/U</th>";
echo "<th title = 'Judgments Per Job' class='cJudgmentsPerJob'>J/Job</th>";
echo "<th title = 'Seconds Per Unit' class='cSecondsPerUnit'>s/U</th>";
echo "<th title = 'Seconds Per Assignment' class='cSecondsPerAssignment'>s/A</th>";
echo "<th title = 'Payments Per Unit' class='cPaymentsPerUnit'>P/U</th>";
echo "<th title = 'Payments Per Assignment' class='cPaymentsPerAssignment'>P/A</th>";
echo "<th title = 'Total Payments Per Unit' class='cTotalPaymentsPerUnit'>TotalP/U</th>";
echo "<th title = 'Total Payments Per Job' class='cTotalPaymentsPerJob'>TotalP/Job</th>";
echo "<th title = 'Payments Per Hour' class='cPaymentsPerHour'>P/H</th>";
echo "<th title = 'Channels Used' class='cChannelsUsed'>ChannelsUsed</th>";
// echo "<th title = 'Channels Percentage' class='cChannelsPercentage'>Chnl%</th>";
echo "<th title = 'Comments'  class='cComments'>Cmt</th>";
echo "<th title = 'Job Judgments Made' class='cJobJudgmentsMade'>JobJ</th>";
echo "<th title = 'Job Completion' class='cJobCompletion'>JobC</th>";
echo "<th title = 'Run Time' class='cRunTime'>RunT</th>";
echo "<th title = 'Average Time Per Job' class='cAverageTimePerJob'>Ave T/Job</th>";
echo "<th title = 'Min Time Per Job' class='cMinTimePerJob'>Min T/J</th>";
echo "<th title = 'Max Time Per Job' class='cMaxTimePerJob'>Max T/J</th>";
echo "<th title = 'Number of Filtered Sentences' class='cNumberofFilteredSentences'>FS</th>";
echo "<th title = 'Total Number of Workers' class='cTotalNumberofWorkers'>TotalW</th>";
echo "<th title = 'Number of Filtered Workers' class='cNumberofFilteredWorkers'>FW</th>";
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
	    echo "<td style ='font-size: 80%' class='cNumberofSentences'>$nr_sentences_file</td>";
	    echo "<td style ='font-size: 80%' class='cTypeofUnits' title = '$type_of_units' >".substr($type_of_units,0, 6)."</td>";
	    echo "<td style ='font-size: 80%' class='cTemplate' title = '$template_info' >$template</td>";
        echo "<td style ='font-size: 80%' class='cMaxJudgmentsPerWorker'>$max_judgments_per_worker</td>";
        echo "<td style ='font-size: 80%' class='cUnitsPerAssignment'>$units_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cUnitsPerJob'>$units_per_job</td>";
        echo "<td style ='font-size: 80%' class='cJudgmentsPerUnit'>$judgments_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cJudgmentsPerJob'>$judgments_per_job</td>";
        echo "<td style ='font-size: 80%' class='cSecondsPerUnit'>$seconds_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cSecondsPerAssignment'>$seconds_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cPaymentsPerUnit'>$payment_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cPaymentsPerAssignment'>$payment_per_assignment</td>";
        echo "<td style ='font-size: 80%' class='cTotalPaymentsPerUnit'>$total_payment_per_unit</td>";
        echo "<td style ='font-size: 80%' class='cTotalPaymentsPerJob'>$total_payment_per_job</td>";
        echo "<td style ='font-size: 80%' class='cPaymentsPerHour'>$payment_per_hour</td>";
    //  echo "<td style ='font-size: 80%' class='cChannelsUsed'>$channels_used</td>";
		echo "<td style ='font-size: 80%' class='cChannelsUsed' title = '$channels_used' >".substr($channels_used,0,16)."</td>";
        echo "<td style ='font-size: 80%' class='cComments' title = '$job_comments' >".substr($job_comments,0,6)."</td>";
        echo "<td style ='font-size: 80%' class='cJobJudgmentsMade'>$job_judgments_made</td>";
        echo "<td style ='font-size: 80%' class='cJobCompletion'>$job_completion</td>";
        echo "<td style ='font-size: 80%' class='cRunTime' title = '$run_time' >$run_time</td>";
		echo "<td style ='font-size: 80%' class='cAverageTimePerJob'>$avg_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cMinTimePerJob'>$min_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cMaxTimePerJob'>$max_time_unitworker</td>";
		echo "<td style ='font-size: 80%' class='cNumberofFilteredSentences'>$no_filtered_sentences</td>";
		echo "<td style ='font-size: 80%' class='cTotalNumberofWorkers' >$no_workers</td>";
		echo "<td style ='font-size: 80%' class='cNumberofFilteredWorkers'><button class = 'blockspammers' title = '$job_id' $checkbox_check >$no_filtered_workers</button></td>";
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
			
			
          <!-- 
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
  --!>
 
  
  
  <div id="tabs-7" class = "generaltab" >
  </br>
	<div class="generalborderframe"  >
	
	<a href="http://sciencetoprofitsblog.com/2012/02/15/second-opinions-ibm-watson-crowdsourcing/" target="_blank" ><img
	src="/wcs/GUI/img/icon-watson.jpg" alt="No show" title = "Crowdsourcing for Watson" style="width: 12%; height: 12%"  /></a>
	
	<a href="http://www.ibm.com/us/en/" target="_blank" ><img
	src="img/IBM.jpg" alt="No show" title = "IBM"  style="width: 12%; height: 12%" /></a>
	
	<a href="http://www.vu.nl/en/index.asp" target="_blank" ><img
		src="img/VU.jpg" alt="No show" title = "VU University Amsterdam"  style="width: 21%; height: 21%" /></a>
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
	<a href="http://nl.linkedin.com/in/dumitracheanca/" target="_blank" class = "filelinks" >Anca Dumitrache</a>, IBM Nederland BV and VU University Amsterdam
	</br>
	<a href="https://plus.google.com/u/0/106755161022646926513/posts" target="_blank" class = "filelinks" >Guillermo Soberon Casado</a>, VU Univeristy Amsterdam
	</br>
	<a href="http://nl.linkedin.com/pub/hui-lin/53/92/b5/" target="_blank" class = "filelinks" >Hui Lin</a>, IBM Nederland BV and Fontys University of Applied Sciences
	</br>
	<a href="http://nl.linkedin.com/pub/oana-inel/5a/99/711/" target="_blank" class = "filelinks" >Oana Inel</a>, IBM Nederland BV and VU University Amsterdam
	</br>
	<a href="http://nl.linkedin.com/pub/manfred-overmeen/0/445/567/" title = "Senior IT Specialist at IBM Nederland BV" target="_blank" class = "filelinks" >Manfred Overmeen</a>, IBM Nederland BV	
	</br>
	<a href="http://nl.linkedin.com/in/rsips/" target="_blank" title = "University Relations Manager at IBM Nederland BV" class = "filelinks" >Robert-Jan Sips</a>, IBM Nederland BV
	</br>
	</br>
	Want to join?
	</br>
	If you are interested in a BSc, MSc or a PhD project in this context, send you CV and motivation to <a href="mailto:lora.aroyo@vu.nl">lora.aroyo@vu.nl</a>
	</br>
	</br>
	Here are some example projects currently running:</p>
	</br>
	<img src="img/crowd-watson/CW23v2.JPG" class = "center" style="width: 65%; height: 65%" >
	</br>
 </div>
 </br>
</div>
  
  </div> 
  <div class="push"></div>
  </div> 
  <div class="footer" >
	<h5 align = "center" class = "copyrightfooter" >Copyright © 2013 <a href = 'http://crowd-watson.nl/wcs/GUI/' target="_blank" class = 'filelinks' title = "http://crowd-watson.nl/wcs/GUI/" >Crowd-Watson</a>. All rights reserved.</h5>
<h5 align = "center" class = "copyrightfooter" >
| <a href = 'http://drwatsonsynonymgame.wordpress.com/about-this-project/' target="_blank" class = 'filelinks' title = 'About Crowd-Watson Project' >About</a> 
| <a href = 'http://mailman.few.vu.nl/mailman/listinfo/crowd-watson-all' target="_blank" class = 'filelinks' title = "Crowd-watson-all" >Contact</a> 
| <a href = 'http://drwatsonsynonymgame.wordpress.com/about/' target="_blank" class = 'filelinks' title = 'About IBM Watson'>IBM Watson</a>  
| <a href = 'http://drwatsonsynonymgame.wordpress.com/research-themes/' target="_blank" class = 'filelinks' title = 'Current four Research Themes'>Research Themes</a>  |</h5>
</br>	
</div>
				
</body>
</html>
