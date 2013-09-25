<?php

require_once('sentences.inc'); 
require_once('relations.inc'); 

if(isset($_GET['relation_id']))
  $relation_id = $_GET['relation_id'];   

if(isset($_GET['test']))
  $relation_id = 'S';


$relations = array('D'=> "RelName",'S'=> "Symptom",'C'=> "RelName",'M'=> "RelName",'L'=> "RelName",'AW'=> "RelName",'P'=> "RelName",'SE'=> "RelName",'IA'=> "RelName",'PO'=> "RelName",'T' => 'Treats','CI'=> "RelName", 'OTH'=> "RelName", "NONE");

$sent_ids = getSentencesForRelation($relation_id); 

foreach($sent_ids  as $sent_id)
  $sent_info[$sent_id] = getSentenceInfo($sent_id); 

$rel_similarity = getRelationSimilarity($relation_id); 
$rel_ambiguity = getRelationAmbiguity($relation_id); 
$rel_clarity = getRelationClarity($relation_id); 

include('header.tpl');
include('relation.tpl'); 

?>
