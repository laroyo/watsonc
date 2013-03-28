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

<form enctype="multipart/form-data" action="/wcs/preprocessing/indexpreprocessing.php" method="POST" id="form" name="form">
	Choose the text files for processing: <input type="file" multiple name="uploadedfile[]" webkitdirectory="" id="uploadedfile" /> <br /><br />
	Choose the filters that should be applied on the uploaded data: <br />
	
	<input type="checkbox" name="filters[]" id="filters1" value="specialcases" checked onchange="checkForFilters1()"> Special cases <br />
		<input type="radio" name="specialcases" id="specialcases" value="withSemicolon"> with special case (semicolon) <br />
		<input type="radio" name="specialcases" id="specialcases" value="withTermBetweenBr"> with special case (term between brackets) <br />
		<input type="radio" name="specialcases" id="specialcases" value="noSemicolon"> without special case (semicolon) <br />
		<input type="radio" name="specialcases" id="specialcases" value="noTermBetweenBr"> without special case (term between brackets) <br />
		<input type="radio" name="specialcases" id="specialcases" value="noSpecialCase" checked> without special cases <br /><br />

	<input type="checkbox" name="filters[]" id="filters2" value="relations" checked onchange="checkForFilters2()"> Relation mentioned <br />
		<input type="radio" name="relation" id="relation" value="withRelationsBetween"> between the two terms <br />
		<input type="radio" name="relation" id="relation" value="withRelationsOutside"> outside the two terms <br />
		<input type="radio" name="relation" id="relation" value="noRelation" checked> no relation mentioned <br /><br />

	<input type="checkbox" name="filters[]" id="filters3" value="length" checked onchange="checkForFilters3()"> Sentence length <br />
		<input type="radio" name="length" id="length" value="long"> long sentences <br />
		<input type="radio" name="length" id="length" value="shortAndAverage" checked> short sentences <br /><br />

	Create the job file: <br />
	Number of sentences from each relation: <input type="text" id="nosentences" name="nosentences" size="3"> 

	<input type="submit" name="action" value="Submit"/>
</form>

</body>
</html>
