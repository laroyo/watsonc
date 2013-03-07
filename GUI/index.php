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
<script src="js/huimain.js"></script>
</head>
<body>
	<div id="content">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Home</a></li>
				<li><a href="#tabs-2">Jobs</a></li>
				<li><a href="#tabs-3">History</a></li>
				<li><a href="#tabs-4">Files</a></li>
				<li><a href="#tabs-5">Examples</a></li>
			</ul>
			<div id="tabs-1">
				<h1>Watson-Crowdsourcing</h1>
				<br> <a href="http://en.wikipedia.org/wiki/Crowdsourcing"><img
					src="graphs/crowdsourcing.jpg" alt="No show" /></a>
			</div>
			<div id="tabs-2">
				<h3>This page is to create new jobs for CrowdFlower</h3>
				<br>
				<div id="jobarea"></div>
			</div>
			<div id="tabs-3">
				<h3>This page is to show the history of jobs created for CrowdFlower</h3>
				<br>
				<div id="historyarea">
				<?php
$con = mysql_connect("localhost", "root", "usbw") or die("Couldn't make connection.");
$db = mysql_select_db("watsoncs", $con) or die("Couldn't select database");

$result =  mysql_query("SELECT * FROM  `csvinput` LIMIT 0 , 30");

echo "<table id='historytable' class='tablesorter'>";
echo "<thead>"; //thead tag is required for using tablesorter
echo "<tr>";
echo "<th>File ID</th>";
echo "<th>Created Date</th>";
echo "<th>File Name</th>";
echo "<th>Job Title</th>";
echo "<th>Judgement Per Unit</th>";
echo "<th>Max Judgement Per Worker</th>";
echo "<th>Units Per Assignment</th>";
echo "<th>Max Judgement Per Ip</th>";
echo "<th>Payment Per Assignment(Cents)</th>";
echo "<th>Comments</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>"; //tbody tag is required for using tablesorter

while($row = mysql_fetch_array($result)){
	extract ( $row );
    echo "<tr>";
        echo "<td>$file_id</td>";
        echo "<td>$created_date</td>";
        echo "<td>$file_name</td>";
        echo "<td>$job_title</td>";
        echo "<td>$judgement_per_unit</td>";
        echo "<td>$max_judgement_per_worker</td>";
        echo "<td>$units_per_assignment</td>";
        echo "<td>$max_judgement_per_ip</td>";
        echo "<td>$cents_per_assignment</td>";
        echo "<td>$comments</td>";
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