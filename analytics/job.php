<?php

require_once('envars.php'); 
require_once(BASE_PATH . '/dataproc/api/api.inc'); 
require_once('relations.inc'); 
require_once('dataproc.inc'); 


if(isset($_GET['job_id'])){
  $job_id = $_GET['job_id'];    
  $job_ids = array($job_id);
} 

if(isset($_GET['set_id'])){
  $set_id = $_GET['set_id'];    
  $job_ids = getJobsInSet($set_id);
} 

if (isset($_POST['job_ids'])){

  $job_ids = array_map('intval', $_POST['job_ids']); 
  sort($job_ids); 

  /* $set_id  = getSetId($job_ids);  */
  //FIXME: extend, to be able to analyze several grouped jobs. 
  $job_id = $job_ids[0];
} 

if (isset($_GET['test'])){  
  $job_id = 196304; 
  $job_ids = array(196304);
}

//FIXME: Move this to a library or somewhere where it could be easily reused. 
$abbr = array('C' =>'[CAUSES]', 
	       'S' => '[SYMPTOM]',
	       'L'=>'[LOCATION]',
	       'P'=>'[PREVENTS]',
	       'D'=>'[DIAGNOSE_BY_TEST_OR_DRUG]',
	       'M'=>'[MANIFESTATION]',
	       'AW'=>'[ASSOCIATED_WITH]',
	       'PO'=>'[PART_OF]',
	       'OTH'=>'[OTHER]',
	       'T'=>'[TREATS]',
	       'NONE'=>'[NONE]',
	       'IA'=>'[IS_A]',
	       'SE'=>'[SIDE_EFFECT]',
	       'CI'=>'[CONTRAINDICATES]',
	       'D'=>'[DIAGNOSED_BY_TEST_OR_DRUG]');


$pivot_table = getPivotTable($job_id); 
$maj_relations = getMajRelations($job_id); 

$compTimes = queryGroup("select unit_id,worker_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) as time from cflower_results where job_id = $job_id 
   order by unit_id asc,time asc limit 80",'unit_id');

include('header.tpl');
include('job.tpl');
?>
