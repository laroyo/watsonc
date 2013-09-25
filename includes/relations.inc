<?php

$relnames = array(
		   'D'=> "RelName",
		   'S'=> "Symptom",
		   'C'=> "RelName",
		   'M'=> "RelName",
		   'L'=> "RelName",
		   'AW'=> "RelName",
		   'P'=> "RelName",
		   'SE'=> "RelName",
		   'IA'=> "RelName",
		   'PO'=> "RelName",
		   'T' => 'Treats',
		   'CI'=> "RelName", 
		   'OTH'=> "RelName", 
		   "NONE"
		   );



function getRelationSimilarity($relation_id, $job_ids = NULL){
  $relnames = array(
		    'D'=> "RelName",
		    'S'=> "Symptom",
		    'C'=> "RelName",
		    'M'=> "RelName",
		    'L'=> "RelName",
		    'AW'=> "RelName",
		    'P'=> "RelName",
		    'SE'=> "RelName",
		    'IA'=> "RelName",
		    'PO'=> "RelName",
		    'T' => 'Treats',
		    'CI'=> "RelName", 
		    'OTH'=> "RelName", 
		    "NONE"
		    );

 
  foreach(array_keys($relnames) as $relation){
    if($relation != $relation_id)
      $rel_similarity[$relation] = rand(0,10) / 10; 
  }  
  
  return $rel_similarity; 
}

function getRelationAmbiguity($relation_id, $job_ids = NULL){
  return 0.2; 
}

function getRelationClarity($relation_id, $job_ids=NULL){
  return 0.2;   
}