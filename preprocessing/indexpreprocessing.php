<?php

function moveFiles($source, $destination) {
	if ($handle = opendir($source)) {
    		while (false !== ($entry = readdir($handle))) {
        		if ($entry != "." && $entry != "..") {
				$output = shell_exec( " cp -r -a ".$source."/* ".$destination." 2>&1 ");
       			 }
    		}
    		closedir($handle);
	}
}

if($_FILES['uploadedfile']) {  
	foreach($_FILES['uploadedfile']['name'] as $key => $info) {  
		$uploads[$key]->name=$_FILES['uploadedfile']['name'][$key];  
		$uploads[$key]->type=$_FILES['uploadedfile']['type'][$key];  
		$uploads[$key]->tmp_name=$_FILES['uploadedfile']['tmp_name'][$key];  
		$uploads[$key]->error=$_FILES['uploadedfile']['error'][$key];  
	}
}  


if (!is_dir('/tmp/PreProcessing/TextFiles')) {
    	if (!mkdir('/tmp/PreProcessing/TextFiles', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir('/tmp/PreProcessing/CSVFiles')) {
    	if (!mkdir('/tmp/PreProcessing/CSVFiles', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir('/tmp/PreProcessing/AppliedFilters')) {
    	if (!mkdir('/tmp/PreProcessing/AppliedFilters', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir('/tmp/PreProcessing/Filters')) {
    	if (!mkdir('/tmp/PreProcessing/Filters', 0777, true)) {
		 die('Failed to create folder...');
	}
}

if (!is_dir('/tmp/PreProcessing/Experiments')) {
    	if (!mkdir('/tmp/PreProcessing/Experiments', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

$directoriesTextFiles = glob('/tmp/PreProcessing/TextFiles/*' , GLOB_ONLYDIR);
$noFiles = 0;
$textFilesAddr = "";
print_r($directoriesTextFiles);
foreach ($directoriesTextFiles as $key => $value) {
	foreach($_FILES['uploadedfile']['name'] as $file => $info) {
		if (file_exists($value."/".$_FILES['uploadedfile']['name'][$file])) {
			$noFiles ++;
		}
	}
	$dir_path = $value."/";
	$count = count(glob($dir_path . "*"));
	if ($noFiles == count($_FILES['uploadedfile']['name']) && $count == count($_FILES['uploadedfile']['name'])) {
		$textFilesAddr = $value;
		break;
	}
	$noFiles = 0;
}

date_default_timezone_set('UTC');

if ($textFilesAddr == "") {
	$currentDate = date('dFY');
	$currentTime = date('H:i:s');
	$timestamp = $currentDate."-".$currentTime;
	$textFilesAddr = "/tmp/PreProcessing/TextFiles/".$timestamp;
	mkdir($textFilesAddr, 0777, true);

	foreach($_FILES['uploadedfile']['name'] as $key => $info) {  
		move_uploaded_file($_FILES['uploadedfile']['tmp_name'][$key], $textFilesAddr."/".$_FILES['uploadedfile']['name'][$key]."");  
	}  

	mkdir('/tmp/PreProcessing/csvFiles', 0777, true);
	mkdir('/tmp/PreProcessing/allCsvFiles', 0777, true);
		 
	$resp = shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/CreateCSVFile.jar ".$textFilesAddr." /tmp/PreProcessing/csvFiles");
	$resp = shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/FormatInputFile.jar /tmp/PreProcessing/csvFiles /tmp/PreProcessing/allCsvFiles");

	mkdir('/tmp/PreProcessing/CSVFiles/'.$timestamp, 0777, true);
	mkdir('/tmp/PreProcessing/AppliedFilters/'.$timestamp, 0777, true);
	moveFiles("/tmp/PreProcessing/allCsvFiles", "/tmp/PreProcessing/CSVFiles/".$timestamp);

	mkdir('/tmp/PreProcessing/Filters/'.$timestamp, 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/noSemicolon", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/withSemicolon", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/noTermBetweenBr", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/withTermBetweenBr", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/noSpecialCase", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/noRelation", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/withRelationsBetween", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/withRelationsOutside", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/long", 0777, true);
	mkdir('/tmp/PreProcessing/Filters/'.$timestamp."/shortAndAverage", 0777, true);

	$resp = shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/SpecialChars.jar /tmp/PreProcessing/CSVFiles/".$timestamp." /tmp/PreProcessing/Filters/".$timestamp);
	$resp = shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/ClusterOnRelation.jar /tmp/PreProcessing/CSVFiles/".$timestamp." /tmp/PreProcessing/Filters/".$timestamp);
	$resp = shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/LengthSelection.jar /tmp/PreProcessing/CSVFiles/".$timestamp." /tmp/PreProcessing/Filters/".$timestamp);

	shell_exec("rm -rf /tmp/PreProcessing/csvFiles");
	shell_exec("rm -rf /tmp/PreProcessing/allCsvFiles");
}

	// extract the timestamp of the text files
	$explode = explode("/", $textFilesAddr);
	$timestamp = $explode[count($explode) - 1];

	$appliedFilter = "";
	$filter1 = ""; 
	$filter2 = "";
	$filter3 = "";
	$arr = $_POST["filters"];
	foreach($arr as $item)
   	{
        	if($item == "specialcases") {
			$filter1 = $_POST["specialcases"];
			if ($filter1 == "withSemicolon") 
				$appliedFilter.="SC-";
			if ($filter1 == "withTermBetweenBr") 
				$appliedFilter.="ABB-";
			if ($filter1 == "noSemicolon") 
				$appliedFilter.="NOSC-";
			if ($filter1 == "noTermBetweenBr") 
				$appliedFilter.="NOABB-";
			if ($filter1 == "noSpecialCase") 
				$appliedFilter.="NOSPC-";
		} 
		if($item == "relations") {
			$filter2 = $_POST["relation"];
			if ($filter2 == "withRelationsBetween") 
				$appliedFilter.="RBA-";
			if ($filter2 == "withRelationsOutside") 
				$appliedFilter.="ROA-";
			if ($filter2 == "noRelation")
				$appliedFilter.="NOR-";
		} 
		if($item == "length") {
			$filter3 = $_POST["length"];
			if ($filter3 == "long") 
				$appliedFilter.="L";
			if ($filter3 == "shortAndAverage")
				$appliedFilter.="S";
		} 
   	}

if ($appliedFilter[strlen($appliedFilter) - 1] == "-")
	$appliedFilter = substr($appliedFilter, 0, -1);

if (!is_dir("/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter)) {
	mkdir("/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter, 0777, true);
	if ($filter1 != "") {
		if ($filter2 != "") {
			mkdir('/tmp/PreProcessing/AppliedFilters/helper1', 0777, true);
			mkdir('/tmp/PreProcessing/AppliedFilters/helper1/noRelation', 0777, true);
			mkdir('/tmp/PreProcessing/AppliedFilters/helper1/withRelationsOutside', 0777, true);
			mkdir('/tmp/PreProcessing/AppliedFilters/helper1/withRelationsBetween', 0777, true);
			shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/ClusterOnRelation.jar /tmp/PreProcessing/Filters/".$timestamp."/".$filter1." /tmp/PreProcessing/AppliedFilters/helper1");
			if ($filter3 != "") {
				mkdir('/tmp/PreProcessing/AppliedFilters/helper2', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper2/long', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper2/shortAndAverage', 0777, true);
				shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/LengthSelection.jar /tmp/PreProcessing/AppliedFilters/helper1/".$filter2." /tmp/PreProcessing/AppliedFilters/helper2");
				moveFiles("/tmp/PreProcessing/AppliedFilters/helper2/".$filter3, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf /tmp/PreProcessing/AppliedFilters/helper2");
				shell_exec("rm -rf /tmp/PreProcessing/AppliedFilters/helper1");
			}
			else {
				moveFiles("/tmp/PreProcessing/AppliedFilters/helper1/".$filter2, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf /tmp/PreProcessing/AppliedFilters/helper1");
			}
		}
		else {
			if ($filter3 != "") {
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1/long', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/LengthSelection.jar /tmp/PreProcessing/Filters/".$timestamp."/".$filter1." /tmp/PreProcessing/AppliedFilters/helper1");
				moveFiles("/tmp/PreProcessing/AppliedFilters/helper1/".$filter3, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf /tmp/PreProcessing/AppliedFilters/helper1");
			}
			else {
				moveFiles("/tmp/PreProcessing/Filters/".$timestamp."/".$filter1, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
			}
		}
	}
	else {
		if ($filter2 != "") {
			if ($filter3 != "") {
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1/long', 0777, true);
				mkdir('/tmp/PreProcessing/AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/LengthSelection.jar /tmp/PreProcessing/Filters/".$timestamp."/".$filter2." /tmp/PreProcessing/AppliedFilters/helper1");
				moveFiles("/tmp/PreProcessing/AppliedFilters/helper1/".$filter3, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf /tmp/PreProcessing/AppliedFilters/helper1");
			}
			else {
				moveFiles("/tmp/PreProcessing/Filters/".$timestamp."/".$filter2, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
			}
		}
		else {
			if ($filter3 != "") {
				moveFiles("/tmp/PreProcessing/Filters/".$timestamp."/".$filter3, "/tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter);
			}
			else {
			}
		}
	}
}

// create the job files
$noSentences = $_POST["nosentences"];

if (!is_dir('/tmp/PreProcessing/Experiments/'.$timestamp)) {
    	if (!mkdir('/tmp/PreProcessing/Experiments/'.$timestamp, 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir('/tmp/PreProcessing/Experiments/'.$timestamp."/".$appliedFilter)) {
    	if (!mkdir('/tmp/PreProcessing/Experiments/'.$timestamp."/".$appliedFilter, 0777, true)) {
		 die('Failed to create folder...');
	}
} 

$jobDate = date('dFY');
$jobTime = date('H:i:s');
$timestampJob = $jobDate."-".$jobTime;
$index = 0;

// extract the batch number
foreach (glob("/tmp/PreProcessing/Experiments/".$timestamp."/".$appliedFilter."/*.csv") as $filename) {
    $index ++;
}

$batchIndex = $index + 1;
$fileName = $timestampJob."_".$timestamp."_".$appliedFilter."_batch".$batchIndex.".csv";


shell_exec("/home/oana/Downloads/jdk1.7.0_13/bin/java -jar /tmp/PreProcessing/JobFileCreation.jar ".$noSentences." /tmp/PreProcessing/AppliedFilters/".$timestamp."/".$appliedFilter." /tmp/PreProcessing/Experiments/".$timestamp."/".$appliedFilter."/".$fileName);

$file = "/tmp/PreProcessing/Experiments/".$timestamp."/".$appliedFilter."/".$fileName;

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
} 

header("Location: preprocinterface.php");
?>
