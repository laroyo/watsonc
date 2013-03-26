<html>
<head>
<title> Create a CrowdFlower job </title>
</head>
<body>
<form enctype="multipart/form-data" action="/crowdflower/indexcrowdflower.php" method="POST">
<div class="borderframe"  > 
	<div class="labelfield">Choose a file to upload:</div> 
	<div class="inputfield"><input name="uploadedfile" type="file"  /></div>
	<div class="labelfield">Job title:</div>  
	<div class="inputfield"><input type="text" name="title" class = "textboxInput"/></div>
	<div class="labelfield">Judgments per unit:</div>  
	<div class="inputfield"><input type="text" name="judgments_per_unit" class = "textboxInput"/></div>
	<div class="labelfield">Maxim judgments per worker:</div>  
	<div class="inputfield"><input type="text" name="max_judgments_per_worker" class = "textboxInput"/></div>
	<div class="labelfield">Units per assignment:</div>  
	<div class="inputfield"><input type="text" name="units_per_assignment" class = "textboxInput"/></div> 
	<div class="labelfield">Maxim judgments per ip:</div>  
	<div class="inputfield"><input type="text" name="max_judgments_per_ip" class = "textboxInput"/></div>
	<div class="labelfield">Payment per assignment (cents):</div>  
	<div class="inputfield"><input type="text" name="payment" class = "textboxInput"/></div>
	<div class="labelfield" title = "The purposes or notes of creating the job">Comments:</div>  
	<div class="inputfield"><textarea name="job_comment" class = "commentboxInput"/></div>
	<div class="labelfield">Choose the template:</div>  
	<div class="inputfield"><input type="radio" name="template" value="t1" checked /> Relations with definitions and with extra questions required</div> 
	<div class="labelfield">&nbsp;</div>  
	<div class="inputfield"><input type="radio" name="template" value="t2" /> Relations with definitions but without extra questions</div>
	<div class="labelfield">&nbsp;</div>  
	<div class="inputfield"><input type="radio" name="template" value="t3" /> Relations without definitions but with extra questions required</div>
	<div class="labelfield">&nbsp;</div>  
	<div class="inputfield"><input type="radio" name="template" value="t4" /> Relations without definitions and without extra questions</div>
	<div class="labelfield">&nbsp;</div>  
	<div class="inputfield">&nbsp;</div>
	<div class="labelfield">&nbsp;</div>  
	<div class="inputfield"><input type="submit" value="Submit" title = "Click Submit to create a new job on CrowdFlower" /></div>
	</div>
</form>
</body>
</html>