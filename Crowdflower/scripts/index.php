<html>
<head>
<title> Create a CrowdFlower job </title>
</head>
<body>
<form enctype="multipart/form-data" action="indexcrowdflower.php" method="POST">
	Choose a file to upload: <input name="uploadedfile" type="file" /> <br />
	Judgments per unit: <input type="text" name="judgments_per_unit"> <br />
	Maxim judgments per worker: <input type="text" name="max_judgments_per_worker"> <br />
	Units per assignment: <input type="text" name="units_per_assignment"> <br />
	Maxim judgments per ip: <input type="text" name="max_judgments_per_ip"> <br />
	<input type="submit" value="Submit" />
</form>
</body>
</html>
