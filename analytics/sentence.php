<?php
if(isset($_GET['sentence_id'])){
  $sentence_id = $_GET['sentence_id']; 
}

?>
<!DOCTYPE html>
<meta charset="utf-8">
<body>
<h2>Sentence Analytics for sentence <?= $sentence_id ?></h2>
<table>
<tr><td>Job ID</td><td>Agr</td><td> Num Labels</td><td>Relation</td></tr>
<tr><td><a href="/analytics/job.php?job_id=179229">179229</td><td>0.0</td><td>0</td><td><a href="/analytics/relation.php?relation_id=T">Treats</a></td></tr>
</table>

<p><span alt=" is the core crowd truth metric for relation extraction.  It is measured for each relation on each sentence as the cosine of the unit vector for the relation with the sentence vector.  The relation score is used for training and evaluation of the relation extraction system, it is viewed as the probability that the sentence expresses the relation.  This is a fundamental shift from the traditional approach, in which sentences are simply labelled as expressing, or not, the relation, and presents new challenges for the evaluation metric and especially for training"><b>Sentence-relation score</b></span> 0.0</p>
<p><span alt="is defined for each sentence as the max relation score for that sentence. If all the workers selected the same relation for a sentence, the max relation score will be 1, indicating a clear sentence.   In Figure \ref{fig:crowd-annotations-final}, sentence 735 has a clarity score of 1, whereas sentence 736 has a clarity score of 0.61, indicating a confusing or ambiguous sentence. Sentence clarity is used to weight sentences in training and evaluation of the relation extraction system, since annotators have a hard time classifying them, the machine should not be penalized as much for getting it wrong in evaluation, nor should it treat such training examples as exemplars."><b>Sentence clarity</b> 0.0</span></p>

