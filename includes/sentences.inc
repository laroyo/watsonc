<?php

require_once('dbquery.inc'); 


/**
 * Returns the sentence ids for which a relation has been annotated. 
 * @param relation_id Identifier of the relation (e.g. 'S','D', 'CI'). 
 * @param job_ids Optional: query modifier, if passed, only the annotations in those jobs will be taken into account. 
 * @param pos Optional: if its passed, it will return only the ids of the sentences for which relation has been the n-most annotated
 **/

function getSentencesForRelation($relation_id, $job_ids=NULL, $pos=NULL){
  if($pos == NULL){
    
  }
  return array('1234', '4567');     
}


/**
 * Returns the sentence information. 
 * @param sent_id Identifier of the sentence (ex: 282840283). 
 **/
function getSentenceInfo($sent_id) {
  
  return array('sent_clarity' => 0.5, 
	       'num_labels' => 8,
	       'sec_relation' =>  'S',
	       'relations' => array('D' => rand(0,10), 
				    'S' => rand(0,10), 
				    'C' => rand(0,10), 
				    'M' => 0,
				    'L' => 0,				    
				    'AW' => rand(0,10), 
				    'P' => rand(0,10), 
				    'SE' => rand(0,10),
				    'IA' => 0,
				    'PO' => 0 ,
				    'T' => rand(0,10),
				    'CI'  => rand(0,10), 
				    'OTH' => rand(0,10)),
	       'text' => 'This is the text of the sentence', 
	       ); 
}


/**
 * Returns the jobs a sentence has been annotated in. 
 * @param sentence_id 
 **/
function getJobsForSentence($sentence_id){
  return array(178569);     
}




