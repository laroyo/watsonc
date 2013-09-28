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

/* Annotation Distribution*/
$pivot_table = getPivotTable($job_id); 

/* Annotated Relations */
$maj_relations = getMajRelations($job_id); 

/* Workers*/

$num_spammers = queryOne("select count(distinct(worker_id)) as count from filtered_workers where set_id = $job_id");
$num_workers = queryOne("select count(distinct(worker_id)) as count from cflower_results where job_id = $job_id");
$num_sentences = queryOne("select count(distinct(unit_id)) as count from cflower_results where job_id = $job_id");

$worker_filters = queryGroup("select worker_id, filter from filtered_workers where set_id = $job_id order by worker_id asc", 'worker_id', 'filter');

$spam_per_filter = queryGroup("select worker_id, filter from filtered_workers where set_id = $job_id order by filter asc,worker_id asc", 'filter', 'worker_id');

$spam_per_channel = queryGroup("select fw.worker_id as worker_id,external_type from filtered_workers fw left join cflower_results cf ". 
			     "on cf.worker_id = fw.worker_id where set_id = $job_id group by worker_id", 'external_type', 'worker_id');

$worker_channels = queryGroup("select worker_id,external_type as channel from cflower_results where job_id = $job_id 
       group by worker_id order by channel asc",'channel','worker_id');

$comp_times = queryGroup("select unit_id,worker_id,UNIX_TIMESTAMP(created_at)-UNIX_TIMESTAMP(started_at) as time from cflower_results where job_id = $job_id 
   order by unit_id asc,time asc limit 80",'unit_id');

include('header.tpl');
include('job.tpl');
?>
