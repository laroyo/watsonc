<?php

if(isset($_GET['relation_id'])){
  $relation_id = $_GET['relation_id'];   
}

$relations = array('D'=> "RelName",'S'=> "Symptom",'C'=> "RelName",'M'=> "RelName",'L'=> "RelName",'AW'=> "RelName",'P'=> "RelName",'SE'=> "RelName",'IA'=> "RelName",'PO'=> "RelName",'T' => 'Treats','CI'=> "RelName", 'OTH'=> "RelName", "NONE");
?>
<!DOCTYPE html>
<meta charset="utf-8">
<body>
<h2>Relation Analytics for "<?= $relations[$relation_id] ?>"</h2>

<h3> Sentences labelled with relation <?= $relations[$relation_id] ?></h3>
<table>
<tr><td>Sentence ID</td><td>Agr</td><td> Num Labels</td><td>2nd Relation</td></tr>
<tr><td><a ref="/analytics/sentence.php?sentence_id=265614956">265614956</a></td><td>0.0</td><td>0</td><td><a href="analytics/relation.php?relation=S">Symptom</a></td></tr>
</table>

<h3>Relation Metrics</h3>
<p><span title="is a pairwise conditional probability that if relation $R_{i}$ is annotated in a sentence, relation $R_{j}$ is as well.  Information about relation similarity is used in training and evaluation, as it roughly indicates how confusable the linguistic expression of two relations are.  This would indicate, for example, that relation co-learning [cite: Mitchell paper on co-learning] would not work for similar relations."><b>Relation similarity</b></span></p> 

<p><span title="is defined for each relation as the max relation similarity for the relation.  If a relation is very clear, then it will have a low score.  Since techniques like relation co-learning have proven effective, it may be a useful property of a set of relations to exclude ambiguous relations from the set."><b>Relation ambiguity</b></span> </p>

<p><span title="is defined for each relation as the max sentence-relation score for the relation over all sentences.  If a relation has a high clarity score, it means that it is at least possible to express the relation clearly.  We find in our experiments that a lot of relations that exist in structured sources a very difficult to express clearly in language, and are not frequently present in textual sources.  Unclear relations may indicate unattainable learning tasks."><b>Relation clarity</b> </p>

