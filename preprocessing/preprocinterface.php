<html>
<head> 
<title> Pre-Processing Interface </title>
<script >
function checkForFilters1() {
	if (!document.getElementById("filters1").checked) {
		var radioButtons = document.getElementsByName('specialcases'); 
		for (var i = 0; i < 5; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('specialcases');
		radioButtons[0].checked = true;

	}
}

function checkForFilters2() {
	if (!document.getElementById("filters2").checked) {
		var radioButtons = document.getElementsByName('relation'); 
		for (var i = 0; i < 3; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('relation');
		radioButtons[0].checked = true;
	}
}

function checkForFilters3() {
	if (!document.getElementById("filters3").checked) {
		var radioButtons = document.getElementsByName('length'); 
		for (var i = 0; i < 2; i ++) {
			if(radioButtons[i].checked == true)
				radioButtons[i].checked = false;
		}
	}
	else {
		var radioButtons = document.getElementsByName('length');
		radioButtons[0].checked = true;
	}
}
</script>
</head>
<body>
	<div id="dialog-confirm-proprocessing" title="Select a folder from the server">
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
while($row = mysql_fetch_array($result)){

	    extract ( $row );
	
	$folderName = substr($folder_name, 0, strrpos($folder_name, "/"));
        echo "<tr>";
        echo "<td><input type='radio' id='radiofilepreprocessing' name ='radiofilepreprocessing'/>$folderName</label></td>";
        echo "<td>$original_name</td>";
        echo "<td>$comment</td>";
        echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>
</div>
<form enctype="multipart/form-data" action="/wcs/preprocessing/indexpreprocessing.php" method="POST" id="form" name="form">
	Select your input data files from your local machine: <input type="file" multiple name="uploadedfile[]" webkitdirectory="" id="uploadedfile" /> <br /> or <br />
	<div class="labelfield"> Select your input data files from your local machine: </div>
	<div class="inputfield">
		<input name="uploadedfilepreprocessing" type="button" id="uploadedfilepreprocessing" value="Choose Server Folder" /> 
		<input type="hidden" name="foldername" id="foldername" /> 
		<input type="hidden" name="filenames" id="filenames" /> 
		<label for="uploadedfilepreprocessing" 	style="font-size: 75%">No Folder Chosen</label>
	</div>
	Provide description of your data: <br /> <textarea name="files_comment" id="files_comment" rows="4" cols="60"/> <br />

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
		<input type="text" id="nostreat" name="nostreat" size="2">  Symptom <br />
		<input type="text" id="nossymptom" name="nossymptom" size="2">  Treat <br /> 

<!--
	Create the job file: <br />
	Number of sentences from each relation: <input type="text" id="nosentences" name="nosentences" size="3"> 
--!>
<br />
	<input type="submit" name="action" value="Create & Save Batch"/>
</form>

</body>
</html>
