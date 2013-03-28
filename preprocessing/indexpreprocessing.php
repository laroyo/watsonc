<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
$jardir = "/var/www/html/wcs/preprocessing/";

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
//error_log("OK1");
if($_FILES['uploadedfile']) {  
	foreach($_FILES['uploadedfile']['name'] as $key => $info) {  
		$uploads[$key]->name=$_FILES['uploadedfile']['name'][$key];  
		$uploads[$key]->type=$_FILES['uploadedfile']['type'][$key];  
		$uploads[$key]->tmp_name=$_FILES['uploadedfile']['tmp_name'][$key];  
		$uploads[$key]->error=$_FILES['uploadedfile']['error'][$key];  
	}
}  

//error_log("OK2");
if (!is_dir($filesdir.'TextFiles')) {
    	if (!mkdir($filesdir.'TextFiles', 0777, true)) {
		 die('Failed to create folder...');
	}
} 
//error_log("OK3");
if (!is_dir($filesdir.'CSVFiles')) {
    	if (!mkdir($filesdir.'CSVFiles', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir($filesdir.'AppliedFilters')) {
    	if (!mkdir($filesdir.'AppliedFilters', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir($filesdir.'Filters')) {
    	if (!mkdir($filesdir.'Filters', 0777, true)) {
		 die('Failed to create folder...');
	}
}

if (!is_dir($filesdir.'Experiments')) {
    	if (!mkdir($filesdir.'Experiments', 0777, true)) {
		 die('Failed to create folder...');
	}
} 
//error_log("OK4");
$directoriesTextFiles = glob($filesdir.'TextFiles/*' , GLOB_ONLYDIR);
$noFiles = 0;
$textFilesAddr = "";
//print_r($directoriesTextFiles);
foreach ($directoriesTextFiles as $key => $value) {
	foreach($_FILES['uploadedfile']['name'] as $file => $info) {
		if (file_exists($value."/".$_FILES['uploadedfile']['name'][$file])) {
			$noFiles ++;
		}
//		error_log("OK5");
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
//error_log("OK6");
if ($textFilesAddr == "") {
	$currentDate = date('dFY');
	$currentTime = date('H:i:s');
	$timestamp = $currentDate."-".$currentTime;
	$textFilesAddr = $filesdir."TextFiles/".$timestamp;
	mkdir($textFilesAddr, 0777, true);
//	error_log("OK7");
	foreach($_FILES['uploadedfile']['name'] as $key => $info) {  
		move_uploaded_file($_FILES['uploadedfile']['tmp_name'][$key], $textFilesAddr."/".$_FILES['uploadedfile']['name'][$key]."");  
	}  

	mkdir($filesdir.'csvFiles', 0777, true);
	mkdir($filesdir.'allCsvFiles', 0777, true);
		 
//	error_log("/usr/bin/java -jar ".$jardir."CreateCSVFile.jar ".$textFilesAddr." ".$filesdir."csvFiles");
//	error_log("/usr/bin/java -jar ".$jardir."FormatInputFile.jar ".$filesdir."csvFiles ".$filesdir."allCsvFiles");

	$resp = shell_exec("/usr/bin/java -jar ".$jardir."CreateCSVFile.jar ".$textFilesAddr." ".$filesdir."csvFiles");
//	echo "here1";
//	echo $resp;
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."FormatInputFile.jar ".$filesdir."csvFiles ".$filesdir."allCsvFiles");
//	echo $resp;
//	echo "here2";
	mkdir($filesdir.'CSVFiles/'.$timestamp, 0777, true);
	mkdir($filesdir.'AppliedFilters/'.$timestamp, 0777, true);
	moveFiles($filesdir."allCsvFiles", $filesdir."CSVFiles/".$timestamp);

	mkdir($filesdir.'Filters/'.$timestamp, 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/noSemicolon", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/withSemicolon", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/noTermBetweenBr", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/withTermBetweenBr", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/noSpecialCase", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/noRelation", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/withRelationsBetween", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/withRelationsOutside", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/long", 0777, true);
	mkdir($filesdir.'Filters/'.$timestamp."/shortAndAverage", 0777, true);
//	error_log("OK8");
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."SpecialChars.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."ClusterOnRelation.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."LengthSelection.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);

	shell_exec("rm -rf ".$filesdir."csvFiles");
	shell_exec("rm -rf ".$filesdir."allCsvFiles");
}
//error_log("OK9");
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

if (!is_dir($filesdir."AppliedFilters/".$timestamp."/".$appliedFilter)) {
	mkdir($filesdir."AppliedFilters/".$timestamp."/".$appliedFilter, 0777, true);
	if ($filter1 != "") {
		if ($filter2 != "") {
			mkdir($filesdir.'AppliedFilters/helper1', 0777, true);
			mkdir($filesdir.'AppliedFilters/helper1/noRelation', 0777, true);
			mkdir($filesdir.'AppliedFilters/helper1/withRelationsOutside', 0777, true);
			mkdir($filesdir.'AppliedFilters/helper1/withRelationsBetween', 0777, true);
			shell_exec("java -jar ".$jardir."ClusterOnRelation.jar ".$filesdir."Filters/".$timestamp."/".$filter1." ".$filesdir."AppliedFilters/helper1");
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper2', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper2/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper2/shortAndAverage', 0777, true);
				shell_exec("java -jar ".$jardir."LengthSelection.jar ".$filesdir."AppliedFilters/helper1/".$filter2." ".$filesdir."AppliedFilters/helper2");
				moveFiles($filesdir."AppliedFilters/helper2/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper2");
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."AppliedFilters/helper1/".$filter2, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
		}
		else {
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper1', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("java -jar ".$jardir."LengthSelection.jar ".$filesdir."Filters/".$timestamp."/".$filter1." ".$filesdir."AppliedFilters/helper1");
				moveFiles($filesdir."AppliedFilters/helper1/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter1, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
			}
		}
	}
	else {
		if ($filter2 != "") {
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper1', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("java -jar ".$jardir."LengthSelection.jar ".$filesdir."Filters/".$timestamp."/".$filter2." ".$filesdir."AppliedFilters/helper1");
				moveFiles($filesdir."AppliedFilters/helper1/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter2, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
			}
		}
		else {
			if ($filter3 != "") {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
			}
			else {
			}
		}
	}
}

// create the job files
$noSentences = $_POST["nosentences"];

if (!is_dir($filesdir.'Experiments/'.$timestamp)) {
    	if (!mkdir($filesdir.'Experiments/'.$timestamp, 0777, true)) {
		 die('Failed to create folder...');
	}
} 

if (!is_dir($filesdir.'Experiments/'.$timestamp."/".$appliedFilter)) {
    	if (!mkdir($filesdir.'Experiments/'.$timestamp."/".$appliedFilter, 0777, true)) {
		 die('Failed to create folder...');
	}
} 

$jobDate = date('dFY');
$jobTime = date('H:i:s');
$timestampJob = $jobDate."-".$jobTime;
$index = 0;

// extract the batch number
foreach (glob($filesdir."Experiments/".$timestamp."/".$appliedFilter."/*.csv") as $filename) {
    $index ++;
}

$batchIndex = $index + 1;
$fileName = $timestampJob."_".$timestamp."_".$appliedFilter."_batch".$batchIndex.".csv";


shell_exec("java -jar ".$jardir."JobFileCreation.jar ".$noSentences." ".$filesdir."AppliedFilters/".$timestamp."/".$appliedFilter." ".$filesdir."Experiments/".$timestamp."/".$appliedFilter."/".$fileName);

$file = $filesdir."Experiments/".$timestamp."/".$appliedFilter."/".$fileName;

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

//header("Location: preprocinterface.php");
//header("Location: ../index.php");
?>
