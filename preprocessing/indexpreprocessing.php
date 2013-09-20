<?php
include_once '../includes/dbinfo.php';
include_once '../includes/functions.php';
$jardir = "/var/www/html/wcs/preprocessing/";
//$relation_name = array("cause", "treat", "contra", "diagnose", "location", "symptom", "prevent");

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

function storePreprocessedFile($filefieldname, $key, $storageFolder) {
        $storage_path = $storageFolder.basename( $_FILES[$filefieldname]['name'][$key]);
        $original_name = $_FILES[$filefieldname]['name'][$key];
        $mime_type = $_FILES[$filefieldname]['type'][$key];
        $filesize = $_FILES[$filefieldname]['size'][$key];
        $query="INSERT INTO `file_storage`(`original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`) 
	        VALUES ('".$original_name."','".$storage_path."','".$mime_type."',".$filesize.",'".$_SERVER['REMOTE_USER']."')";
                mysql_query($query) or dieError("function: storeFile<br/>".$query."<br/>".mysql_error());
        return mysql_insert_id();
}

function storeCSVFile($filefieldname, $storageFolder) {
        $storage_path = $storageFolder."/".$filefieldname;
        $original_name = $filefieldname;
        $mime_type = mime_content_type($storageFolder."/".$filefieldname);
        $filesize = filesize($storageFolder."/".$filefieldname);
        $query="INSERT INTO `file_storage`(`original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`) 
                VALUES ('".$original_name."','".$storage_path."','".$mime_type."','".$filesize."','".$_SERVER['REMOTE_USER']."')";
                mysql_query($query) or dieError("function: storeCSVFile<br/>".$query."<br/>".mysql_error());
        return mysql_insert_id();
}

function getFileRelation($fileName) {
	$relation_name = array("cause", "treat", "contra", "diagnose", "location", "symptom", "prevent");
	foreach($relation_name as $relation) {
		if(strpos($fileName, $relation) !== false) {
			return $relation;
		}
	}
	return "none";
}

function getRelationFileName($dirName, $relationName) {
	$filesFromDir = getAllFilesFromDirectory($dirName);
	foreach($filesFromDir as $file) {
		if(strpos($file, $relationName) !== false) {
			return $file;
		}
	}
	return null;
} 

function getAllFilesFromDirectory($dirName) {
	$files = array();
	if ($handle = opendir($dirName)) {
		while (false !== ($entry = readdir($handle))) {
        		if ($entry != "." && $entry != "..") {
            		//	echo "$entry\n";
			array_push($files, $entry);
        		}
    		}
    		closedir($handle);
	}
	return $files;
} 

function addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3) {
	$storageCSV = "/var/www/files/AppliedFilters/".$timestamp."/".$appliedFilter;
        $csvfiles = getAllFilesFromDirectory($storageCSV);
        foreach($csvfiles as $name) {
        	$fileid = storeCSVFile($name, $storageCSV);
                $storage_path = "/var/www/files/CSVFiles/".$timestamp."/".substr($name, 0, strpos($name, 'all'))."all.csv";
		$queryId="SELECT `id` FROM `file_storage` WHERE `storage_path` = '".$storage_path."'";
                $textFileId = getOneFieldFromQuery($queryId, 'id');
                $queryId="SELECT `id` FROM `processing_file` WHERE `fileid` = ".$textFileId;
                $csvFileId = getOneFieldFromQuery($queryId, 'id');
                $sentence_length = $filter3;
		$relation_location = $filter2;
                $special_cases = $filter1;
                $comment = $_POST["files_comment"];
                $query="INSERT INTO `filtered_file` (`file_id`, `processing_file_id`, `sentence_length`, `relation_location`, 
                       `special_cases`, `comment`, `created_by`) 
                       VALUES ('".$fileid."','".$csvFileId."','".$sentence_length."','".$relation_location."',
                       '".$special_cases."','".$comment."','".$_SERVER['REMOTE_USER']."')";
                mysql_query($query) or dieError("function: filtered_file<br/>".$query."<br/>".mysql_error());
	}
}


if (!is_dir($filesdir.'TextFiles')) {
    	if (!mkdir($filesdir.'TextFiles', 0777, true)) {
		 die('Failed to create folder...');
	}
} 

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

 shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
 shell_exec("rm -rf ".$filesdir."AppliedFilters/helper2");

$directoriesTextFiles = glob($filesdir.'TextFiles/*' , GLOB_ONLYDIR);
$noFiles = 0;
$textFilesAddr = "";

if (!empty($_FILES['uploadedfilepreproc'])) {
foreach ($directoriesTextFiles as $key => $value) {
	foreach($_FILES['uploadedfilepreproc']['name'] as $file => $info) {
		if (file_exists($value."/".$_FILES['uploadedfilepreproc']['name'][$file])) {
			$noFiles ++;
		}
	}
	$dir_path = $value."/";
	$count = count(glob($dir_path . "*"));
	if ($noFiles == count($_FILES['uploadedfilepreproc']['name']) && $count == count($_FILES['uploadedfilepreproc']['name'])) {
		$textFilesAddr = $value;
		break;
	}
	$noFiles = 0;
}
}
if(!empty($_POST["foldername"])) {
	$textFilesAddr = $_POST["foldername"] . "";
}
date_default_timezone_set('UTC');

if ($textFilesAddr == "") {
	$currentDate = date('dFY');
	$currentTime = date('H:i:s');
	$timestamp = $currentDate."-".$currentTime;
	$textFilesAddr = $filesdir."TextFiles/".$timestamp;
	mkdir($textFilesAddr, 0777, true);

	foreach($_FILES['uploadedfilepreproc']['name'] as $key => $info) {
		$storageFolder = $textFilesAddr."/";  
		move_uploaded_file($_FILES['uploadedfilepreproc']['tmp_name'][$key], $storageFolder.$_FILES['uploadedfilepreproc']['name'][$key]."");
		$fileid = storePreprocessedFile('uploadedfilepreproc', $key, $storageFolder);
		$lines = getLines($fileid) - 1;
		$title = $_FILES['uploadedfilepreproc']['name'][$key];
		$comment = $_POST['files_comment'];
		$fileRelation = getFileRelation($_FILES['uploadedfilepreproc']['name'][$key]);

		$query="INSERT INTO `raw_file`(`seedrelationname`, `fileid`, `lines`, `comment`, `createdby`) 
	                VALUES ('".$fileRelation."','".$fileid."','".$lines."','".$comment."','".$_SERVER['REMOTE_USER']."')";
        	        mysql_query($query) or dieError("function: raw_file<br/>".$query."<br/>".mysql_error());		  
	}  

	mkdir($filesdir.'csvFiles', 0777, true);
	mkdir($filesdir.'allCsvFiles', 0777, true);
		 
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."CreateCSVFile.jar ".$textFilesAddr." ".$filesdir."csvFiles");
	$resp = shell_exec("/usr/bin/java -jar ".$jardir."FormatInputFile.jar ".$filesdir."csvFiles ".$filesdir."allCsvFiles");

	mkdir($filesdir.'CSVFiles/'.$timestamp, 0777, true);
	mkdir($filesdir.'AppliedFilters/'.$timestamp, 0777, true);

	$storageCSV = $filesdir."CSVFiles/".$timestamp;
	moveFiles($filesdir."allCsvFiles", $storageCSV);

	$csvfiles = getAllFilesFromDirectory($storageCSV);
	foreach($csvfiles as $name) {
		$fileid = storeCSVFile($name, $storageCSV);
		$lines = getLines($fileid) - 1;
		$comment = $_POST["files_comment"];

		$storage_path = $filesdir."TextFiles/".$timestamp."/".substr(0, strlen($name) - 8);
		$queryId="SELECT `id` FROM `file_storage` WHERE `storage_path` = '".$storage_path."'";
                $rowFileId = getOneFieldFromQuery($queryId, 'id');
	
		$queryId="SELECT `lines` FROM `raw_file` WHERE `fileid` = '".$rowFileId."'";
                $sentences = getOneFieldFromQuery($queryId, 'lines');
		
		$query="INSERT INTO `processing_file`(`fileid`, `rawfileid`, `lines`, `sentences`, `comment`, `createdby`) 
                        VALUES ('".$fileid."','".$rawFileId."','".$lines."','".$sentences."','".$comment."','".$_SERVER['REMOTE_USER']."')";
                        mysql_query($query) or dieError("function: processing_file<br/>".$query."<br/>".mysql_error());
	}

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

	$resp = shell_exec("/usr/bin/java -jar ".$jardir."SpecialChars.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);
	$folders = array("/noSemicolon", "/withSemicolon", "/noTermBetweenBr", "/withTermBetweenBr", "/noSpecialCase");
	$filters = array("NOSC", "SC", "NOABB", "ABB", "NOSPC");
	$storageCSV = $filesdir."Filters/".$timestamp;

	foreach($folders as $keyFolder => $filterName) {
		$oneFilterFiles = getAllFilesFromDirectory($storageCSV.$filterName);
		foreach($oneFilterFiles as $name) {
			$fileid = storeCSVFile($name, $storageCSV.$filterName);
			
			$storage_path = "/var/www/files/Filters/".$timestamp.$filterName."/".$name;
			$queryId="SELECT `id` FROM `file_storage` WHERE `storage_path` = '".$storage_path."'";
                	$preprocFileId = getOneFieldFromQuery($queryId, 'id');

			$comment = $_POST['files_comment'];
			$fileFilter = $filters[$keyFolder];

			$query="INSERT INTO `one_file_filter`(`file_id`, `preprocessing_file_id`, `filter`, `comment`, `created_by`) 
	    	            VALUES ('".$fileid."','".$preprocFileId."','".$fileFilter."','".$comment."','".$_SERVER['REMOTE_USER']."')";
        		        mysql_query($query) or dieError("function: one_filter_file<br/>".$query."<br/>".mysql_error());
		}
	} 

	$resp = shell_exec("/usr/bin/java -jar ".$jardir."ClusterOnRelation.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);
	$folders = array("/noRelation", "/withRelationsBetween", "/withRelationsOutside");
	$filters = array("NOR", "RBA", "ROA");
	$storageCSV = $filesdir."Filters/".$timestamp;
	foreach($folders as $keyFolder => $filterName) {
		$oneFilterFiles = getAllFilesFromDirectory($storageCSV.$filterName);
		foreach($oneFilterFiles as $name) {
			$fileid = storeCSVFile($name, $storageCSV.$filterName);
			$storage_path = "/var/www/files/Filters/".$timestamp.$filterName."/".$name;
			$queryId="SELECT `id` FROM `file_storage` WHERE `storage_path` = '".$storage_path."'";
                	$preprocFileId = getOneFieldFromQuery($queryId, 'id');

			$comment = $_POST['files_comment'];
			$fileFilter = $filters[$keyFolder];

			$query="INSERT INTO `one_file_filter`(`file_id`, `preprocessing_file_id`, `filter`, `comment`, `created_by`) 
	    	            VALUES ('".$fileid."','".$preprocFileId."','".$fileFilter."','".$comment."','".$_SERVER['REMOTE_USER']."')";
        		        mysql_query($query) or dieError("function: one_filter_file<br/>".$query."<br/>".mysql_error());
		}
	} 

	$resp = shell_exec("/usr/bin/java -jar ".$jardir."LengthSelection.jar ".$filesdir."CSVFiles/".$timestamp." ".$filesdir."Filters/".$timestamp);
	$folders = array("/long", "/shortAndAverage");
	$filters = array("long", "short");
	$storageCSV = $filesdir."Filters/".$timestamp;
	foreach($folders as $filterName) {
		$oneFilterFiles = getAllFilesFromDirectory($storageCSV.$filterName);
		foreach($oneFilterFiles as $name) {
			$fileid = storeCSVFile($name, $storageCSV.$filterName);
			$storage_path = "/var/www/files/Filters/".$timestamp.$filterName."/".$name;
			$queryId="SELECT `id` FROM `file_storage` WHERE `storage_path` = '".$storage_path."'";
                	$preprocFileId = getOneFieldFromQuery($queryId, 'id');

			$comment = $_POST['files_comment'];
			$fileFilter = $filters[$keyFolder];

			$query="INSERT INTO `one_file_filter`(`file_id`, `preprocessing_file_id`, `filter`, `comment`, `created_by`) 
	    	            VALUES ('".$fileid."','".$preprocFileId."','".$fileFilter."','".$comment."','".$_SERVER['REMOTE_USER']."')";
        		        mysql_query($query) or dieError("function: one_filter_file<br/>".$query."<br/>".mysql_error());
		}
	} 

	shell_exec("rm -rf ".$filesdir."csvFiles");
	shell_exec("rm -rf ".$filesdir."allCsvFiles");
}

	// extract the timestamp of the text files
	$explode = explode("/", $textFilesAddr);
	$timestamp = $explode[count($explode) - 1];

	$appliedFilter = "";
	$filter1 = "";
	$filter1Caps = ""; 
	$filter2 = "";
	$filter2Caps = "";
	$filter3 = "";
	$filter3Caps = "";
	$arr = $_POST["filters"];
	foreach($arr as $item)
   	{
        	if($item == "specialcases") {
			$filter1 = $_POST["specialcases"];
			if ($filter1 == "withSemicolon") { 
				$appliedFilter.="SC-";
				$filter1Caps = "SC";
			}
			if ($filter1 == "withTermBetweenBr") { 
				$appliedFilter.="ABB-";
				$filter1Caps = "ABB";
			}
			if ($filter1 == "noSemicolon") { 
				$appliedFilter.="NOSC-";
				$filter1Caps = "NOSC";
			}
			if ($filter1 == "noTermBetweenBr") { 
				$appliedFilter.="NOABB-";
				$filter1Caps = "NOABB";	
			}
			if ($filter1 == "noSpecialCase") { 
				$appliedFilter.="NOSPC-";
				$filter1Caps = "NOSPC";
			}
		} 
		if($item == "relations") {
			$filter2 = $_POST["relation"];
			if ($filter2 == "withRelationsBetween") { 
				$appliedFilter.="RBA-";
				$filter2Caps = "RBA";
			}
			if ($filter2 == "withRelationsOutside") { 
				$appliedFilter.="ROA-";
				$filter2Caps = "ROA";
			}
			if ($filter2 == "noRelation") {
				$appliedFilter.="NOR-";
				$filter2Caps = "NOR";
			}
		} 
		if($item == "length") {
			$filter3 = $_POST["length"];
			if ($filter3 == "long") { 
				$appliedFilter.="L";
				$filter3Caps = "long";
			}	
			if ($filter3 == "shortAndAverage") {
				$appliedFilter.="S";
				$filter3Caps = "short";
			}
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
			shell_exec("/usr/bin/java -jar ".$jardir."ClusterOnRelation.jar ".$filesdir."Filters/".$timestamp."/".$filter1." ".$filesdir."AppliedFilters/helper1");
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper2', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper2/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper2/shortAndAverage', 0777, true);
				shell_exec("/usr/bin/java -jar ".$jardir."LengthSelection.jar ".$filesdir."AppliedFilters/helper1/".$filter2." ".$filesdir."AppliedFilters/helper2");
				moveFiles($filesdir."AppliedFilters/helper2/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1Caps, $filter2Caps, $filter3Caps);

				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper2");
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."AppliedFilters/helper1/".$filter2, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
		}
		else {
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper1', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("/usr/bin/java -jar ".$jardir."LengthSelection.jar ".$filesdir."Filters/".$timestamp."/".$filter1." ".$filesdir."AppliedFilters/helper1");
				moveFiles($filesdir."AppliedFilters/helper1/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter1, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
			}
		}
	}
	else {
		if ($filter2 != "") {
			if ($filter3 != "") {
				mkdir($filesdir.'AppliedFilters/helper1', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/long', 0777, true);
				mkdir($filesdir.'AppliedFilters/helper1/shortAndAverage', 0777, true);
				shell_exec("/usr/bin/java -jar ".$jardir."LengthSelection.jar ".$filesdir."Filters/".$timestamp."/".$filter2." ".$filesdir."AppliedFilters/helper1");
				moveFiles($filesdir."AppliedFilters/helper1/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
				shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
			}
			else {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter2, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
			}
		}
		else {
			if ($filter3 != "") {
				moveFiles($filesdir."Filters/".$timestamp."/".$filter3, $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter);
				addAppliedFiltersFiles($timestamp, $appliedFilter, $filter1, $filter2, $filter3);
			}
			else {
			}
		}
	}
}

// create the job files
$dirName = $filesdir."AppliedFilters/".$timestamp."/".$appliedFilter;
$sentNo = array();
$filesNo = array();
if(isset($_POST['noscause']) && strlen(trim($_POST['noscause'])) != 0) {
	array_push($sentNo, $_POST["noscause"]);
	array_push($filesNo, getRelationFileName($dirName, "cause"));
}
if(isset($_POST['noscontra']) && strlen(trim($_POST['noscontra'])) != 0) {
	array_push($sentNo, $_POST["noscontra"]);
	array_push($filesNo, getRelationFileName($dirName, "contra"));
}
if(isset($_POST['nosdiagnose']) && strlen(trim($_POST['nosdiagnose'])) != 0) {
        array_push($sentNo, $_POST["nosdiagnose"]);
        array_push($filesNo, getRelationFileName($dirName, "diagnose"));
}
if(isset($_POST['noslocation']) && strlen(trim($_POST['noslocation'])) != 0) {
        array_push($sentNo, $_POST["noslocation"]);
        array_push($filesNo, getRelationFileName($dirName, "location"));
}
if(isset($_POST['nosprevent']) && strlen(trim($_POST['nosprevent'])) != 0) {
        array_push($sentNo, $_POST["nosprevent"]);
        array_push($filesNo, getRelationFileName($dirName, "prevent"));
}
if(isset($_POST['nossymptom']) && strlen(trim($_POST['nossymptom'])) != 0) {
	array_push($sentNo, $_POST["nossymptom"]);
	array_push($filesNo, getRelationFileName($dirName, "symptom"));
}
if(isset($_POST['nostreat']) && strlen(trim($_POST['nostreat'])) != 0) {
        array_push($sentNo, $_POST["nostreat"]);
        array_push($filesNo, getRelationFileName($dirName, "treat"));
}


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

 shell_exec("rm -rf ".$filesdir."AppliedFilters/helper1");
 shell_exec("rm -rf ".$filesdir."AppliedFilters/helper2");

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
$execCode = "/usr/bin/java -jar JobFileCreation.jar ".$filesdir."Experiments/".$timestamp."/".$appliedFilter."/".$fileName." "; 

for ($i = 0; $i < count($sentNo); $i ++) {
	$execCode .= $sentNo[$i]." ".$filesdir."AppliedFilters/".$timestamp."/".$appliedFilter."/".$filesNo[$i]." ";
}

$result = shell_exec($execCode);

$storageCSV = $filesdir."Experiments/".$timestamp."/".$appliedFilter;
$fileid = storeCSVFile($fileName, $storageCSV);
$batch_size = getLines($fileid) - 2;
$filters = str_replace("-", ", ", $appliedFilter); 
$comment = $_POST["files_comment"];
$query="INSERT INTO `batches_for_cf` (`file_id`, `filter_named`, `batch_size`, `comment`, `created_by`) 
        VALUES ('".$fileid."','".$filters."','".$batch_size."','".$comment."','".$_SERVER['REMOTE_USER']."')";
mysql_query($query) or dieError("function: batches_for_cf<br/>".$query."<br/>".mysql_error());

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
