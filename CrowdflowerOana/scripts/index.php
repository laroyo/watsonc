<html>
<head>
<title> Create a CrowdFlower job </title>
<script language="javascript">

if (window.File && window.FileReader && window.FileList && window.Blob) {
} else {
  alert('The File APIs are not fully supported in this browser.');
}

function handleFileSelect(evt) {

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
 	payment_per_sentence.value = ((parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) + (parseInt(judgments_per_unit) * (parseInt(payment_per_assignment) / parseInt(units_per_assignment))) * 46.35 / 100 ) / 100 ;
	payment_per_job.value = parseInt(total_sentences) * payment_per_sentence.value;
	
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
<form enctype="multipart/form-data" action="indexcrowdflower.php" method="POST" id="form">
	Choose a file to upload: <input name="uploadedfile" type="file" id="uploadedfile"/> <br />
	<input type="hidden" name="sentences" id="sentences" />
	Job title: <input type="text" name="title"> <br />
	Judgments per unit: <input type="text" name="judgments_per_unit" id="judgments_per_unit" oninput="computePayment()" > <br />
	Maxim judgments per worker: <input type="text" name="max_judgments_per_worker"> <br />
	Units per assignment (CF: Units per page): <input type="text" name="units_per_assignment" oninput="computePayment()" id="units_per_assignment"> <br />
	Maxim judgments per ip: <input type="text" name="max_judgments_per_ip"> <br />
	Payment per assignment (CF: Payment per page) (cents): <input type="text" name="payment" oninput="computePayment()" id="payment"> <br />
	Seconds per unit: <input type="text" name="seconds_per_unit" id="seconds_per_unit" oninput="computeTime()"> <br />
	Choose the template: <br />
		<input type="radio" name="template" value="t1" checked> Relations with definitions and extra questions required <br />
		<input type="radio" name="template" value="t2"> Relations with definitions but without extra questions <br />
		<input type="radio" name="template" value="t3"> Relations without definitions and extra questions required <br />
		<input type="radio" name="template" value="t4"> Relations without definitions and without extra questions <br />
	<input type="submit" name="action" value="Create Job" /><br /> <br />


Payment per sentence (dollar): <input type="text" name="payment_per_sentence" id="payment_per_sentence"> <br />
Payment per job (dollar): <input type="text" name="payment_per_job" id="payment_per_job"> <br />
Seconds per assignment: <input type="text" name="seconds_per_assignment" id="seconds_per_assignment"> <br />

</form> <br />

</body>
</html>
