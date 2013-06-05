<?php 
include_once 'dbinfo.php';
$filesdir = "/var/www/files/";
function dieError($msg) {
  error_log($msg);
  die($msg);
}

$file_types = array(
		    "AnalysisFiles", 	       
		    "Appliedfileters",
		    "CFlowerResultFiles",
		    "CSVFiles",
		    "Experiments",
		    "FilteredSentences",
		    "FilteredWorkers",
		    "Filters",
		    "TextFiles", 	       	       
	       ); 


function getFolder() {
	global $filesdir;
	date_default_timezone_set('UTC');
	//and create it if it is not there
	$checkyear    =    date("Y");
	$checkmonth    =    date("m");
	$checkday    =    date("d");
	// Checking for this year's folder
	if(!is_dir($filesdir.$checkyear)) {
		mkdir($filesdir.$checkyear,0755);
	}
	// Checking for this month's folder
	if(!is_dir($filesdir.$checkyear."/".$checkmonth)) {
		mkdir($filesdir.$checkyear."/".$checkmonth,0755);
	}
	// Checking for today's folder
	if(!is_dir($filesdir.$checkyear."/".$checkmonth."/".$checkday)) {
		mkdir($filesdir.$checkyear."/".$checkmonth."/".$checkday,0755);
	}
	return $filesdir.$checkyear."/".$checkmonth."/".$checkday."/";
}
/**
 * Store a file received by POST (on the variable $_FILES)
 **/
function storeFile($filefieldname) {
	$storage_path = getFolder().uniqid()."_".basename( $_FILES[$filefieldname]['name']);
	$original_name = $_FILES[$filefieldname]['name'];
	$mime_type = $_FILES[$filefieldname]['type'];
	$filesize = $_FILES[$filefieldname]['size'];
	if(move_uploaded_file($_FILES[$filefieldname]['tmp_name'], $storage_path)) {
		$query="INSERT INTO `file_storage`(`original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`) 
		VALUES ('".$original_name."','".$storage_path."','".$mime_type."',".$filesize.",'".$_SERVER['REMOTE_USER']."')";
		mysql_query($query) or dieError("function: storeFile<br/>".$query."<br/>".mysql_error());
		return mysql_insert_id();
	} else{
		return null;
	}
}

/**
 * @author Guillermo S. 
 * Build the folder path for storing the different types of file results. 
 **/
function getFolderForResults($type, $time=NULL){
  
  global $file_types; 
  global $filesdir; 
  
  if($time == NULL){
    $time = time(); 
  }
  
  $currentDate = date('dFY',$time);
  $currentTime = date('H:i:s',$time);  
  $timestamp = $currentDate."-".$currentTime;
  if(in_array($type, $file_types)){    
    return $filesdir.  $type."/" . $timestamp;  
  } else {
    throw new Exception("Invalid folder type: ". $type); 
  }
}


/**
 * @author Guillermo S. 
 * Store a file, passing the content as a parameter. 
 **/
function storeContentInFile($file_info, $content,$createdby) {
  $base_dir = getFolderForResults($file_info['file_type']) .'/'; 
  if(!is_dir($base_dir))
    mkdir($base_dir, 0777, true); 

  $storage_path = $base_dir . basename($file_info['name']);	
  
  $original_name = $file_info['name'];
  $mime_type = $file_info['mime_type'];
  $file_type = $file_info['file_type']; 
  $job_id = $file_info['job_id']; 

  // Store the information as a File. 
  switch($mime_type){
  case "text/csv": 
    $filesize = 0; 
    $fp_csv = fopen($storage_path, 'w');
    foreach($content as $row){
      $filesize += fputcsv($fp_csv, $row); 
    }	  
    fclose($fp_csv); 
    break; 
  }

  //Store the information in the database. 
  switch($file_type){
  case 'CFlowerResultFiles': 
    $nline = 0; 
    foreach($content as $row){
      $q = "insert into cflower_results (job_id,unit_id,worker_id,worker_trust,external_type,relation,selected_words,explanation,started_at,created_at,term1,term2,sentence)".

	"values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')";   
      if($nline > 0){
	$row[5] = mysql_real_escape_string($row[5]);
	$row[6] = mysql_real_escape_string($row[6]);
	$row[7] = date("Y-m-d H:i:s",strtotime($row[7])); 
	$row[8] = date("Y-m-d H:i:s",strtotime($row[8])); 
	$row[9] = mysql_real_escape_string($row[9]);
	$row[10] = mysql_real_escape_string($row[10]);
	$row[11] = mysql_real_escape_string($row[11]);
	for($i = 0; $i < sizeof($row); $i++){
	  $row[$i] = trim($row[$i]); 
	}
	
	array_unshift($row,$job_id); 
	$sql = vsprintf($q, $row);            
      
	mysql_query($sql) or dieError("function: storeContentInFile<br/>\n".$sql."<br/>\n".mysql_error());
	$inserted += mysql_affected_rows();
      }
      $nline++; 
    }
    break; 
  }

  // Store the file metadata. 
  if($filesize > 0){       
    $query="INSERT INTO `file_storage`(`original_name`, `storage_path`, `mime_type`, `filesize`, `createdby`) 
		VALUES ('".$original_name."','".$storage_path."','".$mime_type."',".$filesize.",'".$createdby."')";
    mysql_query($query) or dieError("function: storeContentInFile<br/>".$query."<br/>".mysql_error());
    return mysql_insert_id();
  } else{
    return null;
  }
  
  // Store results file path to history table
  	
  	$getresultsfile_id =   mysql_query("select id from file_storage where original_name = '$original_name' ") or die(mysql_error());	
	$resultsfile_ids = array();
	while( $row = mysql_fetch_row($getresultsfile_id))
  {
    $resultsfile_ids[] = $row['id'];
  }
	
  	$query="Update history_table set resultsfile_id = '$resultsfile_ids[0]' WHERE job_id = '$job_id'";
  	mysql_query($query) or dieError("function: storeContentInFile<br/>".$query."<br/>".mysql_error());
  	
}

/**
 * @autor Guillermo S. 
 * Returns a file's storage path. To be used by scripts to directly access the file. 
 **/
function getFileStoragePath($file_type, $search_criteria){     
  //FIXME: First version, to be improved (almost a mock up). 
  //It should use both the file_type (i.e. 'ResultsFile'), and the defined attributes for that type of files to univocally retrieve the file path. 
  // So far, it uses only the original name to retrieve the file. (The user has to take care of naming appropriately). 

  $query = "SELECT storage_path FROM file_storage where `original_name` = '" . $search_criteria['original_name'] . "'";  
  $res = mysql_query($query) or dieError("function: storeFile<br/>".$query."<br/>".mysql_error());
  if($res){
    return mysql_fetch_field($res);    
  }
}

function getOneFieldFromQuery($query,$field) {
	
	$data = mysql_query($query) or dieError("getOneFieldFromQuery<br/>".$query."<br/>".mysql_error());
	$row = mysql_fetch_assoc($data);
	return $row[$field];
}

function getLines($id) {
	$query="SELECT storage_path FROM file_storage WHERE id = '$id'";
	$filename = getOneFieldFromQuery($query, 'storage_path');
	$linecount = 0;
	$handle = fopen($filename, "r");
	if ($handle) {
		while(!feof($handle)){
			$line = fgets($handle);
			$linecount++;
		}
		fclose($handle);
	} else  {
		dieError("function: getLines cannot open:".$filename);
	}
	
	return $linecount;
}


function updateRuntime($job_id)
{
	
	// Get current run_time
	$getCreatedDate = mysql_query("Select created_date From history_table Where job_id = '$job_id' ");
	list($date1) = mysql_fetch_row ($getCreatedDate);
	
	$date2 = date('Y-m-d H:i:s');
	$ts1 = strtotime($date1);
	$ts2 = strtotime($date2);
	
	$diff = $ts2 - $ts1;
	$days = floor($diff/86400);   //24*60*60
	$hours = round(($diff-$days*60*60*24)/(60*60));
	if($hours == 24)
	{
		$days += 1;
		$hours = 0;
	}
	$run_time = $days."d ".$hours."h";
	
	return $run_time;
}



?>
