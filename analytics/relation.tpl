<body>
<h3>Relation Analytics for "<?= $relnames[$relation_id] ?>"</h3>

<p> Sentences labelled with relation <?= $relnames[$relation_id] ?></p>
<table class="table table-condensed" style="border-collapse:collapse;">
<thead>
<tr><td>Sentence ID</td><td>Sent. Clarity</td><td> Num Labels</td><td>2nd Relation</td></tr>
</thead>
<tbody>
<?php 
   foreach($sent_info as $sent_id => $info){
     ?> 
     <tr>
     <td><a ref='sentence.php?sentence_id=$sent_id'><?= $sent_id ?></a></td>
     <td><?= $info['sent_clarity'] ?></td>
     <td><?= $info['num_labels'] ?></td>
     <td><a href='relation.php?relation_id=<?= $info['sec_relation'] ?>'>Symptom</a></td>
     <td><span style='display: none' class='sentText'><?= $info['text'] ?></span></td>     
    </tr> 
   <?php } ?>
</tbody>
</table>

<!-- <a onclick='toggleLink'>Show text</a> -->
<button>Show sentence text</button>
<script>
   $( "button" ).click(function() {
       $( ".sentText" ).toggle();
       $(this).text(function(i, text){
          return text === "Show sentence text" ? "Hide sentence text" : "Show sentence text";
      })
     });
</script>

<h3>Relation Metrics</h3>
<p><span title="is a pairwise conditional probability that if relation $R_{i}$ is annotated in a sentence, relation $R_{j}$ is as well.  Information about relation similarity is used in training and evaluation, as it roughly indicates how confusable the linguistic expression of two relations are.  This would indicate, for example, that relation co-learning would not work for similar relations."><b>Relation similarity</b></span></p> 
<table>
<?php
 foreach($rel_similarity as $rel => $score){
   echo "<tr><td> ". $rel . " ". $score . "</td></tr>"; 
 }
?>
</table>

<p><span title="is defined for each relation as the max relation similarity for the relation.  If a relation is very clear, then it will have a low score.  Since techniques like relation co-learning have proven effective, it may be a useful property of a set of relations to exclude ambiguous relations from the set."><b>Relation ambiguity</b></span> <?= $rel_ambiguity ?></p>

<p><span title="is defined for each relation as the max sentence-relation score for the relation over all sentences.  If a relation has a high clarity score, it means that it is at least possible to express the relation clearly.  We find in our experiments that a lot of relations that exist in structured sources a very difficult to express clearly in language, and are not frequently present in textual sources.  Unclear relations may indicate unattainable learning tasks."><b>Relation clarity</b> <?= $rel_clarity ?></p>
</body>
</html>