<?php

require_once("sentences.inc"); 

if(isset($_GET['sentence_id']))
  $sentence_id = $_GET['sentence_id']; 

if(isset($_GET['test']))
  $sentence_id = 282840283;

$job_ids = getJobsForSentence($sentence_id); 

foreach($job_ids  as $job_id)
  $job_info[$job_id] = getSentenceInfo($job_id); 


//TODO: replace mockup with actual sentence text value. 
$sentence_text = "Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus"; 

include('header.tpl');
include('sentence.tpl'); 

?>

